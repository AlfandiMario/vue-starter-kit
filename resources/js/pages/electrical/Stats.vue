<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, computed, watch, onUnmounted } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import * as XLSX from 'xlsx/xlsx.mjs';
import type { PaginationEvent } from '@/types';

// ECharts - Let's load these once the component is mounted
let echarts: any = null;

// Define breadcrumbs for navigation
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Electrical',
        href: '/electrical',
    },
    {
        title: 'Statistics',
        href: '/electrical/stats',
    },
];

// Current date for display
const currentDate = new Date();
const currentYear = currentDate.getFullYear();
const currentMonth = currentDate.getMonth();
const formattedDate = new Date().toLocaleString('en-US', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
});

// Filter states
const selectedYear = ref(currentYear);
const dateRange = ref({
    start: new Date(currentYear, currentMonth - 1, 1).toISOString().split('T')[0], // 1 month ago
    end: new Date(currentYear, currentMonth, 0).toISOString().split('T')[0] // Last day of previous month
});

// Pagination state for billing table
const currentPage = ref(1);
const itemsPerPage = 8;

// Available years for the filter
const years = [2023, 2024, 2025];

// Available rooms
const rooms = [
    { id: 1, name: 'Living Room', color: '#00a1e5' },
    { id: 2, name: 'Kitchen', color: '#0e7da7' },
    { id: 3, name: 'Bedroom', color: '#5bc0de' },
    { id: 4, name: 'Bathroom', color: '#4cae4c' }
];

// Chart containers refs
const dailyChartRef = ref<HTMLElement | null>(null);
const monthlyChartRef = ref<HTMLElement | null>(null);
const annualChartRef = ref<HTMLElement | null>(null);

// Chart instances
let dailyChart: any = null;
let monthlyChart: any = null;
let annualChart: any = null;

// Mock data for daily consumption
const generateDailyData = (year: number, month: number) => {
    const daysInMonth = new Date(year, month, 0).getDate();
    const result = [];
    
    for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(year, month - 1, day).toISOString().split('T')[0];
        const roomData: any = { date };
        
        let total = 0;
        rooms.forEach(room => {
            // Base value that's somewhat consistent for each room
            const baseValue = room.id === 1 ? 0.8 : room.id === 2 ? 2.4 : room.id === 3 ? 0.6 : 0.3;
            // Add some randomness, but keep it within 20% of base value
            const randomFactor = 0.8 + Math.random() * 0.4; // 80% to 120%
            const value = parseFloat((baseValue * randomFactor).toFixed(2));
            roomData[room.name] = value;
            total += value;
        });
        
        roomData['Total'] = parseFloat(total.toFixed(2));
        result.push(roomData);
    }
    
    return result;
};

// Mock data for monthly consumption
const generateMonthlyData = (year: number) => {
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return monthNames.map((month, index) => {
        const roomData: any = { month, year };
        
        let total = 0;
        rooms.forEach(room => {
            // Create a pattern where usage is higher in winter/summer and lower in spring/fall
            const seasonality = Math.sin((index / 12) * Math.PI * 2) * 0.3 + 1;
            // Base value that's somewhat consistent for each room
            const baseValue = room.id === 1 ? 24 : room.id === 2 ? 72 : room.id === 3 ? 18 : 9;
            // Add some randomness, but keep it within 10% of seasonal value
            const randomFactor = 0.9 + Math.random() * 0.2; // 90% to 110%
            const value = parseFloat((baseValue * seasonality * randomFactor).toFixed(2));
            roomData[room.name] = value;
            total += value;
        });
        
        roomData['Total'] = parseFloat(total.toFixed(2));
        return roomData;
    });
};

// Mock data for annual consumption
const generateAnnualData = () => {
    return years.map(year => {
        const roomData: any = { year };
        
        let total = 0;
        rooms.forEach(room => {
            // Base value that's somewhat consistent for each room
            const baseValue = room.id === 1 ? 288 : room.id === 2 ? 864 : room.id === 3 ? 216 : 108;
            // Small increase each year (5%)
            const yearFactor = 1 + (year - years[0]) * 0.05;
            // Add some randomness, but keep it within 5% of year-adjusted value
            const randomFactor = 0.95 + Math.random() * 0.1; // 95% to 105%
            const value = parseFloat((baseValue * yearFactor * randomFactor).toFixed(2));
            roomData[room.name] = value;
            total += value;
        });
        
        roomData['Total'] = parseFloat(total.toFixed(2));
        return roomData;
    });
};

// Mock data for monthly billing
interface BillingRecord {
    id: number;
    month: string;
    year: number;
    kWh: number;
    bill: number;
    change: number; // Percentage change from previous month
    changeType: 'increase' | 'decrease' | 'unchanged';
}

const generateBillingData = () => {
    const months = ['January', 'February', 'March', 'April', 'May', 'June', 
                    'July', 'August', 'September', 'October', 'November', 'December'];
    const result: BillingRecord[] = [];
    
    // Generate 3 years of billing data
    for (const year of years) {
        for (let i = 0; i < 12; i++) {
            const monthName = months[i];
            
            // Create a pattern where usage is higher in winter/summer and lower in spring/fall
            const seasonality = Math.sin((i / 12) * Math.PI * 2) * 0.3 + 1;
            // Base value
            const baseKWh = 120;
            // Small increase each year (3%)
            const yearFactor = 1 + (year - years[0]) * 0.03;
            // Add some randomness, but keep it within 10% of seasonal value
            const randomFactor = 0.9 + Math.random() * 0.2; // 90% to 110%
            const kWh = parseFloat((baseKWh * seasonality * yearFactor * randomFactor).toFixed(2));
            
            // Calculate bill (assume $0.15 per kWh)
            const bill = parseFloat((kWh * 0.15).toFixed(2));
            
            // Calculate change from previous month
            let change = 0;
            let changeType: 'increase' | 'decrease' | 'unchanged' = 'unchanged';
            
            if (result.length > 0) {
                const prevKWh = result[result.length - 1].kWh;
                change = parseFloat((((kWh - prevKWh) / prevKWh) * 100).toFixed(1));
                changeType = change > 0 ? 'increase' : change < 0 ? 'decrease' : 'unchanged';
            }
            
            result.push({
                id: result.length + 1,
                month: monthName,
                year,
                kWh,
                bill,
                change,
                changeType
            });
        }
    }
    
    return result;
};

// Data stores
const dailyData = ref(generateDailyData(currentYear, currentMonth));
const monthlyData = ref(generateMonthlyData(currentYear));
const annualData = ref(generateAnnualData());
const billingData = ref(generateBillingData());

// Apply date range filter to daily data
const filteredDailyData = computed(() => {
    return dailyData.value.filter(item => {
        return item.date >= dateRange.value.start && item.date <= dateRange.value.end;
    });
});

// Filter billing data by selected year
const filteredBillingData = computed(() => {
    return billingData.value.filter(item => item.year === selectedYear.value);
});

// Pagination logic for billing table
const totalPages = computed(() => Math.ceil(filteredBillingData.value.length / itemsPerPage));
const paginatedBillingData = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredBillingData.value.slice(start, end);
});

// Function to handle pagination
const handlePageChange = (page: number) => {
    currentPage.value = page;
};

// Export functions
const exportDailyData = () => {
    // Prepare data for export
    const data = [
        ['Date', ...rooms.map(room => room.name), 'Total']
    ];
    
    filteredDailyData.value.forEach(item => {
        const row = [
            item.date, 
            ...rooms.map(room => item[room.name]),
            item['Total']
        ];
        data.push(row);
    });
    
    // Create workbook and worksheet
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(data);
    
    // Add worksheet to workbook
    XLSX.utils.book_append_sheet(wb, ws, 'Daily Consumption');
    
    // Generate file name
    const fileName = `Daily_Electricity_Consumption_${dateRange.value.start}_to_${dateRange.value.end}.xlsx`;
    
    // Export workbook
    XLSX.writeFile(wb, fileName);
};

const exportMonthlyData = () => {
    // Prepare data for export
    const data = [
        ['Month', 'Year', ...rooms.map(room => room.name), 'Total']
    ];
    
    monthlyData.value.forEach(item => {
        const row = [
            item.month, 
            item.year,
            ...rooms.map(room => item[room.name]),
            item['Total']
        ];
        data.push(row);
    });
    
    // Create workbook and worksheet
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(data);
    
    // Add worksheet to workbook
    XLSX.utils.book_append_sheet(wb, ws, 'Monthly Consumption');
    
    // Generate file name
    const fileName = `Monthly_Electricity_Consumption_${selectedYear.value}.xlsx`;
    
    // Export workbook
    XLSX.writeFile(wb, fileName);
};

const exportAnnualData = () => {
    // Prepare data for export
    const data = [
        ['Year', ...rooms.map(room => room.name), 'Total']
    ];
    
    annualData.value.forEach(item => {
        const row = [
            item.year, 
            ...rooms.map(room => item[room.name]),
            item['Total']
        ];
        data.push(row);
    });
    
    // Create workbook and worksheet
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(data);
    
    // Add worksheet to workbook
    XLSX.utils.book_append_sheet(wb, ws, 'Annual Consumption');
    
    // Generate file name
    const fileName = `Annual_Electricity_Consumption_All_Years.xlsx`;
    
    // Export workbook
    XLSX.writeFile(wb, fileName);
};

const exportBillingData = () => {
    // Prepare data for export
    const data = [
        ['Month', 'Year', 'kWh', 'Bill ($)', 'Change From Last Month (%)']
    ];
    
    filteredBillingData.value.forEach(item => {
        const row = [
            item.month, 
            item.year,
            item.kWh,
            item.bill,
            item.change
        ];
        data.push(row);
    });
    
    // Create workbook and worksheet
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(data);
    
    // Add worksheet to workbook
    XLSX.utils.book_append_sheet(wb, ws, 'Electricity Bills');
    
    // Generate file name
    const fileName = `Electricity_Bills_${selectedYear.value}.xlsx`;
    
    // Export workbook
    XLSX.writeFile(wb, fileName);
};

// Initialize charts
const initCharts = async () => {
    // Dynamically import echarts
    if (!echarts) {
        echarts = await import('echarts/core');
        const { LineChart, BarChart } = await import('echarts/charts');
        const { GridComponent, TooltipComponent, LegendComponent, TitleComponent, ToolboxComponent } = await import('echarts/components');
        const { CanvasRenderer } = await import('echarts/renderers');
        
        // Register components
        echarts.use([
            LineChart,
            BarChart, 
            GridComponent, 
            TooltipComponent, 
            LegendComponent,
            TitleComponent,
            ToolboxComponent,
            CanvasRenderer
        ]);
    }
    
    // Initialize charts if refs are ready
    if (dailyChartRef.value) {
        initDailyChart();
    }
    
    if (monthlyChartRef.value) {
        initMonthlyChart();
    }
    
    if (annualChartRef.value) {
        initAnnualChart();
    }
};

// Initialize daily consumption chart
const initDailyChart = () => {
    // Dispose if exists
    if (dailyChart) {
        dailyChart.dispose();
    }
    
    // Initialize chart
    dailyChart = echarts.init(dailyChartRef.value);
    
    // Prepare series data
    const series = rooms.map(room => ({
        name: room.name,
        type: 'line',
        data: filteredDailyData.value.map(item => item[room.name]),
        smooth: true,
        itemStyle: {
            color: room.color
        }
    }));
    
    // Add total
    series.push({
        name: 'Total',
        type: 'line',
        data: filteredDailyData.value.map(item => item['Total']),
        smooth: true,
        lineStyle: {
            width: 3,
            type: 'dashed'
        },
        itemStyle: {
            color: '#ff9800'
        }
    });
    
    // Set chart options
    const option = {
        title: {
            text: 'Daily Electricity Consumption',
            left: 'center'
        },
        tooltip: {
            trigger: 'axis',
            formatter: function(params: any) {
                let result = params[0].axisValue + '<br/>';
                params.forEach((param: any) => {
                    result += `${param.marker} ${param.seriesName}: ${param.value} kWh<br/>`;
                });
                return result;
            }
        },
        legend: {
            data: [...rooms.map(room => room.name), 'Total'],
            bottom: 0
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '10%',
            top: '15%',
            containLabel: true
        },
        toolbox: {
            feature: {
                saveAsImage: {}
            }
        },
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: filteredDailyData.value.map(item => item.date)
        },
        yAxis: {
            type: 'value',
            name: 'kWh',
            nameLocation: 'middle',
            nameGap: 30
        },
        series
    };
    
    // Set chart options
    dailyChart.setOption(option);
    
    // Resize handler
    window.addEventListener('resize', () => {
        dailyChart.resize();
    });
};

// Initialize monthly consumption chart
const initMonthlyChart = () => {
    // Dispose if exists
    if (monthlyChart) {
        monthlyChart.dispose();
    }
    
    // Initialize chart
    monthlyChart = echarts.init(monthlyChartRef.value);
    
    // Filter data for selected year
    const yearData = monthlyData.value.filter(item => item.year === selectedYear.value);
    
    // Prepare series data
    const series = rooms.map(room => ({
        name: room.name,
        type: 'bar',
        stack: 'total',
        emphasis: {
            focus: 'series'
        },
        data: yearData.map(item => item[room.name]),
        itemStyle: {
            color: room.color
        }
    }));
    
    // Add total as a line
    series.push({
        name: 'Total',
        type: 'line',
        data: yearData.map(item => item['Total']),
        smooth: true,
        symbol: 'circle',
        symbolSize: 8,
        lineStyle: {
            width: 3
        },
        itemStyle: {
            color: '#ff9800'
        }
    });
    
    // Set chart options
    const option = {
        title: {
            text: `Monthly Electricity Consumption (${selectedYear.value})`,
            left: 'center'
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params: any) {
                let result = params[0].axisValue + '<br/>';
                let total = 0;
                
                // First show individual rooms
                for (let i = 0; i < params.length - 1; i++) {
                    const param = params[i];
                    result += `${param.marker} ${param.seriesName}: ${param.value} kWh<br/>`;
                    total += param.value;
                }
                
                // Then show the total
                result += `<br/><strong>Total: ${total.toFixed(2)} kWh</strong>`;
                return result;
            }
        },
        legend: {
            data: [...rooms.map(room => room.name), 'Total'],
            bottom: 0
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '10%',
            top: '15%',
            containLabel: true
        },
        toolbox: {
            feature: {
                saveAsImage: {}
            }
        },
        xAxis: {
            type: 'category',
            data: yearData.map(item => item.month)
        },
        yAxis: {
            type: 'value',
            name: 'kWh',
            nameLocation: 'middle',
            nameGap: 30
        },
        series
    };
    
    // Set chart options
    monthlyChart.setOption(option);
    
    // Resize handler
    window.addEventListener('resize', () => {
        monthlyChart.resize();
    });
};

// Initialize annual consumption chart
const initAnnualChart = () => {
    // Dispose if exists
    if (annualChart) {
        annualChart.dispose();
    }
    
    // Initialize chart
    annualChart = echarts.init(annualChartRef.value);
    
    // Prepare series data
    const series = rooms.map(room => ({
        name: room.name,
        type: 'bar',
        stack: 'total',
        emphasis: {
            focus: 'series'
        },
        data: annualData.value.map(item => item[room.name]),
        itemStyle: {
            color: room.color
        }
    }));
    
    // Add total as a line
    series.push({
        name: 'Total',
        type: 'line',
        data: annualData.value.map(item => item['Total']),
        smooth: true,
        symbol: 'circle',
        symbolSize: 8,
        lineStyle: {
            width: 3
        },
        itemStyle: {
            color: '#ff9800'
        }
    });
    
    // Set chart options
    const option = {
        title: {
            text: 'Annual Electricity Consumption',
            left: 'center'
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params: any) {
                let result = params[0].axisValue + '<br/>';
                let total = 0;
                
                // First show individual rooms
                for (let i = 0; i < params.length - 1; i++) {
                    const param = params[i];
                    result += `${param.marker} ${param.seriesName}: ${param.value} kWh<br/>`;
                    total += param.value;
                }
                
                // Then show the total
                result += `<br/><strong>Total: ${total.toFixed(2)} kWh</strong>`;
                return result;
            }
        },
        legend: {
            data: [...rooms.map(room => room.name), 'Total'],
            bottom: 0
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '10%',
            top: '15%',
            containLabel: true
        },
        toolbox: {
            feature: {
                saveAsImage: {}
            }
        },
        xAxis: {
            type: 'category',
            data: annualData.value.map(item => item.year)
        },
        yAxis: {
            type: 'value',
            name: 'kWh',
            nameLocation: 'middle',
            nameGap: 30
        },
        series
    };
    
    // Set chart options
    annualChart.setOption(option);
    
    // Resize handler
    window.addEventListener('resize', () => {
        annualChart.resize();
    });
};

// Watch for changes in filters and update charts
watch([selectedYear, dateRange], ([newYear, newDateRange], [oldYear, oldDateRange]) => {
    // Update monthly data and chart when year changes
    if (newYear !== oldYear) {
        initMonthlyChart();
        // Reset pagination when year changes
        currentPage.value = 1;
    }
    
    // Update daily chart when date range changes
    if (newDateRange.start !== oldDateRange?.start || newDateRange.end !== oldDateRange?.end) {
        initDailyChart();
    }
}, { deep: true });

// Initialize on component mount
onMounted(async () => {
    // Initialize charts
    await initCharts();
    
    // Watch for window resize
    const resizeObserver = new ResizeObserver(() => {
        if (dailyChart) dailyChart.resize();
        if (monthlyChart) monthlyChart.resize();
        if (annualChart) annualChart.resize();
    });

    if (dailyChartRef.value) resizeObserver.observe(dailyChartRef.value);
    if (monthlyChartRef.value) resizeObserver.observe(monthlyChartRef.value);
    if (annualChartRef.value) resizeObserver.observe(annualChartRef.value);

    // Clean up on component unmount
    onUnmounted(() => {
        resizeObserver.disconnect();
        window.removeEventListener('resize', handleResize);
    });
});

const handleResize = () => {
    if (dailyChart) dailyChart.resize();
    if (monthlyChart) monthlyChart.resize();
    if (annualChart) annualChart.resize();
};

</script>

<template>
    <Head title="Electricity Statistics" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 px-8 py-6">
            <!-- Header with filters -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-foreground">Electricity Statistics</h1>
                    <p class="text-sm text-muted-foreground">Analyze consumption patterns and trends</p>
                </div>
                
                <div class="flex items-center gap-3">
                    <select 
                        v-model="selectedYear"
                        class="pl-4 pr-10 py-2 rounded-md border border-border bg-card text-card-foreground shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                    >
                        <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                    </select>
                </div>
            </div>

            <!-- Daily consumption chart section -->
            <div class="rounded-xl border border-border bg-card shadow-sm dark:bg-card/50">
                <div class="border-b border-border/50 px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <div>
                        <h2 class="text-xl font-semibold text-foreground">Daily Consumption</h2>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Daily electricity usage by room
                        </p>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-3 mt-3 sm:mt-0">
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-muted-foreground">From:</label>
                            <input 
                                type="date" 
                                v-model="dateRange.start"
                                class="px-3 py-1 rounded-md border border-border bg-card text-card-foreground text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                            />
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-muted-foreground">To:</label>
                            <input 
                                type="date" 
                                v-model="dateRange.end"
                                class="px-3 py-1 rounded-md border border-border bg-card text-card-foreground text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                            />
                        </div>
                        <Button @click="exportDailyData" variant="outline" size="sm" class="ml-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export
                        </Button>
                    </div>
                </div>
                
                <div class="p-6" style="min-height: 320px; position: relative;">
                    <div ref="dailyChartRef" class="w-full" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;"></div>
                </div>
            </div>

            <!-- Monthly consumption chart section -->
            <div class="rounded-xl border border-border bg-card shadow-sm dark:bg-card/50">
                <div class="border-b border-border/50 px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <div>
                        <h2 class="text-xl font-semibold text-foreground">Monthly Consumption</h2>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Monthly electricity usage by room for {{ selectedYear }}
                        </p>
                    </div>
                    
                    <Button @click="exportMonthlyData" variant="outline" size="sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export
                    </Button>
                </div>
                
                <div class="p-6" style="min-height: 320px; position: relative;">
                    <div ref="monthlyChartRef" class="w-full" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;"></div>
                </div>
            </div>

            <!-- Annual consumption chart section -->
            <div class="rounded-xl border border-border bg-card shadow-sm dark:bg-card/50">
                <div class="border-b border-border/50 px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <div>
                        <h2 class="text-xl font-semibold text-foreground">Annual Consumption</h2>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Yearly electricity usage by room
                        </p>
                    </div>
                    
                    <Button @click="exportAnnualData" variant="outline" size="sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export
                    </Button>
                </div>
                
                <div class="p-6" style="min-height: 320px; position: relative;">
                    <div ref="annualChartRef" class="w-full" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;"></div>
                </div>
            </div>

            <!-- Monthly billing table section -->
            <div class="rounded-xl border border-border bg-card shadow-sm dark:bg-card/50">
                <div class="border-b border-border/50 px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <div>
                        <h2 class="text-xl font-semibold text-foreground">Electricity Bill</h2>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Last updated: {{ formattedDate }}
                        </p>
                    </div>
                    
                    <Button @click="exportBillingData" variant="outline" size="sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export
                    </Button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-border/50 bg-muted/50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Month</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Year</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">kWh</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Bill</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Than Last Month</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="bill in paginatedBillingData" :key="bill.id" class="border-b border-border/50 hover:bg-muted/30">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-card-foreground">{{ bill.month }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-card-foreground">{{ bill.year }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-card-foreground">{{ bill.kWh }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-card-foreground">${{ bill.bill.toFixed(2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span 
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :class="{
                                            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': bill.changeType === 'decrease',
                                            'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': bill.changeType === 'increase',
                                            'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200': bill.changeType === 'unchanged'
                                        }"
                                    >
                                        <svg v-if="bill.changeType === 'increase'" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        </svg>
                                        <svg v-else-if="bill.changeType === 'decrease'" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                        <span v-if="bill.change !== 0">{{ bill.change > 0 ? '+' : '' }}{{ bill.change }}%</span>
                                        <span v-else>No change</span>
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="paginatedBillingData.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-muted-foreground">No billing data available for this year.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 flex justify-between items-center">
                    <div class="text-sm text-muted-foreground">
                        Showing {{ paginatedBillingData.length > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0 }} to {{ Math.min(currentPage * itemsPerPage, filteredBillingData.length) }} of {{ filteredBillingData.length }} entries
                    </div>
                    <div class="flex space-x-1">
                        <button 
                            @click="handlePageChange(currentPage - 1)" 
                            :disabled="currentPage === 1"
                            class="px-3 py-1 rounded border border-border bg-card text-card-foreground disabled:opacity-50"
                        >
                            Previous
                        </button>
                        <button 
                            v-for="page in totalPages" 
                            :key="page" 
                            @click="handlePageChange(page)"
                            class="px-3 py-1 rounded border"
                            :class="currentPage === page ? 'bg-primary text-primary-foreground' : 'border-border bg-card text-card-foreground'"
                        >
                            {{ page }}
                        </button>
                        <button 
                            @click="handlePageChange(currentPage + 1)" 
                            :disabled="currentPage === totalPages || totalPages === 0"
                            class="px-3 py-1 rounded border border-border bg-card text-card-foreground disabled:opacity-50"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Additional custom styles can be added here if needed */
</style>
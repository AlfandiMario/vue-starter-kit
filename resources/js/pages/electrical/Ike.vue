<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, computed, watch, onUnmounted } from 'vue';
import Button from '@/components/ui/button/Button.vue';

// ECharts - Let's load these once the component is mounted
let echarts: any = null;
let XLSX: any = null;

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
        title: 'IKE',
        href: '/electrical/ike',
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

// Available years for the filter
const years = [2023, 2024, 2025];

// IKE Index categories and their colors
const ikeCategories = [
    { name: 'Sangat Efisien', threshold: 15, color: '#4caf50' },
    { name: 'Efisien', threshold: 25, color: '#8bc34a' },
    { name: 'Cukup', threshold: 37.5, color: '#ffeb3b' },
    { name: 'Boros', threshold: 50, color: '#ff9800' },
    { name: 'Sangat Boros', threshold: Infinity, color: '#f44336' }
];

// Chart containers refs
const monthlyIkeChartRef = ref<HTMLElement | null>(null);
const annualIkeChartRef = ref<HTMLElement | null>(null);

// Chart instances
let monthlyIkeChart: any = null;
let annualIkeChart: any = null;

// Building area (sqm) for IKE calculation (kWh/sqm)
const buildingArea = 1500; // sqm

// Helper function to get IKE category based on value
const getIkeCategory = (ikeValue: number) => {
    for (const category of ikeCategories) {
        if (ikeValue <= category.threshold) {
            return category;
        }
    }
    return ikeCategories[ikeCategories.length - 1]; // Default to last category if above all thresholds
};

// Mock data for consumption summaries
const generateConsumptionSummary = () => {
    // Generate current month consumption
    const thisMonthKwh = parseFloat((120 + Math.random() * 20).toFixed(2));
    const thisMonthCost = parseFloat((thisMonthKwh * 0.15).toFixed(2));
    
    // Generate last month consumption (slightly different)
    const lastMonthKwh = parseFloat((110 + Math.random() * 30).toFixed(2));
    const lastMonthCost = parseFloat((lastMonthKwh * 0.15).toFixed(2));
    
    return {
        thisMonthKwh,
        thisMonthCost,
        lastMonthKwh,
        lastMonthCost
    };
};

// Mock data for monthly consumption
const generateMonthlyData = (year: number) => {
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return monthNames.map((month, index) => {
        // Create a pattern where usage is higher in winter/summer and lower in spring/fall
        const seasonality = Math.sin((index / 12) * Math.PI * 2) * 0.3 + 1;
        // Base value with yearly increase
        const yearFactor = 1 + (year - years[0]) * 0.03;
        // Add some randomness, but keep it within 10% of seasonal value
        const randomFactor = 0.9 + Math.random() * 0.2; // 90% to 110%
        const kWh = parseFloat((120 * seasonality * yearFactor * randomFactor).toFixed(2));
        
        // Calculate cost (assume $0.15 per kWh)
        const cost = parseFloat((kWh * 0.15).toFixed(2));
        
        // Calculate IKE (kWh/sqm)
        const ike = parseFloat((kWh / buildingArea).toFixed(2));
        
        // Determine IKE category
        const ikeCategory = getIkeCategory(ike);
        
        return {
            month,
            year,
            kWh,
            cost,
            ike,
            ikeCategory: ikeCategory.name,
            ikeColor: ikeCategory.color
        };
    });
};

// Mock data for annual consumption
const generateAnnualData = () => {
    return years.map(year => {
        // Base value with yearly increase
        const yearFactor = 1 + (year - years[0]) * 0.05;
        // Add some randomness
        const randomFactor = 0.95 + Math.random() * 0.1; // 95% to 105%
        const kWh = parseFloat((1400 * yearFactor * randomFactor).toFixed(2));
        
        // Calculate cost (assume $0.15 per kWh)
        const cost = parseFloat((kWh * 0.15).toFixed(2));
        
        // Calculate IKE (kWh/sqm)
        const ike = parseFloat((kWh / buildingArea).toFixed(2));
        
        // Determine IKE category
        const ikeCategory = getIkeCategory(ike);
        
        return {
            year,
            kWh,
            cost,
            ike,
            ikeCategory: ikeCategory.name,
            ikeColor: ikeCategory.color
        };
    });
};

// Data stores
const consumptionSummary = ref(generateConsumptionSummary());
const monthlyData = ref(generateMonthlyData(currentYear));
const annualData = ref(generateAnnualData());

// Filtered monthly data based on selected year
const filteredMonthlyData = computed(() => {
    return monthlyData.value.filter(item => item.year === selectedYear.value);
});

// Export functions
const exportMonthlyData = async () => {
    // Dynamically import XLSX if not already loaded
    if (!XLSX) {
        XLSX = await import('xlsx');
    }
    
    // Prepare data for export
    const data = [
        ['Month', 'Year', 'kWh', 'Cost ($)', 'IKE (kWh/sqm)', 'Category']
    ];
    
    filteredMonthlyData.value.forEach(item => {
        const row = [
            item.month, 
            item.year,
            item.kWh,
            item.cost,
            item.ike,
            item.ikeCategory
        ];
        data.push(row);
    });
    
    // Create workbook and worksheet
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(data);
    
    // Add worksheet to workbook
    XLSX.utils.book_append_sheet(wb, ws, 'Monthly IKE');
    
    // Generate file name
    const fileName = `Monthly_IKE_${selectedYear.value}.xlsx`;
    
    // Export workbook
    XLSX.writeFile(wb, fileName);
};

const exportAnnualData = async () => {
    // Dynamically import XLSX if not already loaded
    if (!XLSX) {
        XLSX = await import('xlsx');
    }
    
    // Prepare data for export
    const data = [
        ['Year', 'kWh', 'Cost ($)', 'IKE (kWh/sqm)', 'Category']
    ];
    
    annualData.value.forEach(item => {
        const row = [
            item.year,
            item.kWh,
            item.cost,
            item.ike,
            item.ikeCategory
        ];
        data.push(row);
    });
    
    // Create workbook and worksheet
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(data);
    
    // Add worksheet to workbook
    XLSX.utils.book_append_sheet(wb, ws, 'Annual IKE');
    
    // Generate file name
    const fileName = `Annual_IKE_All_Years.xlsx`;
    
    // Export workbook
    XLSX.writeFile(wb, fileName);
};

// Initialize charts
const initCharts = async () => {
    // Dynamically import echarts
    if (!echarts) {
        echarts = await import('echarts');
    }
    
    // Initialize charts if refs are ready
    if (monthlyIkeChartRef.value) {
        initMonthlyIkeChart();
    }
    
    if (annualIkeChartRef.value) {
        initAnnualIkeChart();
    }
};

// Initialize monthly IKE chart
const initMonthlyIkeChart = () => {
    // Dispose if exists
    if (monthlyIkeChart) {
        monthlyIkeChart.dispose();
    }
    
    // Initialize chart
    monthlyIkeChart = echarts.init(monthlyIkeChartRef.value);
    
    // Prepare data
    const months = filteredMonthlyData.value.map(item => item.month);
    const ikeValues = filteredMonthlyData.value.map(item => item.ike);
    const colors = filteredMonthlyData.value.map(item => item.ikeColor);
    
    // Set chart options
    const option = {
        title: {
            text: `Monthly Energy Consumption Index (${selectedYear.value})`,
            left: 'center'
        },
        tooltip: {
            trigger: 'axis',
            formatter: function(params: any) {
                const item = filteredMonthlyData.value[params[0].dataIndex];
                return `
                    <div style="font-weight: bold; margin-bottom: 5px;">${item.month} ${item.year}</div>
                    <div>kWh: ${item.kWh}</div>
                    <div>Cost: $${item.cost}</div>
                    <div style="color: ${item.ikeColor}; font-weight: bold;">IKE Index: ${item.ike} (${item.ikeCategory})</div>
                `;
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '10%',
            top: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: months
        },
        yAxis: {
            type: 'value',
            name: 'IKE (kWh/sqm)',
            nameLocation: 'middle',
            nameGap: 30
        },
        series: [{
            name: 'IKE',
            type: 'bar',
            data: ikeValues,
            itemStyle: {
                color: function(params: any) {
                    return colors[params.dataIndex];
                }
            }
        }]
    };
    
    // Set chart options
    monthlyIkeChart.setOption(option);
    
    // Resize handler
    window.addEventListener('resize', () => {
        monthlyIkeChart.resize();
    });
};

// Initialize annual IKE chart
const initAnnualIkeChart = () => {
    // Dispose if exists
    if (annualIkeChart) {
        annualIkeChart.dispose();
    }
    
    // Initialize chart
    annualIkeChart = echarts.init(annualIkeChartRef.value);
    
    // Prepare data
    const years = annualData.value.map(item => item.year);
    const ikeValues = annualData.value.map(item => item.ike);
    const colors = annualData.value.map(item => item.ikeColor);
    
    // Set chart options
    const option = {
        title: {
            text: 'Annual Energy Consumption Index',
            left: 'center'
        },
        tooltip: {
            trigger: 'axis',
            formatter: function(params: any) {
                const item = annualData.value[params[0].dataIndex];
                return `
                    <div style="font-weight: bold; margin-bottom: 5px;">${item.year}</div>
                    <div>kWh: ${item.kWh}</div>
                    <div>Cost: $${item.cost}</div>
                    <div style="color: ${item.ikeColor}; font-weight: bold;">IKE Index: ${item.ike} (${item.ikeCategory})</div>
                `;
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '10%',
            top: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: years
        },
        yAxis: {
            type: 'value',
            name: 'IKE (kWh/sqm)',
            nameLocation: 'middle',
            nameGap: 30
        },
        series: [{
            name: 'IKE',
            type: 'bar',
            data: ikeValues,
            itemStyle: {
                color: function(params: any) {
                    return colors[params.dataIndex];
                }
            }
        }]
    };
    
    // Set chart options
    annualIkeChart.setOption(option);
    
    // Resize handler
    window.addEventListener('resize', () => {
        annualIkeChart.resize();
    });
};

// Watch for changes in filters and update charts
watch([selectedYear], ([newYear], [oldYear]) => {
    // Update monthly chart when year changes
    if (newYear !== oldYear) {
        initMonthlyIkeChart();
    }
}, { deep: true });

// Initialize on component mount
onMounted(async () => {
    // Initialize charts
    await initCharts();
    
    // Watch for window resize
    const resizeObserver = new ResizeObserver(() => {
        if (monthlyIkeChart) monthlyIkeChart.resize();
        if (annualIkeChart) annualIkeChart.resize();
    });

    if (monthlyIkeChartRef.value) resizeObserver.observe(monthlyIkeChartRef.value);
    if (annualIkeChartRef.value) resizeObserver.observe(annualIkeChartRef.value);

    onUnmounted(() => {
        resizeObserver.disconnect();
        window.removeEventListener('resize', handleResize);
    });
});

const handleResize = () => {
    if (monthlyIkeChart) monthlyIkeChart.resize();
    if (annualIkeChart) annualIkeChart.resize();
};
</script>

<template>
    <Head title="Energy Consumption Index" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 px-8 py-6">
            <!-- Header with filters -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-foreground">Energy Consumption Index (IKE)</h1>
                    <p class="text-sm text-muted-foreground">Monitor energy efficiency and consumption patterns</p>
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

            <!-- Consumption summary section -->
            <div class="rounded-xl border border-border bg-card shadow-sm dark:bg-card/50">
                <div class="border-b border-border/50 px-6 py-4">
                    <h2 class="text-xl font-semibold text-foreground">Electricity Consumption Total</h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Current and previous month consumption overview
                    </p>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <!-- This Month kWh -->
                        <div class="flex flex-col rounded-lg border border-border bg-background p-4 shadow-sm">
                            <span class="text-sm text-muted-foreground">This Month</span>
                            <div class="mt-1 flex items-baseline">
                                <span class="text-2xl font-bold text-foreground">{{ consumptionSummary.thisMonthKwh }}</span>
                                <span class="ml-1 text-sm text-muted-foreground">kWh</span>
                            </div>
                        </div>
                        
                        <!-- This Month Cost -->
                        <div class="flex flex-col rounded-lg border border-border bg-background p-4 shadow-sm">
                            <span class="text-sm text-muted-foreground">This Month Cost</span>
                            <div class="mt-1 flex items-baseline">
                                <span class="text-2xl font-bold text-foreground">${{ consumptionSummary.thisMonthCost }}</span>
                                <span class="ml-1 text-sm text-muted-foreground">USD</span>
                            </div>
                        </div>
                        
                        <!-- Last Month kWh -->
                        <div class="flex flex-col rounded-lg border border-border bg-background p-4 shadow-sm">
                            <span class="text-sm text-muted-foreground">Last Month</span>
                            <div class="mt-1 flex items-baseline">
                                <span class="text-2xl font-bold text-foreground">{{ consumptionSummary.lastMonthKwh }}</span>
                                <span class="ml-1 text-sm text-muted-foreground">kWh</span>
                            </div>
                        </div>
                        
                        <!-- Last Month Cost -->
                        <div class="flex flex-col rounded-lg border border-border bg-background p-4 shadow-sm">
                            <span class="text-sm text-muted-foreground">Last Month Cost</span>
                            <div class="mt-1 flex items-baseline">
                                <span class="text-2xl font-bold text-foreground">${{ consumptionSummary.lastMonthCost }}</span>
                                <span class="ml-1 text-sm text-muted-foreground">USD</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly IKE chart section -->
            <div class="rounded-xl border border-border bg-card shadow-sm dark:bg-card/50">
                <div class="border-b border-border/50 px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <div>
                        <h2 class="text-xl font-semibold text-foreground">Monthly IKE</h2>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Monthly energy consumption index for {{ selectedYear }}
                        </p>
                    </div>
                    
                    <Button @click="() => exportMonthlyData()" variant="outline" size="sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export
                    </Button>
                </div>
                
                <div class="p-6" style="min-height: 320px; position: relative;">
                    <div ref="monthlyIkeChartRef" class="w-full" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;"></div>
                </div>
                
                <!-- IKE Legend -->
                <div class="border-t border-border/50 px-6 py-4">
                    <div class="flex flex-wrap gap-4 justify-center">
                        <div v-for="category in ikeCategories" :key="category.name" class="flex items-center">
                            <div class="w-4 h-4 mr-2 rounded" :style="{ backgroundColor: category.color }"></div>
                            <span class="text-sm text-muted-foreground">{{ category.name }} (≤ {{ category.threshold === Infinity ? '∞' : category.threshold }})</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Annual IKE chart section -->
            <div class="rounded-xl border border-border bg-card shadow-sm dark:bg-card/50">
                <div class="border-b border-border/50 px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <div>
                        <h2 class="text-xl font-semibold text-foreground">Annual IKE</h2>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Yearly energy consumption index
                        </p>
                    </div>
                    
                    <Button @click="() => exportAnnualData()" variant="outline" size="sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export
                    </Button>
                </div>
                
                <div class="p-6 h-80">
                    <div ref="annualIkeChartRef" class="w-full h-full"></div>
                </div>
                
                <!-- IKE Legend -->
                <div class="border-t border-border/50 px-6 py-4">
                    <div class="flex flex-wrap gap-4 justify-center">
                        <div v-for="category in ikeCategories" :key="category.name" class="flex items-center">
                            <div class="w-4 h-4 mr-2 rounded" :style="{ backgroundColor: category.color }"></div>
                            <span class="text-sm text-muted-foreground">{{ category.name }} (≤ {{ category.threshold === Infinity ? '∞' : category.threshold }})</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Additional custom styles can be added here if needed */
</style>
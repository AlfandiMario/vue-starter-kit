<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import Button from '@/components/ui/button/Button.vue';
import { ref } from 'vue';

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
        title: 'Monitor',
        href: '/electrical/monitor',
    },
];

// Mock data for rooms and their energy measurements
interface MeasurementData {
    label: string;
    value: number;
    unit: string;
    icon?: string;
}

interface Room {
    id: number;
    name: string;
    lastActivity: {
        datetime: string;
        username?: string;
    };
    realtimeData: MeasurementData[];
    usageData: MeasurementData[];
}

const rooms = ref<Room[]>([
    {
        id: 1,
        name: 'Living Room',
        lastActivity: {
            datetime: '2025-05-10 14:23',
            username: ''
        },
        realtimeData: [
            { label: 'Current Power', value: 42.5, unit: 'W' },
            { label: 'Voltage', value: 220, unit: 'V' },
            { label: 'Current', value: 0.19, unit: 'A' },
            { label: 'Power Factor', value: 0.92, unit: '' },
            { label: 'Frequency', value: 50, unit: 'Hz' },
            { label: 'Apparent Power', value: 46.2, unit: 'VA' },
            { label: 'Reactive Power', value: 18.1, unit: 'VAR' },
            { label: 'Temperature', value: 24.3, unit: '°C' }
        ],
        usageData: [
            { label: 'Today', value: 0.8, unit: 'kWh' },
            { label: 'Yesterday', value: 1.3, unit: 'kWh' },
            { label: 'This Week', value: 8.4, unit: 'kWh' },
            { label: 'This Month', value: 32.6, unit: 'kWh' },
            { label: 'Daily Average', value: 1.2, unit: 'kWh' },
            { label: 'Peak Hour', value: 19, unit: ':00' },
            { label: 'Cost Today', value: 0.12, unit: '$' },
            { label: 'Projected Monthly', value: 4.89, unit: '$' }
        ]
    },
    {
        id: 2,
        name: 'Kitchen',
        lastActivity: {
            datetime: '2025-05-10 13:45',
            username: ''
        },
        realtimeData: [
            { label: 'Current Power', value: 126.8, unit: 'W' },
            { label: 'Voltage', value: 220, unit: 'V' },
            { label: 'Current', value: 0.58, unit: 'A' },
            { label: 'Power Factor', value: 0.95, unit: '' },
            { label: 'Frequency', value: 50, unit: 'Hz' },
            { label: 'Apparent Power', value: 133.5, unit: 'VA' },
            { label: 'Reactive Power', value: 42.6, unit: 'VAR' },
            { label: 'Temperature', value: 25.7, unit: '°C' }
        ],
        usageData: [
            { label: 'Today', value: 2.4, unit: 'kWh' },
            { label: 'Yesterday', value: 2.6, unit: 'kWh' },
            { label: 'This Week', value: 16.8, unit: 'kWh' },
            { label: 'This Month', value: 65.3, unit: 'kWh' },
            { label: 'Daily Average', value: 2.5, unit: 'kWh' },
            { label: 'Peak Hour', value: 18, unit: ':00' },
            { label: 'Cost Today', value: 0.36, unit: '$' },
            { label: 'Projected Monthly', value: 9.80, unit: '$' }
        ]
    },
    {
        id: 3,
        name: 'Bedroom',
        lastActivity: {
            datetime: '2025-05-10 12:12',
            username: ''
        },
        realtimeData: [
            { label: 'Current Power', value: 35.2, unit: 'W' },
            { label: 'Voltage', value: 220, unit: 'V' },
            { label: 'Current', value: 0.16, unit: 'A' },
            { label: 'Power Factor', value: 0.90, unit: '' },
            { label: 'Frequency', value: 50, unit: 'Hz' },
            { label: 'Apparent Power', value: 39.1, unit: 'VA' },
            { label: 'Reactive Power', value: 16.9, unit: 'VAR' },
            { label: 'Temperature', value: 23.1, unit: '°C' }
        ],
        usageData: [
            { label: 'Today', value: 0.6, unit: 'kWh' },
            { label: 'Yesterday', value: 0.9, unit: 'kWh' },
            { label: 'This Week', value: 5.8, unit: 'kWh' },
            { label: 'This Month', value: 22.5, unit: 'kWh' },
            { label: 'Daily Average', value: 0.85, unit: 'kWh' },
            { label: 'Peak Hour', value: 22, unit: ':00' },
            { label: 'Cost Today', value: 0.09, unit: '$' },
            { label: 'Projected Monthly', value: 3.38, unit: '$' }
        ]
    },
    {
        id: 4,
        name: 'Bathroom',
        lastActivity: {
            datetime: '2025-05-10 09:50',
            username: ''
        },
        realtimeData: [
            { label: 'Current Power', value: 18.3, unit: 'W' },
            { label: 'Voltage', value: 220, unit: 'V' },
            { label: 'Current', value: 0.08, unit: 'A' },
            { label: 'Power Factor', value: 0.93, unit: '' },
            { label: 'Frequency', value: 50, unit: 'Hz' },
            { label: 'Apparent Power', value: 19.7, unit: 'VA' },
            { label: 'Reactive Power', value: 7.2, unit: 'VAR' },
            { label: 'Temperature', value: 22.8, unit: '°C' }
        ],
        usageData: [
            { label: 'Today', value: 0.3, unit: 'kWh' },
            { label: 'Yesterday', value: 0.4, unit: 'kWh' },
            { label: 'This Week', value: 2.5, unit: 'kWh' },
            { label: 'This Month', value: 10.2, unit: 'kWh' },
            { label: 'Daily Average', value: 0.38, unit: 'kWh' },
            { label: 'Peak Hour', value: 7, unit: ':00' },
            { label: 'Cost Today', value: 0.05, unit: '$' },
            { label: 'Projected Monthly', value: 1.53, unit: '$' }
        ]
    },
    {
        id: 5,
        name: 'All Rooms',
        lastActivity: {
            datetime: '2025-05-10 14:23',
            username: ''
        },
        realtimeData: [
            { label: 'Current Power', value: 222.8, unit: 'W' },
            { label: 'Voltage', value: 220, unit: 'V' },
            { label: 'Current', value: 1.01, unit: 'A' },
            { label: 'Power Factor', value: 0.93, unit: '' },
            { label: 'Frequency', value: 50, unit: 'Hz' },
            { label: 'Apparent Power', value: 238.5, unit: 'VA' },
            { label: 'Reactive Power', value: 84.8, unit: 'VAR' },
            { label: 'Temperature', value: 24.2, unit: '°C' }
        ],
        usageData: [
            { label: 'Today', value: 4.1, unit: 'kWh' },
            { label: 'Yesterday', value: 5.2, unit: 'kWh' },
            { label: 'This Week', value: 33.5, unit: 'kWh' },
            { label: 'This Month', value: 130.6, unit: 'kWh' },
            { label: 'Daily Average', value: 4.9, unit: 'kWh' },
            { label: 'Peak Hour', value: 19, unit: ':00' },
            { label: 'Cost Today', value: 0.62, unit: '$' },
            { label: 'Projected Monthly', value: 19.59, unit: '$' }
        ]
    }
]);

// Selected room for filtering
const selectedRoom = ref<number>(5); // Default to All Rooms

// Get current date and time for display
// const currentDateTime = new Date().toLocaleString('en-US', { 
//     year: 'numeric', 
//     month: 'long', 
//     day: 'numeric',
//     hour: '2-digit',
//     minute: '2-digit'
// });

// Get selected room data
const getSelectedRoomData = () => {
    return rooms.value.find(room => room.id === selectedRoom.value) || rooms.value[0];
};
</script>

<template>
    <Head title="Electricity Monitoring" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 px-8 py-6">
            <!-- Room selection dropdown -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-foreground">Electricity Monitoring</h1>
                    <p class="text-sm text-muted-foreground">Consumption statistics for {{ getSelectedRoomData().name }}</p>
                </div>
                
                <div class="relative">
                    <select 
                        v-model="selectedRoom"
                        class="pl-4 pr-10 py-2 rounded-md border border-border bg-card text-card-foreground shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                    >
                        <option v-for="room in rooms" :key="room.id" :value="room.id">
                            {{ room.name }}
                        </option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-muted-foreground">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Real Time Monitoring Section -->
            <div 
                class="rounded-xl border border-border bg-card shadow-sm dark:bg-card/50"
            >
                <!-- Section header -->
                <div class="border-b border-border/50 px-6 py-4">
                    <h2 class="text-xl font-semibold text-foreground">Real Time Monitoring</h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Last activity: {{ getSelectedRoomData().lastActivity.datetime }}
                    </p>
                </div>
                
                <!-- Measurement grid -->
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-4">
                        <!-- Measurement cards -->
                        <div 
                            v-for="(measurement, index) in getSelectedRoomData().realtimeData" 
                            :key="index"
                            class="flex flex-col rounded-lg border border-border bg-background p-4 shadow-sm"
                        >
                            <span class="text-sm text-muted-foreground">{{ measurement.label }}</span>
                            <div class="mt-1 flex items-baseline">
                                <span class="text-2xl font-bold text-foreground">{{ measurement.value }}</span>
                                <span class="ml-1 text-sm text-muted-foreground">{{ measurement.unit }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Energy Usage Section -->
            <div 
                class="rounded-xl border border-border bg-card shadow-sm dark:bg-card/50"
            >
                <!-- Section header -->
                <div class="border-b border-border/50 px-6 py-4">
                    <h2 class="text-xl font-semibold text-foreground">Energy Usage</h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Last activity: {{ getSelectedRoomData().lastActivity.datetime }}
                    </p>
                </div>
                
                <!-- Usage statistics grid -->
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-4">
                        <!-- Usage stat cards -->
                        <div 
                            v-for="(stat, index) in getSelectedRoomData().usageData" 
                            :key="index"
                            class="flex flex-col rounded-lg border border-border bg-background p-4 shadow-sm"
                        >
                            <span class="text-sm text-muted-foreground">{{ stat.label }}</span>
                            <div class="mt-1 flex items-baseline">
                                <span class="text-2xl font-bold text-foreground">{{ stat.value }}</span>
                                <span class="ml-1 text-sm text-muted-foreground">{{ stat.unit }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Any additional custom styles can be added here */
</style>

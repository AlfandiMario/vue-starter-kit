<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

// Define breadcrumbs for navigation
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Smart BAMS',    
        href: '/dashboard',
    },
    {
        title: 'Electrical',
        href: '/electrical',
    },
    {
        title: 'Device Control',
        href: '/electrical/control',
    },
];

// Mock data for rooms and devices
interface Device {
    id: number;
    name: string;
    active: boolean;
}

interface Room {
    id: number;
    name: string;
    devices: Device[];
    lastActivity: {
        datetime: string;
        username: string;
    };
}

const rooms = ref<Room[]>([
    {
        id: 1,
        name: 'Living Room',
        lastActivity: {
            datetime: '2025-05-10 14:23',
            username: 'John Doe'
        },
        devices: [
            { id: 1, name: 'Main Light', active: false },
            { id: 2, name: 'Table Lamp', active: true },
            { id: 3, name: 'TV', active: false },
            { id: 4, name: 'Air Conditioner', active: true },
            { id: 5, name: 'Fan', active: false },
            { id: 6, name: 'Smart Speaker', active: true },
        ],
    },
    {
        id: 2,
        name: 'Kitchen',
        lastActivity: {
            datetime: '2025-05-10 10:15',
            username: 'Jane Smith'
        },
        devices: [
            { id: 7, name: 'Ceiling Light', active: true },
            { id: 8, name: 'Under-Cabinet Light', active: false },
            { id: 9, name: 'Refrigerator', active: true },
            { id: 10, name: 'Microwave', active: false },
            { id: 11, name: 'Coffee Maker', active: false },
        ],
    },
    {
        id: 3,
        name: 'Bedroom',
        lastActivity: {
            datetime: '2025-05-09 22:45',
            username: 'John Doe'
        },
        devices: [
            { id: 12, name: 'Main Light', active: false },
            { id: 13, name: 'Bedside Lamp', active: true },
            { id: 14, name: 'Air Conditioner', active: false },
            { id: 15, name: 'TV', active: false },
        ],
    },
    {
        id: 4,
        name: 'Bathroom',
        lastActivity: {
            datetime: '2025-05-10 08:32',
            username: 'Jane Smith'
        },
        devices: [
            { id: 16, name: 'Main Light', active: false },
            { id: 17, name: 'Mirror Light', active: false },
            { id: 18, name: 'Exhaust Fan', active: false },
        ],
    },
]);

// Toggle device state
const toggleDevice = (roomId: number, deviceId: number) => {
    const room = rooms.value.find(r => r.id === roomId);
    if (room) {
        const device = room.devices.find(d => d.id === deviceId);
        if (device) {
            device.active = !device.active;
        }
    }
};
</script>

<template>
    <Head title="Device Control" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 px-8 py-6">
            <!-- Room sections with device controls -->
            <div 
                v-for="room in rooms" 
                :key="room.id" 
                class="mb-6 rounded-xl border border-border bg-card shadow-sm dark:bg-card/50"
            >
                <!-- Room header -->
                <div class="border-b border-border/50 px-6 py-4">
                    <h2 class="text-xl font-semibold text-foreground">{{ room.name }}</h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Last activity: {{ room.lastActivity.datetime }} by {{ room.lastActivity.username }}
                    </p>
                </div>
                
                <!-- Devices grid -->
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                        <!-- Device card with switch -->
                        <div 
                            v-for="device in room.devices" 
                            :key="device.id"
                            class="flex items-center justify-between rounded-lg border border-border bg-background p-4 shadow-sm transition-colors hover:bg-accent/20"
                        >
                            <span class="font-medium text-card-foreground">{{ device.name }}</span>
                            <button 
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                :class="device.active ? 'bg-primary' : 'bg-muted'"
                                @click="toggleDevice(room.id, device.id)"
                                type="button"
                                role="switch"
                                :aria-checked="device.active"
                            >
                                <span 
                                    class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform" 
                                    :class="device.active ? 'translate-x-5' : 'translate-x-1'"
                                />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

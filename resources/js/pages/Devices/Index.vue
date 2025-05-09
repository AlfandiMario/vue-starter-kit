<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import axios from 'axios';
import { ref } from 'vue';

interface Device {
    id: number;
    name: string;
    type: string;
    status: string;
    location: string | null;
    manufacturer: string | null;
    model: string | null;
    firmware_version: string | null;
    settings: Record<string, any> | null;
    created_at: string;
    updated_at: string;
}

defineProps<{
    devices: Device[];
    canControl: boolean;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Devices',
        href: '/devices',
    }
];

// Methods converted to composable functions
const controlDevice = async (deviceId: number, action: string): Promise<void> => {
    try {
        await axios.post(route('devices.control', deviceId), { action });
        router.reload();
    } catch (error) {
        console.error('Error controlling device:', error);
        alert('An error occurred while controlling the device');
    }
};

const confirmDelete = (device: Device): void => {
    if (confirm(`Are you sure you want to delete the device "${device.name}"?`)) {
        router.delete(route('devices.destroy', device.id));
    }
};
</script>

<template>

    <Head title="Devices" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Button to add new device - only visible to users with control permission -->
                    <div v-if="canControl" class="mb-6">
                        <Link :href="route('devices.create')"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500">
                        Add New Device
                        </Link>
                    </div>

                    <!-- Devices list -->
                    <div v-if="devices && devices.length"
                        class="overflow-hidden rounded-lg border border-gray-300 dark:border-gray-700">
                        <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Name</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Type</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Location</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="device in devices" :key="device.id">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ device.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ device.type }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full"
                                            :class="device.status === 'online' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'">
                                            {{ device.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ device.location || 'Not specified' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex space-x-5">
                                            <Link :href="route('devices.show', device.id)"
                                                class="text-blue-600 hover:underline">
                                            View
                                            </Link>

                                            <!-- Edit/Delete actions only visible to users with control permission -->
                                            <template v-if="canControl">
                                                <Link :href="route('devices.edit', device.id)"
                                                    class="text-yellow-600 hover:underline">
                                                Edit
                                                </Link>

                                                <button @click="confirmDelete(device)"
                                                    class="text-red-600 hover:underline">
                                                    Delete
                                                </button>
                                            </template>
                                        </div>

                                        <!-- Control actions -->
                                        <!-- <div v-if="canControl && device.status === 'online'" class="mt-2">
                                            <button @click="controlDevice(device.id, 'off')"
                                                class="text-sm px-2 py-1 bg-red-100 text-red-800 rounded hover:bg-red-200 dark:bg-red-900 dark:text-red-200 dark:hover:bg-red-800">
                                                Turn Off
                                            </button>
                                        </div>

                                        <div v-if="canControl && device.status === 'offline'" class="mt-2">
                                            <button @click="controlDevice(device.id, 'on')"
                                                class="text-sm px-2 py-1 bg-green-100 text-green-800 rounded hover:bg-green-200 dark:bg-green-900 dark:text-green-200 dark:hover:bg-green-800">
                                                Turn On
                                            </button>
                                        </div> -->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center py-8">No devices found.</div>
                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            </div>
        </div>
    </AppLayout>
</template>
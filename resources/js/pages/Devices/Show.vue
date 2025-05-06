<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    device: Object,
    canControl: Boolean
});
</script>

<template>

    <Head title="Device Details" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Device Details
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6">
                    <a :href="route('devices.index')"
                        class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-500">
                        Back to Devices
                    </a>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex flex-col md:flex-row gap-6">
                            <!-- Device Information -->
                            <div class="w-full md:w-1/2">
                                <h3 class="text-lg font-semibold mb-4">Device Information</h3>
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Name</p>
                                        <p>{{ device.name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Type</p>
                                        <p>{{ device.type }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                                        <p>
                                            <span class="px-2 py-1 text-xs rounded-full"
                                                :class="device.status === 'online' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'">
                                                {{ device.status }}
                                            </span>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Manufacturer</p>
                                        <p>{{ device.details.manufacturer }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Model</p>
                                        <p>{{ device.details.model }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Firmware</p>
                                        <p>{{ device.details.firmware }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Device Controls - Only visible for users with control permission -->
                            <div v-if="canControl" class="w-full md:w-1/2">
                                <h3 class="text-lg font-semibold mb-4">Device Controls</h3>
                                <div class="space-y-4 bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                                        You have permission to control this device. Use the buttons below to manage it.
                                    </p>

                                    <div class="flex space-x-4">
                                        <button @click="controlDevice('on')"
                                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-500">
                                            Turn On
                                        </button>
                                        <button @click="controlDevice('off')"
                                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-500">
                                            Turn Off
                                        </button>
                                    </div>

                                    <div class="mt-6">
                                        <h4 class="text-md font-medium mb-2">Advanced Settings</h4>
                                        <div class="flex flex-col space-y-4">
                                            <div>
                                                <label class="block text-sm mb-1">Brightness</label>
                                                <input type="range" min="0" max="100" class="w-full" />
                                            </div>
                                            <div>
                                                <label class="block text-sm mb-1">Schedule</label>
                                                <input type="time"
                                                    class="rounded border p-2 dark:bg-gray-800 dark:border-gray-600" />
                                            </div>
                                            <button @click="controlDevice('adjust')"
                                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500 mt-2">
                                                Apply Settings
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Message for users without control permission -->
                            <div v-else class="w-full md:w-1/2">
                                <h3 class="text-lg font-semibold mb-4">Device Controls</h3>
                                <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        You do not have permission to control this device. Please contact an
                                        administrator if
                                        you need to manage this device.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
export default {
    methods: {
        controlDevice(action) {
            // In a real app, you would implement axios call to your backend
            console.log(`Controlling device ${this.device.id} with action: ${action}`);

            // Example of how you would call the backend:
            // axios.post(route('devices.control', this.device.id), { action })
            //   .then(response => {
            //     console.log(response.data.message);
            //     // Show success message to user
            //   })
            //   .catch(error => {
            //     console.error(error);
            //     // Show error message to user
            //   });
        }
    }
}
</script>
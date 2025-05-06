<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// Access the authenticated user and their permissions via the page props
const page = usePage();
const user = computed(() => page.props.auth.user);

// Helper method to check if the user has a permission
const hasPermission = (permission) => {
    if (!user.value || !user.value.permissions) return false;
    return user.value.permissions.includes(permission);
};

// Determine menu visibility based on permissions
const canManageUsers = computed(() => hasPermission('create users'));
const canManageRoles = computed(() => hasPermission('create roles'));
const canViewDevices = computed(() => hasPermission('view devices'));
const canControlDevices = computed(() => hasPermission('control devices'));
</script>

<template>
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="shrink-0 flex items-center">
                        <!-- Logo -->
                        <a href="/">
                            <img src="/favicon.svg" class="h-9 w-auto" alt="Logo" />
                        </a>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <!-- Common Navigation Links - visible to all authenticated users -->
                        <a :href="route('dashboard')"
                            class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out"
                            :class="{ 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100': route().current('dashboard') }">
                            Dashboard
                        </a>

                        <!-- Devices Link - Only visible to users with 'view devices' permission -->
                        <a v-if="canViewDevices" :href="route('devices.index')"
                            class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out"
                            :class="{ 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100': route().current('devices.index') }">
                            Devices
                        </a>

                        <!-- User Management Link - Only visible to SuperAdmin -->
                        <a v-if="canManageUsers" :href="route('users.index')"
                            class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out"
                            :class="{ 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100': route().current('users.index') }">
                            Users
                        </a>

                        <!-- Role Management Link - Only visible to SuperAdmin -->
                        <a v-if="canManageRoles" :href="route('roles.index')"
                            class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out"
                            :class="{ 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100': route().current('roles.index') }">
                            Roles
                        </a>
                    </div>
                </div>

                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    <!-- User Dropdown -->
                    <div class="ml-3 relative">
                        <div class="flex items-center">
                            <span class="text-sm text-gray-800 dark:text-gray-200 mr-3">
                                {{ user?.name }}
                            </span>
                            <button
                                class="bg-white dark:bg-gray-800 rounded-full h-8 w-8 overflow-hidden border border-gray-300 dark:border-gray-700">
                                <!-- User avatar or initials -->
                                <span class="sr-only">User Menu</span>
                                <div
                                    class="h-full w-full flex items-center justify-center bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-300">
                                    {{ user?.name.charAt(0).toUpperCase() }}
                                </div>
                            </button>
                        </div>

                        <!-- User Role Badge -->
                        <div class="mt-2 text-xs px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded-full text-center">
                            {{ user?.roles?.[0]?.name || 'No Role' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>
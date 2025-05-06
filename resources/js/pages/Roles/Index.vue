<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    roles: Array
});
</script>

<template>

    <Head title="Roles Management" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Roles Management
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Add Role Button -->
                        <div class="mb-6">
                            <Link :href="route('roles.create')"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500">
                            Add New Role
                            </Link>
                        </div>

                        <!-- Roles list -->
                        <div v-if="roles && roles.length"
                            class="overflow-hidden rounded-lg border border-gray-300 dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Role Name</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Permissions</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="role in roles" :key="role.id">
                                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ role.name }}</td>
                                        <td class="px-6 py-4">
                                            <div v-if="role.permissions && role.permissions.length"
                                                class="flex flex-wrap gap-1">
                                                <span v-for="permission in role.permissions" :key="permission.id"
                                                    class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 mb-1 mr-1">
                                                    {{ permission.name }}
                                                </span>
                                            </div>
                                            <span v-else class="text-sm text-gray-500 dark:text-gray-400">No permissions
                                                assigned</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap space-x-2">
                                            <Link :href="route('roles.edit', role.id)"
                                                class="text-blue-600 hover:underline">
                                            Edit
                                            </Link>
                                            <button @click="confirmDelete(role)" class="text-red-600 hover:underline">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center py-8">No roles found.</div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
export default {
    methods: {
        confirmDelete(role) {
            if (confirm(`Are you sure you want to delete the role "${role.name}"?`)) {
                this.$inertia.delete(route('roles.destroy', role.id));
            }
        }
    }
}
</script>
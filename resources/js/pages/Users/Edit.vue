<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    user: Object,
    roles: Array
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    roles: props.user.roles.map(role => role.id)
});

function submit() {
    form.put(route('users.update', props.user.id), {
        onSuccess: () => {
            form.password = '';
            form.password_confirmation = '';
        }
    });
}
</script>

<template>

    <Head title="Edit User" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit User: {{ user.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link :href="route('users.index')"
                        class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-500">
                    Back to Users
                    </Link>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Name -->
                            <div>
                                <label for="name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                <input id="name" v-model="form.name" type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required autofocus>
                                <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                <input id="email" v-model="form.email" type="email"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}
                                </div>
                            </div>

                            <!-- Password - optional when updating -->
                            <div>
                                <label for="password"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Password <span class="text-gray-500 text-xs">(leave blank to keep current
                                        password)</span>
                                </label>
                                <input id="password" v-model="form.password" type="password"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    autocomplete="new-password">
                                <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{
                                    form.errors.password }}
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Confirm Password
                                </label>
                                <input id="password_confirmation" v-model="form.password_confirmation" type="password"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- Roles -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Assign
                                    Roles</label>
                                <div class="space-y-2">
                                    <div v-for="role in roles" :key="role.id" class="flex items-center">
                                        <input :id="`role-${role.id}`" v-model="form.roles" :value="role.id"
                                            type="checkbox"
                                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <label :for="`role-${role.id}`"
                                            class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                            {{ role.name }}
                                        </label>
                                    </div>
                                </div>
                                <div v-if="form.errors.roles" class="text-red-500 text-sm mt-1">{{ form.errors.roles }}
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div>
                                <button type="submit"
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    :disabled="form.processing">
                                    Update User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
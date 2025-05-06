<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
  role: Object,
  permissions: Array
});

const form = useForm({
  name: props.role.name,
  permissions: props.role.permissions.map(permission => permission.id)
});

function submit() {
  form.put(route('roles.update', props.role.id));
}
</script>

<template>
  <Head title="Edit Role" />

  <AppLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Edit Role: {{ role.name }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
          <Link 
            :href="route('roles.index')" 
            class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-500"
          >
            Back to Roles
          </Link>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <form @submit.prevent="submit" class="space-y-6">
              <!-- Role Name -->
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role Name</label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                  autofocus
                >
                <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
              </div>

              <!-- Permissions -->
              <div>
                <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Assign Permissions</h3>
                
                <!-- Group: User Management -->
                <div class="mb-6">
                  <h4 class="text-md font-medium text-gray-600 dark:text-gray-400 mb-2">User Management</h4>
                  <div class="space-y-2 ml-2">
                    <div v-for="permission in permissions.filter(p => p.name.includes('users'))" 
                         :key="permission.id" 
                         class="flex items-center">
                      <input
                        :id="`permission-${permission.id}`"
                        v-model="form.permissions"
                        :value="permission.id"
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                      >
                      <label :for="`permission-${permission.id}`" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                        {{ permission.name }}
                      </label>
                    </div>
                  </div>
                </div>
                
                <!-- Group: Role Management -->
                <div class="mb-6">
                  <h4 class="text-md font-medium text-gray-600 dark:text-gray-400 mb-2">Role Management</h4>
                  <div class="space-y-2 ml-2">
                    <div v-for="permission in permissions.filter(p => p.name.includes('roles'))" 
                         :key="permission.id" 
                         class="flex items-center">
                      <input
                        :id="`permission-${permission.id}`"
                        v-model="form.permissions"
                        :value="permission.id"
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                      >
                      <label :for="`permission-${permission.id}`" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                        {{ permission.name }}
                      </label>
                    </div>
                  </div>
                </div>
                
                <!-- Group: Permission Management -->
                <div class="mb-6">
                  <h4 class="text-md font-medium text-gray-600 dark:text-gray-400 mb-2">Permission Management</h4>
                  <div class="space-y-2 ml-2">
                    <div v-for="permission in permissions.filter(p => p.name.includes('permission'))" 
                         :key="permission.id" 
                         class="flex items-center">
                      <input
                        :id="`permission-${permission.id}`"
                        v-model="form.permissions"
                        :value="permission.id"
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                      >
                      <label :for="`permission-${permission.id}`" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                        {{ permission.name }}
                      </label>
                    </div>
                  </div>
                </div>
                
                <!-- Group: Device Management -->
                <div class="mb-6">
                  <h4 class="text-md font-medium text-gray-600 dark:text-gray-400 mb-2">Device Management</h4>
                  <div class="space-y-2 ml-2">
                    <div v-for="permission in permissions.filter(p => p.name.includes('devices'))" 
                         :key="permission.id" 
                         class="flex items-center">
                      <input
                        :id="`permission-${permission.id}`"
                        v-model="form.permissions"
                        :value="permission.id"
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                      >
                      <label :for="`permission-${permission.id}`" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                        {{ permission.name }}
                      </label>
                    </div>
                  </div>
                </div>
                
                <!-- Other Permissions -->
                <div class="mb-6">
                  <h4 class="text-md font-medium text-gray-600 dark:text-gray-400 mb-2">Other Permissions</h4>
                  <div class="space-y-2 ml-2">
                    <div v-for="permission in permissions.filter(p => 
                         !p.name.includes('users') && 
                         !p.name.includes('roles') && 
                         !p.name.includes('permission') && 
                         !p.name.includes('devices'))" 
                         :key="permission.id" 
                         class="flex items-center">
                      <input
                        :id="`permission-${permission.id}`"
                        v-model="form.permissions"
                        :value="permission.id"
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                      >
                      <label :for="`permission-${permission.id}`" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                        {{ permission.name }}
                      </label>
                    </div>
                  </div>
                </div>
                
                <div v-if="form.errors.permissions" class="text-red-500 text-sm mt-1">{{ form.errors.permissions }}</div>
              </div>

              <!-- Submit Button -->
              <div>
                <button
                  type="submit"
                  class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  :disabled="form.processing"
                >
                  Update Role
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const form = useForm({
  name: '',
  type: '',
  manufacturer: '',
  model: '',
  location: ''
});

function submit() {
  form.post(route('devices.store'), {
    onSuccess: () => form.reset()
  });
}
</script>

<template>
  <Head title="Add New Device" />

  <AppLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Add New Device
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
          <Link 
            :href="route('devices.index')" 
            class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-500"
          >
            Back to Devices
          </Link>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <form @submit.prevent="submit" class="space-y-6">
              <!-- Device Name -->
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Device Name</label>
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

              <!-- Device Type -->
              <div>
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Device Type</label>
                <select
                  id="type"
                  v-model="form.type"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                >
                  <option value="">Select a type</option>
                  <option value="lighting">Lighting</option>
                  <option value="sensor">Sensor</option>
                  <option value="security">Security</option>
                  <option value="climate">Climate Control</option>
                  <option value="entertainment">Entertainment</option>
                </select>
                <div v-if="form.errors.type" class="text-red-500 text-sm mt-1">{{ form.errors.type }}</div>
              </div>

              <!-- Manufacturer -->
              <div>
                <label for="manufacturer" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Manufacturer</label>
                <input
                  id="manufacturer"
                  v-model="form.manufacturer"
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                <div v-if="form.errors.manufacturer" class="text-red-500 text-sm mt-1">{{ form.errors.manufacturer }}</div>
              </div>

              <!-- Model -->
              <div>
                <label for="model" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Model</label>
                <input
                  id="model"
                  v-model="form.model"
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                <div v-if="form.errors.model" class="text-red-500 text-sm mt-1">{{ form.errors.model }}</div>
              </div>

              <!-- Location -->
              <div>
                <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Location</label>
                <input
                  id="location"
                  v-model="form.location"
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  placeholder="e.g. Living Room, Kitchen, etc."
                >
                <div v-if="form.errors.location" class="text-red-500 text-sm mt-1">{{ form.errors.location }}</div>
              </div>

              <!-- Submit Button -->
              <div>
                <button
                  type="submit"
                  class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  :disabled="form.processing"
                >
                  Add Device
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
  title: '',
  description: '',
  surface_m2: null as number | null,
  price: null as number | null,
  city: '',
  zip_code: '',
  latitude: null as number | null,
  longitude: null as number | null,
  viabilised: false,
  source_url: '',
  source_platform: '',
});

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Terrains',
    href: route('terrains.index'),
  },
  {
    title: 'Add Terrain',
    href: route('terrains.create'),
  },
];

const submit = () => {
  form.post(route('terrains.store'), {
    onSuccess: () => {
      form.reset();
    },
  });
};
</script>

<template>
  <Head title="Add Terrain" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto max-w-4xl p-4">
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add New Terrain</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
          Enter the details of the terrain you want to analyze.
        </p>
      </div>

      <div class="overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border dark:bg-sidebar-bg">
        <form @submit.prevent="submit" class="p-6">
          <!-- Basic Information Section -->
          <div class="mb-8">
            <h2 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Basic Information</h2>
            <div class="grid gap-6 md:grid-cols-2">
              <div class="col-span-2">
                <label for="title" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                  Title <span class="text-red-500">*</span>
                </label>
                <input
                  id="title"
                  v-model="form.title"
                  type="text"
                  class="block w-full rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-sidebar-border dark:bg-sidebar-bg/50 dark:text-white"
                  required
                  placeholder="Enter terrain title"
                />
                <div v-if="form.errors.title" class="mt-1 text-sm text-red-500">
                  {{ form.errors.title }}
                </div>
              </div>

              <div class="col-span-2">
                <label for="description" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                  Description
                </label>
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="3"
                  class="block w-full rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-sidebar-border dark:bg-sidebar-bg/50 dark:text-white"
                  placeholder="Describe the terrain"
                ></textarea>
                <div v-if="form.errors.description" class="mt-1 text-sm text-red-500">
                  {{ form.errors.description }}
                </div>
              </div>

              <div>
                <label for="surface_m2" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                  Surface (m²) <span class="text-red-500">*</span>
                </label>
                <input
                  id="surface_m2"
                  v-model="form.surface_m2"
                  type="number"
                  min="0"
                  step="0.01"
                  class="block w-full rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-sidebar-border dark:bg-sidebar-bg/50 dark:text-white"
                  required
                  placeholder="Enter surface area"
                />
                <div v-if="form.errors.surface_m2" class="mt-1 text-sm text-red-500">
                  {{ form.errors.surface_m2 }}
                </div>
              </div>

              <div>
                <label for="price" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                  Price (€) <span class="text-red-500">*</span>
                </label>
                <input
                  id="price"
                  v-model="form.price"
                  type="number"
                  min="0"
                  step="0.01"
                  class="block w-full rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-sidebar-border dark:bg-sidebar-bg/50 dark:text-white"
                  required
                  placeholder="Enter price"
                />
                <div v-if="form.errors.price" class="mt-1 text-sm text-red-500">
                  {{ form.errors.price }}
                </div>
              </div>

              <div>
                <label for="city" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                  City <span class="text-red-500">*</span>
                </label>
                <input
                  id="city"
                  v-model="form.city"
                  type="text"
                  class="block w-full rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-sidebar-border dark:bg-sidebar-bg/50 dark:text-white"
                  required
                  placeholder="Enter city"
                />
                <div v-if="form.errors.city" class="mt-1 text-sm text-red-500">
                  {{ form.errors.city }}
                </div>
              </div>

              <div>
                <label for="zip_code" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                  ZIP Code <span class="text-red-500">*</span>
                </label>
                <input
                  id="zip_code"
                  v-model="form.zip_code"
                  type="text"
                  class="block w-full rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-sidebar-border dark:bg-sidebar-bg/50 dark:text-white"
                  required
                  placeholder="Enter ZIP code"
                />
                <div v-if="form.errors.zip_code" class="mt-1 text-sm text-red-500">
                  {{ form.errors.zip_code }}
                </div>
              </div>

              <div class="col-span-2">
                <div class="flex items-center">
                  <input
                    id="viabilised"
                    v-model="form.viabilised"
                    type="checkbox"
                    class="h-4 w-4 rounded border-sidebar-border/70 text-primary focus:ring-primary dark:border-sidebar-border"
                  />
                  <label for="viabilised" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">
                    This terrain is already viabilised (has utilities and access)
                  </label>
                </div>
                <div v-if="form.errors.viabilised" class="mt-1 text-sm text-red-500">
                  {{ form.errors.viabilised }}
                </div>
              </div>
            </div>
          </div>

          <!-- Location Section -->
          <div class="mb-8">
            <h2 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Location (Optional)</h2>
            <div class="grid gap-6 md:grid-cols-2">
              <div>
                <label for="latitude" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                  Latitude
                </label>
                <input
                  id="latitude"
                  v-model="form.latitude"
                  type="number"
                  step="0.00000001"
                  class="block w-full rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-sidebar-border dark:bg-sidebar-bg/50 dark:text-white"
                  placeholder="Enter latitude"
                />
                <div v-if="form.errors.latitude" class="mt-1 text-sm text-red-500">
                  {{ form.errors.latitude }}
                </div>
              </div>

              <div>
                <label for="longitude" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                  Longitude
                </label>
                <input
                  id="longitude"
                  v-model="form.longitude"
                  type="number"
                  step="0.00000001"
                  class="block w-full rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-sidebar-border dark:bg-sidebar-bg/50 dark:text-white"
                  placeholder="Enter longitude"
                />
                <div v-if="form.errors.longitude" class="mt-1 text-sm text-red-500">
                  {{ form.errors.longitude }}
                </div>
              </div>
            </div>
          </div>

          <!-- Source Section -->
          <div class="mb-8">
            <h2 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Source Information (Optional)</h2>
            <div class="grid gap-6 md:grid-cols-2">
              <div>
                <label for="source_url" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                  Source URL
                </label>
                <input
                  id="source_url"
                  v-model="form.source_url"
                  type="url"
                  class="block w-full rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-sidebar-border dark:bg-sidebar-bg/50 dark:text-white"
                  placeholder="https://example.com/terrain-listing"
                />
                <div v-if="form.errors.source_url" class="mt-1 text-sm text-red-500">
                  {{ form.errors.source_url }}
                </div>
              </div>

              <div>
                <label for="source_platform" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                  Source Platform
                </label>
                <select
                  id="source_platform"
                  v-model="form.source_platform"
                  class="block w-full rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-sidebar-border dark:bg-sidebar-bg/50 dark:text-white"
                >
                  <option value="">Select a platform</option>
                  <option value="Leboncoin">Leboncoin</option>
                  <option value="SeLoger">SeLoger</option>
                  <option value="PAP">PAP</option>
                  <option value="Bien'ici">Bien'ici</option>
                  <option value="Logic-Immo">Logic-Immo</option>
                  <option value="Other">Other</option>
                </select>
                <div v-if="form.errors.source_platform" class="mt-1 text-sm text-red-500">
                  {{ form.errors.source_platform }}
                </div>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3">
            <Link
              :href="route('terrains.index')"
              class="rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:border-sidebar-border dark:bg-sidebar-bg dark:text-gray-300 dark:hover:bg-sidebar-bg/80"
            >
              Cancel
            </Link>
            <button
              type="submit"
              class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
              :disabled="form.processing"
            >
              <span v-if="form.processing">Creating...</span>
              <span v-else>Create Terrain</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

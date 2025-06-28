<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
  name: '',
  description: '',
});

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Teams',
    href: route('teams.index'),
  },
  {
    title: 'Create Team',
    href: route('teams.create'),
  },
];

const submit = () => {
  form.post(route('teams.store'), {
    onSuccess: () => {
      form.reset();
    },
  });
};
</script>

<template>
  <Head title="Create Team" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="mx-auto max-w-3xl p-4">
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Team</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
          Create a new team to collaborate with your colleagues on investment projects.
        </p>
      </div>

      <div class="overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border dark:bg-sidebar-bg">
        <form @submit.prevent="submit" class="p-6">
          <div class="mb-6">
            <label for="name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
              Team Name <span class="text-red-500">*</span>
            </label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              class="block w-full rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-sidebar-border dark:bg-sidebar-bg/50 dark:text-white"
              required
              placeholder="Enter team name"
            />
            <div v-if="form.errors.name" class="mt-1 text-sm text-red-500">
              {{ form.errors.name }}
            </div>
          </div>

          <div class="mb-6">
            <label for="description" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
              Description
            </label>
            <textarea
              id="description"
              v-model="form.description"
              rows="4"
              class="block w-full rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-sidebar-border dark:bg-sidebar-bg/50 dark:text-white"
              placeholder="Describe the purpose of this team"
            ></textarea>
            <div v-if="form.errors.description" class="mt-1 text-sm text-red-500">
              {{ form.errors.description }}
            </div>
          </div>

          <div class="flex items-center justify-end gap-3">
            <a
              :href="route('teams.index')"
              class="rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:border-sidebar-border dark:bg-sidebar-bg dark:text-gray-300 dark:hover:bg-sidebar-bg/80"
            >
              Cancel
            </a>
            <button
              type="submit"
              class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
              :disabled="form.processing"
            >
              <span v-if="form.processing">Creating...</span>
              <span v-else>Create Team</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

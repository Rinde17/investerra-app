<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

// Define props for the component
defineProps<{
  teams: Array<{
    id: number;
    name: string;
    description: string | null;
    owner_id: number;
  }>;
  currentTeam: {
    id: number;
    name: string;
  } | null;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Teams',
    href: route('teams.index'),
  },
];
</script>

<template>
  <Head title="Teams" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-6 p-4">
      <!-- Header with Create Team button -->
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Your Teams</h1>
        <Link
          :href="route('teams.create')"
          class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
        >
          Create New Team
        </Link>
      </div>

      <!-- Teams grid -->
      <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="team in teams"
          :key="team.id"
          class="flex flex-col overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm transition-all hover:shadow-md dark:border-sidebar-border dark:bg-sidebar-bg"
        >
          <div class="flex flex-1 flex-col p-6">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ team.name }}</h3>
              <span
                v-if="currentTeam && currentTeam.id === team.id"
                class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-100"
              >
                Current
              </span>
            </div>
            <p v-if="team.description" class="mt-2 flex-1 text-sm text-gray-500 dark:text-gray-400">
              {{ team.description }}
            </p>
            <p v-else class="mt-2 flex-1 text-sm italic text-gray-400 dark:text-gray-500">
              No description provided
            </p>
          </div>
          <div class="flex border-t border-sidebar-border/70 bg-gray-50 dark:border-sidebar-border dark:bg-sidebar-bg/50">
            <Link
              :href="route('teams.show', team.id)"
              class="flex flex-1 items-center justify-center p-3 text-sm font-medium text-primary hover:bg-gray-100 dark:hover:bg-sidebar-bg/80"
            >
              View Details
            </Link>
            <Link
              v-if="currentTeam?.id !== team.id"
              :href="route('teams.switch', team.id)"
              method="post"
              as="button"
              type="button"
              class="flex flex-1 items-center justify-center border-l border-sidebar-border/70 p-3 text-sm font-medium text-primary hover:bg-gray-100 dark:border-sidebar-border dark:hover:bg-sidebar-bg/80"
            >
              Switch to this Team
            </Link>
          </div>
        </div>

        <!-- Empty state when no teams -->
        <div
          v-if="teams.length === 0"
          class="col-span-full flex flex-col items-center justify-center rounded-lg border border-dashed border-sidebar-border/70 bg-gray-50 p-12 text-center dark:border-sidebar-border dark:bg-sidebar-bg/30"
        >
          <svg
            class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            aria-hidden="true"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1.5"
              d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"
            />
          </svg>
          <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No teams yet</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new team.</p>
          <div class="mt-6">
            <Link
              :href="route('teams.create')"
              class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
            >
              Create New Team
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

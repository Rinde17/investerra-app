<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

// Define props for the component
const props = defineProps<{
  projects: Array<{
    id: number;
    name: string;
    description: string | null;
    user_id: number;
    team_id: number | null;
    notes: string | null;
    is_saved: boolean;
    created_at: string;
    updated_at: string;
    terrains: Array<{
      id: number;
      title: string;
      analysis: {
        ai_score: number;
        profitability_label: string;
      } | null;
    }>;
    team: {
      id: number;
      name: string;
    } | null;
  }>;
}>();

const searchQuery = ref('');
const sortBy = ref('created_at');
const sortOrder = ref('desc');
const filterTeam = ref<number | null>(null);

const filteredProjects = computed(() => {
  let filtered = [...props.projects];

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(project =>
      project.name.toLowerCase().includes(query) ||
      (project.description && project.description.toLowerCase().includes(query)) ||
      (project.team && project.team.name.toLowerCase().includes(query))
    );
  }

  // Apply team filter
  if (filterTeam.value !== null) {
    filtered = filtered.filter(project => project.team_id === filterTeam.value);
  }

  // Apply sorting
  filtered.sort((a, b) => {
    let comparison = 0;

    switch (sortBy.value) {
      case 'name':
        comparison = a.name.localeCompare(b.name);
        break;
      case 'terrains':
        comparison = a.terrains.length - b.terrains.length;
        break;
      case 'team':
        const aTeam = a.team ? a.team.name : '';
        const bTeam = b.team ? b.team.name : '';
        comparison = aTeam.localeCompare(bTeam);
        break;
      default:
        // Default to sorting by created_at (most recent first)
        comparison = new Date(b.created_at).getTime() - new Date(a.created_at).getTime();
    }

    return sortOrder.value === 'asc' ? comparison : -comparison;
  });

  return filtered;
});

const toggleSort = (field: string) => {
  if (sortBy.value === field) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortBy.value = field;
    sortOrder.value = 'desc';
  }
};

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

// Get unique teams for filtering
const teams = computed(() => {
  const uniqueTeams = new Map();
  props.projects.forEach(project => {
    if (project.team) {
      uniqueTeams.set(project.team.id, project.team);
    }
  });
  return Array.from(uniqueTeams.values());
});

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Projects',
    href: route('projects.index'),
  },
];
</script>

<template>
  <Head title="Projects" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-6 p-4">
      <!-- Header with Create Project button and search -->
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Projects</h1>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
          <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
              <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
              </svg>
            </div>
            <input
              v-model="searchQuery"
              type="text"
              class="block w-full rounded-md border border-sidebar-border/70 bg-white py-1.5 pl-10 pr-3 text-gray-900 placeholder:text-gray-400 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary sm:text-sm sm:leading-6 dark:border-sidebar-border dark:bg-sidebar-bg/50 dark:text-white"
              placeholder="Search projects..."
            />
          </div>
          <Link
            :href="route('projects.create')"
            class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
          >
            Create New Project
          </Link>
        </div>
      </div>

      <!-- Filters and sorting -->
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-wrap gap-2">
          <button
            @click="toggleSort('name')"
            :class="[
              sortBy === 'name' ? 'bg-primary/10 text-primary' : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-sidebar-bg dark:text-gray-300 dark:hover:bg-sidebar-bg/80',
              'inline-flex items-center rounded-md border border-sidebar-border/70 px-3 py-1 text-sm font-medium dark:border-sidebar-border',
            ]"
          >
            Name
            <svg v-if="sortBy === 'name'" class="ml-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" :transform="sortOrder === 'asc' ? 'rotate(180)' : ''" />
            </svg>
          </button>
          <button
            @click="toggleSort('terrains')"
            :class="[
              sortBy === 'terrains' ? 'bg-primary/10 text-primary' : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-sidebar-bg dark:text-gray-300 dark:hover:bg-sidebar-bg/80',
              'inline-flex items-center rounded-md border border-sidebar-border/70 px-3 py-1 text-sm font-medium dark:border-sidebar-border',
            ]"
          >
            Terrains
            <svg v-if="sortBy === 'terrains'" class="ml-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" :transform="sortOrder === 'asc' ? 'rotate(180)' : ''" />
            </svg>
          </button>
          <button
            @click="toggleSort('team')"
            :class="[
              sortBy === 'team' ? 'bg-primary/10 text-primary' : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-sidebar-bg dark:text-gray-300 dark:hover:bg-sidebar-bg/80',
              'inline-flex items-center rounded-md border border-sidebar-border/70 px-3 py-1 text-sm font-medium dark:border-sidebar-border',
            ]"
          >
            Team
            <svg v-if="sortBy === 'team'" class="ml-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" :transform="sortOrder === 'asc' ? 'rotate(180)' : ''" />
            </svg>
          </button>
          <button
            @click="toggleSort('created_at')"
            :class="[
              sortBy === 'created_at' ? 'bg-primary/10 text-primary' : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-sidebar-bg dark:text-gray-300 dark:hover:bg-sidebar-bg/80',
              'inline-flex items-center rounded-md border border-sidebar-border/70 px-3 py-1 text-sm font-medium dark:border-sidebar-border',
            ]"
          >
            Date
            <svg v-if="sortBy === 'created_at'" class="ml-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" :transform="sortOrder === 'asc' ? 'rotate(180)' : ''" />
            </svg>
          </button>
        </div>

        <div v-if="teams.length > 0" class="flex items-center">
          <label for="team-filter" class="mr-2 text-sm font-medium text-gray-700 dark:text-gray-300">Team:</label>
          <select
            id="team-filter"
            v-model="filterTeam"
            class="rounded-md border border-sidebar-border/70 bg-white px-3 py-1 text-sm text-gray-900 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-sidebar-border dark:bg-sidebar-bg dark:text-white"
          >
            <option :value="null">All Teams</option>
            <option v-for="team in teams" :key="team.id" :value="team.id">{{ team.name }}</option>
          </select>
        </div>
      </div>

      <!-- Projects grid -->
      <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="project in filteredProjects"
          :key="project.id"
          class="flex flex-col overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm transition-all hover:shadow-md dark:border-sidebar-border dark:bg-sidebar-bg"
        >
          <div class="flex flex-1 flex-col p-6">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ project.name }}</h3>
              <span v-if="project.team" class="rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-100">
                {{ project.team.name }}
              </span>
            </div>
            <p v-if="project.description" class="mt-2 flex-1 text-sm text-gray-500 dark:text-gray-400">
              {{ project.description }}
            </p>
            <p v-else class="mt-2 flex-1 text-sm italic text-gray-400 dark:text-gray-500">
              No description provided
            </p>
            <div class="mt-4 grid grid-cols-2 gap-4">
              <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Terrains</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ project.terrains.length }}</p>
              </div>
              <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Created</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ formatDate(project.created_at) }}</p>
              </div>
            </div>
            <div v-if="project.terrains.length > 0" class="mt-4">
              <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Terrains</p>
              <div class="mt-1 flex flex-wrap gap-2">
                <span
                  v-for="terrain in project.terrains.slice(0, 3)"
                  :key="terrain.id"
                  :class="[
                    terrain.analysis && terrain.analysis.profitability_label === 'Excellent'
                      ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100'
                      : terrain.analysis && terrain.analysis.profitability_label === 'Good'
                      ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100'
                      : terrain.analysis && terrain.analysis.profitability_label === 'Average'
                      ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100'
                      : terrain.analysis && terrain.analysis.profitability_label === 'Poor'
                      ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100'
                      : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100',
                    'inline-flex rounded-full px-2 py-0.5 text-xs font-medium',
                  ]"
                >
                  {{ terrain.title }}
                </span>
                <span v-if="project.terrains.length > 3" class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                  +{{ project.terrains.length - 3 }} more
                </span>
              </div>
            </div>
          </div>
          <div class="flex border-t border-sidebar-border/70 bg-gray-50 dark:border-sidebar-border dark:bg-sidebar-bg/50">
            <Link
              :href="route('projects.show', project.id)"
              class="flex flex-1 items-center justify-center p-3 text-sm font-medium text-primary hover:bg-gray-100 dark:hover:bg-sidebar-bg/80"
            >
              View Details
            </Link>
            <Link
              :href="route('projects.edit', project.id)"
              class="flex flex-1 items-center justify-center border-l border-sidebar-border/70 p-3 text-sm font-medium text-primary hover:bg-gray-100 dark:border-sidebar-border dark:hover:bg-sidebar-bg/80"
            >
              Edit
            </Link>
          </div>
        </div>

        <!-- Empty state when no projects -->
        <div
          v-if="filteredProjects.length === 0"
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
              d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"
            />
          </svg>
          <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No projects found</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ searchQuery || filterTeam !== null ? 'No projects match your search criteria.' : 'Get started by creating a new project.' }}
          </p>
          <div class="mt-6">
            <Link
              :href="route('projects.create')"
              class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
            >
              Create New Project
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

// Define props for the component
const props = defineProps<{
  project: {
    id: number;
    name: string;
    description: string | null;
    user_id: number;
    team_id: number | null;
    notes: string | null;
    is_saved: boolean;
    created_at: string;
    updated_at: string;
    user: {
      id: number;
      name: string;
      email: string;
    };
    team: {
      id: number;
      name: string;
      owner_id: number;
    } | null;
  };
  terrains: Array<{
    id: number;
    title: string;
    description: string | null;
    surface_m2: number;
    price: number;
    city: string;
    zip_code: string;
    viabilised: boolean;
    analysis: {
      price_m2: number;
      market_price_m2: number;
      viability_cost: number;
      lots_possible: number;
      resale_estimate_min: number;
      resale_estimate_max: number;
      net_margin_estimate: number;
      ai_score: number;
      profitability_label: string;
    } | null;
    pivot: {
      project_id: number;
      terrain_id: number;
      notes: string | null;
    };
  }>;
  totalInvestment: number;
  totalProfit: number;
  averageScore: number;
}>();

const activeTab = ref('overview');
const editingNotes = ref<{ [key: number]: boolean }>({});
const terrainNotes = ref<{ [key: number]: string }>({});

// Initialize terrain notes from pivot data
props.terrains.forEach(terrain => {
  terrainNotes.value[terrain.id] = terrain.pivot.notes || '';
});

const confirmDelete = () => {
  if (confirm('Are you sure you want to delete this project? This action cannot be undone.')) {
    router.delete(route('projects.destroy', { project: props.project.id }));
  }
};

const removeTerrain = (terrainId: number) => {
  if (confirm('Are you sure you want to remove this terrain from the project?')) {
    router.delete(route('projects.terrains.destroy', {
      project: props.project.id,
      terrain: terrainId
    }));
  }
};

const startEditingNotes = (terrainId: number) => {
  editingNotes.value[terrainId] = true;
};

const saveNotes = (terrainId: number) => {
  router.put(route('projects.terrains.notes.update', {
    project: props.project.id,
    terrain: terrainId
  }), {
    notes: terrainNotes.value[terrainId]
  }, {
    preserveScroll: true,
    onSuccess: () => {
      editingNotes.value[terrainId] = false;
    }
  });
};

const cancelEditingNotes = (terrainId: number) => {
  // Reset to original value
  const terrain = props.terrains.find(t => t.id === terrainId);
  if (terrain) {
    terrainNotes.value[terrainId] = terrain.pivot.notes || '';
  }
  editingNotes.value[terrainId] = false;
};

// Format price as currency
const formatPrice = (price: number) => {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price);
};

// Format surface with units
const formatSurface = (surface: number) => {
  return new Intl.NumberFormat('fr-FR').format(surface) + ' m²';
};

// Format date
const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

// Format percentage
const formatPercentage = (value: number) => {
  return new Intl.NumberFormat('fr-FR', { style: 'percent', maximumFractionDigits: 2 }).format(value / 100);
};

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Projects',
    href: route('projects.index'),
  },
  {
    title: props.project.name,
    href: route('projects.show', { project: props.project.id }),
  },
];
</script>

<template>
  <Head :title="project.name" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-6 p-4">
      <!-- Project header with actions -->
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ project.name }}</h1>
          <p v-if="project.team" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Team: {{ project.team.name }}
          </p>
        </div>
        <div class="flex flex-wrap gap-2">
          <Link
            :href="route('projects.terrains.add', project.id)"
            class="rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:border-sidebar-border dark:bg-sidebar-bg dark:text-gray-300 dark:hover:bg-sidebar-bg/80"
          >
            Add Terrain
          </Link>
          <Link
            :href="route('projects.edit', project.id)"
            class="rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:border-sidebar-border dark:bg-sidebar-bg dark:text-gray-300 dark:hover:bg-sidebar-bg/80"
          >
            Edit Project
          </Link>
          <button
            @click="confirmDelete"
            class="rounded-md border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-700 shadow-sm hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30"
          >
            Delete Project
          </button>
        </div>
      </div>

      <!-- Key metrics cards -->
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-sidebar-border/70 bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-sidebar-bg">
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Investment</p>
          <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ formatPrice(totalInvestment) }}</p>
        </div>
        <div class="rounded-lg border border-sidebar-border/70 bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-sidebar-bg">
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Estimated Profit</p>
          <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ formatPrice(totalProfit) }}</p>
        </div>
        <div class="rounded-lg border border-sidebar-border/70 bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-sidebar-bg">
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">ROI</p>
          <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">
            {{ totalInvestment > 0 ? formatPercentage(totalProfit / totalInvestment * 100) : 'N/A' }}
          </p>
        </div>
        <div class="rounded-lg border border-sidebar-border/70 bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-sidebar-bg">
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Average AI Score</p>
          <div class="mt-1 flex items-center">
            <span class="text-xl font-semibold text-gray-900 dark:text-white">{{ Number(averageScore).toFixed(1) }}</span>
            <div class="ml-2 h-2 w-24 rounded-full bg-gray-200 dark:bg-gray-700">
              <div
                class="h-2 rounded-full"
                :class="[
                  averageScore >= 80
                    ? 'bg-green-500'
                    : averageScore >= 60
                    ? 'bg-blue-500'
                    : averageScore >= 40
                    ? 'bg-yellow-500'
                    : 'bg-red-500',
                ]"
                :style="{ width: `${averageScore}%` }"
              ></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabs navigation -->
      <div class="border-b border-sidebar-border/70 dark:border-sidebar-border">
        <nav class="-mb-px flex space-x-8">
          <button
            @click="activeTab = 'overview'"
            :class="[
              activeTab === 'overview'
                ? 'border-primary text-primary'
                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300',
              'whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium',
            ]"
          >
            Overview
          </button>
          <button
            @click="activeTab = 'terrains'"
            :class="[
              activeTab === 'terrains'
                ? 'border-primary text-primary'
                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300',
              'whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium',
            ]"
          >
            Terrains ({{ terrains.length }})
          </button>
        </nav>
      </div>

      <!-- Overview tab content -->
      <div v-if="activeTab === 'overview'" class="grid gap-6 md:grid-cols-2">
        <div class="overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border dark:bg-sidebar-bg">
          <div class="px-6 py-5">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Project Information</h3>
          </div>
          <div class="border-t border-sidebar-border/70 px-6 py-5 dark:border-sidebar-border">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
              <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ project.description || 'No description provided' }}
                </dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created By</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ project.user.name }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Team</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ project.team ? project.team.name : 'Personal Project' }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ formatDate(project.created_at) }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ formatDate(project.updated_at) }}</dd>
              </div>
              <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Notes</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ project.notes || 'No notes provided' }}
                </dd>
              </div>
            </dl>
          </div>
        </div>

        <div class="overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border dark:bg-sidebar-bg">
          <div class="px-6 py-5">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Project Summary</h3>
          </div>
          <div class="border-t border-sidebar-border/70 px-6 py-5 dark:border-sidebar-border">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Number of Terrains</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ terrains.length }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Surface Area</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ formatSurface(terrains.reduce((sum, terrain) => sum + Number(terrain.surface_m2), 0)) }}
                </dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Purchase Price</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ formatPrice(terrains.reduce((sum, terrain) => sum + Number(terrain.price), 0)) }}
                </dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Viability Cost</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ formatPrice(terrains.reduce((sum, terrain) => sum + (Number(terrain.analysis?.viability_cost) || 0), 0)) }}
                </dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Resale Estimate (Min)</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ formatPrice(terrains.reduce((sum, terrain) => sum + (Number(terrain.analysis?.resale_estimate_min) || 0), 0)) }}
                </dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Resale Estimate (Max)</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ formatPrice(terrains.reduce((sum, terrain) => sum + (Number(terrain.analysis?.resale_estimate_max) || 0), 0)) }}
                </dd>
              </div>
              <div class="sm:col-span-2 border-t border-sidebar-border/70 pt-4 dark:border-sidebar-border">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Investment</dt>
                <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ formatPrice(totalInvestment) }}</dd>
              </div>
              <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Profit</dt>
                <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ formatPrice(totalProfit) }}</dd>
              </div>
              <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Return on Investment</dt>
                <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                  {{ totalInvestment > 0 ? formatPercentage(totalProfit / totalInvestment * 100) : 'N/A' }}
                </dd>
              </div>
            </dl>
          </div>
        </div>
      </div>

      <!-- Terrains tab content -->
      <div v-if="activeTab === 'terrains'">
        <div class="overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border dark:bg-sidebar-bg">
          <div class="px-6 py-5 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Project Terrains</h3>
            <Link
              :href="route('projects.terrains.add', project.id)"
              class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
            >
              Add Terrain
            </Link>
          </div>

          <div v-if="terrains.length > 0" class="border-t border-sidebar-border/70 dark:border-sidebar-border">
            <ul class="divide-y divide-sidebar-border/70 dark:divide-sidebar-border">
              <li v-for="terrain in terrains" :key="terrain.id" class="p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                  <div class="flex-1">
                    <div class="flex items-start justify-between">
                      <div>
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ terrain.title }}</h4>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ terrain.city }}, {{ terrain.zip_code }}</p>
                      </div>
                      <div class="flex items-center">
                        <span
                          v-if="terrain.analysis"
                          :class="[
                            terrain.analysis.profitability_label === 'Excellent'
                              ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100'
                              : terrain.analysis.profitability_label === 'Good'
                              ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100'
                              : terrain.analysis.profitability_label === 'Average'
                              ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100'
                              : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100',
                            'inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium',
                          ]"
                        >
                          {{ terrain.analysis.profitability_label }}
                        </span>
                      </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                      <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Surface</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ formatSurface(terrain.surface_m2) }}</p>
                      </div>
                      <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Price</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ formatPrice(terrain.price) }}</p>
                      </div>
                      <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Status</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                          {{ terrain.viabilised ? 'Viabilised' : 'Not Viabilised' }}
                        </p>
                      </div>
                    </div>

                    <div v-if="terrain.analysis" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                      <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Viability Cost</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ formatPrice(terrain.analysis.viability_cost) }}</p>
                      </div>
                      <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Estimated Profit</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ formatPrice(terrain.analysis.net_margin_estimate) }}</p>
                      </div>
                      <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">AI Score</p>
                        <div class="flex items-center">
                          <span class="text-sm font-medium text-gray-900 dark:text-white">{{ Number(terrain.analysis.ai_score).toFixed(1) }}</span>
                          <div class="ml-2 h-1.5 w-16 rounded-full bg-gray-200 dark:bg-gray-700">
                            <div
                              class="h-1.5 rounded-full"
                              :class="[
                                terrain.analysis.ai_score >= 80
                                  ? 'bg-green-500'
                                  : terrain.analysis.ai_score >= 60
                                  ? 'bg-blue-500'
                                  : terrain.analysis.ai_score >= 40
                                  ? 'bg-yellow-500'
                                  : 'bg-red-500',
                              ]"
                              :style="{ width: `${terrain.analysis.ai_score}%` }"
                            ></div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="mt-4">
                      <div class="flex items-center justify-between">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Notes</p>
                        <div class="flex space-x-2">
                          <button
                            v-if="!editingNotes[terrain.id]"
                            @click="startEditingNotes(terrain.id)"
                            class="text-xs font-medium text-primary hover:text-primary-dark"
                          >
                            Edit
                          </button>
                          <div v-else class="flex space-x-2">
                            <button
                              @click="saveNotes(terrain.id)"
                              class="text-xs font-medium text-green-600 hover:text-green-800 dark:text-green-500 dark:hover:text-green-400"
                            >
                              Save
                            </button>
                            <button
                              @click="cancelEditingNotes(terrain.id)"
                              class="text-xs font-medium text-red-600 hover:text-red-800 dark:text-red-500 dark:hover:text-red-400"
                            >
                              Cancel
                            </button>
                          </div>
                        </div>
                      </div>
                      <div v-if="!editingNotes[terrain.id]" class="mt-1">
                        <p v-if="terrain.pivot.notes" class="text-sm text-gray-900 dark:text-white">{{ terrain.pivot.notes }}</p>
                        <p v-else class="text-sm italic text-gray-400 dark:text-gray-500">No notes for this terrain</p>
                      </div>
                      <div v-else class="mt-1">
                        <textarea
                          v-model="terrainNotes[terrain.id]"
                          rows="3"
                          class="block w-full rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-sm text-gray-900 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary dark:border-sidebar-border dark:bg-sidebar-bg/50 dark:text-white"
                          placeholder="Add notes about this terrain in the project"
                        ></textarea>
                      </div>
                    </div>
                  </div>

                  <div class="flex flex-row justify-end gap-2 sm:flex-col">
                    <Link
                      :href="route('terrains.show', terrain.id)"
                      class="rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:border-sidebar-border dark:bg-sidebar-bg dark:text-gray-300 dark:hover:bg-sidebar-bg/80"
                    >
                      View Terrain
                    </Link>
                    <button
                      @click="removeTerrain(terrain.id)"
                      class="rounded-md border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-700 shadow-sm hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30"
                    >
                      Remove
                    </button>
                  </div>
                </div>
              </li>
            </ul>
          </div>

          <!-- Empty state when no terrains -->
          <div
            v-else
            class="border-t border-sidebar-border/70 flex flex-col items-center justify-center p-12 text-center dark:border-sidebar-border"
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
                d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z"
              />
            </svg>
            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No terrains in this project</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              Get started by adding a terrain to this project.
            </p>
            <div class="mt-6">
              <Link
                :href="route('projects.terrains.add', project.id)"
                class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
              >
                Add Terrain
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

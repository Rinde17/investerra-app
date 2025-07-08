<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

// Define props for the component
const props = defineProps<{
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
      ai_score: number;
      profitability_label: string;
      net_margin_estimate: number;
    } | null;
  }>;
  canCompare: boolean;
}>();

const searchQuery = ref('');
const sortBy = ref('created_at');
const sortOrder = ref('desc');
const selectedTerrains = ref<number[]>([]);

const compareTerrains = () => {
  if (selectedTerrains.value.length < 2) {
    alert('Please select at least 2 terrains to compare.');
    return;
  }

  if (selectedTerrains.value.length > 5) {
    alert('You can compare a maximum of 5 terrains at once.');
    return;
  }

  router.post(route('terrains.analysis.compare'), {
    terrain_ids: selectedTerrains.value
  });
};

const filteredTerrains = computed(() => {
  let filtered = [...props.terrains];

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(terrain =>
      terrain.title.toLowerCase().includes(query) ||
      terrain.city.toLowerCase().includes(query) ||
      terrain.zip_code.includes(query) ||
      (terrain.description && terrain.description.toLowerCase().includes(query))
    );
  }

  // Apply sorting
  filtered.sort((a, b) => {
    let comparison = 0;

    switch (sortBy.value) {
      case 'title':
        comparison = a.title.localeCompare(b.title);
        break;
      case 'price':
        comparison = a.price - b.price;
        break;
      case 'surface':
        comparison = a.surface_m2 - b.surface_m2;
        break;
      case 'price_m2':
        const aPrice = a.analysis?.price_m2 || 0;
        const bPrice = b.analysis?.price_m2 || 0;
        comparison = aPrice - bPrice;
        break;
      case 'score':
        const aScore = a.analysis?.ai_score || 0;
        const bScore = b.analysis?.ai_score || 0;
        comparison = aScore - bScore;
        break;
      case 'profit':
        const aProfit = a.analysis?.net_margin_estimate || 0;
        const bProfit = b.analysis?.net_margin_estimate || 0;
        comparison = aProfit - bProfit;
        break;
      default:
        // Default to sorting by ID (most recent first)
        comparison = b.id - a.id;
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

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price);
};

const formatSurface = (surface: number) => {
  return new Intl.NumberFormat('fr-FR').format(surface) + ' m²';
};

const formatPricePerM2 = (price: number) => {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price) + '/m²';
};

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Terrains',
    href: route('terrains.index'),
  },
];
</script>

<template>
  <Head title="Terrains" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-6 p-4">
      <!-- Header with Create Terrain button and search -->
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Terrains</h1>
          <div v-if="canCompare && selectedTerrains.length > 0" class="flex items-center">
            <span class="rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-medium text-primary dark:bg-primary/20">
              {{ selectedTerrains.length }} selected
            </span>
          </div>
        </div>
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
              placeholder="Search terrains..."
            />
          </div>
          <button
            v-if="canCompare && selectedTerrains.length >= 2"
            @click="compareTerrains"
            class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
          >
            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Compare Terrains
          </button>
          <Link
            :href="route('terrains.create')"
            class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
          >
            Add New Terrain
          </Link>
        </div>
      </div>

      <!-- Upgrade message for comparison feature -->
      <div v-if="!canCompare && filteredTerrains.length > 0" class="rounded-lg border border-sidebar-border/70 bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-sidebar-bg">
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-primary" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Terrain Comparison Tool</h3>
            <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              <p>Upgrade to Pro or Investor plan to access our powerful terrain comparison tool. Compare up to 5 terrains side by side to make better investment decisions.</p>
            </div>
            <div class="mt-2">
              <Link
                :href="route('pricing.index')"
                class="text-sm font-medium text-primary hover:text-primary-dark dark:text-primary-light dark:hover:text-primary"
              >
                View Pricing Plans
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Sorting options -->
      <div class="flex flex-wrap gap-2">
        <button
          @click="toggleSort('title')"
          :class="[
            sortBy === 'title' ? 'bg-primary/10 text-primary' : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-sidebar-bg dark:text-gray-300 dark:hover:bg-sidebar-bg/80',
            'inline-flex items-center rounded-md border border-sidebar-border/70 px-3 py-1 text-sm font-medium dark:border-sidebar-border',
          ]"
        >
          Name
          <svg v-if="sortBy === 'title'" class="ml-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" :transform="sortOrder === 'asc' ? 'rotate(180)' : ''" />
          </svg>
        </button>
        <button
          @click="toggleSort('price')"
          :class="[
            sortBy === 'price' ? 'bg-primary/10 text-primary' : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-sidebar-bg dark:text-gray-300 dark:hover:bg-sidebar-bg/80',
            'inline-flex items-center rounded-md border border-sidebar-border/70 px-3 py-1 text-sm font-medium dark:border-sidebar-border',
          ]"
        >
          Price
          <svg v-if="sortBy === 'price'" class="ml-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" :transform="sortOrder === 'asc' ? 'rotate(180)' : ''" />
          </svg>
        </button>
        <button
          @click="toggleSort('surface')"
          :class="[
            sortBy === 'surface' ? 'bg-primary/10 text-primary' : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-sidebar-bg dark:text-gray-300 dark:hover:bg-sidebar-bg/80',
            'inline-flex items-center rounded-md border border-sidebar-border/70 px-3 py-1 text-sm font-medium dark:border-sidebar-border',
          ]"
        >
          Surface
          <svg v-if="sortBy === 'surface'" class="ml-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" :transform="sortOrder === 'asc' ? 'rotate(180)' : ''" />
          </svg>
        </button>
        <button
          @click="toggleSort('price_m2')"
          :class="[
            sortBy === 'price_m2' ? 'bg-primary/10 text-primary' : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-sidebar-bg dark:text-gray-300 dark:hover:bg-sidebar-bg/80',
            'inline-flex items-center rounded-md border border-sidebar-border/70 px-3 py-1 text-sm font-medium dark:border-sidebar-border',
          ]"
        >
          Price/m²
          <svg v-if="sortBy === 'price_m2'" class="ml-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" :transform="sortOrder === 'asc' ? 'rotate(180)' : ''" />
          </svg>
        </button>
        <button
          @click="toggleSort('score')"
          :class="[
            sortBy === 'score' ? 'bg-primary/10 text-primary' : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-sidebar-bg dark:text-gray-300 dark:hover:bg-sidebar-bg/80',
            'inline-flex items-center rounded-md border border-sidebar-border/70 px-3 py-1 text-sm font-medium dark:border-sidebar-border',
          ]"
        >
          AI Score
          <svg v-if="sortBy === 'score'" class="ml-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" :transform="sortOrder === 'asc' ? 'rotate(180)' : ''" />
          </svg>
        </button>
        <button
          @click="toggleSort('profit')"
          :class="[
            sortBy === 'profit' ? 'bg-primary/10 text-primary' : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-sidebar-bg dark:text-gray-300 dark:hover:bg-sidebar-bg/80',
            'inline-flex items-center rounded-md border border-sidebar-border/70 px-3 py-1 text-sm font-medium dark:border-sidebar-border',
          ]"
        >
          Profit
          <svg v-if="sortBy === 'profit'" class="ml-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" :transform="sortOrder === 'asc' ? 'rotate(180)' : ''" />
          </svg>
        </button>
      </div>

      <!-- Terrains grid -->
      <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="terrain in filteredTerrains"
          :key="terrain.id"
          class="flex flex-col overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm transition-all hover:shadow-md dark:border-sidebar-border dark:bg-sidebar-bg"
          :class="{ 'ring-2 ring-primary': canCompare && selectedTerrains.includes(terrain.id) }"
        >
          <div class="flex flex-1 flex-col p-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div v-if="canCompare && terrain.analysis" class="flex items-center">
                  <input
                    type="checkbox"
                    :id="`terrain-${terrain.id}`"
                    :checked="selectedTerrains.includes(terrain.id)"
                    @change="(e) => {
                      if (e.target.checked) {
                        if (selectedTerrains.length < 5) {
                          selectedTerrains.push(terrain.id);
                        } else {
                          e.target.checked = false;
                          alert('You can select a maximum of 5 terrains for comparison.');
                        }
                      } else {
                        selectedTerrains = selectedTerrains.filter(id => id !== terrain.id);
                      }
                    }"
                    class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700"
                  />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ terrain.title }}</h3>
              </div>
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
                  'rounded-full px-2.5 py-0.5 text-xs font-medium',
                ]"
              >
                {{ terrain.analysis.profitability_label }}
              </span>
            </div>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
              {{ terrain.city }}, {{ terrain.zip_code }}
            </p>
            <div class="mt-4 grid grid-cols-2 gap-4">
              <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Price</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ formatPrice(terrain.price) }}</p>
              </div>
              <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Surface</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ formatSurface(terrain.surface_m2) }}</p>
              </div>
              <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Price/m²</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                  {{ terrain.analysis ? formatPricePerM2(terrain.analysis.price_m2) : 'N/A' }}
                </p>
              </div>
              <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">AI Score</p>
                <div class="flex items-center">
                  <span class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ terrain.analysis ? Number(terrain.analysis.ai_score).toFixed(1) : 'N/A' }}
                  </span>
                  <div v-if="terrain.analysis" class="ml-2 h-2 w-16 rounded-full bg-gray-200 dark:bg-gray-700">
                    <div
                      class="h-2 rounded-full"
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
              <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Status</p>
              <div class="flex items-center">
                <span
                  :class="[
                    terrain.viabilised
                      ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100'
                      : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100',
                    'mt-1 inline-flex rounded-full px-2 py-0.5 text-xs font-medium',
                  ]"
                >
                  {{ terrain.viabilised ? 'Viabilised' : 'Not Viabilised' }}
                </span>
              </div>
            </div>
            <div v-if="terrain.analysis && terrain.analysis.net_margin_estimate" class="mt-4">
              <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Estimated Profit</p>
              <p class="text-sm font-semibold text-gray-900 dark:text-white">
                {{ formatPrice(terrain.analysis.net_margin_estimate) }}
              </p>
            </div>
          </div>
          <div class="flex border-t border-sidebar-border/70 bg-gray-50 dark:border-sidebar-border dark:bg-sidebar-bg/50">
            <Link
              :href="route('terrains.show', terrain.id)"
              class="flex flex-1 items-center justify-center p-3 text-sm font-medium text-primary hover:bg-gray-100 dark:hover:bg-sidebar-bg/80"
            >
              View Details
            </Link>
            <Link
              :href="route('terrains.edit', terrain.id)"
              class="flex flex-1 items-center justify-center border-l border-sidebar-border/70 p-3 text-sm font-medium text-primary hover:bg-gray-100 dark:border-sidebar-border dark:hover:bg-sidebar-bg/80"
            >
              Edit
            </Link>
          </div>
        </div>

        <!-- Empty state when no terrains -->
        <div
          v-if="filteredTerrains.length === 0"
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
              d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z"
            />
          </svg>
          <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No terrains found</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ searchQuery ? 'No terrains match your search criteria.' : 'Get started by adding a new terrain.' }}
          </p>
          <div class="mt-6">
            <Link
              :href="route('terrains.create')"
              class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
            >
              Add New Terrain
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

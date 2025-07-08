<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

// Define props for the component
const props = defineProps<{
  terrain: {
    id: number;
    title: string;
    description: string | null;
    surface_m2: number;
    price: number;
    city: string;
    zip_code: string;
    latitude: number | null;
    longitude: number | null;
    viabilised: boolean;
    source_url: string | null;
    source_platform: string | null;
    user_id: number;
    created_at: string;
    updated_at: string;
  };
  analysis: {
    id: number;
    terrain_id: number;
    price_m2: number;
    market_price_m2: number;
    price_difference_percentage: number;
    viability_cost: number;
    lots_possible: number;
    resale_estimate_min: number;
    resale_estimate_max: number;
    net_margin_estimate: number;
    profit_margin_percentage: number;
    ai_score: number;
    profitability_label: string;
    overall_risk: string;
    overall_recommendation: string;
    analysis_details: {
      calculation_method: string;
      market_data_source: string;
      division_strategy: string;
      price_analysis: any;
      development_potential: any;
      profitability_analysis: any;
      risk_assessment: any;
      recommendations: any;
      resale_estimate_min: number;
      resale_estimate_max: number;
    };
    analyzed_at: string;
  } | null;
  projects: Array<{
    id: number;
    name: string;
    description: string | null;
  }>;
}>();

const activeTab = ref('details');

const confirmDelete = () => {
  if (confirm('Are you sure you want to delete this terrain? This action cannot be undone.')) {
    router.delete(route('terrains.destroy', { terrain: props.terrain.id }));
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

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};


const totalInvestmentCost = computed(() => {
   if (!props.analysis) {
    return props.terrain.price;
  }
  return Number(props.terrain.price) + Number(props.analysis.viability_cost);
});

const averageResaleEstimate = computed(() => {
  if (!props.analysis) {
    return 0;
  }
  return (Number(props.analysis.resale_estimate_min) + Number(props.analysis.resale_estimate_max)) / 2;
});

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Terrains',
    href: route('terrains.index'),
  },
  {
    title: props.terrain.title,
    href: route('terrains.show', { terrain: props.terrain.id }),
  },
];
</script>

<template>
  <Head :title="terrain.title" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col gap-6 p-4">
      <!-- Terrain header with actions -->
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ terrain.title }}</h1>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ terrain.city }}, {{ terrain.zip_code }}
          </p>
        </div>
        <div class="flex flex-wrap gap-2">
          <Link
            :href="route('terrains.edit', terrain.id)"
            class="rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:border-sidebar-border dark:bg-sidebar-bg dark:text-gray-300 dark:hover:bg-sidebar-bg/80"
          >
            Edit Terrain
          </Link>
          <button
            @click="confirmDelete"
            class="rounded-md border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-700 shadow-sm hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30"
          >
            Delete Terrain
          </button>
        </div>
      </div>

      <!-- Key metrics cards -->
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-sidebar-border/70 bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-sidebar-bg">
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Price</p>
          <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ formatPrice(terrain.price) }}</p>
        </div>
        <div class="rounded-lg border border-sidebar-border/70 bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-sidebar-bg">
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Surface</p>
          <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ formatSurface(terrain.surface_m2) }}</p>
        </div>
        <div class="rounded-lg border border-sidebar-border/70 bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-sidebar-bg">
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Price per m²</p>
          <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">
            {{ analysis ? formatPricePerM2(analysis.price_m2) : formatPricePerM2(terrain.price / terrain.surface_m2) }}
          </p>
        </div>
        <div class="rounded-lg border border-sidebar-border/70 bg-white p-4 shadow-sm dark:border-sidebar-border dark:bg-sidebar-bg">
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</p>
          <p class="mt-1">
            <span
              :class="[
                terrain.viabilised
                  ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100'
                  : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100',
                'inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium',
              ]"
            >
              {{ terrain.viabilised ? 'Viabilised' : 'Not Viabilised' }}
            </span>
          </p>
        </div>
      </div>

      <!-- Tabs navigation -->
      <div class="border-b border-sidebar-border/70 dark:border-sidebar-border">
        <nav class="-mb-px flex space-x-8">
          <button
            @click="activeTab = 'details'"
            :class="[
              activeTab === 'details'
                ? 'border-primary text-primary'
                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300',
              'whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium',
            ]"
          >
            Details
          </button>
          <button
            @click="activeTab = 'analysis'"
            :class="[
              activeTab === 'analysis'
                ? 'border-primary text-primary'
                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300',
              'whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium',
            ]"
          >
            Analysis
          </button>
          <button
            @click="activeTab = 'projects'"
            :class="[
              activeTab === 'projects'
                ? 'border-primary text-primary'
                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300',
              'whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium',
            ]"
          >
            Projects
          </button>
        </nav>
      </div>

      <!-- Details tab content -->
      <div v-if="activeTab === 'details'" class="grid gap-6 md:grid-cols-2">
        <div class="overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border dark:bg-sidebar-bg">
          <div class="px-6 py-5">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Basic Information</h3>
          </div>
          <div class="border-t border-sidebar-border/70 px-6 py-5 dark:border-sidebar-border">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
              <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ terrain.description || 'No description provided' }}
                </dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">City</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ terrain.city }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ZIP Code</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ terrain.zip_code }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ formatDate(terrain.created_at) }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ formatDate(terrain.updated_at) }}</dd>
              </div>
            </dl>
          </div>
        </div>

        <div class="overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border dark:bg-sidebar-bg">
          <div class="px-6 py-5">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Additional Information</h3>
          </div>
          <div class="border-t border-sidebar-border/70 px-6 py-5 dark:border-sidebar-border">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
              <div v-if="terrain.latitude && terrain.longitude" class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Location</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ terrain.latitude }}, {{ terrain.longitude }}
                </dd>
              </div>
              <div v-if="terrain.source_url" class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Source URL</dt>
                <dd class="mt-1 text-sm text-primary dark:text-primary-light">
                  <a :href="terrain.source_url" target="_blank" rel="noopener noreferrer" class="hover:underline">
                    {{ terrain.source_url }}
                  </a>
                </dd>
              </div>
              <div v-if="terrain.source_platform">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Source Platform</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ terrain.source_platform }}</dd>
              </div>
            </dl>
          </div>
        </div>

          <div v-if="terrain.latitude && terrain.longitude" class="md:col-span-2 overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border dark:bg-sidebar-bg">
              <div class="px-6 py-5">
                  <h3 class="text-lg font-medium text-gray-900 dark:text-white">Map</h3>
              </div>
              <div class="border-t border-sidebar-border/70 px-6 py-5 dark:border-sidebar-border">
<!--                  <Map :latitude="Number(terrain.latitude)" :longitude="Number(terrain.longitude)" />-->
              </div>
          </div>
      </div>

      <!-- Analysis tab content -->
      <div v-if="activeTab === 'analysis'" class="grid gap-6 md:grid-cols-2">
        <div v-if="analysis" class="overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border dark:bg-sidebar-bg">
          <div class="px-6 py-5">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white">Analysis Results</h3>
              <span
                :class="[
                  analysis.profitability_label === 'Excellent'
                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100'
                    : analysis.profitability_label === 'Good'
                    ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100'
                    : analysis.profitability_label === 'Average'
                    ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100'
                    : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100',
                  'rounded-full px-2.5 py-0.5 text-xs font-medium',
                ]"
              >
                {{ analysis.profitability_label }}
              </span>
            </div>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              Analysis performed on {{ formatDate(analysis.analyzed_at) }}
            </p>

            <!-- Export buttons -->
            <div class="mt-4 flex flex-wrap gap-2">
              <a
                :href="route('terrains.analysis.pdf', terrain.id)"
                target="_blank"
                class="inline-flex items-center rounded-md bg-primary px-3 py-1.5 text-sm font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
              >
                <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export as PDF
              </a>
              <a
                :href="route('terrains.analysis.csv', terrain.id)"
                class="inline-flex items-center rounded-md border border-sidebar-border/70 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:border-sidebar-border dark:bg-sidebar-bg dark:text-gray-300 dark:hover:bg-sidebar-bg/80"
              >
                <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export as CSV
              </a>
            </div>
          </div>
          <div class="border-t border-sidebar-border/70 px-6 py-5 dark:border-sidebar-border">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">AI Score</dt>
                <dd class="mt-1 flex items-center">
                    <span class="text-lg font-semibold text-gray-900 dark:text-white">{{ Number(analysis.ai_score).toFixed(1) }}</span>
                  <div class="ml-2 h-2 w-24 rounded-full bg-gray-200 dark:bg-gray-700">
                    <div
                      class="h-2 rounded-full"
                      :class="[
                        analysis.ai_score >= 80
                          ? 'bg-green-500'
                          : analysis.ai_score >= 60
                          ? 'bg-blue-500'
                          : analysis.ai_score >= 40
                          ? 'bg-yellow-500'
                          : 'bg-red-500',
                      ]"
                      :style="{ width: `${analysis.ai_score}%` }"
                    ></div>
                  </div>
                </dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Market Price/m²</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ formatPricePerM2(analysis.market_price_m2) }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Price Difference</dt>
                <dd class="mt-1 text-sm" :class="analysis.price_difference_percentage <= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                  {{ analysis.price_difference_percentage > 0 ? '+' : '' }}{{ Number(analysis.price_difference_percentage).toFixed(2) }}%
                </dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Viability Cost</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ formatPrice(analysis.viability_cost) }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Possible Lots</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ analysis.lots_possible }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Resale Estimate (Min)</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ formatPrice(analysis.resale_estimate_min) }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Resale Estimate (Max)</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ formatPrice(analysis.resale_estimate_max) }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Net Margin Estimate</dt>
                <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ formatPrice(analysis.net_margin_estimate) }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Profit Margin</dt>
                  <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ Number(analysis.profit_margin_percentage).toFixed(2) }}%</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Overall Risk</dt>
                <dd class="mt-1 text-sm">
                  <span
                    :class="[
                      analysis.overall_risk === 'low'
                        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100'
                        : analysis.overall_risk === 'medium'
                        ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100'
                        : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100',
                      'rounded-full px-2.5 py-0.5 text-xs font-medium',
                    ]"
                  >
                    {{ analysis.overall_risk?.charAt(0).toUpperCase() + analysis.overall_risk?.slice(1) }}
                  </span>
                </dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Recommendation</dt>
                <dd class="mt-1 text-sm">
                  <span
                    :class="[
                      analysis.overall_recommendation === 'strong_buy'
                        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100'
                        : analysis.overall_recommendation === 'buy'
                        ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100'
                        : analysis.overall_recommendation === 'neutral'
                        ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                        : analysis.overall_recommendation === 'caution'
                        ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100'
                        : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100',
                      'rounded-full px-2.5 py-0.5 text-xs font-medium',
                    ]"
                  >
                    {{ analysis.overall_recommendation === 'strong_buy'
                      ? 'Strong Buy'
                      : analysis.overall_recommendation?.charAt(0).toUpperCase() + analysis.overall_recommendation?.slice(1) }}
                  </span>
                </dd>
              </div>
            </dl>
          </div>
        </div>

        <div v-if="analysis" class="overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border dark:bg-sidebar-bg">
          <div class="px-6 py-5">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Investment Summary</h3>
          </div>
          <div class="border-t border-sidebar-border/70 px-6 py-5 dark:border-sidebar-border">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6">
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Purchase Price</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ formatPrice(terrain.price) }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Viability Cost</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ formatPrice(analysis.viability_cost) }}</dd>
              </div>
              <div class="border-t border-sidebar-border/70 pt-4 dark:border-sidebar-border">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Investment</dt>
                <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ formatPrice(totalInvestmentCost) }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Average Resale Estimate</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ formatPrice(averageResaleEstimate) }}</dd>
              </div>
              <div class="border-t border-sidebar-border/70 pt-4 dark:border-sidebar-border">
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Net Profit</dt>
                <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ formatPrice(analysis.net_margin_estimate) }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Return on Investment</dt>
                  <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ Number(analysis.profit_margin_percentage).toFixed(2) }}%</dd>
              </div>
            </dl>
          </div>
        </div>

        <div v-if="!analysis" class="col-span-full flex flex-col items-center justify-center rounded-lg border border-dashed border-sidebar-border/70 bg-gray-50 p-12 text-center dark:border-sidebar-border dark:bg-sidebar-bg/30">
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
              d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
            />
          </svg>
          <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No analysis available</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            This terrain hasn't been analyzed yet.
          </p>
        </div>
      </div>

      <!-- Projects tab content -->
      <div v-if="activeTab === 'projects'" class="overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border dark:bg-sidebar-bg">
        <div class="p-6">
          <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Projects Including This Terrain</h2>
            <Link
              href="/projects/create"
              class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
            >
              New Project
            </Link>
          </div>

          <div v-if="projects.length > 0" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div
              v-for="project in projects"
              :key="project.id"
              class="flex flex-col overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm transition-all hover:shadow-md dark:border-sidebar-border dark:bg-sidebar-bg"
            >
              <div class="flex flex-1 flex-col p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ project.name }}</h3>
                <p v-if="project.description" class="mt-2 flex-1 text-sm text-gray-500 dark:text-gray-400">
                  {{ project.description }}
                </p>
                <p v-else class="mt-2 flex-1 text-sm italic text-gray-400 dark:text-gray-500">
                  No description provided
                </p>
              </div>
              <div class="border-t border-sidebar-border/70 bg-gray-50 p-4 dark:border-sidebar-border dark:bg-sidebar-bg/50">
                <Link
                  :href="`/projects/${project.id}`"
                  class="flex w-full items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                >
                  View Project
                </Link>
              </div>
            </div>
          </div>

          <div
            v-else
            class="flex flex-col items-center justify-center rounded-lg border border-dashed border-sidebar-border/70 bg-gray-50 p-12 text-center dark:border-sidebar-border dark:bg-sidebar-bg/30"
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
            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No projects yet</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add this terrain to a project to organize your investments.</p>
            <div class="mt-6">
              <Link
                href="/projects/create"
                class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
              >
                Create New Project
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

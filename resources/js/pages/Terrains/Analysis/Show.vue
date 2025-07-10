<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

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
    canCompare: boolean;
    comparableTerrains: Array<{
        id: number;
        title: string;
        city: string;
        zip_code: string;
    }>;
}>();

const selectedTerrains = ref<number[]>([props.terrain.id]);

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price);
};

// const formatSurface = (surface: number) => {
//     return new Intl.NumberFormat('fr-FR').format(surface) + ' m²';
// };

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

// const totalInvestmentCost = computed(() => {
//     if (!props.analysis) {
//         return props.terrain.price;
//     }
//     return Number(props.terrain.price) + Number(props.analysis.viability_cost);
// });

// const averageResaleEstimate = computed(() => {
//     if (!props.analysis) {
//         return 0;
//     }
//     return (Number(props.analysis.resale_estimate_min) + Number(props.analysis.resale_estimate_max)) / 2;
// });

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Terrains',
        href: route('terrains.index'),
    },
    {
        title: props.terrain.title,
        href: route('terrains.show', { terrain: props.terrain.id }),
    },
    {
        title: 'Analysis',
        href: route('terrains.analysis.show', { terrain: props.terrain.id }),
    },
];

const compareTerrains = () => {
    if (selectedTerrains.value.length < 2) {
        alert('Please select at least one more terrain to compare.');
        return;
    }

    router.post(route('terrains.analysis.compare'), {
        terrain_ids: selectedTerrains.value,
    });
};
</script>

<template>
    <AuthenticatedLayout :title="`${terrain.title} - Analysis`" :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <!-- Header with actions -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ terrain.title }} - Analysis</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ terrain.city }}, {{ terrain.zip_code }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Link
                        :href="route('terrains.show', terrain.id)"
                        class="dark:bg-sidebar-bg dark:hover:bg-sidebar-bg/80 rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none dark:border-sidebar-border dark:text-gray-300"
                    >
                        Back to Terrain
                    </Link>
                </div>
            </div>

            <!-- Analysis content -->
            <div v-if="analysis" class="grid gap-6 md:grid-cols-2">
                <div
                    class="dark:bg-sidebar-bg overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border"
                >
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
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Analysis performed on {{ formatDate(analysis.analyzed_at) }}</p>

                        <!-- Export buttons -->
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a
                                :href="route('terrains.analysis.pdf', terrain.id)"
                                target="_blank"
                                class="hover:bg-primary-dark inline-flex items-center rounded-md bg-primary px-3 py-1.5 text-sm font-medium text-white focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none"
                            >
                                <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    />
                                </svg>
                                Export as PDF
                            </a>
                            <a
                                :href="route('terrains.analysis.csv', terrain.id)"
                                class="dark:bg-sidebar-bg dark:hover:bg-sidebar-bg/80 inline-flex items-center rounded-md border border-sidebar-border/70 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none dark:border-sidebar-border dark:text-gray-300"
                            >
                                <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    />
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
                                    <span class="text-lg font-semibold text-gray-900 dark:text-white">{{
                                        Number(analysis.ai_score).toFixed(1)
                                    }}</span>
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
                                <dd
                                    class="mt-1 text-sm"
                                    :class="
                                        analysis.price_difference_percentage <= 0
                                            ? 'text-green-600 dark:text-green-400'
                                            : 'text-red-600 dark:text-red-400'
                                    "
                                >
                                    {{ analysis.price_difference_percentage > 0 ? '+' : ''
                                    }}{{ Number(analysis.price_difference_percentage).toFixed(2) }}%
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
                                <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ formatPrice(analysis.net_margin_estimate) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Profit Margin</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ Number(analysis.profit_margin_percentage).toFixed(2) }}%
                                </dd>
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
                        </dl>
                    </div>
                </div>

                <!-- Compare with other terrains section -->
                <div
                    v-if="canCompare"
                    class="dark:bg-sidebar-bg overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border"
                >
                    <div class="px-6 py-5">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Compare with Other Terrains</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Select terrains to compare with this one</p>
                    </div>
                    <div class="border-t border-sidebar-border/70 px-6 py-5 dark:border-sidebar-border">
                        <div class="mb-4">
                            <p class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Selected terrains:</p>
                            <div class="flex flex-wrap gap-2">
                                <div class="rounded-md bg-primary/10 px-3 py-1 text-sm text-primary dark:bg-primary/20">
                                    {{ terrain.title }} (Current)
                                </div>
                                <div
                                    v-for="id in selectedTerrains.filter((t) => t !== terrain.id)"
                                    :key="id"
                                    class="flex items-center rounded-md bg-gray-100 px-3 py-1 text-sm text-gray-700 dark:bg-gray-700 dark:text-gray-300"
                                >
                                    {{ comparableTerrains.find((t) => t.id === id)?.title }}
                                    <button
                                        @click="selectedTerrains = selectedTerrains.filter((t) => t !== id)"
                                        class="ml-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="terrain-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >Add terrain to comparison:</label
                            >
                            <select
                                id="terrain-select"
                                class="mt-1 block w-full rounded-md border-gray-300 py-2 pr-10 pl-3 text-base focus:border-primary focus:ring-primary focus:outline-none sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                @change="
                                    (e) => {
                                        const value = parseInt((e.target as HTMLSelectElement).value);
                                        if (value && !selectedTerrains.includes(value)) {
                                            selectedTerrains.push(value);
                                        }
                                        (e.target as HTMLSelectElement).value = '';
                                    }
                                "
                            >
                                <option value="">Select a terrain</option>
                                <option
                                    v-for="t in comparableTerrains.filter((t) => t.id !== terrain.id && !selectedTerrains.includes(t.id))"
                                    :key="t.id"
                                    :value="t.id"
                                >
                                    {{ t.title }} ({{ t.city }})
                                </option>
                            </select>
                        </div>

                        <button
                            @click="compareTerrains"
                            class="hover:bg-primary-dark inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-white focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none"
                            :disabled="selectedTerrains.length < 2"
                        >
                            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                />
                            </svg>
                            Compare Terrains
                        </button>
                    </div>
                </div>

                <div
                    v-else
                    class="dark:bg-sidebar-bg overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border"
                >
                    <div class="px-6 py-5">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Terrain Comparison</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Upgrade your subscription to access the terrain comparison feature
                        </p>
                    </div>
                    <div class="border-t border-sidebar-border/70 px-6 py-5 dark:border-sidebar-border">
                        <p class="mb-4 text-sm text-gray-700 dark:text-gray-300">
                            The terrain comparison tool allows you to compare multiple terrains side by side to make better investment decisions.
                        </p>
                        <Link
                            :href="route('pricing.index')"
                            class="hover:bg-primary-dark inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-white focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none"
                        >
                            Upgrade Subscription
                        </Link>
                    </div>
                </div>
            </div>

            <div v-else class="dark:bg-sidebar-bg rounded-lg border border-sidebar-border/70 bg-white p-6 shadow-sm dark:border-sidebar-border">
                <p class="text-center text-gray-700 dark:text-gray-300">No analysis available for this terrain.</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Link } from '@inertiajs/vue3';

interface AnalysisDetails {
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
}

interface Analysis {
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
    analysis_details: AnalysisDetails;
    analyzed_at: string;
}

interface Terrain {
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
    analysis: Analysis;
}

// Define props for the component
defineProps<{
    terrains: Array<Terrain> | null;
}>();

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price);
};

const formatSurface = (surface: number) => {
    return new Intl.NumberFormat('fr-FR').format(surface) + ' m²';
};

const formatPricePerM2 = (price: number) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price) + '/m²';
};

// const formatDate = (dateString: string) => {
//   return new Date(dateString).toLocaleDateString('fr-FR', {
//     year: 'numeric',
//     month: 'long',
//     day: 'numeric',
//   });
// };

const totalInvestmentCost = (terrain: Terrain) => {
    if (!terrain.analysis) {
        return terrain.price;
    }
    return Number(terrain.price) + Number(terrain.analysis.viability_cost);
};

// const averageResaleEstimate = (terrain: Terrain) => {
//   if (!terrain.analysis) {
//     return 0;
//   }
//   return (Number(terrain.analysis.resale_estimate_min) + Number(terrain.analysis.resale_estimate_max)) / 2;
// };

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Terrains',
        href: route('terrains.index'),
    },
    {
        title: 'Comparison',
        href: route('terrains.analysis.compare'),
    },
];
</script>

<template>
    <AuthenticatedLayout title="Terrain Comparison" :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Terrain Comparison</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Compare multiple terrains and their analyses</p>
                </div>
            </div>

            <!-- Comparison table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-sidebar-border/70 dark:divide-sidebar-border">
                    <thead class="dark:bg-sidebar-bg/50 bg-gray-50">
                        <tr>
                            <th scope="col" class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6 dark:text-white">
                                Property
                            </th>
                            <th
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                scope="col"
                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white"
                            >
                                {{ terrain.title }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="dark:bg-sidebar-bg divide-y divide-sidebar-border/70 bg-white dark:divide-sidebar-border">
                        <!-- Basic Information -->
                        <tr class="bg-gray-100 dark:bg-gray-800">
                            <th colspan="100%" class="px-3 py-2 text-left text-sm font-semibold text-gray-900 dark:text-white">Basic Information</th>
                        </tr>
                        <tr>
                            <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-6 dark:text-white">City</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                            >
                                {{ terrain.city }}
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-6 dark:text-white">ZIP Code</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                            >
                                {{ terrain.zip_code }}
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-6 dark:text-white">Surface</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                            >
                                {{ formatSurface(terrain.surface_m2) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-6 dark:text-white">Price</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                            >
                                {{ formatPrice(terrain.price) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-6 dark:text-white">Viabilised</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                            >
                                <span
                                    :class="[
                                        terrain.viabilised
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100'
                                            : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100',
                                        'inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium',
                                    ]"
                                >
                                    {{ terrain.viabilised ? 'Yes' : 'No' }}
                                </span>
                            </td>
                        </tr>

                        <!-- Analysis Information -->
                        <tr class="bg-gray-100 dark:bg-gray-800">
                            <th colspan="100%" class="px-3 py-2 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                Analysis Information
                            </th>
                        </tr>
                        <tr>
                            <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-6 dark:text-white">AI Score</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                            >
                                <div v-if="terrain.analysis" class="flex items-center">
                                    <span class="font-semibold">{{ Number(terrain.analysis.ai_score).toFixed(1) }}</span>
                                    <div class="ml-2 h-2 w-24 rounded-full bg-gray-200 dark:bg-gray-700">
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
                                <span v-else>N/A</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-6 dark:text-white">Price per m²</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                            >
                                {{
                                    terrain.analysis
                                        ? formatPricePerM2(terrain.analysis.price_m2)
                                        : formatPricePerM2(terrain.price / terrain.surface_m2)
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-6 dark:text-white">
                                Market Price per m²
                            </td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                            >
                                {{ terrain.analysis ? formatPricePerM2(terrain.analysis.market_price_m2) : 'N/A' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-6 dark:text-white">
                                Price Difference
                            </td>
                            <td v-for="terrain in terrains" :key="terrain.id" class="px-3 py-4 text-sm whitespace-nowrap">
                                <span
                                    v-if="terrain.analysis"
                                    :class="
                                        terrain.analysis.price_difference_percentage <= 0
                                            ? 'text-green-600 dark:text-green-400'
                                            : 'text-red-600 dark:text-red-400'
                                    "
                                >
                                    {{ terrain.analysis.price_difference_percentage > 0 ? '+' : ''
                                    }}{{ Number(terrain.analysis.price_difference_percentage).toFixed(2) }}%
                                </span>
                                <span v-else>N/A</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-6 dark:text-white">Viability Cost</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                            >
                                {{ terrain.analysis ? formatPrice(terrain.analysis.viability_cost) : 'N/A' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-6 dark:text-white">
                                Total Investment
                            </td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                            >
                                {{ formatPrice(totalInvestmentCost(terrain)) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-6 dark:text-white">Possible Lots</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                            >
                                {{ terrain.analysis ? terrain.analysis.lots_possible : 'N/A' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-6 dark:text-white">
                                Resale Estimate (Min)
                            </td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                            >
                                {{ terrain.analysis ? formatPrice(terrain.analysis.resale_estimate_min) : 'N/A' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-6 dark:text-white">
                                Resale Estimate (Max)
                            </td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                            >
                                {{ terrain.analysis ? formatPrice(terrain.analysis.resale_estimate_max) : 'N/A' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-6 dark:text-white">
                                Net Margin Estimate
                            </td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="px-3 py-4 text-sm font-semibold whitespace-nowrap"
                                :class="
                                    terrain.analysis && terrain.analysis.net_margin_estimate > 0
                                        ? 'text-green-600 dark:text-green-400'
                                        : 'text-red-600 dark:text-red-400'
                                "
                            >
                                {{ terrain.analysis ? formatPrice(terrain.analysis.net_margin_estimate) : 'N/A' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-6 dark:text-white">Profit Margin</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="px-3 py-4 text-sm font-semibold whitespace-nowrap"
                                :class="
                                    terrain.analysis && terrain.analysis.profit_margin_percentage > 0
                                        ? 'text-green-600 dark:text-green-400'
                                        : 'text-red-600 dark:text-red-400'
                                "
                            >
                                {{ terrain.analysis ? Number(terrain.analysis.profit_margin_percentage).toFixed(2) + '%' : 'N/A' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-6 dark:text-white">Profitability</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                            >
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
                                <span v-else>N/A</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-6 dark:text-white">Overall Risk</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                            >
                                <span
                                    v-if="terrain.analysis"
                                    :class="[
                                        terrain.analysis.overall_risk === 'low'
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100'
                                            : terrain.analysis.overall_risk === 'medium'
                                              ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100'
                                              : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100',
                                        'inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium',
                                    ]"
                                >
                                    {{ terrain.analysis.overall_risk?.charAt(0).toUpperCase() + terrain.analysis.overall_risk?.slice(1) }}
                                </span>
                                <span v-else>N/A</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-6 dark:text-white">View Details</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                            >
                                <Link
                                    :href="route('terrains.analysis.show', terrain.id)"
                                    class="hover:text-primary-dark dark:text-primary-light text-primary dark:hover:text-primary"
                                >
                                    View Analysis
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

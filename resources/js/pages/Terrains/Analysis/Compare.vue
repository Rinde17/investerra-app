<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

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
    analysis: Analysis; // Ensure analysis is always present for comparison, or handle null gracefully
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

const totalInvestmentCost = (terrain: Terrain) => {
    if (!terrain.analysis) {
        return terrain.price; // Fallback if analysis is not available
    }
    return Number(terrain.price) + Number(terrain.analysis.viability_cost);
};

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
        <div class="p-4 lg:p-8">
            <Card class="max-w-full overflow-hidden border-border bg-card">
                <CardHeader>
                    <CardTitle class="text-foreground">Terrain Comparison</CardTitle>
                    <CardDescription class="text-muted-foreground">Compare multiple terrains and their analyses side-by-side.</CardDescription>
                </CardHeader>
                <CardContent class="p-0"> <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="[&_tr]:border-b bg-muted/50">
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <th scope="col" class="h-12 px-4 text-left align-middle font-medium text-foreground whitespace-nowrap sticky left-0 bg-muted/50 z-10">
                                Property
                            </th>
                            <th
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                scope="col"
                                class="h-12 px-4 text-left align-middle font-medium text-foreground whitespace-nowrap"
                            >
                                {{ terrain.title }}
                            </th>
                        </tr>
                        </thead>
                        <tbody class="[&_tr:last-child]:border-0 bg-card">
                        <tr class="bg-muted/50 border-b">
                            <th colspan="100%" class="h-12 px-4 text-left align-middle font-semibold text-foreground sticky left-0 bg-muted/50 z-10">Basic Information</th>
                        </tr>
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <td class="p-4 align-middle font-medium text-foreground whitespace-nowrap sticky left-0 bg-card z-10">City</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="p-4 align-middle text-muted-foreground whitespace-nowrap"
                            >
                                {{ terrain.city }}
                            </td>
                        </tr>
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <td class="p-4 align-middle font-medium text-foreground whitespace-nowrap sticky left-0 bg-card z-10">ZIP Code</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="p-4 align-middle text-muted-foreground whitespace-nowrap"
                            >
                                {{ terrain.zip_code }}
                            </td>
                        </tr>
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <td class="p-4 align-middle font-medium text-foreground whitespace-nowrap sticky left-0 bg-card z-10">Surface</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="p-4 align-middle text-muted-foreground whitespace-nowrap"
                            >
                                {{ formatSurface(terrain.surface_m2) }}
                            </td>
                        </tr>
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <td class="p-4 align-middle font-medium text-foreground whitespace-nowrap sticky left-0 bg-card z-10">Price</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="p-4 align-middle text-muted-foreground whitespace-nowrap"
                            >
                                {{ formatPrice(terrain.price) }}
                            </td>
                        </tr>
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <td class="p-4 align-middle font-medium text-foreground whitespace-nowrap sticky left-0 bg-card z-10">Viabilised</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="p-4 align-middle text-muted-foreground whitespace-nowrap"
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

                        <tr class="bg-muted/50 border-b">
                            <th colspan="100%" class="h-12 px-4 text-left align-middle font-semibold text-foreground sticky left-0 bg-muted/50 z-10">
                                Analysis Information
                            </th>
                        </tr>
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <td class="p-4 align-middle font-medium text-foreground whitespace-nowrap sticky left-0 bg-card z-10">AI Score</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="p-4 align-middle text-muted-foreground whitespace-nowrap"
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
                                <span v-else class="text-muted-foreground">N/A</span>
                            </td>
                        </tr>
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <td class="p-4 align-middle font-medium text-foreground whitespace-nowrap sticky left-0 bg-card z-10">Price per m²</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="p-4 align-middle text-muted-foreground whitespace-nowrap"
                            >
                                {{
                                    terrain.analysis
                                        ? formatPricePerM2(terrain.analysis.price_m2)
                                        : formatPricePerM2(terrain.price / terrain.surface_m2)
                                }}
                            </td>
                        </tr>
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <td class="p-4 align-middle font-medium text-foreground whitespace-nowrap sticky left-0 bg-card z-10">
                                Market Price per m²
                            </td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="p-4 align-middle text-muted-foreground whitespace-nowrap"
                            >
                                {{ terrain.analysis ? formatPricePerM2(terrain.analysis.market_price_m2) : 'N/A' }}
                            </td>
                        </tr>
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <td class="p-4 align-middle font-medium text-foreground whitespace-nowrap sticky left-0 bg-card z-10">
                                Price Difference
                            </td>
                            <td v-for="terrain in terrains" :key="terrain.id" class="p-4 align-middle whitespace-nowrap">
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
                                <span v-else class="text-muted-foreground">N/A</span>
                            </td>
                        </tr>
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <td class="p-4 align-middle font-medium text-foreground whitespace-nowrap sticky left-0 bg-card z-10">Viability Cost</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="p-4 align-middle text-muted-foreground whitespace-nowrap"
                            >
                                {{ terrain.analysis ? formatPrice(terrain.analysis.viability_cost) : 'N/A' }}
                            </td>
                        </tr>
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <td class="p-4 align-middle font-medium text-foreground whitespace-nowrap sticky left-0 bg-card z-10">
                                Total Investment
                            </td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="p-4 align-middle text-muted-foreground whitespace-nowrap"
                            >
                                {{ formatPrice(totalInvestmentCost(terrain)) }}
                            </td>
                        </tr>
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <td class="p-4 align-middle font-medium text-foreground whitespace-nowrap sticky left-0 bg-card z-10">Possible Lots</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="p-4 align-middle text-muted-foreground whitespace-nowrap"
                            >
                                {{ terrain.analysis ? terrain.analysis.lots_possible : 'N/A' }}
                            </td>
                        </tr>
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <td class="p-4 align-middle font-medium text-foreground whitespace-nowrap sticky left-0 bg-card z-10">
                                Resale Estimate (Min)
                            </td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="p-4 align-middle text-muted-foreground whitespace-nowrap"
                            >
                                {{ terrain.analysis ? formatPrice(terrain.analysis.resale_estimate_min) : 'N/A' }}
                            </td>
                        </tr>
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <td class="p-4 align-middle font-medium text-foreground whitespace-nowrap sticky left-0 bg-card z-10">
                                Resale Estimate (Max)
                            </td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="p-4 align-middle text-muted-foreground whitespace-nowrap"
                            >
                                {{ terrain.analysis ? formatPrice(terrain.analysis.resale_estimate_max) : 'N/A' }}
                            </td>
                        </tr>
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <td class="p-4 align-middle font-medium text-foreground whitespace-nowrap sticky left-0 bg-card z-10">
                                Net Margin Estimate
                            </td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="p-4 align-middle font-semibold whitespace-nowrap"
                                :class="
                                            terrain.analysis && terrain.analysis.net_margin_estimate > 0
                                                ? 'text-green-600 dark:text-green-400'
                                                : 'text-red-600 dark:text-red-400'
                                        "
                            >
                                {{ terrain.analysis ? formatPrice(terrain.analysis.net_margin_estimate) : 'N/A' }}
                            </td>
                        </tr>
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <td class="p-4 align-middle font-medium text-foreground whitespace-nowrap sticky left-0 bg-card z-10">Profit Margin</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="p-4 align-middle font-semibold whitespace-nowrap"
                                :class="
                                            terrain.analysis && terrain.analysis.profit_margin_percentage > 0
                                                ? 'text-green-600 dark:text-green-400'
                                                : 'text-red-600 dark:text-red-400'
                                        "
                            >
                                {{ terrain.analysis ? Number(terrain.analysis.profit_margin_percentage).toFixed(2) + '%' : 'N/A' }}
                            </td>
                        </tr>
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <td class="p-4 align-middle font-medium text-foreground whitespace-nowrap sticky left-0 bg-card z-10">Profitability</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="p-4 align-middle text-muted-foreground whitespace-nowrap"
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
                                <span v-else class="text-muted-foreground">N/A</span>
                            </td>
                        </tr>
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <td class="p-4 align-middle font-medium text-foreground whitespace-nowrap sticky left-0 bg-card z-10">Overall Risk</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="p-4 align-middle text-muted-foreground whitespace-nowrap"
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
                                <span v-else class="text-muted-foreground">N/A</span>
                            </td>
                        </tr>
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <td class="p-4 align-middle font-medium text-foreground whitespace-nowrap sticky left-0 bg-card z-10">View Details</td>
                            <td
                                v-for="terrain in terrains"
                                :key="terrain.id"
                                class="p-4 align-middle text-muted-foreground whitespace-nowrap"
                            >
                                <Link
                                    :href="route('terrains.show', terrain.id)"
                                    class="text-primary hover:text-primary/90 underline-offset-4 hover:underline"
                                >
                                    <Button type="button" variant="outline" class="border-border text-foreground hover:bg-muted"> View Analysis </Button>
                                </Link>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                </CardContent>
            </Card>
            <div v-if="!terrains || terrains.length === 0" class="mt-6 flex flex-col items-center justify-center rounded-xl border border-dashed border-border bg-card p-12 text-center">
                <svg
                    class="mx-auto h-12 w-12 text-muted-foreground"
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
                <h3 class="mt-4 text-base font-semibold text-foreground">No terrains selected for comparison</h3>
                <p class="mt-2 text-sm text-muted-foreground">
                    Please select terrains from the main listing page to compare them here.
                </p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

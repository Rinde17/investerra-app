<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import DeleteTerrain from '@/components/DeleteTerrain.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'; // Import Card components
import { Separator } from '@/components/ui/separator'; // Can be used for visual separation

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
    <AuthenticatedLayout :title="terrain.title" :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4 lg:p-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-foreground">{{ terrain.title }}</h1>
                    <p class="mt-2 text-base text-muted-foreground">{{ terrain.city }}, {{ terrain.zip_code }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Link :href="route('terrains.edit', terrain.id)">
                        <Button variant="outline" class="border-border text-foreground hover:bg-muted"> Edit Terrain </Button>
                    </Link>
                    <DeleteTerrain :terrainId="props.terrain.id" :terrainTitle="props.terrain.title" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <Card class="border-border bg-card">
                    <CardHeader class="pb-2">
                        <CardDescription class="text-muted-foreground">Price</CardDescription>
                        <CardTitle class="text-2xl text-foreground">{{ formatPrice(terrain.price) }}</CardTitle>
                    </CardHeader>
                </Card>
                <Card class="border-border bg-card">
                    <CardHeader class="pb-2">
                        <CardDescription class="text-muted-foreground">Surface</CardDescription>
                        <CardTitle class="text-2xl text-foreground">{{ formatSurface(terrain.surface_m2) }}</CardTitle>
                    </CardHeader>
                </Card>
                <Card class="border-border bg-card">
                    <CardHeader class="pb-2">
                        <CardDescription class="text-muted-foreground">Price per m²</CardDescription>
                        <CardTitle class="text-2xl text-foreground">
                            {{ analysis ? formatPricePerM2(analysis.price_m2) : formatPricePerM2(terrain.price / terrain.surface_m2) }}
                        </CardTitle>
                    </CardHeader>
                </Card>
                <Card class="border-border bg-card">
                    <CardHeader class="pb-2">
                        <CardDescription class="text-muted-foreground">Status</CardDescription>
                        <CardTitle class="text-2xl">
                            <span
                                :class="[
                                    terrain.viabilised
                                        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100'
                                        : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100',
                                    'inline-flex rounded-full px-2.5 py-0.5 text-sm font-medium',
                                ]"
                            >
                                {{ terrain.viabilised ? 'Viabilised' : 'Not Viabilised' }}
                            </span>
                        </CardTitle>
                    </CardHeader>
                </Card>
            </div>

            <div class="border-b border-border">
                <nav class="-mb-px flex space-x-8">
                    <Button
                        variant="ghost"
                        @click="activeTab = 'details'"
                        :class="[
                            activeTab === 'details'
                                ? 'border-primary text-primary shadow-none'
                                : 'border-transparent text-muted-foreground',
                            'rounded-none border-b-2 px-1 pt-4 pb-3 text-base font-medium', // Adjusted padding and font size for tabs
                        ]"
                    >
                        Details
                    </Button>
                    <Button
                        variant="ghost"
                        @click="activeTab = 'analysis'"
                        :class="[
                            activeTab === 'analysis'
                                ? 'border-primary text-primary shadow-none'
                                : 'border-transparent text-muted-foreground hover:text-foreground',
                            'rounded-none border-b-2 px-1 pt-4 pb-3 text-base font-medium',
                        ]"
                    >
                        Analysis
                    </Button>
                    <Button
                        variant="ghost"
                        @click="activeTab = 'projects'"
                        :class="[
                            activeTab === 'projects'
                                ? 'border-primary text-primary shadow-none'
                                : 'border-transparent text-muted-foreground hover:text-foreground',
                            'rounded-none border-b-2 px-1 pt-4 pb-3 text-base font-medium',
                        ]"
                    >
                        Projects
                    </Button>
                </nav>
            </div>

            <div v-if="activeTab === 'details'" class="grid gap-6 md:grid-cols-2">
                <Card class="border-border bg-card">
                    <CardHeader>
                        <CardTitle class="text-foreground">Basic Information</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-muted-foreground">Description</dt>
                                <dd class="mt-1 text-sm text-foreground">
                                    {{ terrain.description || 'No description provided' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">City</dt>
                                <dd class="mt-1 text-sm text-foreground">{{ terrain.city }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">ZIP Code</dt>
                                <dd class="mt-1 text-sm text-foreground">{{ terrain.zip_code }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Created</dt>
                                <dd class="mt-1 text-sm text-foreground">{{ formatDate(terrain.created_at) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Last Updated</dt>
                                <dd class="mt-1 text-sm text-foreground">{{ formatDate(terrain.updated_at) }}</dd>
                            </div>
                        </dl>
                    </CardContent>
                </Card>

                <Card class="border-border bg-card">
                    <CardHeader>
                        <CardTitle class="text-foreground">Additional Information</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div v-if="terrain.latitude && terrain.longitude" class="sm:col-span-2">
                                <dt class="text-sm font-medium text-muted-foreground">Location</dt>
                                <dd class="mt-1 text-sm text-foreground">{{ terrain.latitude }}, {{ terrain.longitude }}</dd>
                            </div>
                            <div v-if="terrain.source_url" class="sm:col-span-2">
                                <dt class="text-sm font-medium text-muted-foreground">Source URL</dt>
                                <dd class="mt-1 text-sm text-primary">
                                    <a :href="terrain.source_url" target="_blank" rel="noopener noreferrer" class="hover:underline">
                                        {{ terrain.source_url }}
                                    </a>
                                </dd>
                            </div>
                            <div v-if="terrain.source_platform">
                                <dt class="text-sm font-medium text-muted-foreground">Source Platform</dt>
                                <dd class="mt-1 text-sm text-foreground">{{ terrain.source_platform }}</dd>
                            </div>
                        </dl>
                    </CardContent>
                </Card>

                <Card v-if="terrain.latitude && terrain.longitude" class="md:col-span-2 border-border bg-card">
                    <CardHeader>
                        <CardTitle class="text-foreground">Map</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex h-64 items-center justify-center rounded-md bg-muted text-muted-foreground">
                            Map Placeholder
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-if="activeTab === 'analysis'" class="grid gap-6 md:grid-cols-2">
                <Card v-if="analysis" class="border-border bg-card">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <CardTitle class="text-foreground">Analysis Results</CardTitle>
                            <span
                                :class="[
                                    analysis.profitability_label === 'excellent'
                                        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100'
                                        : analysis.profitability_label === 'good'
                                          ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100'
                                          : analysis.profitability_label === 'average'
                                            ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100'
                                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100',
                                    'rounded-full px-2.5 py-0.5 text-xs font-medium',
                                ]"
                            >
                                {{ analysis.profitability_label }}
                            </span>
                        </div>
                        <CardDescription class="text-muted-foreground">Analysis performed on {{ formatDate(analysis.analyzed_at) }}</CardDescription>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <a :href="route('terrains.analysis.pdf', terrain.id)" target="_blank">
                                <Button class="bg-primary text-primary-foreground hover:bg-primary/90">
                                    <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                        />
                                    </svg>
                                    Export as PDF
                                </Button>
                            </a>
                            <a :href="route('terrains.analysis.csv', terrain.id)">
                                <Button variant="outline" class="border-border text-foreground hover:bg-muted">
                                    <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                        />
                                    </svg>
                                    Export as CSV
                                </Button>
                            </a>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">AI Score</dt>
                                <dd class="mt-1 flex items-center">
                                    <span class="text-lg font-semibold text-foreground">{{
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
                                <dt class="text-sm font-medium text-muted-foreground">Market Price/m²</dt>
                                <dd class="mt-1 text-sm text-foreground">{{ formatPricePerM2(analysis.market_price_m2) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Price Difference</dt>
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
                                <dt class="text-sm font-medium text-muted-foreground">Viability Cost</dt>
                                <dd class="mt-1 text-sm text-foreground">{{ formatPrice(analysis.viability_cost) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Possible Lots</dt>
                                <dd class="mt-1 text-sm text-foreground">{{ analysis.lots_possible }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Resale Estimate (Min)</dt>
                                <dd class="mt-1 text-sm text-foreground">{{ formatPrice(analysis.resale_estimate_min) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Resale Estimate (Max)</dt>
                                <dd class="mt-1 text-sm text-foreground">{{ formatPrice(analysis.resale_estimate_max) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Net Margin Estimate</dt>
                                <dd class="mt-1 text-sm font-semibold text-foreground">
                                    {{ formatPrice(analysis.net_margin_estimate) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Profit Margin</dt>
                                <dd class="mt-1 text-sm font-semibold text-foreground">
                                    {{ Number(analysis.profit_margin_percentage).toFixed(2) }}%
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Overall Risk</dt>
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
                                <dt class="text-sm font-medium text-muted-foreground">Recommendation</dt>
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
                                        {{
                                            analysis.overall_recommendation === 'strong_buy'
                                                ? 'Strong Buy'
                                                : analysis.overall_recommendation?.charAt(0).toUpperCase() + analysis.overall_recommendation?.slice(1)
                                        }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </CardContent>
                </Card>

                <Card v-if="analysis" class="border-border bg-card">
                    <CardHeader>
                        <CardTitle class="text-foreground">Investment Summary</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6">
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Purchase Price</dt>
                                <dd class="mt-1 text-sm text-foreground">{{ formatPrice(terrain.price) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Viability Cost</dt>
                                <dd class="mt-1 text-sm text-foreground">{{ formatPrice(analysis.viability_cost) }}</dd>
                            </div>
                            <Separator class="my-4 bg-border" />
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Total Investment</dt>
                                <dd class="mt-1 text-lg font-semibold text-foreground">{{ formatPrice(totalInvestmentCost) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Average Resale Estimate</dt>
                                <dd class="mt-1 text-sm text-foreground">{{ formatPrice(averageResaleEstimate) }}</dd>
                            </div>
                            <Separator class="my-4 bg-border" />
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Net Profit</dt>
                                <dd class="mt-1 text-lg font-semibold text-foreground">
                                    {{ formatPrice(analysis.net_margin_estimate) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Return on Investment</dt>
                                <dd class="mt-1 text-lg font-semibold text-foreground">
                                    {{ Number(analysis.profit_margin_percentage).toFixed(2) }}%
                                </dd>
                            </div>
                        </dl>
                    </CardContent>
                </Card>

                <div
                    v-if="!analysis"
                    class="col-span-full flex flex-col items-center justify-center rounded-xl border border-dashed border-border bg-card p-12 text-center"
                >
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
                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 012-2V5a2 2 0 012-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                        />
                    </svg>
                    <h3 class="mt-4 text-base font-semibold text-foreground">No analysis available</h3>
                    <p class="mt-2 text-sm text-muted-foreground">This terrain hasn't been analyzed yet.</p>
                </div>
            </div>

            <Card v-if="activeTab === 'projects'" class="border-border bg-card">
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle class="text-foreground">Projects Including This Terrain</CardTitle>
                        <Link :href="route('projects.create')">
                            <Button class="bg-primary text-primary-foreground hover:bg-primary/90"> New Project </Button>
                        </Link>
                    </div>
                </CardHeader>
                <CardContent>
                    <div v-if="projects.length > 0" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <Card v-for="project in projects" :key="project.id" class="flex flex-col transition-all hover:shadow-lg border-border bg-card">
                            <CardHeader>
                                <CardTitle class="text-foreground">{{ project.name }}</CardTitle>
                                <CardDescription v-if="project.description" class="mt-2 flex-1 text-sm text-muted-foreground">
                                    {{ project.description }}
                                </CardDescription>
                                <CardDescription v-else class="mt-2 flex-1 text-sm text-muted-foreground italic">
                                    No description provided
                                </CardDescription>
                            </CardHeader>
                            <CardContent class="mt-auto pt-0">
                                <Link :href="route('projects.show', { project: project.id })" class="block">
                                    <Button class="w-full bg-primary text-primary-foreground hover:bg-primary/90"> View Project </Button>
                                </Link>
                            </CardContent>
                        </Card>
                    </div>

                    <div
                        v-else
                        class="flex flex-col items-center justify-center rounded-xl border border-dashed border-border bg-card p-12 text-center"
                    >
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
                                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"
                            />
                        </svg>
                        <h3 class="mt-4 text-base font-semibold text-foreground">No projects yet</h3>
                        <p class="mt-2 text-sm text-muted-foreground">Add this terrain to a project to organize your investments.</p>
                        <div class="mt-6">
                            <Link :href="route('projects.create')">
                                <Button class="bg-primary text-primary-foreground hover:bg-primary/90"> Create New Project </Button>
                            </Link>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>

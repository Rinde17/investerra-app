<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { Blocks, PlusCircle } from 'lucide-vue-next';
import DeleteProject from '@/components/DeleteProject.vue';
import RemoveTerrainFromProject from '@/components/RemoveTerrainFromProject.vue';

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
    // These are now passed directly as props from the backend, so no need for computed properties in the frontend
    totalInvestment: number;
    totalProfit: number;
    averageScore: number;
}>();

const activeTab = ref('overview');

// Reactive state for editing terrain notes
const editingNotes = ref<{ [key: number]: boolean }>({});
const terrainNotes = ref<{ [key: number]: string }>({});

// Initialize terrain notes from pivot data
props.terrains.forEach((terrain) => {
    terrainNotes.value[terrain.id] = terrain.pivot.notes || '';
});

const startEditingNotes = (terrainId: number) => {
    editingNotes.value[terrainId] = true;
};

const saveNotes = (terrainId: number) => {
    router.put(
        route('projects.terrains.notes.update', {
            project: props.project.id,
            terrain: terrainId,
        }),
        {
            notes: terrainNotes.value[terrainId],
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                editingNotes.value[terrainId] = false;
                const terrain = props.terrains.find((t) => t.id === terrainId);
                if (terrain) {
                    terrain.pivot.notes = terrainNotes.value[terrainId];
                }
            },
        },
    );
};

const cancelEditingNotes = (terrainId: number) => {
    // Reset to original value
    const terrain = props.terrains.find((t) => t.id === terrainId);
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
    <AuthenticatedLayout :title="project.name" :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-8 p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-foreground">{{ project.name }}</h1>
                    <p v-if="project.team" class="mt-2 text-base text-muted-foreground">Team: {{ project.team.name }}</p>
                    <p v-else class="mt-2 text-base text-muted-foreground">Personal Project</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <Link :href="route('projects.terrains.add', project.id)">
                        <Button
                            variant="outline"
                            class="border-border text-foreground hover:text-foreground/90"
                        >
                            <PlusCircle class="mr-2 h-4 w-4" /> Add Terrain
                        </Button>
                    </Link>
                    <Link :href="route('projects.edit', project.id)">
                        <Button
                            variant="outline"
                            class="border-border text-foreground hover:text-foreground/90"
                        >
                            Edit Project
                        </Button>
                    </Link>
                    <DeleteProject
                        :projectId="props.project.id"
                        :projectName="props.project.name"
                    />
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl border border-border bg-card p-6 shadow-lg">
                    <p class="text-sm font-medium text-muted-foreground">Total Investment</p>
                    <p class="mt-2 text-2xl font-bold text-foreground">{{ formatPrice(totalInvestment) }}</p>
                </div>
                <div class="rounded-xl border border-border bg-card p-6 shadow-lg">
                    <p class="text-sm font-medium text-muted-foreground">Estimated Profit</p>
                    <p class="mt-2 text-2xl font-bold text-foreground">{{ formatPrice(totalProfit) }}</p>
                </div>
                <div class="rounded-xl border border-border bg-card p-6 shadow-lg">
                    <p class="text-sm font-medium text-muted-foreground">ROI</p>
                    <p class="mt-2 text-2xl font-bold text-foreground">
                        {{ totalInvestment > 0 ? formatPercentage((totalProfit / totalInvestment) * 100) : 'N/A' }}
                    </p>
                </div>
                <div class="rounded-xl border border-border bg-card p-6 shadow-lg">
                    <p class="text-sm font-medium text-muted-foreground">Average AI Score</p>
                    <div class="mt-2 flex items-center">
                        <span class="text-2xl font-bold text-foreground">{{ Number(averageScore).toFixed(1) }}</span>
                        <div class="ml-4 h-2.5 w-28 rounded-full bg-muted">
                            <div
                                class="h-2.5 rounded-full transition-all duration-300"
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

            <div class="border-b border-border">
                <nav class="-mb-px flex space-x-8">
                    <button
                        @click="activeTab = 'overview'"
                        :class="[
                            activeTab === 'overview'
                                ? 'border-primary text-primary'
                                : 'border-transparent text-muted-foreground hover:border-border hover:text-foreground',
                            'border-b-2 px-1 py-4 text-base font-medium whitespace-nowrap transition-colors duration-200',
                        ]"
                    >
                        Overview
                    </button>
                    <button
                        @click="activeTab = 'terrains'"
                        :class="[
                            activeTab === 'terrains'
                                ? 'border-primary text-primary'
                                : 'border-transparent text-muted-foreground hover:border-border hover:text-foreground',
                            'border-b-2 px-1 py-4 text-base font-medium whitespace-nowrap transition-colors duration-200',
                        ]"
                    >
                        Terrains ({{ terrains.length }})
                    </button>
                </nav>
            </div>

            <div v-if="activeTab === 'overview'" class="grid gap-6 md:grid-cols-2">
                <div class="overflow-hidden rounded-xl border border-border bg-card shadow-lg">
                    <div class="px-6 py-5">
                        <h3 class="text-xl font-semibold text-foreground">Project Information</h3>
                    </div>
                    <div class="border-t border-border px-6 py-5">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-muted-foreground">Description</dt>
                                <dd class="mt-1 text-base text-foreground">
                                    {{ project.description || 'No description provided' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Created By</dt>
                                <dd class="mt-1 text-base text-foreground">{{ project.user.name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Team</dt>
                                <dd class="mt-1 text-base text-foreground">
                                    {{ project.team ? project.team.name : 'Personal Project' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Created</dt>
                                <dd class="mt-1 text-base text-foreground">{{ formatDate(project.created_at) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Last Updated</dt>
                                <dd class="mt-1 text-base text-foreground">{{ formatDate(project.updated_at) }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-muted-foreground">Notes</dt>
                                <dd class="mt-1 text-base text-foreground">
                                    {{ project.notes || 'No notes provided' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="overflow-hidden rounded-xl border border-border bg-card shadow-lg">
                    <div class="px-6 py-5">
                        <h3 class="text-xl font-semibold text-foreground">Project Summary</h3>
                    </div>
                    <div class="border-t border-border px-6 py-5">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Number of Terrains</dt>
                                <dd class="mt-1 text-base text-foreground">{{ terrains.length }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Total Surface Area</dt>
                                <dd class="mt-1 text-base text-foreground">
                                    {{ formatSurface(terrains.reduce((sum, terrain) => sum + Number(terrain.surface_m2), 0)) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Total Purchase Price</dt>
                                <dd class="mt-1 text-base text-foreground">
                                    {{ formatPrice(terrains.reduce((sum, terrain) => sum + Number(terrain.price), 0)) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Total Viability Cost</dt>
                                <dd class="mt-1 text-base text-foreground">
                                    {{ formatPrice(terrains.reduce((sum, terrain) => sum + (Number(terrain.analysis?.viability_cost) || 0), 0)) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Total Resale Estimate (Min)</dt>
                                <dd class="mt-1 text-base text-foreground">
                                    {{
                                        formatPrice(terrains.reduce((sum, terrain) => sum + (Number(terrain.analysis?.resale_estimate_min) || 0), 0))
                                    }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Total Resale Estimate (Max)</dt>
                                <dd class="mt-1 text-base text-foreground">
                                    {{
                                        formatPrice(terrains.reduce((sum, terrain) => sum + (Number(terrain.analysis?.resale_estimate_max) || 0), 0))
                                    }}
                                </dd>
                            </div>
                            <div class="border-t border-border pt-4 sm:col-span-2">
                                <dt class="text-sm font-medium text-muted-foreground">Total Investment</dt>
                                <dd class="mt-1 text-base font-semibold text-foreground">{{ formatPrice(totalInvestment) }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-muted-foreground">Total Profit</dt>
                                <dd class="mt-1 text-base font-semibold text-foreground">{{ formatPrice(totalProfit) }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-muted-foreground">Return on Investment</dt>
                                <dd class="mt-1 text-base font-semibold text-foreground">
                                    {{ totalInvestment > 0 ? formatPercentage((totalProfit / totalInvestment) * 100) : 'N/A' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <div v-if="activeTab === 'terrains'">
                <div class="overflow-hidden rounded-xl border border-border bg-card shadow-lg">
                    <div class="flex items-center justify-between px-6 py-5">
                        <h3 class="text-xl font-semibold text-foreground">Project Terrains</h3>
                        <Link :href="route('projects.terrains.add', project.id)">
                            <Button
                                class="bg-primary text-primary-foreground hover:bg-primary/90 active:bg-primary/80"
                            >
                                <PlusCircle class="mr-2 h-4 w-4" /> Add Terrain
                            </Button>
                        </Link>
                    </div>

                    <div v-if="terrains.length > 0" class="border-t border-border">
                        <ul class="divide-y divide-border">
                            <li v-for="terrain in terrains" :key="terrain.id" class="p-6">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <h4 class="text-lg font-medium text-foreground">{{ terrain.title }}</h4>
                                                <p class="mt-1 text-sm text-muted-foreground">
                                                    {{ terrain.city }}, {{ terrain.zip_code }}
                                                </p>
                                            </div>
                                            <div class="flex items-center">
                                                <span
                                                    v-if="terrain.analysis"
                                                    :class="[
                                                        terrain.analysis.profitability_label === 'excellent'
                                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100'
                                                            : terrain.analysis.profitability_label === 'good'
                                                              ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100'
                                                              : terrain.analysis.profitability_label === 'average'
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
                                                <p class="text-xs font-medium text-muted-foreground">Surface</p>
                                                <p class="text-base font-medium text-foreground">
                                                    {{ formatSurface(terrain.surface_m2) }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs font-medium text-muted-foreground">Price</p>
                                                <p class="text-base font-medium text-foreground">{{ formatPrice(terrain.price) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs font-medium text-muted-foreground">Status</p>
                                                <p class="text-base font-medium text-foreground">
                                                    {{ terrain.viabilised ? 'Viabilised' : 'Not Viabilised' }}
                                                </p>
                                            </div>
                                        </div>

                                        <div v-if="terrain.analysis" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                                            <div>
                                                <p class="text-xs font-medium text-muted-foreground">Viability Cost</p>
                                                <p class="text-base font-medium text-foreground">
                                                    {{ formatPrice(terrain.analysis.viability_cost) }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs font-medium text-muted-foreground">Estimated Profit</p>
                                                <p class="text-base font-medium text-foreground">
                                                    {{ formatPrice(terrain.analysis.net_margin_estimate) }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs font-medium text-muted-foreground">AI Score</p>
                                                <div class="flex items-center">
                                                    <span class="text-base font-medium text-foreground">{{
                                                            Number(terrain.analysis.ai_score).toFixed(1)
                                                        }}</span>
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
                                                <p class="text-sm font-medium text-muted-foreground">Notes for this project</p>
                                                <div class="flex space-x-2">
                                                    <Button
                                                        v-if="!editingNotes[terrain.id]"
                                                        @click="startEditingNotes(terrain.id)"
                                                        variant="outline"
                                                        size="sm"
                                                        class="text-foreground hover:text-foreground/90"
                                                    >
                                                        Edit
                                                    </Button>
                                                    <div v-else class="flex space-x-2">
                                                        <Button
                                                            @click="saveNotes(terrain.id)"
                                                            variant="outline"
                                                            size="sm"
                                                            class="text-foreground hover:text-foreground/90"
                                                        >
                                                            Save
                                                        </Button>
                                                        <Button
                                                            @click="cancelEditingNotes(terrain.id)"
                                                            variant="outline"
                                                            size="sm"
                                                            class="text-destructive hover:text-destructive/90"
                                                        >
                                                            Cancel
                                                        </Button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-if="!editingNotes[terrain.id]" class="mt-1">
                                                <p v-if="terrain.pivot.notes" class="text-base text-foreground">
                                                    {{ terrain.pivot.notes }}
                                                </p>
                                                <p v-else class="text-base text-muted-foreground italic">
                                                    No notes for this terrain in this project
                                                </p>
                                            </div>
                                            <div v-else class="mt-1">
                                                <Textarea
                                                    v-model="terrainNotes[terrain.id]"
                                                    rows="3"
                                                    class="border-border bg-input text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-primary"
                                                    placeholder="Add notes about this terrain in the project"
                                                ></Textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-row justify-end gap-2 sm:flex-col">
                                        <Link :href="route('terrains.show', terrain.id)">
                                            <Button
                                                variant="outline"
                                                class="border-border text-foreground hover:text-foreground/90"
                                            >
                                                View Terrain
                                            </Button>
                                        </Link>
                                        <RemoveTerrainFromProject
                                            :projectId="props.project.id"
                                            :projectName="props.project.name"
                                            :terrainId="terrain.id"
                                            :terrainTitle="terrain.title"
                                        />
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div v-else class="flex flex-col items-center justify-center border-t border-border p-16 text-center">
                        <Blocks class="mx-auto h-16 w-16 text-muted-foreground" />
                        <h3 class="mt-4 text-lg font-semibold text-foreground">No terrains in this project</h3>
                        <p class="mt-2 text-base text-muted-foreground">Get started by adding a terrain to this project.</p>
                        <div class="mt-8">
                            <Link :href="route('projects.terrains.add', project.id)">
                                <Button
                                    class="bg-primary text-primary-foreground hover:bg-primary/90 active:bg-primary/80"
                                >
                                    <PlusCircle class="mr-2 h-4 w-4" /> Add Terrain
                                </Button>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

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
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ project.name }}</h1>
                    <p v-if="project.team" class="mt-2 text-base text-gray-600 dark:text-gray-400">Team: {{ project.team.name }}</p>
                    <p v-else class="mt-2 text-base text-gray-600 dark:text-gray-400">Personal Project</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <Link :href="route('projects.terrains.add', project.id)">
                        <Button
                            variant="outline"
                            class="border-gray-300 text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                        >
                            <PlusCircle class="mr-2 h-4 w-4" /> Add Terrain
                        </Button>
                    </Link>
                    <Link :href="route('projects.edit', project.id)">
                        <Button
                            variant="outline"
                            class="border-gray-300 text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
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
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Investment</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ formatPrice(totalInvestment) }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Estimated Profit</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ formatPrice(totalProfit) }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">ROI</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                        {{ totalInvestment > 0 ? formatPercentage((totalProfit / totalInvestment) * 100) : 'N/A' }}
                    </p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Average AI Score</p>
                    <div class="mt-2 flex items-center">
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ Number(averageScore).toFixed(1) }}</span>
                        <div class="ml-4 h-2.5 w-28 rounded-full bg-gray-200 dark:bg-gray-700">
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

            <div class="border-b border-gray-200 dark:border-gray-800">
                <nav class="-mb-px flex space-x-8">
                    <button
                        @click="activeTab = 'overview'"
                        :class="[
                            activeTab === 'overview'
                                ? 'border-indigo-500 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400'
                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-700 dark:hover:text-gray-300',
                            'border-b-2 px-1 py-4 text-base font-medium whitespace-nowrap transition-colors duration-200', // Adjusted font size
                        ]"
                    >
                        Overview
                    </button>
                    <button
                        @click="activeTab = 'terrains'"
                        :class="[
                            activeTab === 'terrains'
                                ? 'border-indigo-500 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400'
                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-700 dark:hover:text-gray-300',
                            'border-b-2 px-1 py-4 text-base font-medium whitespace-nowrap transition-colors duration-200',
                        ]"
                    >
                        Terrains ({{ terrains.length }})
                    </button>
                </nav>
            </div>

            <div v-if="activeTab === 'overview'" class="grid gap-6 md:grid-cols-2">
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
                    <div class="px-6 py-5">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Project Information</h3>
                    </div>
                    <div class="border-t border-gray-200 px-6 py-5 dark:border-gray-800">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Description</dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-white">
                                    {{ project.description || 'No description provided' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Created By</dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-white">{{ project.user.name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Team</dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-white">
                                    {{ project.team ? project.team.name : 'Personal Project' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Created</dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-white">{{ formatDate(project.created_at) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Last Updated</dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-white">{{ formatDate(project.updated_at) }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Notes</dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-white">
                                    {{ project.notes || 'No notes provided' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
                    <div class="px-6 py-5">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Project Summary</h3>
                    </div>
                    <div class="border-t border-gray-200 px-6 py-5 dark:border-gray-800">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Number of Terrains</dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-white">{{ terrains.length }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Surface Area</dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-white">
                                    {{ formatSurface(terrains.reduce((sum, terrain) => sum + Number(terrain.surface_m2), 0)) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Purchase Price</dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-white">
                                    {{ formatPrice(terrains.reduce((sum, terrain) => sum + Number(terrain.price), 0)) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Viability Cost</dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-white">
                                    {{ formatPrice(terrains.reduce((sum, terrain) => sum + (Number(terrain.analysis?.viability_cost) || 0), 0)) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Resale Estimate (Min)</dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-white">
                                    {{
                                        formatPrice(terrains.reduce((sum, terrain) => sum + (Number(terrain.analysis?.resale_estimate_min) || 0), 0))
                                    }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Resale Estimate (Max)</dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-white">
                                    {{
                                        formatPrice(terrains.reduce((sum, terrain) => sum + (Number(terrain.analysis?.resale_estimate_max) || 0), 0))
                                    }}
                                </dd>
                            </div>
                            <div class="border-t border-gray-200 pt-4 sm:col-span-2 dark:border-gray-800">
                                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Investment</dt>
                                <dd class="mt-1 text-base font-semibold text-gray-900 dark:text-white">{{ formatPrice(totalInvestment) }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Profit</dt>
                                <dd class="mt-1 text-base font-semibold text-gray-900 dark:text-white">{{ formatPrice(totalProfit) }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Return on Investment</dt>
                                <dd class="mt-1 text-base font-semibold text-gray-900 dark:text-white">
                                    {{ totalInvestment > 0 ? formatPercentage((totalProfit / totalInvestment) * 100) : 'N/A' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <div v-if="activeTab === 'terrains'">
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center justify-between px-6 py-5">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Project Terrains</h3>
                        <Link :href="route('projects.terrains.add', project.id)">
                            <Button
                                class="bg-indigo-600 text-white hover:bg-indigo-700 active:bg-indigo-800 dark:bg-indigo-700 dark:hover:bg-indigo-600 dark:active:bg-indigo-800"
                            >
                                <PlusCircle class="mr-2 h-4 w-4" /> Add Terrain
                            </Button>
                        </Link>
                    </div>

                    <div v-if="terrains.length > 0" class="border-t border-gray-200 dark:border-gray-800">
                        <ul class="divide-y divide-gray-200 dark:divide-gray-800">
                            <li v-for="terrain in terrains" :key="terrain.id" class="p-6">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ terrain.title }}</h4>
                                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ terrain.city }}, {{ terrain.zip_code }}
                                                </p>
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
                                                <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Surface</p>
                                                <p class="text-base font-medium text-gray-900 dark:text-white">
                                                    {{ formatSurface(terrain.surface_m2) }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Price</p>
                                                <p class="text-base font-medium text-gray-900 dark:text-white">{{ formatPrice(terrain.price) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Status</p>
                                                <p class="text-base font-medium text-gray-900 dark:text-white">
                                                    {{ terrain.viabilised ? 'Viabilised' : 'Not Viabilised' }}
                                                </p>
                                            </div>
                                        </div>

                                        <div v-if="terrain.analysis" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                                            <div>
                                                <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Viability Cost</p>
                                                <p class="text-base font-medium text-gray-900 dark:text-white">
                                                    {{ formatPrice(terrain.analysis.viability_cost) }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Estimated Profit</p>
                                                <p class="text-base font-medium text-gray-900 dark:text-white">
                                                    {{ formatPrice(terrain.analysis.net_margin_estimate) }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs font-medium text-gray-600 dark:text-gray-400">AI Score</p>
                                                <div class="flex items-center">
                                                    <span class="text-base font-medium text-gray-900 dark:text-white">{{
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
                                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Notes for this project</p>
                                                <div class="flex space-x-2">
                                                    <Button
                                                        v-if="!editingNotes[terrain.id]"
                                                        @click="startEditingNotes(terrain.id)"
                                                        variant="ghost"
                                                        size="sm"
                                                        class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
                                                    >
                                                        Edit
                                                    </Button>
                                                    <div v-else class="flex space-x-2">
                                                        <Button
                                                            @click="saveNotes(terrain.id)"
                                                            variant="ghost"
                                                            size="sm"
                                                            class="text-green-600 hover:text-green-700 dark:text-green-500 dark:hover:text-green-400"
                                                        >
                                                            Save
                                                        </Button>
                                                        <Button
                                                            @click="cancelEditingNotes(terrain.id)"
                                                            variant="ghost"
                                                            size="sm"
                                                            class="text-red-600 hover:text-red-700 dark:text-red-500 dark:hover:text-red-400"
                                                        >
                                                            Cancel
                                                        </Button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-if="!editingNotes[terrain.id]" class="mt-1">
                                                <p v-if="terrain.pivot.notes" class="text-base text-gray-900 dark:text-white">
                                                    {{ terrain.pivot.notes }}
                                                </p>
                                                <p v-else class="text-base text-gray-500 italic dark:text-gray-600">
                                                    No notes for this terrain in this project
                                                </p>
                                            </div>
                                            <div v-else class="mt-1">
                                                <Textarea
                                                    v-model="terrainNotes[terrain.id]"
                                                    rows="3"
                                                    class="border-gray-300 bg-gray-50 text-gray-800 placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500"
                                                    placeholder="Add notes about this terrain in the project"
                                                ></Textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-row justify-end gap-2 sm:flex-col">
                                        <Link :href="route('terrains.show', terrain.id)">
                                            <Button
                                                variant="outline"
                                                class="border-gray-300 text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
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

                    <div v-else class="flex flex-col items-center justify-center border-t border-gray-200 p-16 text-center dark:border-gray-800">
                        <Blocks class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" />
                        <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">No terrains in this project</h3>
                        <p class="mt-2 text-base text-gray-600 dark:text-gray-400">Get started by adding a terrain to this project.</p>
                        <div class="mt-8">
                            <Link :href="route('projects.terrains.add', project.id)">
                                <Button
                                    class="bg-indigo-600 text-white hover:bg-indigo-700 active:bg-indigo-800 dark:bg-indigo-700 dark:hover:bg-indigo-600 dark:active:bg-indigo-800"
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

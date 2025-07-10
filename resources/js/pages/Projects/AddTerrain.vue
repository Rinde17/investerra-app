<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

// Define props for the component
const props = defineProps<{
    project: {
        id: number;
        name: string;
        description: string | null;
        user_id: number;
        team_id: number | null;
    };
    terrains: Array<{
        id: number;
        title: string;
        city: string;
        zip_code: string;
        surface_m2: number;
        price: number;
        analysis: {
            ai_score: number;
            profitability_label: string;
        } | null;
    }>;
}>();

const form = useForm({
    terrain_id: null as number | null,
    notes: '',
});

const searchQuery = ref('');
const selectedTerrain = ref<number | null>(null);

// Filter terrains based on a search query
const filteredTerrains = computed(() => {
    if (!searchQuery.value) {
        return props.terrains;
    }

    const query = searchQuery.value.toLowerCase();
    return props.terrains.filter(
        (terrain) => terrain.title.toLowerCase().includes(query) || terrain.city.toLowerCase().includes(query) || terrain.zip_code.includes(query),
    );
});

// Select a terrain
const selectTerrain = (terrainId: number) => {
    selectedTerrain.value = terrainId;
    form.terrain_id = terrainId;
};

// Format price as currency
const formatPrice = (price: number) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price);
};

// Format surface with units
const formatSurface = (surface: number) => {
    return new Intl.NumberFormat('fr-FR').format(surface) + ' m²';
};

const submit = () => {
    form.post(route('projects.terrains.store', { project: props.project.id }), {
        onSuccess: () => {
            form.reset();
            selectedTerrain.value = null;
        },
    });
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
    {
        title: 'Add Terrain',
        href: route('projects.terrains.add', { project: props.project.id }),
    },
];
</script>

<template>
    <AuthenticatedLayout title="Add Terrain to Project" :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-7xl p-4">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add Terrain to Project</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add a terrain to your project "{{ project.name }}".</p>
            </div>

            <form @submit.prevent="submit" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Terrain Selection -->
                <div class="lg:col-span-2">
                    <div
                        class="dark:bg-sidebar-bg overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border"
                    >
                        <div class="px-6 py-5">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Select a Terrain</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose a terrain to add to this project.</p>
                        </div>
                        <div class="border-t border-sidebar-border/70 px-6 py-5 dark:border-sidebar-border">
                            <div class="mb-4">
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path
                                                fill-rule="evenodd"
                                                d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </div>
                                    <input
                                        v-model="searchQuery"
                                        type="text"
                                        class="dark:bg-sidebar-bg/50 block w-full rounded-md border border-sidebar-border/70 bg-white py-1.5 pr-3 pl-10 text-gray-900 placeholder:text-gray-400 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none sm:text-sm sm:leading-6 dark:border-sidebar-border dark:text-white"
                                        placeholder="Search terrains..."
                                    />
                                </div>
                            </div>

                            <div v-if="form.errors.terrain_id" class="mb-4 rounded-md bg-red-50 p-4 dark:bg-red-900/20">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg
                                            class="h-5 w-5 text-red-400 dark:text-red-500"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                            aria-hidden="true"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">{{ form.errors.terrain_id }}</h3>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ selectedTerrain ? 'Selected 1 terrain' : 'No terrain selected' }}
                                </p>
                            </div>

                            <div class="max-h-96 overflow-y-auto">
                                <ul class="divide-y divide-sidebar-border/70 dark:divide-sidebar-border">
                                    <li v-for="terrain in filteredTerrains" :key="terrain.id" class="py-4">
                                        <div class="flex items-start">
                                            <div class="flex h-5 items-center">
                                                <input
                                                    :id="`terrain-${terrain.id}`"
                                                    :value="terrain.id"
                                                    type="radio"
                                                    name="terrain_selection"
                                                    :checked="selectedTerrain === terrain.id"
                                                    @change="selectTerrain(terrain.id)"
                                                    class="h-4 w-4 border-sidebar-border/70 text-primary focus:ring-primary dark:border-sidebar-border"
                                                />
                                            </div>
                                            <div class="ml-3 flex-1">
                                                <label
                                                    :for="`terrain-${terrain.id}`"
                                                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between"
                                                >
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ terrain.title }}</p>
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ terrain.city }}, {{ terrain.zip_code }}
                                                        </p>
                                                    </div>
                                                    <div class="mt-2 flex flex-col sm:mt-0 sm:items-end">
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ formatPrice(terrain.price) }}
                                                        </p>
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ formatSurface(terrain.surface_m2) }}
                                                        </p>
                                                    </div>
                                                </label>
                                                <div class="mt-2 flex items-center">
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
                                                            'inline-flex rounded-full px-2 py-0.5 text-xs font-medium',
                                                        ]"
                                                    >
                                                        {{ terrain.analysis.profitability_label }}
                                                    </span>
                                                    <div v-if="terrain.analysis" class="ml-2 flex items-center">
                                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">AI Score:</span>
                                                        <span class="ml-1 text-xs font-medium text-gray-900 dark:text-white">{{
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
                                        </div>
                                    </li>
                                </ul>

                                <!-- Empty state when no terrains match search -->
                                <div
                                    v-if="filteredTerrains.length === 0"
                                    class="dark:bg-sidebar-bg/30 flex flex-col items-center justify-center rounded-lg border border-dashed border-sidebar-border/70 bg-gray-50 p-8 text-center dark:border-sidebar-border"
                                >
                                    <svg
                                        class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-500"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.5"
                                            d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No terrains found</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No terrains match your search criteria.</p>
                                </div>

                                <!-- Empty state when no terrains at all -->
                                <div
                                    v-if="props.terrains.length === 0"
                                    class="dark:bg-sidebar-bg/30 flex flex-col items-center justify-center rounded-lg border border-dashed border-sidebar-border/70 bg-gray-50 p-8 text-center dark:border-sidebar-border"
                                >
                                    <svg
                                        class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-500"
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
                                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No terrains available</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        You need to add terrains before you can include them in a project.
                                    </p>
                                    <div class="mt-6">
                                        <Link
                                            :href="route('terrains.create')"
                                            class="hover:bg-primary-dark rounded-md bg-primary px-4 py-2 text-sm font-medium text-white focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none"
                                        >
                                            Add New Terrain
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="lg:col-span-1">
                    <div
                        class="dark:bg-sidebar-bg overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border"
                    >
                        <div class="px-6 py-5">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Terrain Notes</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add notes about this terrain in the context of this project.</p>
                        </div>
                        <div class="border-t border-sidebar-border/70 px-6 py-5 dark:border-sidebar-border">
                            <div class="mb-6">
                                <label for="notes" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> Notes (Optional) </label>
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="6"
                                    class="dark:bg-sidebar-bg/50 block w-full rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none dark:border-sidebar-border dark:text-white"
                                    placeholder="Add any notes about this terrain in the context of this project"
                                    :disabled="!selectedTerrain"
                                ></textarea>
                                <div v-if="form.errors.notes" class="mt-1 text-sm text-red-500">
                                    {{ form.errors.notes }}
                                </div>
                                <p v-if="!selectedTerrain" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    Select a terrain first to add notes.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="lg:col-span-3">
                    <div class="flex items-center justify-end gap-3">
                        <Link
                            :href="route('projects.show', { project: props.project.id })"
                            class="dark:bg-sidebar-bg dark:hover:bg-sidebar-bg/80 rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none dark:border-sidebar-border dark:text-gray-300"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            class="cursor-pointer hover:bg-primary-dark rounded-md bg-primary px-4 py-2 text-sm font-medium text-white focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none"
                            :disabled="form.processing || !form.terrain_id"
                        >
                            <span v-if="form.processing">Adding...</span>
                            <span v-else>Add to Project</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

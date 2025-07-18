<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import { Blocks, Frown, Info, PlusCircle, Search } from 'lucide-vue-next';

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

// Select a terrain - ensures form.terrain_id is updated
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
        onError: () => {},
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
        <div class="mx-auto max-w-7xl p-6 lg:p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-foreground">Add Terrain to Project</h1>
                <p class="mt-2 text-base text-muted-foreground">Add an existing terrain to your project "{{ project.name }}".</p>
            </div>

            <form @submit.prevent="submit" class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <div class="overflow-hidden rounded-xl border border-border bg-card shadow-lg">
                        <div class="px-6 py-5">
                            <h2 class="text-xl font-semibold text-foreground">Select a Terrain</h2>
                            <p class="mt-1 text-sm text-muted-foreground">
                                Choose a terrain from your existing list to add to this project.
                            </p>
                        </div>
                        <div class="border-t border-border px-6 py-5">
                            <div class="mb-4">
                                <div class="relative">
                                    <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                    <Input
                                        v-model="searchQuery"
                                        type="text"
                                        placeholder="Search terrains by title, city, or zip code..."
                                        class="rounded-md border border-border bg-input py-2 pr-3 pl-10 text-foreground focus:border-primary focus:ring-primary"
                                    />
                                </div>
                            </div>

                            <div v-if="form.errors.terrain_id" class="mb-4 rounded-md bg-destructive/10 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <Info class="h-5 w-5 text-destructive" />
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-destructive-foreground">Error: {{ form.errors.terrain_id }}</h3>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4 text-sm font-medium text-foreground">
                                {{ selectedTerrain ? '1 terrain selected' : 'No terrain selected' }}
                            </div>

                            <div class="max-h-96 overflow-y-auto rounded-md border border-border">
                                <ul class="divide-y divide-border">
                                    <li
                                        v-for="terrain in filteredTerrains"
                                        :key="terrain.id"
                                        class="px-4 py-3 hover:bg-muted"
                                    >
                                        <label :for="`terrain-${terrain.id}`" class="flex cursor-pointer items-start">
                                            <input
                                                type="radio"
                                                :id="`terrain-${terrain.id}`"
                                                :value="terrain.id"
                                                name="terrain_selection"
                                                :checked="selectedTerrain === terrain.id"
                                                @change="selectTerrain(terrain.id)"
                                                class="mt-1 mr-3 h-4 w-4 border-border text-primary focus:ring-primary dark:checked:bg-primary dark:focus:ring-primary"
                                            />
                                            <div class="flex-1">
                                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                                    <div>
                                                        <p class="text-base font-medium text-foreground">{{ terrain.title }}</p>
                                                        <p class="text-sm text-muted-foreground">
                                                            {{ terrain.city }}, {{ terrain.zip_code }}
                                                        </p>
                                                    </div>
                                                    <div class="mt-2 flex flex-col sm:mt-0 sm:items-end">
                                                        <p class="text-base font-medium text-foreground">
                                                            {{ formatPrice(terrain.price) }}
                                                        </p>
                                                        <p class="text-sm text-muted-foreground">
                                                            {{ formatSurface(terrain.surface_m2) }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="mt-2 flex items-center">
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
                                                            'inline-flex rounded-full px-2 py-0.5 text-xs font-medium',
                                                        ]"
                                                    >
                                                        {{ terrain.analysis.profitability_label }}
                                                    </span>
                                                    <div v-if="terrain.analysis" class="ml-3 flex items-center">
                                                        <span class="text-xs font-medium text-muted-foreground">AI Score:</span>
                                                        <span class="ml-1 text-xs font-medium text-foreground">{{
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
                                        </label>
                                    </li>
                                </ul>

                                <div
                                    v-if="filteredTerrains.length === 0 && props.terrains.length > 0"
                                    class="flex flex-col items-center justify-center rounded-b-md border-t border-dashed border-border bg-card p-8 text-center"
                                >
                                    <Frown class="mx-auto h-12 w-12 text-muted-foreground" />
                                    <h3 class="mt-2 text-base font-semibold text-foreground">No terrains found</h3>
                                    <p class="mt-1 text-sm text-muted-foreground">No terrains match your search criteria.</p>
                                </div>

                                <div
                                    v-if="props.terrains.length === 0"
                                    class="flex flex-col items-center justify-center rounded-b-md border-t border-dashed border-border bg-card p-8 text-center"
                                >
                                    <Blocks class="mx-auto h-12 w-12 text-muted-foreground" />
                                    <h3 class="mt-2 text-base font-semibold text-foreground">No terrains available</h3>
                                    <p class="mt-1 text-sm text-muted-foreground">
                                        You need to add terrains before you can include them in a project.
                                    </p>
                                    <div class="mt-6">
                                        <Link :href="route('terrains.create')">
                                            <Button class="bg-primary text-primary-foreground hover:bg-primary/90">
                                                <PlusCircle class="mr-2 h-4 w-4" /> Add New Terrain
                                            </Button>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="overflow-hidden rounded-xl border border-border bg-card shadow-lg">
                        <div class="px-6 py-5">
                            <h2 class="text-xl font-semibold text-foreground">Terrain Notes</h2>
                            <p class="mt-1 text-sm text-muted-foreground">Add notes about this terrain in the context of this project.</p>
                        </div>
                        <div class="border-t border-border px-6 py-5">
                            <div class="mb-6">
                                <Label for="notes" class="mb-2 block text-sm font-medium text-foreground"> Notes (Optional) </Label>
                                <Textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="6"
                                    placeholder="Add any notes about this terrain in the context of this project"
                                    :disabled="!selectedTerrain"
                                    class="border-border bg-input text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-primary"
                                ></Textarea>
                                <div v-if="form.errors.notes" class="mt-2 text-sm text-destructive">
                                    {{ form.errors.notes }}
                                </div>
                                <p v-if="!selectedTerrain" class="mt-2 text-sm text-muted-foreground">
                                    Select a terrain first to add notes.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-3">
                    <div class="flex items-center justify-end gap-3 pt-4">
                        <Link :href="route('projects.show', { project: props.project.id })">
                            <Button
                                variant="outline"
                                class="border-border text-foreground hover:bg-muted"
                            >
                                Cancel
                            </Button>
                        </Link>
                        <Button
                            type="submit"
                            :disabled="form.processing || !form.terrain_id"
                            class="bg-primary text-primary-foreground hover:bg-primary/90 active:bg-primary/80"
                        >
                            <span v-if="form.processing">Adding...</span>
                            <span v-else>Add to Project</span>
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

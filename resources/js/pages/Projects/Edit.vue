<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Link, useForm } from '@inertiajs/vue3';
import { Blocks, Frown, Info, PlusCircle, Search } from 'lucide-vue-next';
import { computed, ref } from 'vue';

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
    };
    teams: Array<{
        id: number;
        name: string;
    }>;
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
    projectTerrainIds: number[];
}>();

const form = useForm({
    name: props.project.name,
    description: props.project.description || '',
    team_id: props.project.team_id,
    terrains: [...props.projectTerrainIds],
    notes: props.project.notes || '',
});

const searchQuery = ref('');

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

// Toggle terrain selection
const toggleTerrain = (terrainId: number) => {
    const index = form.terrains.indexOf(terrainId);
    if (index === -1) {
        form.terrains.push(terrainId);
    } else {
        form.terrains.splice(index, 1);
    }
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
    form.put(route('projects.update', { project: props.project.id }), {
        preserveScroll: true,
        onSuccess: () => {},
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
        title: 'Edit Project',
        href: route('projects.edit', { project: props.project.id }),
    },
];
</script>

<template>
    <AuthenticatedLayout title="Edit Project" :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-7xl p-6 lg:p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Project</h1>
                <p class="mt-2 text-base text-gray-600 dark:text-gray-400">Update the details of your project.</p>
            </div>

            <form @submit.prevent="submit" class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="lg:col-span-1">
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
                        <div class="px-6 py-5">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Project Information</h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Basic information about your project.</p>
                        </div>
                        <div class="border-t border-gray-200 px-6 py-5 dark:border-gray-800">
                            <div class="mb-6">
                                <Label for="name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                                    Project Name <span class="text-red-500">*</span>
                                </Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    placeholder="Enter project name"
                                    class="border-gray-300 bg-gray-50 text-gray-800 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                />
                                <div v-if="form.errors.name" class="mt-2 text-sm text-red-500">
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <div class="mb-6">
                                <Label for="description" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> Description </Label>
                                <Textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="4"
                                    placeholder="Describe the purpose of this project"
                                    class="border-gray-300 bg-gray-50 text-gray-800 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                ></Textarea>
                                <div v-if="form.errors.description" class="mt-2 text-sm text-red-500">
                                    {{ form.errors.description }}
                                </div>
                            </div>

                            <div class="mb-6">
                                <Label for="team_id" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> Team (Optional) </Label>
                                <select
                                    id="team_id"
                                    v-model="form.team_id"
                                    class="block w-full rounded-md border border-gray-300 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                >
                                    <option :value="null">Personal Project (No Team)</option>
                                    <option v-for="team in teams" :key="team.id" :value="team.id">{{ team.name }}</option>
                                </select>
                                <div v-if="form.errors.team_id" class="mt-2 text-sm text-red-500">
                                    {{ form.errors.team_id }}
                                </div>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Assigning a project to a team allows team members to collaborate on it.
                                </p>
                            </div>

                            <div class="mb-6">
                                <Label for="notes" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> Notes </Label>
                                <Textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="4"
                                    placeholder="Add any additional notes about this project"
                                    class="border-gray-300 bg-gray-50 text-gray-800 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                ></Textarea>
                                <div v-if="form.errors.notes" class="mt-2 text-sm text-red-500">
                                    {{ form.errors.notes }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
                        <div class="px-6 py-5">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Manage Terrains</h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Select terrains to include in this project.</p>
                        </div>
                        <div class="border-t border-gray-200 px-6 py-5 dark:border-gray-800">
                            <div class="mb-4">
                                <div class="relative">
                                    <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                                    <Input
                                        v-model="searchQuery"
                                        type="text"
                                        placeholder="Search terrains..."
                                        class="rounded-md border border-gray-300 bg-gray-50 py-2 pr-3 pl-10 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                    />
                                </div>
                            </div>

                            <div v-if="form.errors.terrains" class="mb-4 rounded-md bg-red-50 p-4 dark:bg-red-900/20">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <Info class="h-5 w-5 text-red-400 dark:text-red-500" />
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Error: {{ form.errors.terrains }}</h3>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Selected: {{ form.terrains.length }} terrain(s)
                            </div>

                            <div class="max-h-96 overflow-y-auto rounded-md border border-gray-200 dark:border-gray-700">
                                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <li
                                        v-for="terrain in filteredTerrains"
                                        :key="terrain.id"
                                        class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800"
                                    >
                                        <label :for="`terrain-${terrain.id}`" class="flex cursor-pointer items-start">
                                            <input
                                                :id="`terrain-${terrain.id}`"
                                                :value="terrain.id"
                                                type="checkbox"
                                                :checked="form.terrains.includes(terrain.id)"
                                                @change="toggleTerrain(terrain.id)"
                                                class="mt-1 mr-3 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-700 dark:text-indigo-500 dark:focus:ring-indigo-500"
                                            />
                                            <div class="flex-1">
                                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                                    <div>
                                                        <p class="text-base font-medium text-gray-900 dark:text-white">{{ terrain.title }}</p>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                                            {{ terrain.city }}, {{ terrain.zip_code }}
                                                        </p>
                                                    </div>
                                                    <div class="mt-2 flex flex-col sm:mt-0 sm:items-end">
                                                        <p class="text-base font-medium text-gray-900 dark:text-white">
                                                            {{ formatPrice(terrain.price) }}
                                                        </p>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                                            {{ formatSurface(terrain.surface_m2) }}
                                                        </p>
                                                    </div>
                                                </div>
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
                                                    <div v-if="terrain.analysis" class="ml-3 flex items-center">
                                                        <span class="text-xs font-medium text-gray-600 dark:text-gray-400">AI Score:</span>
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
                                        </label>
                                    </li>
                                </ul>

                                <div
                                    v-if="filteredTerrains.length === 0 && props.terrains.length > 0"
                                    class="flex flex-col items-center justify-center rounded-b-md border-t border-dashed border-gray-300 bg-gray-50 p-8 text-center dark:border-gray-700 dark:bg-gray-800"
                                >
                                    <Frown class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" />
                                    <h3 class="mt-2 text-base font-semibold text-gray-900 dark:text-white">No terrains found</h3>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">No terrains match your search criteria.</p>
                                </div>

                                <div
                                    v-if="props.terrains.length === 0"
                                    class="flex flex-col items-center justify-center rounded-b-md border-t border-dashed border-gray-300 bg-gray-50 p-8 text-center dark:border-gray-700 dark:bg-gray-800"
                                >
                                    <Blocks class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" />
                                    <h3 class="mt-2 text-base font-semibold text-gray-900 dark:text-white">No terrains available</h3>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        You need to add terrains before you can include them in a project.
                                    </p>
                                    <div class="mt-6">
                                        <Link :href="route('terrains.create')">
                                            <Button class="bg-indigo-600 text-white hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600">
                                                <PlusCircle class="mr-2 h-4 w-4" /> Add New Terrain
                                            </Button>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-3">
                    <div class="flex items-center justify-end gap-3 pt-4">
                        <Link :href="route('projects.show', { project: props.project.id })">
                            <Button
                                variant="outline"
                                class="border-gray-300 text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                            >
                                Cancel
                            </Button>
                        </Link>
                        <Button
                            type="submit"
                            :disabled="form.processing || !form.isDirty"
                            class="bg-indigo-600 text-white hover:bg-indigo-700 active:bg-indigo-800 dark:bg-indigo-700 dark:hover:bg-indigo-600 dark:active:bg-indigo-800"
                        >
                            <span v-if="form.processing">Saving...</span>
                            <span v-else>Save Changes</span>
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Link, useForm } from '@inertiajs/vue3';
import { FilePlus, Search, XCircle } from 'lucide-vue-next';
import { computed, ref } from 'vue';

// Define props for the component
const props = defineProps<{
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
}>();

const form = useForm({
    name: '',
    description: '',
    team_id: null as number | null,
    terrains: [] as number[],
    notes: '',
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
    form.post(route('projects.store'), {
        onSuccess: () => {
            form.reset();
        },
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Projects',
        href: route('projects.index'),
    },
    {
        title: 'Create Project',
        href: route('projects.create'),
    },
];
</script>

<template>
    <AuthenticatedLayout title="Create Project" :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-7xl p-6">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create New Project</h1>
                <p class="mt-2 text-base text-gray-600 dark:text-gray-400">Create a new project to organize and analyze your terrains.</p>
            </div>

            <form @submit.prevent="submit" class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="lg:col-span-1">
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
                        <div class="px-6 py-5">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Project Information</h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Basic information about your project.</p>
                        </div>
                        <div class="border-t border-gray-200 px-6 py-5 dark:border-gray-800">
                            <div class="mb-6 grid gap-4">
                                <Label for="name" class="text-gray-700 dark:text-gray-200"> Project Name <span class="text-red-500">*</span> </Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="border-gray-300 bg-gray-50 text-gray-800 placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500"
                                    required
                                    placeholder="Enter project name"
                                />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="mb-6 grid gap-4">
                                <Label for="description" class="text-gray-700 dark:text-gray-200"> Description </Label>
                                <Textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="4"
                                    class="border-gray-300 bg-gray-50 text-gray-800 placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500"
                                    placeholder="Describe the purpose of this project"
                                ></Textarea>
                                <InputError :message="form.errors.description" />
                            </div>

                            <div class="mb-6 grid gap-4">
                                <Label for="team_id" class="text-gray-700 dark:text-gray-200"> Team (Optional) </Label>
                                <select
                                    id="team_id"
                                    v-model="form.team_id"
                                    class="block w-full rounded-md border border-gray-300 bg-gray-50 px-4 py-2 text-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                >
                                    <option :value="null">Personal Project (No Team)</option>
                                    <option v-for="team in teams" :key="team.id" :value="team.id">{{ team.name }}</option>
                                </select>
                                <InputError :message="form.errors.team_id" />
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Assigning a project to a team allows team members to collaborate on it.
                                </p>
                            </div>

                            <div class="grid gap-4">
                                <Label for="notes" class="text-gray-700 dark:text-gray-200"> Notes </Label>
                                <Textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="4"
                                    class="border-gray-300 bg-gray-50 text-gray-800 placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500"
                                    placeholder="Add any additional notes about this project"
                                ></Textarea>
                                <InputError :message="form.errors.notes" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
                        <div class="px-6 py-5">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Select Terrains (Optional)</h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Choose terrains to include in this project. You can add more later.
                            </p>
                        </div>
                        <div class="border-t border-gray-200 px-6 py-5 dark:border-gray-800">
                            <div class="mb-4">
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <Search class="h-5 w-5 text-gray-400" />
                                    </div>
                                    <Input
                                        v-model="searchQuery"
                                        type="text"
                                        class="border-gray-300 bg-gray-50 pl-10 text-gray-800 placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500"
                                        placeholder="Search terrains..."
                                    />
                                </div>
                            </div>

                            <div v-if="form.errors.terrains" class="mb-4 rounded-lg bg-red-50 p-4 dark:bg-red-900/20">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <XCircle class="h-5 w-5 text-red-400 dark:text-red-500" />
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">{{ form.errors.terrains }}</h3>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Selected: {{ form.terrains.length }} terrain(s)</p>
                            </div>

                            <div class="custom-scrollbar max-h-96 overflow-y-auto">
                                <ul class="divide-y divide-gray-200 dark:divide-gray-800">
                                    <li v-for="terrain in filteredTerrains" :key="terrain.id" class="py-4">
                                        <div class="flex items-start">
                                            <div class="flex h-5 items-center">
                                                <input
                                                    :id="`terrain-${terrain.id}`"
                                                    :value="terrain.id"
                                                    type="checkbox"
                                                    :checked="form.terrains.includes(terrain.id)"
                                                    @change="toggleTerrain(terrain.id)"
                                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:checked:bg-indigo-600 dark:focus:ring-offset-gray-900"
                                                />
                                            </div>
                                            <div class="ml-3 flex-1">
                                                <label
                                                    :for="`terrain-${terrain.id}`"
                                                    class="flex cursor-pointer flex-col sm:flex-row sm:items-center sm:justify-between"
                                                >
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
                                                </label>
                                                <div class="mt-2 flex flex-wrap items-center gap-2">
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
                                                    <div v-if="terrain.analysis" class="ml-2 flex items-center">
                                                        <span class="text-xs font-medium text-gray-600 dark:text-gray-400">AI Score:</span>
                                                        <span class="ml-1 text-sm font-medium text-gray-900 dark:text-white">{{
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

                                <div
                                    v-if="filteredTerrains.length === 0 && props.terrains.length > 0"
                                    class="mt-4 flex flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 bg-gray-50 p-8 text-center dark:border-gray-700 dark:bg-gray-900/50"
                                >
                                    <Search class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-500" />
                                    <h3 class="mt-2 text-base font-semibold text-gray-900 dark:text-white">No terrains found</h3>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">No terrains match your search criteria.</p>
                                </div>

                                <div
                                    v-if="props.terrains.length === 0"
                                    class="mt-4 flex flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 bg-gray-50 p-8 text-center dark:border-gray-700 dark:bg-gray-900/50"
                                >
                                    <FilePlus class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-500" />
                                    <h3 class="mt-2 text-base font-semibold text-gray-900 dark:text-white">No terrains available</h3>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        You need to add terrains before you can include them in a project.
                                    </p>
                                    <div class="mt-6">
                                        <Link :href="route('terrains.create')">
                                            <Button
                                                class="bg-indigo-600 text-white hover:bg-indigo-700 active:bg-indigo-800 dark:bg-indigo-700 dark:hover:bg-indigo-600 dark:active:bg-indigo-800"
                                            >
                                                Add New Terrain
                                            </Button>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 lg:col-span-3">
                    <div class="flex items-center justify-end gap-3">
                        <Link :href="route('projects.index')">
                            <Button
                                variant="outline"
                                class="border-gray-300 text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                            >
                                Cancel
                            </Button>
                        </Link>
                        <Button
                            type="submit"
                            class="bg-indigo-600 text-white hover:bg-indigo-700 active:bg-indigo-800 dark:bg-indigo-700 dark:hover:bg-indigo-600 dark:active:bg-indigo-800"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Creating...</span>
                            <span v-else>Create Project</span>
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<style>
/* Custom scrollbar for better cross-browser consistency */
.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1; /* Light grey for track in light mode */
    border-radius: 10px;
}

.dark .custom-scrollbar::-webkit-scrollbar-track {
    background: #333; /* Dark grey for track in dark mode */
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #888; /* Grey for thumb in light mode */
    border-radius: 10px;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: #555; /* Darker grey for thumb in dark mode */
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #555; /* Even darker grey on hover in light mode */
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #777; /* Lighter grey on hover in dark mode */
}
</style>

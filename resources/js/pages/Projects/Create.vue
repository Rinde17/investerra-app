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
                <h1 class="text-3xl font-bold text-foreground">Create New Project</h1>
                <p class="mt-2 text-base text-muted-foreground">Create a new project to organize and analyze your terrains.</p>
            </div>

            <form @submit.prevent="submit" class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="lg:col-span-1">
                    <div class="overflow-hidden rounded-xl border border-border bg-card shadow-lg">
                        <div class="px-6 py-5">
                            <h2 class="text-xl font-semibold text-foreground">Project Information</h2>
                            <p class="mt-1 text-sm text-muted-foreground">Basic information about your project.</p>
                        </div>
                        <div class="border-t border-border px-6 py-5">
                            <div class="mb-6 grid gap-4">
                                <Label for="name" class="text-foreground"> Project Name <span class="text-destructive">*</span> </Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="border-border bg-input text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-primary"
                                    required
                                    placeholder="Enter project name"
                                />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="mb-6 grid gap-4">
                                <Label for="description" class="text-foreground"> Description </Label>
                                <Textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="4"
                                    class="border-border bg-input text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-primary"
                                    placeholder="Describe the purpose of this project"
                                ></Textarea>
                                <InputError :message="form.errors.description" />
                            </div>

                            <div class="mb-6 grid gap-4">
                                <Label for="team_id" class="text-foreground"> Team (Optional) </Label>
                                <select
                                    id="team_id"
                                    v-model="form.team_id"
                                    class="block w-full rounded-md border border-border bg-input px-4 py-2 text-foreground shadow-sm focus:border-primary focus:ring-primary focus:outline-none"
                                >
                                    <option :value="null">Personal Project (No Team)</option>
                                    <option v-for="team in teams" :key="team.id" :value="team.id">{{ team.name }}</option>
                                </select>
                                <InputError :message="form.errors.team_id" />
                                <p class="mt-2 text-sm text-muted-foreground">
                                    Assigning a project to a team allows team members to collaborate on it.
                                </p>
                            </div>

                            <div class="grid gap-4">
                                <Label for="notes" class="text-foreground"> Notes </Label>
                                <Textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="4"
                                    class="border-border bg-input text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-primary"
                                    placeholder="Add any additional notes about this project"
                                ></Textarea>
                                <InputError :message="form.errors.notes" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="overflow-hidden rounded-xl border border-border bg-card shadow-lg">
                        <div class="px-6 py-5">
                            <h2 class="text-xl font-semibold text-foreground">Select Terrains (Optional)</h2>
                            <p class="mt-1 text-sm text-muted-foreground">
                                Choose terrains to include in this project. You can add more later.
                            </p>
                        </div>
                        <div class="border-t border-border px-6 py-5">
                            <div class="mb-4">
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <Search class="h-5 w-5 text-muted-foreground" />
                                    </div>
                                    <Input
                                        v-model="searchQuery"
                                        type="text"
                                        class="border-border bg-input pl-10 text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-primary"
                                        placeholder="Search terrains..."
                                    />
                                </div>
                            </div>

                            <div v-if="form.errors.terrains" class="mb-4 rounded-lg bg-destructive/10 p-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <XCircle class="h-5 w-5 text-destructive" />
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-destructive-foreground">{{ form.errors.terrains }}</h3>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <p class="text-sm font-medium text-foreground">Selected: {{ form.terrains.length }} terrain(s)</p>
                            </div>

                            <div class="custom-scrollbar max-h-96 overflow-y-auto">
                                <ul class="divide-y divide-border">
                                    <li v-for="terrain in filteredTerrains" :key="terrain.id" class="py-4">
                                        <div class="flex items-start">
                                            <div class="flex h-5 items-center">
                                                <input
                                                    :id="`terrain-${terrain.id}`"
                                                    :value="terrain.id"
                                                    type="checkbox"
                                                    :checked="form.terrains.includes(terrain.id)"
                                                    @change="toggleTerrain(terrain.id)"
                                                    class="h-4 w-4 rounded border-border text-primary focus:ring-primary dark:checked:bg-primary dark:focus:ring-offset-background"
                                                />
                                            </div>
                                            <div class="ml-3 flex-1">
                                                <label
                                                    :for="`terrain-${terrain.id}`"
                                                    class="flex cursor-pointer flex-col sm:flex-row sm:items-center sm:justify-between"
                                                >
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
                                                </label>
                                                <div class="mt-2 flex flex-wrap items-center gap-2">
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
                                                    <div v-if="terrain.analysis" class="ml-2 flex items-center">
                                                        <span class="text-xs font-medium text-muted-foreground">AI Score:</span>
                                                        <span class="ml-1 text-sm font-medium text-foreground">{{
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
                                    class="mt-4 flex flex-col items-center justify-center rounded-lg border border-dashed border-border bg-card p-8 text-center"
                                >
                                    <Search class="mx-auto h-10 w-10 text-muted-foreground" />
                                    <h3 class="mt-2 text-base font-semibold text-foreground">No terrains found</h3>
                                    <p class="mt-1 text-sm text-muted-foreground">No terrains match your search criteria.</p>
                                </div>

                                <div
                                    v-if="props.terrains.length === 0"
                                    class="mt-4 flex flex-col items-center justify-center rounded-lg border border-dashed border-border bg-card p-8 text-center"
                                >
                                    <FilePlus class="mx-auto h-10 w-10 text-muted-foreground" />
                                    <h3 class="mt-2 text-base font-semibold text-foreground">No terrains available</h3>
                                    <p class="mt-1 text-sm text-muted-foreground">
                                        You need to add terrains before you can include them in a project.
                                    </p>
                                    <div class="mt-6">
                                        <Link :href="route('terrains.create')">
                                            <Button
                                                class="bg-primary text-primary-foreground hover:bg-primary/90 active:bg-primary/80"
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
                                class="border-border text-foreground hover:bg-muted"
                            >
                                Cancel
                            </Button>
                        </Link>
                        <Button
                            type="submit"
                            class="bg-primary text-primary-foreground hover:bg-primary/90 active:bg-primary/80"
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
    background: var(--background); /* Light grey for track in light mode */
    border-radius: 10px;
}

.dark .custom-scrollbar::-webkit-scrollbar-track {
    background: var(--muted); /* Dark grey for track in dark mode */
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: var(--muted-foreground); /* Grey for thumb in light mode */
    border-radius: 10px;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: var(--foreground); /* Darker grey for thumb in dark mode */
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: var(--foreground); /* Even darker grey on hover in light mode */
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: var(--muted-foreground); /* Lighter grey on hover in dark mode */
}
</style>

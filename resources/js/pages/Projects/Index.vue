<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { computed, ref } from 'vue';

// Define props for the component
const props = defineProps<{
    projects: Array<{
        id: number;
        name: string;
        description: string | null;
        user_id: number;
        team_id: number | null;
        notes: string | null;
        is_saved: boolean;
        created_at: string;
        updated_at: string;
        terrains: Array<{
            id: number;
            title: string;
            analysis: {
                ai_score: number;
                profitability_label: string;
            } | null;
        }>;
        team: {
            id: number;
            name: string;
        } | null;
    }>;
}>();

const searchQuery = ref('');
const sortBy = ref('created_at');
const sortOrder = ref('desc');
const filterTeam = ref<number | null>(null);

const filteredProjects = computed(() => {
    let filtered = [...props.projects];

    // Apply search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (project) =>
                project.name.toLowerCase().includes(query) ||
                (project.description && project.description.toLowerCase().includes(query)) ||
                (project.team && project.team.name.toLowerCase().includes(query)),
        );
    }

    // Apply team filter
    if (filterTeam.value !== null) {
        filtered = filtered.filter((project) => project.team_id === filterTeam.value);
    }

    // Apply sorting
    filtered.sort((a, b) => {
        let comparison = 0;

        switch (sortBy.value) {
            case 'name':
                comparison = a.name.localeCompare(b.name);
                break;
            case 'terrains':
                comparison = a.terrains.length - b.terrains.length;
                break;
            case 'team':
                const aTeam = a.team ? a.team.name : '';
                const bTeam = b.team ? b.team.name : '';
                comparison = aTeam.localeCompare(bTeam);
                break;
            default:
                // Default to sorting by created_at (most recent first)
                comparison = new Date(b.created_at).getTime() - new Date(a.created_at).getTime();
        }

        return sortOrder.value === 'asc' ? comparison : -comparison;
    });

    return filtered;
});

const toggleSort = (field: string) => {
    if (sortBy.value === field) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortBy.value = field;
        sortOrder.value = 'desc'; // Default to desc when changing sort field
    }
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        // Changed to en-US for consistency
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

// Get unique teams for filtering
const teams = computed(() => {
    const uniqueTeams = new Map();
    props.projects.forEach((project) => {
        if (project.team) {
            uniqueTeams.set(project.team.id, project.team);
        }
    });
    return Array.from(uniqueTeams.values());
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Projects',
        href: route('projects.index'),
    },
];
</script>

<template>
    <AuthenticatedLayout title="Projects" :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-8 p-6">
            <!-- Increased overall padding and gap -->
            <!-- Header with Create Project button and search -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-3xl font-bold text-foreground">Projects</h1>
                <!-- Larger title -->
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <Search class="h-5 w-5 text-muted-foreground" />
                            <!-- Lucide icon for search -->
                        </div>
                        <Input
                            v-model="searchQuery"
                            type="text"
                            class="border-border bg-input pl-10 text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-primary"
                            placeholder="Search projects..."
                        />
                    </div>
                    <Link :href="route('projects.create')">
                        <Button
                            class="bg-primary text-primary-foreground hover:bg-primary/90 active:bg-primary/80"
                        >
                            Create New Project
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Filters and sorting -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-wrap gap-2">
                    <Button
                        v-for="field in ['name', 'terrains', 'team', 'created_at']"
                        :key="field"
                        @click="toggleSort(field)"
                        variant="outline"
                        :class="[
                            sortBy === field
                                ? 'border-primary/20 bg-primary/10 text-primary hover:bg-muted-foreground'
                                : 'border-border text-foreground hover:bg-muted-foreground',
                            'inline-flex items-center transition-colors duration-200',
                        ]"
                    >
                        {{ field.charAt(0).toUpperCase() + field.slice(1).replace('_', ' ') }}
                        <!-- Capitalize and format -->
                        <svg v-if="sortBy === field" class="ml-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                                :transform="sortOrder === 'asc' ? 'rotate(180 10 10)' : ''"
                            />
                        </svg>
                    </Button>
                </div>

                <div v-if="teams.length > 0" class="flex items-center">
                    <Label for="team-filter" class="mr-2 text-sm font-medium text-foreground">Team:</Label>
                    <select
                        id="team-filter"
                        v-model="filterTeam"
                        class="block w-full rounded-md border border-border bg-input px-3 py-1.5 text-sm text-foreground shadow-sm focus:border-primary focus:ring-primary focus:outline-none"
                    >
                        <option :value="null">All Teams</option>
                        <option v-for="team in teams" :key="team.id" :value="team.id">{{ team.name }}</option>
                    </select>
                </div>
            </div>

            <!-- Projects grid -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="project in filteredProjects"
                    :key="project.id"
                    class="flex flex-col overflow-hidden rounded-xl border border-border bg-card shadow-lg transition-all duration-300 hover:shadow-xl"
                >
                    <div class="flex flex-1 flex-col p-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-foreground">{{ project.name }}</h3>
                            <span
                                v-if="project.team"
                                class="rounded-full bg-secondary/80 px-3 py-1 text-xs font-medium text-secondary-foreground"
                            >
                                {{ project.team.name }}
                            </span>
                        </div>
                        <p v-if="project.description" class="mt-3 flex-1 text-base text-muted-foreground">
                            {{ project.description }}
                        </p>
                        <p v-else class="mt-3 flex-1 text-base text-muted-foreground italic">No description provided</p>
                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs font-medium text-muted-foreground">Terrains</p>
                                <p class="text-lg font-semibold text-foreground">{{ project.terrains.length }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-muted-foreground">Created</p>
                                <p class="text-lg font-semibold text-foreground">{{ formatDate(project.created_at) }}</p>
                            </div>
                        </div>
                        <div v-if="project.terrains.length > 0" class="mt-6">
                            <!-- Increased margin-top -->
                            <p class="mb-2 text-sm font-medium text-foreground">Related Terrains:</p>
                            <!-- Adjusted text and added margin -->
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="terrain in project.terrains.slice(0, 3)"
                                    :key="terrain.id"
                                    :class="[
                                        terrain.analysis && terrain.analysis.profitability_label === 'Excellent'
                                            ? 'bg-primary/20 text-primary-foreground'
                                            : terrain.analysis && terrain.analysis.profitability_label === 'Good'
                                              ? 'bg-secondary/20 text-secondary-foreground'
                                              : terrain.analysis && terrain.analysis.profitability_label === 'Average'
                                                ? 'bg-accent/20 text-accent-foreground'
                                                : terrain.analysis && terrain.analysis.profitability_label === 'Poor'
                                                  ? 'bg-destructive/20 text-destructive-foreground'
                                                  : 'bg-muted/20 text-muted-foreground',
                                        'inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium', // Adjusted padding
                                    ]"
                                >
                                    {{ terrain.title }}
                                </span>
                                <span
                                    v-if="project.terrains.length > 3"
                                    class="inline-flex rounded-full bg-muted/20 px-2.5 py-0.5 text-xs font-medium text-muted-foreground"
                                >
                                    +{{ project.terrains.length - 3 }} more
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex border-t border-border bg-muted/50">
                        <Link
                            :href="route('projects.show', project.id)"
                            class="flex flex-1 items-center justify-center p-4 text-base font-medium text-primary transition-colors duration-200 hover:bg-muted"
                        >
                            View Details
                        </Link>
                        <Link
                            :href="route('projects.edit', project.id)"
                            class="flex flex-1 items-center justify-center border-l border-border p-4 text-base font-medium text-primary transition-colors duration-200 hover:bg-muted"
                        >
                            Edit
                        </Link>
                    </div>
                </div>

                <!-- Empty state when no projects -->
                <div
                    v-if="filteredProjects.length === 0"
                    class="col-span-full flex flex-col items-center justify-center rounded-xl border border-dashed border-border bg-card p-16 text-center"
                >
                    <svg
                        class="mx-auto h-16 w-16 text-muted-foreground"
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
                    <h3 class="mt-4 text-lg font-semibold text-foreground">No projects found</h3>
                    <p class="mt-2 text-base text-muted-foreground">
                        {{
                            searchQuery || filterTeam !== null ? 'No projects match your search criteria.' : 'Get started by creating a new project.'
                        }}
                    </p>
                    <div class="mt-8">
                        <Link :href="route('projects.create')">
                            <Button
                                class="bg-primary text-primary-foreground hover:bg-primary/90 active:bg-primary/80"
                            >
                                Create New Project
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

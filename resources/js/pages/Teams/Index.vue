<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Link } from '@inertiajs/vue3';

// Define props for the component
defineProps<{
    teams: Array<{
        id: number;
        name: string;
        description: string | null;
        owner_id: number;
    }>;
    currentTeam: {
        id: number;
        name: string;
    } | null;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Teams',
        href: route('teams.index'),
    },
];
</script>

<template>
    <AuthenticatedLayout title="Teams" :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-8 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-foreground">Your Teams</h1>
                <Link :href="route('teams.create')">
                    <Button
                        class="bg-primary text-primary-foreground hover:bg-primary/90 active:bg-primary/80"
                    >
                        Create New Team
                    </Button>
                </Link>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="team in teams"
                    :key="team.id"
                    class="flex flex-col overflow-hidden rounded-xl border border-border bg-card shadow-lg transition-all duration-300 hover:shadow-xl"
                >
                    <div class="flex flex-1 flex-col p-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-foreground">{{ team.name }}</h3>
                            <span
                                v-if="currentTeam && currentTeam.id === team.id"
                                class="rounded-full bg-primary/80 px-3 py-1 text-xs font-medium text-primary-foreground"
                            >
                                Current
                            </span>
                        </div>
                        <p v-if="team.description" class="mt-3 flex-1 text-base text-muted-foreground">
                            {{ team.description }}
                        </p>
                        <p v-else class="mt-3 flex-1 text-base text-muted-foreground italic">No description provided</p>
                    </div>
                    <div class="flex border-t border-border bg-muted/50">
                        <Link
                            :href="route('teams.show', team.id)"
                            class="flex flex-1 items-center justify-center p-4 text-base font-medium text-primary transition-colors duration-200 hover:bg-muted"
                        >
                            View Details
                        </Link>
                        <Link
                            v-if="currentTeam?.id !== team.id"
                            :href="route('teams.switch', team.id)"
                            method="post"
                            as="button"
                            type="button"
                            class="flex flex-1 items-center justify-center border-l border-border p-4 text-base font-medium text-primary transition-colors duration-200 hover:bg-muted"
                        >
                            Switch to this Team
                        </Link>
                    </div>
                </div>

                <div
                    v-if="teams.length === 0"
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
                            d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"
                        />
                    </svg>
                    <h3 class="mt-4 text-lg font-semibold text-foreground">No teams yet</h3>
                    <p class="mt-2 text-base text-muted-foreground">Get started by creating a new team.</p>
                    <div class="mt-8">
                        <Link :href="route('teams.create')">
                            <Button
                                class="bg-primary text-primary-foreground hover:bg-primary/90 active:bg-primary/80"
                            >
                                Create New Team
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

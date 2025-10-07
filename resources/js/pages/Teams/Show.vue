<script setup lang="ts">
import { Button } from '@/components/ui/button'; // Import shadcn Button
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import DeleteTeam from '@/components/DeleteTeam.vue';
import RemoveTeamMember from '@/components/RemoveTeamMember.vue';

// Define props for the component
const props = defineProps<{
    team: {
        id: number;
        name: string;
        description: string | null;
        owner_id: number;
    };
    members: Array<{
        id: number;
        name: string;
        email: string;
        pivot: {
            role: string;
        };
        plan: {
            name: string;
        } | null;
    }>;
    projects: Array<{
        id: number;
        name: string;
        description: string | null;
        terrains: Array<{
            id: number;
            title: string;
        }>;
    }>;
    userRole: string; // The role of the currently logged-in user in THIS team
}>();

const activeTab = ref('members');

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Teams',
        href: route('teams.index'),
    },
    {
        title: 'Team Details',
        href: route('teams.show', { team: props.team.id }),
    },
];
/*
const featureIcon = (hasFeature: boolean) => {
    return hasFeature
        ? '<span class="text-primary mr-2">&#10003;</span>' // Checkmark
        : '<span class="text-destructive mr-2">&#10008;</span>'; // Cross
};
*/
</script>

<template>
    <AuthenticatedLayout :title="team.name" :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-8 p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-foreground">{{ team.name }}</h1>
                    <p v-if="team.description" class="mt-2 text-base text-muted-foreground">
                        {{ team.description }}
                    </p>
                    <p v-else class="mt-2 text-base text-muted-foreground italic">No description provided</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <Link v-if="userRole === 'owner' || userRole === 'admin'" :href="route('teams.edit', team.id)">
                        <Button
                            variant="outline"
                            class="border-border text-foreground hover:bg-muted"
                        >
                            Edit Team
                        </Button>
                    </Link>
                    <Link v-if="userRole === 'owner' || userRole === 'admin'" :href="route('teams.members.add', team.id)">
                        <Button
                            variant="outline"
                            class="border-border text-foreground hover:bg-muted"
                        >
                            Add Member
                        </Button>
                    </Link>
                    <DeleteTeam
                        v-if="userRole === 'owner'"
                        :teamId="props.team.id"
                        :teamName="props.team.name"
                    />
                </div>
            </div>

            <div class="border-b border-border">
                <nav class="-mb-px flex space-x-8">
                    <button
                        @click="activeTab = 'members'"
                        :class="[
                            activeTab === 'members'
                                ? 'border-primary text-primary'
                                : 'border-transparent text-muted-foreground hover:border-border hover:text-foreground',
                            'border-b-2 px-1 py-4 text-base font-medium whitespace-nowrap transition-colors duration-200',
                        ]"
                    >
                        Members
                    </button>
                    <button
                        @click="activeTab = 'projects'"
                        :class="[
                            activeTab === 'projects'
                                ? 'border-primary text-primary'
                                : 'border-transparent text-muted-foreground hover:border-border hover:text-foreground',
                            'border-b-2 px-1 py-4 text-base font-medium whitespace-nowrap transition-colors duration-200',
                        ]"
                    >
                        Projects
                    </button>
                </nav>
            </div>

            <div
                v-if="activeTab === 'members'"
                class="overflow-hidden rounded-xl border border-border bg-card shadow-lg"
            >
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-border">
                        <thead class="bg-muted">
                        <tr>
                            <th
                                scope="col"
                                class="px-6 py-4 text-left text-xs font-medium tracking-wider text-muted-foreground uppercase"
                            >
                                Name
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-4 text-left text-xs font-medium tracking-wider text-muted-foreground uppercase"
                            >
                                Email
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-4 text-left text-xs font-medium tracking-wider text-muted-foreground uppercase"
                            >
                                Role
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-4 text-left text-xs font-medium tracking-wider text-muted-foreground uppercase"
                            >
                                Plan
                            </th>
                            <th
                                v-if="userRole === 'owner' || userRole === 'admin'"
                                scope="col"
                                class="px-6 py-4 text-right text-xs font-medium tracking-wider text-muted-foreground uppercase"
                            >
                                Actions
                            </th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-border bg-card">
                        <tr v-for="member in members" :key="member.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        <div
                                            class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-primary"
                                        >
                                            {{ member.name.charAt(0).toUpperCase() }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-foreground">{{ member.name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-muted-foreground">{{ member.email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="[
                                            member.pivot.role === 'owner'
                                                ? 'bg-accent/80 text-accent-foreground'
                                                : member.pivot.role === 'admin'
                                                  ? 'bg-secondary/80 text-secondary-foreground'
                                                  : 'bg-muted/80 text-muted-foreground',
                                            'inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium',
                                        ]"
                                    >
                                        {{ member.pivot.role.charAt(0).toUpperCase() + member.pivot.role.slice(1) }}
                                    </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-muted-foreground">
                                    {{ member.plan ? member.plan.name : 'Free' }}
                                </div>
                            </td>
                            <td
                                v-if="userRole === 'owner' || userRole === 'admin'"
                                class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap"
                            >
                                <div class="flex justify-end gap-3">
                                    <Link
                                        v-if="
                                                member.pivot.role !== 'owner' &&
                                                (userRole === 'owner' || (userRole === 'admin' && member.pivot.role === 'member'))
                                            "
                                        :href="route('teams.members.update', { team: team.id, user: member.id })"
                                        method="put"
                                        :data="{ role: member.pivot.role === 'admin' ? 'member' : 'admin' }"
                                        as="button"
                                        type="button"
                                        class="cursor-pointer text-primary transition-colors duration-200 hover:text-primary/90"
                                    >
                                        {{ member.pivot.role === 'admin' ? 'Demote to Member' : 'Promote to Admin' }}
                                    </Link>
                                    <RemoveTeamMember
                                        v-if="member.pivot.role !== 'owner'"
                                        :teamId="team.id"
                                        :memberId="member.id"
                                        :memberName="member.name"
                                        :teamName="team.name"
                                    />
                                </div>
                            </td>
                        </tr>
                        <tr v-if="members.length === 0">
                            <td colspan="5" class="px-6 py-6 text-center text-base text-muted-foreground">No members found.</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div
                v-if="activeTab === 'projects'"
                class="overflow-hidden rounded-xl border border-border bg-card shadow-lg"
            >
                <div class="p-6">
                    <div class="mb-6 flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-foreground">Team Projects</h2>
                        <Link :href="route('projects.create')">
                            <Button
                                class="bg-primary text-primary-foreground hover:bg-primary/90 active:bg-primary/80"
                            >
                                New Project
                            </Button>
                        </Link>
                    </div>

                    <div v-if="projects.length > 0" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="project in projects"
                            :key="project.id"
                            class="flex flex-col overflow-hidden rounded-xl border border-border bg-card shadow-md transition-all duration-300 hover:shadow-lg"
                        >
                            <div class="flex flex-1 flex-col p-6">
                                <h3 class="text-lg font-semibold text-foreground">{{ project.name }}</h3>
                                <p v-if="project.description" class="mt-2 flex-1 text-sm text-muted-foreground">
                                    {{ project.description }}
                                </p>
                                <p v-else class="mt-2 flex-1 text-sm text-muted-foreground italic">No description provided</p>
                                <div class="mt-4">
                                    <p class="text-sm font-medium text-foreground">Terrains: {{ project.terrains.length }}</p>
                                </div>
                            </div>
                            <div class="border-t border-border bg-muted/50 p-4">
                                <Link :href="route('projects.show', { project: project.id })" class="block">
                                    <Button
                                        class="w-full bg-primary text-primary-foreground hover:bg-primary/90 active:bg-primary/80"
                                    >
                                        View Project
                                    </Button>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="flex flex-col items-center justify-center rounded-xl border border-dashed border-border bg-card p-16 text-center"
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
                        <h3 class="mt-4 text-lg font-semibold text-foreground">No projects yet</h3>
                        <p class="mt-2 text-base text-muted-foreground">Get started by creating a new project.</p>
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
        </div>
    </AuthenticatedLayout>
</template>

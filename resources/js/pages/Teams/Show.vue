<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

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
    userRole: string;
}>();

const activeTab = ref('members');

const confirmDelete = () => {
    if (confirm('Are you sure you want to delete this team? This action cannot be undone.')) {
        router.delete(route('teams.destroy', { team: props.team.id }));
    }
};

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
</script>

<template>
    <AuthenticatedLayout :title="team.name" :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <!-- Team header with actions -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ team.name }}</h1>
                    <p v-if="team.description" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ team.description }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Link
                        v-if="userRole === 'owner' || userRole === 'admin'"
                        :href="route('teams.edit', team.id)"
                        class="dark:bg-sidebar-bg dark:hover:bg-sidebar-bg/80 rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none dark:border-sidebar-border dark:text-gray-300"
                    >
                        Edit Team
                    </Link>
                    <Link
                        v-if="userRole === 'owner' || userRole === 'admin'"
                        :href="route('teams.members.add', team.id)"
                        class="dark:bg-sidebar-bg dark:hover:bg-sidebar-bg/80 rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none dark:border-sidebar-border dark:text-gray-300"
                    >
                        Add Member
                    </Link>
                    <button
                        v-if="userRole === 'owner'"
                        @click="confirmDelete"
                        class="rounded-md border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-700 shadow-sm hover:bg-red-50 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:outline-none dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30"
                    >
                        Delete Team
                    </button>
                </div>
            </div>

            <!-- Tabs navigation -->
            <div class="border-b border-sidebar-border/70 dark:border-sidebar-border">
                <nav class="-mb-px flex space-x-8">
                    <button
                        @click="activeTab = 'members'"
                        :class="[
                            activeTab === 'members'
                                ? 'border-primary text-primary'
                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300',
                            'border-b-2 px-1 py-4 text-sm font-medium whitespace-nowrap',
                        ]"
                    >
                        Members
                    </button>
                    <button
                        @click="activeTab = 'projects'"
                        :class="[
                            activeTab === 'projects'
                                ? 'border-primary text-primary'
                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300',
                            'border-b-2 px-1 py-4 text-sm font-medium whitespace-nowrap',
                        ]"
                    >
                        Projects
                    </button>
                </nav>
            </div>

            <!-- Members tab content -->
            <div
                v-if="activeTab === 'members'"
                class="dark:bg-sidebar-bg overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border"
            >
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-sidebar-border/70 dark:divide-sidebar-border">
                        <thead class="dark:bg-sidebar-bg/50 bg-gray-50">
                            <tr>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400"
                                >
                                    Name
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400"
                                >
                                    Email
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400"
                                >
                                    Role
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400"
                                >
                                    Plan
                                </th>
                                <th
                                    v-if="userRole === 'owner' || userRole === 'admin'"
                                    scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="dark:bg-sidebar-bg divide-y divide-sidebar-border/70 bg-white dark:divide-sidebar-border">
                            <tr v-for="member in members" :key="member.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-primary">
                                                {{ member.name.charAt(0).toUpperCase() }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ member.name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ member.email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="[
                                            member.pivot.role === 'owner'
                                                ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-100'
                                                : member.pivot.role === 'admin'
                                                  ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100'
                                                  : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100',
                                            'inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium',
                                        ]"
                                    >
                                        {{ member.pivot.role.charAt(0).toUpperCase() + member.pivot.role.slice(1) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ member.plan ? member.plan.name : 'Free' }}
                                    </div>
                                </td>
                                <td
                                    v-if="userRole === 'owner' || userRole === 'admin'"
                                    class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap"
                                >
                                    <div class="flex justify-end gap-2">
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
                                            class="hover:text-primary-dark text-primary"
                                        >
                                            {{ member.pivot.role === 'admin' ? 'Demote to Member' : 'Promote to Admin' }}
                                        </Link>
                                        <Link
                                            v-if="member.pivot.role !== 'owner'"
                                            :href="route('teams.members.destroy', { team: team.id, user: member.id })"
                                            method="delete"
                                            as="button"
                                            type="button"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        >
                                            Remove
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="members.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No members found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Projects tab content -->
            <div
                v-if="activeTab === 'projects'"
                class="dark:bg-sidebar-bg overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border"
            >
                <div class="p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Team Projects</h2>
                        <Link
                            :href="route('projects.create')"
                            class="hover:bg-primary-dark rounded-md bg-primary px-4 py-2 text-sm font-medium text-white focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none"
                        >
                            New Project
                        </Link>
                    </div>

                    <div v-if="projects.length > 0" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="project in projects"
                            :key="project.id"
                            class="dark:bg-sidebar-bg flex flex-col overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm transition-all hover:shadow-md dark:border-sidebar-border"
                        >
                            <div class="flex flex-1 flex-col p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ project.name }}</h3>
                                <p v-if="project.description" class="mt-2 flex-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ project.description }}
                                </p>
                                <p v-else class="mt-2 flex-1 text-sm text-gray-400 italic dark:text-gray-500">No description provided</p>
                                <div class="mt-4">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Terrains: {{ project.terrains.length }}</p>
                                </div>
                            </div>
                            <div class="dark:bg-sidebar-bg/50 border-t border-sidebar-border/70 bg-gray-50 p-4 dark:border-sidebar-border">
                                <Link
                                    :href="route('projects.show', { project: project.id })"
                                    class="hover:bg-primary-dark flex w-full items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-white focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none"
                                >
                                    View Project
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="dark:bg-sidebar-bg/30 flex flex-col items-center justify-center rounded-lg border border-dashed border-sidebar-border/70 bg-gray-50 p-12 text-center dark:border-sidebar-border"
                    >
                        <svg
                            class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"
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
                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No projects yet</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new project.</p>
                        <div class="mt-6">
                            <Link
                                :href="route('projects.create')"
                                class="hover:bg-primary-dark rounded-md bg-primary px-4 py-2 text-sm font-medium text-white focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none"
                            >
                                Create New Project
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

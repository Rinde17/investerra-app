<script setup lang="ts">
import { Button } from '@/components/ui/button'; // Import shadcn Button
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
    userRole: string; // The role of the currently logged-in user in THIS team
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
        <div class="flex flex-col gap-8 p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ team.name }}</h1>
                    <p v-if="team.description" class="mt-2 text-base text-gray-600 dark:text-gray-400">
                        {{ team.description }}
                    </p>
                    <p v-else class="mt-2 text-base text-gray-500 italic dark:text-gray-600">No description provided</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <Link v-if="userRole === 'owner' || userRole === 'admin'" :href="route('teams.edit', team.id)">
                        <Button
                            variant="outline"
                            class="border-gray-300 text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                        >
                            Edit Team
                        </Button>
                    </Link>
                    <Link v-if="userRole === 'owner' || userRole === 'admin'" :href="route('teams.members.add', team.id)">
                        <Button
                            variant="outline"
                            class="border-gray-300 text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                        >
                            Add Member
                        </Button>
                    </Link>
                    <Button
                        v-if="userRole === 'owner'"
                        @click="confirmDelete"
                        variant="destructive"
                        class="bg-red-600 text-white hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600"
                    >
                        Delete Team
                    </Button>
                </div>
            </div>

            <div class="border-b border-gray-200 dark:border-gray-800">
                <nav class="-mb-px flex space-x-8">
                    <button
                        @click="activeTab = 'members'"
                        :class="[
                            activeTab === 'members'
                                ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' // Using indigo for primary
                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300',
                            'border-b-2 px-1 py-4 text-base font-medium whitespace-nowrap transition-colors duration-200', // Adjusted font size
                        ]"
                    >
                        Members
                    </button>
                    <button
                        @click="activeTab = 'projects'"
                        :class="[
                            activeTab === 'projects'
                                ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' // Using indigo for primary
                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300',
                            'border-b-2 px-1 py-4 text-base font-medium whitespace-nowrap transition-colors duration-200', // Adjusted font size
                        ]"
                    >
                        Projects
                    </button>
                </nav>
            </div>

            <div
                v-if="activeTab === 'members'"
                class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900"
            >
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-950/50">
                            <tr>
                                <th
                                    scope="col"
                                    class="px-6 py-4 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400"
                                >
                                    Name
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-4 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400"
                                >
                                    Email
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-4 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400"
                                >
                                    Role
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-4 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400"
                                >
                                    Plan
                                </th>
                                <th
                                    v-if="userRole === 'owner' || userRole === 'admin'"
                                    scope="col"
                                    class="px-6 py-4 text-right text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-800 dark:bg-gray-900">
                            <tr v-for="member in members" :key="member.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <div
                                                class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200"
                                            >
                                                {{ member.name.charAt(0).toUpperCase() }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ member.name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ member.email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="[
                                            member.pivot.role === 'owner'
                                                ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200'
                                                : member.pivot.role === 'admin'
                                                  ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                                                  : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                            'inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium',
                                        ]"
                                    >
                                        {{ member.pivot.role.charAt(0).toUpperCase() + member.pivot.role.slice(1) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
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
                                            class="text-indigo-600 transition-colors duration-200 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                        >
                                            {{ member.pivot.role === 'admin' ? 'Demote to Member' : 'Promote to Admin' }}
                                        </Link>
                                        <Link
                                            v-if="member.pivot.role !== 'owner'"
                                            :href="route('teams.members.destroy', { team: team.id, user: member.id })"
                                            method="delete"
                                            as="button"
                                            type="button"
                                            class="text-red-600 transition-colors duration-200 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        >
                                            Remove
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="members.length === 0">
                                <td colspan="5" class="px-6 py-6 text-center text-base text-gray-600 dark:text-gray-400">No members found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div
                v-if="activeTab === 'projects'"
                class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900"
            >
                <div class="p-6">
                    <div class="mb-6 flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Team Projects</h2>
                        <Link :href="route('projects.create')">
                            <Button
                                class="bg-indigo-600 text-white hover:bg-indigo-700 active:bg-indigo-800 dark:bg-indigo-700 dark:hover:bg-indigo-600 dark:active:bg-indigo-800"
                            >
                                New Project
                            </Button>
                        </Link>
                    </div>

                    <div v-if="projects.length > 0" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="project in projects"
                            :key="project.id"
                            class="flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md transition-all duration-300 hover:shadow-lg dark:border-gray-800 dark:bg-gray-900"
                        >
                            <div class="flex flex-1 flex-col p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ project.name }}</h3>
                                <p v-if="project.description" class="mt-2 flex-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ project.description }}
                                </p>
                                <p v-else class="mt-2 flex-1 text-sm text-gray-500 italic dark:text-gray-600">No description provided</p>
                                <div class="mt-4">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Terrains: {{ project.terrains.length }}</p>
                                </div>
                            </div>
                            <div class="border-t border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950/50">
                                <Link :href="route('projects.show', { project: project.id })" class="block">
                                    <Button
                                        class="w-full bg-indigo-600 text-white hover:bg-indigo-700 active:bg-indigo-800 dark:bg-indigo-700 dark:hover:bg-indigo-600 dark:active:bg-indigo-800"
                                    >
                                        View Project
                                    </Button>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="flex flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 bg-gray-50 p-16 text-center dark:border-gray-700 dark:bg-gray-900/50"
                    >
                        <svg
                            class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500"
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
                        <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">No projects yet</h3>
                        <p class="mt-2 text-base text-gray-600 dark:text-gray-400">Get started by creating a new project.</p>
                        <div class="mt-8">
                            <Link :href="route('projects.create')">
                                <Button
                                    class="bg-indigo-600 text-white hover:bg-indigo-700 active:bg-indigo-800 dark:bg-indigo-700 dark:hover:bg-indigo-600 dark:active:bg-indigo-800"
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

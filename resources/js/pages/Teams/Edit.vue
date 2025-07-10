<script setup lang="ts">
import { type BreadcrumbItem } from '@/types';
import { Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';

// Define props for the component
const props = defineProps<{
    team: {
        id: number;
        name: string;
        description: string | null;
    };
}>();

const form = useForm({
    name: props.team.name,
    description: props.team.description || '',
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Teams',
        href: route('teams.index'),
    },
    {
        title: props.team.name,
        href: route('teams.show', { team: props.team.id }),
    },
    {
        title: 'Edit Team',
        href: route('teams.edit', { team: props.team.id }),
    },
];

const submit = () => {
    form.put(route('teams.update', { team: props.team.id }), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AuthenticatedLayout title="Edit Team" :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl p-4">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Team</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update your team's information.</p>
            </div>

            <div class="dark:bg-sidebar-bg overflow-hidden rounded-lg border border-sidebar-border/70 bg-white shadow-sm dark:border-sidebar-border">
                <form @submit.prevent="submit" class="p-6">
                    <div class="mb-6">
                        <label for="name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                            Team Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="dark:bg-sidebar-bg/50 block w-full rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none dark:border-sidebar-border dark:text-white"
                            required
                            placeholder="Enter team name"
                        />
                        <div v-if="form.errors.name" class="mt-1 text-sm text-red-500">
                            {{ form.errors.name }}
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="description" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> Description </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            class="dark:bg-sidebar-bg/50 block w-full rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none dark:border-sidebar-border dark:text-white"
                            placeholder="Describe the purpose of this team"
                        ></textarea>
                        <div v-if="form.errors.description" class="mt-1 text-sm text-red-500">
                            {{ form.errors.description }}
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <Link
                            :href="route('teams.show', { team: props.team.id })"
                            class="dark:bg-sidebar-bg dark:hover:bg-sidebar-bg/80 rounded-md border border-sidebar-border/70 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none dark:border-sidebar-border dark:text-gray-300"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            class="hover:bg-primary-dark rounded-md bg-primary px-4 py-2 text-sm font-medium text-white focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none"
                            :disabled="form.processing || !form.isDirty"
                        >
                            <span v-if="form.processing">Saving...</span>
                            <span v-else>Save Changes</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

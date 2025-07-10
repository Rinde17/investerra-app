<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Link, useForm } from '@inertiajs/vue3';

// Define props for the component
const props = defineProps<{
    team: {
        id: number;
        name: string;
    };
}>();

const form = useForm({
    email: '',
    role: 'member',
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
        title: 'Add Member',
        href: route('teams.members.add', { team: props.team.id }),
    },
];

const submit = () => {
    form.post(route('teams.members.store', { team: props.team.id }), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <AuthenticatedLayout title="Add Team Member" :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl p-6">
            <!-- Increased padding -->
            <div class="mb-8">
                <!-- Increased margin-bottom -->
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Add Team Member</h1>
                <!-- Larger title -->
                <p class="mt-2 text-base text-gray-600 dark:text-gray-400">
                    <!-- Adjusted size and color -->
                    Add a new member to your team "{{ props.team.name }}".
                </p>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
                <!-- Consistent card styling -->
                <form @submit.prevent="submit" class="p-8">
                    <!-- Increased padding -->
                    <div class="mb-6 grid gap-4">
                        <!-- Consistent grid gap -->
                        <Label for="email" class="text-gray-700 dark:text-gray-200">
                            <!-- Consistent label color -->
                            Email Address <span class="text-red-500">*</span>
                        </Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="border-gray-300 bg-gray-50 text-gray-800 placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500"
                            required
                            placeholder="Enter email address"
                        />
                        <InputError :message="form.errors.email" />
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">The user must already be registered in the system.</p>
                    </div>

                    <div class="mb-6 grid gap-4">
                        <!-- Consistent grid gap -->
                        <Label for="role" class="text-gray-700 dark:text-gray-200">
                            <!-- Consistent label color -->
                            Role <span class="text-red-500">*</span>
                        </Label>
                        <!-- Using native select with updated styling for consistency -->
                        <select
                            id="role"
                            v-model="form.role"
                            class="block w-full rounded-md border border-gray-300 bg-gray-50 px-4 py-2 text-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            required
                        >
                            <option value="member">Member</option>
                            <option value="admin">Admin</option>
                        </select>
                        <InputError :message="form.errors.role" />

                        <!-- Role descriptions with radio buttons -->
                        <div class="mt-4 space-y-4">
                            <!-- Increased space-y for better separation -->
                            <div class="flex items-start">
                                <div class="flex h-5 items-center">
                                    <input
                                        id="role-member"
                                        name="role-description"
                                        type="radio"
                                        value="member"
                                        v-model="form.role"
                                        class="h-4 w-4 rounded-full border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:checked:bg-indigo-600 dark:focus:ring-offset-gray-900"
                                    />
                                </div>
                                <div class="ml-3">
                                    <label for="role-member" class="text-base font-medium text-gray-900 dark:text-white">Member</label>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Can view and create projects, but cannot add or remove team members.
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex h-5 items-center">
                                    <input
                                        id="role-admin"
                                        name="role-description"
                                        type="radio"
                                        value="admin"
                                        v-model="form.role"
                                        class="h-4 w-4 rounded-full border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:checked:bg-indigo-600 dark:focus:ring-offset-gray-900"
                                    />
                                </div>
                                <div class="ml-3">
                                    <label for="role-admin" class="text-base font-medium text-gray-900 dark:text-white">Admin</label>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Can add and remove team members, and manage team settings.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex items-center justify-end gap-3">
                        <!-- Increased margin-top -->
                        <Link :href="route('teams.show', { team: props.team.id })">
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
                            <span v-if="form.processing">Adding...</span>
                            <span v-else>Add Member</span>
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

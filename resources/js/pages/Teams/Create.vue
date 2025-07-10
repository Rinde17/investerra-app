<script setup lang="ts">
import InputError from '@/components/InputError.vue'; // Assuming this is a custom component for errors
import { Button } from '@/components/ui/button'; // Import shadcn Button
import { Input } from '@/components/ui/input'; // Import shadcn Input
import { Label } from '@/components/ui/label'; // Import shadcn Label
import { Textarea } from '@/components/ui/textarea'; // Assuming shadcn Textarea
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    description: '',
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Teams',
        href: route('teams.index'),
    },
    {
        title: 'Create Team',
        href: route('teams.create'),
    },
];

const submit = () => {
    form.post(route('teams.store'), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <AuthenticatedLayout title="Create Team" :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-3xl p-6">
            <!-- Increased padding -->
            <div class="mb-8">
                <!-- Increased margin-bottom -->
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create New Team</h1>
                <!-- Larger title -->
                <p class="mt-2 text-base text-gray-600 dark:text-gray-400">
                    <!-- Adjusted size and color -->
                    Create a new team to collaborate with your colleagues on investment projects.
                </p>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
                <!-- Consistent card styling -->
                <form @submit.prevent="submit" class="p-8">
                    <!-- Increased padding -->
                    <div class="mb-6 grid gap-4">
                        <!-- Using grid gap for consistency -->
                        <Label for="name" class="text-gray-700 dark:text-gray-200">
                            <!-- Consistent label color -->
                            Team Name <span class="text-red-500">*</span>
                        </Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="border-gray-300 bg-gray-50 text-gray-800 placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500"
                            required
                            placeholder="Enter team name"
                        />
                        <InputError :message="form.errors.name" />
                        <!-- Using InputError component -->
                    </div>

                    <div class="mb-6 grid gap-2">
                        <!-- Using grid gap for consistency -->
                        <Label for="description" class="text-gray-700 dark:text-gray-200"> Description </Label>
                        <Textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            class="border-gray-300 bg-gray-50 text-gray-800 placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500"
                            placeholder="Describe the purpose of this team"
                        ></Textarea>
                        <InputError :message="form.errors.description" />
                        <!-- Using InputError component -->
                    </div>

                    <div class="mt-8 flex items-center justify-end gap-3">
                        <!-- Increased margin-top -->
                        <Link :href="route('teams.index')">
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
                            <span v-else>Create Team</span>
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

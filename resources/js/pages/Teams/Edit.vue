<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Link, useForm } from '@inertiajs/vue3';

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
        <div class="mx-auto w-xl max-w-3xl p-6">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Team</h1>
                <p class="mt-2 text-base text-gray-600 dark:text-gray-400">Update your team's information.</p>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
                <form @submit.prevent="submit" class="p-8">
                    <div class="mb-6 grid gap-4">
                        <Label for="name" class="text-gray-700 dark:text-gray-200"> Team Name <span class="text-red-500">*</span> </Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="border-gray-300 bg-gray-50 text-gray-800 placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder:text-gray-500"
                            required
                            placeholder="Enter team name"
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
                            placeholder="Describe the purpose of this team"
                        ></Textarea>
                        <InputError :message="form.errors.description" />
                    </div>

                    <div class="mt-8 flex items-center justify-end gap-3">
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
                            :disabled="form.processing || !form.isDirty"
                        >
                            <span v-if="form.processing">Saving...</span>
                            <span v-else>Save Changes</span>
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

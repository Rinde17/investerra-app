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
                <h1 class="text-3xl font-bold text-foreground">Create New Team</h1>
                <!-- Larger title -->
                <p class="mt-2 text-base text-muted-foreground">
                    <!-- Adjusted size and color -->
                    Create a new team to collaborate with your colleagues on investment projects.
                </p>
            </div>

            <div class="overflow-hidden rounded-xl border border-border bg-card shadow-lg">
                <!-- Consistent card styling -->
                <form @submit.prevent="submit" class="p-8">
                    <!-- Increased padding -->
                    <div class="mb-6 grid gap-4">
                        <!-- Using grid gap for consistency -->
                        <Label for="name" class="text-foreground">
                            <!-- Consistent label color -->
                            Team Name <span class="text-destructive">*</span>
                        </Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="border-border bg-input text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-primary"
                            required
                            placeholder="Enter team name"
                        />
                        <InputError :message="form.errors.name" />
                        <!-- Using InputError component -->
                    </div>

                    <div class="mb-6 grid gap-2">
                        <!-- Using grid gap for consistency -->
                        <Label for="description" class="text-foreground"> Description </Label>
                        <Textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            class="border-border bg-input text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-primary"
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
                            <span v-else>Create Team</span>
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

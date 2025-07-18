<script setup lang="ts">
import InputError from '@/components/InputError.vue'; // Assuming this is a custom component for errors
import { Button } from '@/components/ui/button'; // Import shadcn Button
import { Input } from '@/components/ui/input'; // Import shadcn Input
import { Label } from '@/components/ui/label'; // Import shadcn Label
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
                <h1 class="text-3xl font-bold text-foreground">Add Team Member</h1>
                <!-- Larger title -->
                <p class="mt-2 text-base text-muted-foreground">
                    <!-- Adjusted size and color -->
                    Add a new member to your team "{{ props.team.name }}".
                </p>
            </div>

            <div class="overflow-hidden rounded-xl border border-border bg-card shadow-lg">
                <!-- Consistent card styling -->
                <form @submit.prevent="submit" class="p-8">
                    <!-- Increased padding -->
                    <div class="mb-6 grid gap-4">
                        <!-- Consistent grid gap -->
                        <Label for="email" class="text-foreground">
                            <!-- Consistent label color -->
                            Email Address <span class="text-destructive">*</span>
                        </Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="border-border bg-input text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-primary"
                            required
                            placeholder="Enter email address"
                        />
                        <InputError :message="form.errors.email" />
                        <p class="mt-2 text-sm text-muted-foreground">The user must already be registered in the system.</p>
                    </div>

                    <div class="mb-6 grid gap-4">
                        <!-- Consistent grid gap -->
                        <Label for="role" class="text-foreground">
                            <!-- Consistent label color -->
                            Role <span class="text-destructive">*</span>
                        </Label>
                        <!-- Using native select with updated styling for consistency -->
                        <select
                            id="role"
                            v-model="form.role"
                            class="block w-full rounded-md border border-border bg-input px-4 py-2 text-foreground shadow-sm focus:border-primary focus:ring-primary focus:outline-none"
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
                                        class="h-4 w-4 rounded-full border-border text-primary focus:ring-primary dark:checked:bg-primary dark:focus:ring-offset-background"
                                    />
                                </div>
                                <div class="ml-3">
                                    <label for="role-member" class="text-base font-medium text-foreground">Member</label>
                                    <p class="text-sm text-muted-foreground">
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
                                        class="h-4 w-4 rounded-full border-border text-primary focus:ring-primary dark:checked:bg-primary dark:focus:ring-offset-background"
                                    />
                                </div>
                                <div class="ml-3">
                                    <label for="role-admin" class="text-base font-medium text-foreground">Admin</label>
                                    <p class="text-sm text-muted-foreground">Can add and remove team members, and manage team settings.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex items-center justify-end gap-3">
                        <!-- Increased margin-top -->
                        <Link :href="route('teams.show', { team: props.team.id })">
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
                            <span v-if="form.processing">Adding...</span>
                            <span v-else>Add Member</span>
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

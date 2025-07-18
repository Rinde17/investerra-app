<script setup lang="ts">
import InputError from '@/components/InputError.vue'; // Assuming this is a custom component for errors
import { Button } from '@/components/ui/button'; // Import shadcn Button
import { Input } from '@/components/ui/input'; // Import shadcn Input
import { Label } from '@/components/ui/label'; // Import shadcn Label
import { Textarea } from '@/components/ui/textarea'; // Assuming shadcn Textarea
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
                <h1 class="text-3xl font-bold text-foreground">Edit Team</h1>
                <p class="mt-2 text-base text-muted-foreground">Update your team's information.</p>
            </div>

            <div class="overflow-hidden rounded-xl border border-border bg-card shadow-lg">
                <form @submit.prevent="submit" class="p-8">
                    <div class="mb-6 grid gap-4">
                        <Label for="name" class="text-foreground"> Team Name <span class="text-destructive">*</span> </Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="border-border bg-input text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-primary"
                            required
                            placeholder="Enter team name"
                        />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="mb-6 grid gap-4">
                        <Label for="description" class="text-foreground"> Description </Label>
                        <Textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            class="border-border bg-input text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-primary"
                            placeholder="Describe the purpose of this team"
                        ></Textarea>
                        <InputError :message="form.errors.description" />
                    </div>

                    <div class="mt-8 flex items-center justify-end gap-3">
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

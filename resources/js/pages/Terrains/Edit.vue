<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Link, useForm } from '@inertiajs/vue3';

// Shadcn UI Components
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

// Define props for the component
const props = defineProps<{
    terrain: {
        id: number;
        title: string;
        description: string | null;
        surface_m2: number;
        price: number;
        city: string;
        zip_code: string;
        latitude: number | null;
        longitude: number | null;
        viabilised: boolean;
        source_url: string | null;
        source_platform: string | null;
    };
}>();

// Initialize the form with existing terrain data
const form = useForm({
    title: props.terrain.title,
    description: props.terrain.description || '',
    surface_m2: props.terrain.surface_m2,
    price: props.terrain.price,
    city: props.terrain.city,
    zip_code: props.terrain.zip_code,
    // Convert null to undefined for shadcn/ui Input compatibility
    latitude: props.terrain.latitude === null ? undefined : props.terrain.latitude,
    longitude: props.terrain.longitude === null ? undefined : props.terrain.longitude,
    viabilised: props.terrain.viabilised,
    source_url: props.terrain.source_url || '',
    source_platform: props.terrain.source_platform || '',
});

// Define breadcrumbs for the layout
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Terrains',
        href: route('terrains.index'),
    },
    {
        title: props.terrain.title,
        href: route('terrains.show', { terrain: props.terrain.id }),
    },
    {
        title: 'Edit Terrain',
        href: route('terrains.edit', { terrain: props.terrain.id }),
    },
];

// Form submission handler
const submit = () => {
    form.put(route('terrains.update', { terrain: props.terrain.id }), {
        preserveScroll: true,
        onSuccess: () => {
            // Optionally, add a success toast/notification here if you have one setup
        },
        onError: () => {
            // Optionally, add an error toast/notification here
        },
    });
};
</script>

<template>
    <AuthenticatedLayout title="Edit Terrain" :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-4xl p-6 lg:p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-foreground">Edit Terrain</h1>
                <p class="mt-2 text-base text-muted-foreground">Update the details of this terrain.</p>
            </div>

            <div class="overflow-hidden rounded-xl border border-border bg-card shadow-lg">
                <form @submit.prevent="submit" class="p-8">
                    <div class="mb-8">
                        <h2 class="mb-5 text-xl font-semibold text-foreground">Basic Information</h2>
                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <Label for="title" class="mb-2 text-foreground"> Title <span class="text-destructive">*</span> </Label>
                                <Input
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    required
                                    placeholder="Enter terrain title"
                                    :class="{ 'border-destructive': form.errors.title }"
                                    class="border-border bg-input text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-primary"
                                />
                                <div v-if="form.errors.title" class="mt-2 text-sm text-destructive">
                                    {{ form.errors.title }}
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <Label for="description" class="mb-2 text-foreground"> Description </Label>
                                <Textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="3"
                                    placeholder="Describe the terrain"
                                    :class="{ 'border-destructive': form.errors.description }"
                                    class="border-border bg-input text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-primary"
                                ></Textarea>
                                <div v-if="form.errors.description" class="mt-2 text-sm text-destructive">
                                    {{ form.errors.description }}
                                </div>
                            </div>

                            <div>
                                <Label for="surface_m2" class="mb-2 text-foreground"> Surface (m²) <span class="text-destructive">*</span> </Label>
                                <Input
                                    id="surface_m2"
                                    v-model="form.surface_m2"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    required
                                    placeholder="Enter surface area"
                                    :class="{ 'border-destructive': form.errors.surface_m2 }"
                                    class="border-border bg-input text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-primary"
                                />
                                <div v-if="form.errors.surface_m2" class="mt-2 text-sm text-destructive">
                                    {{ form.errors.surface_m2 }}
                                </div>
                            </div>

                            <div>
                                <Label for="price" class="mb-2 text-foreground"> Price (€) <span class="text-destructive">*</span> </Label>
                                <Input
                                    id="price"
                                    v-model="form.price"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    required
                                    placeholder="Enter price"
                                    :class="{ 'border-destructive': form.errors.price }"
                                    class="border-border bg-input text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-primary"
                                />
                                <div v-if="form.errors.price" class="mt-2 text-sm text-destructive">
                                    {{ form.errors.price }}
                                </div>
                            </div>

                            <div>
                                <Label for="city" class="mb-2 text-foreground"> City <span class="text-destructive">*</span> </Label>
                                <Input
                                    id="city"
                                    v-model="form.city"
                                    type="text"
                                    required
                                    placeholder="Enter city"
                                    :class="{ 'border-destructive': form.errors.city }"
                                    class="border-border bg-input text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-primary"
                                />
                                <div v-if="form.errors.city" class="mt-2 text-sm text-destructive">
                                    {{ form.errors.city }}
                                </div>
                            </div>

                            <div>
                                <Label for="zip_code" class="mb-2 text-foreground"> ZIP Code <span class="text-destructive">*</span> </Label>
                                <Input
                                    id="zip_code"
                                    v-model="form.zip_code"
                                    type="text"
                                    required
                                    placeholder="Enter ZIP code"
                                    :class="{ 'border-destructive': form.errors.zip_code }"
                                    class="border-border bg-input text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-primary"
                                />
                                <div v-if="form.errors.zip_code" class="mt-2 text-sm text-destructive">
                                    {{ form.errors.zip_code }}
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <div class="mt-2 flex items-center">
                                    <input
                                        id="viabilised"
                                        v-model="form.viabilised"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-border text-primary focus:ring-primary dark:checked:bg-primary"
                                    />
                                    <Label for="viabilised" class="!mb-0 ml-2 text-sm text-foreground">
                                        This terrain is already viabilised (has utilities and access)
                                    </Label>
                                </div>
                                <div v-if="form.errors.viabilised" class="mt-2 text-sm text-destructive">
                                    {{ form.errors.viabilised }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <h2 class="mb-5 text-xl font-semibold text-foreground">Location (Optional)</h2>
                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <Label for="latitude" class="mb-2 text-foreground"> Latitude </Label>
                                <Input
                                    id="latitude"
                                    v-model="form.latitude"
                                    type="number"
                                    step="0.00000001"
                                    placeholder="Enter latitude"
                                    :class="{ 'border-destructive': form.errors.latitude }"
                                    class="border-border bg-input text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-primary"
                                />
                                <div v-if="form.errors.latitude" class="mt-2 text-sm text-destructive">
                                    {{ form.errors.latitude }}
                                </div>
                            </div>

                            <div>
                                <Label for="longitude" class="mb-2 text-foreground"> Longitude </Label>
                                <Input
                                    id="longitude"
                                    v-model="form.longitude"
                                    type="number"
                                    step="0.00000001"
                                    placeholder="Enter longitude"
                                    :class="{ 'border-destructive': form.errors.longitude }"
                                    class="border-border bg-input text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-primary"
                                />
                                <div v-if="form.errors.longitude" class="mt-2 text-sm text-destructive">
                                    {{ form.errors.longitude }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <h2 class="mb-5 text-xl font-semibold text-foreground">Source Information (Optional)</h2>
                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <Label for="source_url" class="mb-2 text-foreground"> Source URL </Label>
                                <Input
                                    id="source_url"
                                    v-model="form.source_url"
                                    type="url"
                                    placeholder="https://example.com/terrain-listing"
                                    :class="{ 'border-destructive': form.errors.source_url }"
                                    class="border-border bg-input text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-primary"
                                />
                                <div v-if="form.errors.source_url" class="mt-2 text-sm text-destructive">
                                    {{ form.errors.source_url }}
                                </div>
                            </div>

                            <div>
                                <Label for="source_platform" class="mb-2 text-foreground"> Source Platform </Label>
                                <div class="relative">
                                    <select
                                        id="source_platform"
                                        v-model="form.source_platform"
                                        class="block w-full appearance-none rounded-md border border-border bg-input px-3 py-2 pr-8 text-sm ring-offset-background placeholder:text-muted-foreground focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                        :class="{ 'border-destructive': form.errors.source_platform }"
                                    >
                                        <option value="">Select a platform</option>
                                        <option value="Leboncoin">Leboncoin</option>
                                        <option value="SeLoger">SeLoger</option>
                                        <option value="PAP">PAP</option>
                                        <option value="Bien'ici">Bien'ici</option>
                                        <option value="Logic-Immo">Logic-Immo</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    <svg
                                        class="pointer-events-none absolute top-1/2 right-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </div>
                                <div v-if="form.errors.source_platform" class="mt-2 text-sm text-destructive">
                                    {{ form.errors.source_platform }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-border pt-6">
                        <Link :href="route('terrains.show', { terrain: props.terrain.id })">
                            <Button type="button" variant="outline" class="border-border text-foreground hover:bg-muted"> Cancel </Button>
                        </Link>
                        <Button type="submit" :disabled="form.processing || !form.isDirty" class="bg-primary text-primary-foreground hover:bg-primary/90">
                            <span v-if="form.processing">Saving...</span>
                            <span v-else>Save Changes</span>
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

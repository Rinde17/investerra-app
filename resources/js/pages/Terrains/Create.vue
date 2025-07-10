<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

// Define the form structure
const form = useForm({
    title: '',
    description: '',
    surface_m2: undefined as number | undefined,
    price: undefined as number | undefined,
    city: '',
    zip_code: '',
    latitude: undefined as number | undefined,
    longitude: undefined as number | undefined,
    viabilised: false,
    source_url: '',
    source_platform: '',
});

// Define breadcrumbs for the layout
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Terrains',
        href: route('terrains.index'),
    },
    {
        title: 'Add Terrain',
        href: route('terrains.create'),
    },
];

// Form submission handler
const submit = () => {
    form.post(route('terrains.store'), {
        onSuccess: () => {
            form.reset();
            // Optionally, add a success toast/notification here if you have one setup
        },
        onError: () => {
            // Optionally, add an error toast/notification here
        },
    });
};
</script>

<template>
    <AuthenticatedLayout title="Add Terrain" :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-4xl p-6 lg:p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Add New Terrain</h1>
                <p class="mt-2 text-base text-gray-600 dark:text-gray-400">Enter the details of the terrain you want to analyze.</p>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
                <form @submit.prevent="submit" class="p-8">
                    <div class="mb-8">
                        <h2 class="mb-5 text-xl font-semibold text-gray-900 dark:text-white">Basic Information</h2>
                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <Label for="title" class="mb-2"> Title <span class="text-red-500">*</span> </Label>
                                <Input
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    required
                                    placeholder="Enter terrain title"
                                    :class="{ 'border-red-500': form.errors.title }"
                                />
                                <div v-if="form.errors.title" class="mt-2 text-sm text-red-500">
                                    {{ form.errors.title }}
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <Label for="description" class="mb-2"> Description </Label>
                                <Textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="3"
                                    placeholder="Describe the terrain"
                                    :class="{ 'border-red-500': form.errors.description }"
                                ></Textarea>
                                <div v-if="form.errors.description" class="mt-2 text-sm text-red-500">
                                    {{ form.errors.description }}
                                </div>
                            </div>

                            <div>
                                <Label for="surface_m2" class="mb-2"> Surface (m²) <span class="text-red-500">*</span> </Label>
                                <Input
                                    id="surface_m2"
                                    v-model="form.surface_m2"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    required
                                    placeholder="Enter surface area"
                                    :class="{ 'border-red-500': form.errors.surface_m2 }"
                                />
                                <div v-if="form.errors.surface_m2" class="mt-2 text-sm text-red-500">
                                    {{ form.errors.surface_m2 }}
                                </div>
                            </div>

                            <div>
                                <Label for="price" class="mb-2"> Price (€) <span class="text-red-500">*</span> </Label>
                                <Input
                                    id="price"
                                    v-model="form.price"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    required
                                    placeholder="Enter price"
                                    :class="{ 'border-red-500': form.errors.price }"
                                />
                                <div v-if="form.errors.price" class="mt-2 text-sm text-red-500">
                                    {{ form.errors.price }}
                                </div>
                            </div>

                            <div>
                                <Label for="city" class="mb-2"> City <span class="text-red-500">*</span> </Label>
                                <Input
                                    id="city"
                                    v-model="form.city"
                                    type="text"
                                    required
                                    placeholder="Enter city"
                                    :class="{ 'border-red-500': form.errors.city }"
                                />
                                <div v-if="form.errors.city" class="mt-2 text-sm text-red-500">
                                    {{ form.errors.city }}
                                </div>
                            </div>

                            <div>
                                <Label for="zip_code" class="mb-2"> ZIP Code <span class="text-red-500">*</span> </Label>
                                <Input
                                    id="zip_code"
                                    v-model="form.zip_code"
                                    type="text"
                                    required
                                    placeholder="Enter ZIP code"
                                    :class="{ 'border-red-500': form.errors.zip_code }"
                                />
                                <div v-if="form.errors.zip_code" class="mt-2 text-sm text-red-500">
                                    {{ form.errors.zip_code }}
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <div class="mt-2 flex items-center">
                                    <input
                                        id="viabilised"
                                        v-model="form.viabilised"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-indigo-500"
                                    />
                                    <Label for="viabilised" class="!mb-0 ml-2 text-sm">
                                        This terrain is already viabilised (has utilities and access)
                                    </Label>
                                </div>
                                <div v-if="form.errors.viabilised" class="mt-2 text-sm text-red-500">
                                    {{ form.errors.viabilised }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <h2 class="mb-5 text-xl font-semibold text-gray-900 dark:text-white">Location (Optional)</h2>
                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <Label for="latitude" class="mb-2"> Latitude </Label>
                                <Input
                                    id="latitude"
                                    v-model="form.latitude"
                                    type="number"
                                    step="0.00000001"
                                    placeholder="Enter latitude"
                                    :class="{ 'border-red-500': form.errors.latitude }"
                                />
                                <div v-if="form.errors.latitude" class="mt-2 text-sm text-red-500">
                                    {{ form.errors.latitude }}
                                </div>
                            </div>

                            <div>
                                <Label for="longitude" class="mb-2"> Longitude </Label>
                                <Input
                                    id="longitude"
                                    v-model="form.longitude"
                                    type="number"
                                    step="0.00000001"
                                    placeholder="Enter longitude"
                                    :class="{ 'border-red-500': form.errors.longitude }"
                                />
                                <div v-if="form.errors.longitude" class="mt-2 text-sm text-red-500">
                                    {{ form.errors.longitude }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <h2 class="mb-5 text-xl font-semibold text-gray-900 dark:text-white">Source Information (Optional)</h2>
                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <Label for="source_url" class="mb-2"> Source URL </Label>
                                <Input
                                    id="source_url"
                                    v-model="form.source_url"
                                    type="url"
                                    placeholder="https://example.com/terrain-listing"
                                    :class="{ 'border-red-500': form.errors.source_url }"
                                />
                                <div v-if="form.errors.source_url" class="mt-2 text-sm text-red-500">
                                    {{ form.errors.source_url }}
                                </div>
                            </div>

                            <div>
                                <Label for="source_platform" class="mb-2"> Source Platform </Label>
                                <div class="relative">
                                    <select
                                        id="source_platform"
                                        v-model="form.source_platform"
                                        class="block w-full appearance-none rounded-md border border-gray-300 bg-background px-3 py-2 pr-8 text-sm ring-offset-background placeholder:text-muted-foreground focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:focus:ring-offset-gray-900"
                                        :class="{ 'border-red-500': form.errors.source_platform }"
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
                                        class="pointer-events-none absolute top-1/2 right-3 h-4 w-4 -translate-y-1/2 text-gray-400"
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
                                <div v-if="form.errors.source_platform" class="mt-2 text-sm text-red-500">
                                    {{ form.errors.source_platform }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-800">
                        <Link :href="route('terrains.index')">
                            <Button type="button" variant="outline"> Cancel </Button>
                        </Link>
                        <Button type="submit" :disabled="form.processing">
                            <span v-if="form.processing">Creating...</span>
                            <span v-else>Create Terrain</span>
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

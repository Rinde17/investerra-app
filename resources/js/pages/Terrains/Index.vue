<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { BarChart2, Blocks, ChevronDown, Frown, Info, PlusCircle, Search } from 'lucide-vue-next';
import { computed, ref } from 'vue';

// Define props for the component
const props = defineProps<{
    terrains: Array<{
        id: number;
        title: string;
        description: string | null;
        surface_m2: number;
        price: number;
        city: string;
        zip_code: string;
        viabilised: boolean;
        analysis: {
            price_m2: number;
            ai_score: number;
            profitability_label: string;
            net_margin_estimate: number;
        } | null;
    }>;
    canCompare: boolean;
}>();

const searchQuery = ref('');
const sortBy = ref('created_at');
const sortOrder = ref('desc');
const selectedTerrains = ref<number[]>([]);

const compareTerrains = () => {
    if (selectedTerrains.value.length < 2) {
        alert('Please select at least 2 terrains to compare.'); // Replace with a more sophisticated notification if available
        return;
    }

    if (selectedTerrains.value.length > 5) {
        alert('You can compare a maximum of 5 terrains at once.'); // Replace with a more sophisticated notification
        return;
    }

    router.post(route('terrains.analysis.compare'), {
        terrain_ids: selectedTerrains.value,
    });
};

const filteredTerrains = computed(() => {
    let filtered = [...props.terrains];

    // Apply search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (terrain) =>
                terrain.title.toLowerCase().includes(query) ||
                terrain.city.toLowerCase().includes(query) ||
                terrain.zip_code.includes(query) ||
                (terrain.description && terrain.description.toLowerCase().includes(query)),
        );
    }

    // Apply sorting
    filtered.sort((a, b) => {
        let comparison = 0;

        switch (sortBy.value) {
            case 'title':
                comparison = a.title.localeCompare(b.title);
                break;
            case 'price':
                comparison = a.price - b.price;
                break;
            case 'surface':
                comparison = a.surface_m2 - b.surface_m2;
                break;
            case 'price_m2':
                const aPrice = a.analysis?.price_m2 || 0;
                const bPrice = b.analysis?.price_m2 || 0;
                comparison = aPrice - bPrice;
                break;
            case 'score':
                const aScore = a.analysis?.ai_score || 0;
                const bScore = b.analysis?.ai_score || 0;
                comparison = aScore - bScore;
                break;
            case 'profit':
                const aProfit = a.analysis?.net_margin_estimate || 0;
                const bProfit = b.analysis?.net_margin_estimate || 0;
                comparison = aProfit - bProfit;
                break;
            default:
                // Default to sorting by ID (most recent first)
                comparison = b.id - a.id;
        }

        return sortOrder.value === 'asc' ? comparison : -comparison;
    });

    return filtered;
});

const toggleSort = (field: string) => {
    if (sortBy.value === field) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortBy.value = field;
        sortOrder.value = 'desc';
    }
};

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price);
};

const formatSurface = (surface: number) => {
    return new Intl.NumberFormat('fr-FR').format(surface) + ' m²';
};

const formatPricePerM2 = (price: number) => {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price) + '/m²';
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Terrains',
        href: route('terrains.index'),
    },
];

const handleTerrainCheckboxChange = (event: Event, terrainId: number) => {
    const target = event.target as HTMLInputElement;
    if (target?.checked) {
        if (selectedTerrains.value.length < 5) {
            selectedTerrains.value.push(terrainId);
        } else {
            target.checked = false;
            alert('You can select a maximum of 5 terrains for comparison.');
        }
    } else {
        selectedTerrains.value = selectedTerrains.value.filter((id) => id !== terrainId);
    }
};
</script>

<template>
    <AuthenticatedLayout title="Terrains" :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-7xl p-6 lg:p-8">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Terrains</h1>
                    <div v-if="canCompare && selectedTerrains.length > 0" class="flex items-center">
                        <span
                            class="rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-800 dark:bg-indigo-900/20 dark:text-indigo-300"
                        >
                            {{ selectedTerrains.length }} selected
                        </span>
                    </div>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative w-full sm:w-auto">
                        <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                        <Input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search terrains..."
                            class="rounded-md border border-gray-300 bg-gray-50 py-2 pr-3 pl-10 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        />
                    </div>
                    <Button
                        v-if="canCompare && selectedTerrains.length >= 2"
                        @click="compareTerrains"
                        class="bg-indigo-600 text-white hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600"
                    >
                        <BarChart2 class="mr-2 h-4 w-4" />
                        Compare Terrains
                    </Button>
                    <Link :href="route('terrains.create')">
                        <Button class="bg-indigo-600 text-white hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600">
                            <PlusCircle class="mr-2 h-4 w-4" /> Add New Terrain
                        </Button>
                    </Link>
                </div>
            </div>

            <div
                v-if="!canCompare && filteredTerrains.length > 0"
                class="mb-8 rounded-xl border border-gray-200 bg-white p-4 shadow-lg dark:border-gray-800 dark:bg-gray-900"
            >
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <Info class="h-5 w-5 text-indigo-500" />
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">Terrain Comparison Tool</h3>
                        <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            <p>
                                Upgrade to Pro or Investor plan to access our powerful terrain comparison tool. Compare up to 5 terrains side by side
                                to make better investment decisions.
                            </p>
                        </div>
                        <div class="mt-2">
                            <Link
                                :href="route('pricing.index')"
                                class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
                            >
                                View Pricing Plans &rarr;
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6 flex flex-wrap gap-2">
                <Button
                    variant="outline"
                    @click="toggleSort('title')"
                    :class="{
                        'border-indigo-200 bg-indigo-50 text-indigo-700 dark:border-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300':
                            sortBy === 'title',
                    }"
                >
                    Name
                    <ChevronDown v-if="sortBy === 'title'" class="ml-1 h-4 w-4 transition-transform" :class="{ 'rotate-180': sortOrder === 'asc' }" />
                </Button>
                <Button
                    variant="outline"
                    @click="toggleSort('price')"
                    :class="{
                        'border-indigo-200 bg-indigo-50 text-indigo-700 dark:border-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300':
                            sortBy === 'price',
                    }"
                >
                    Price
                    <ChevronDown v-if="sortBy === 'price'" class="ml-1 h-4 w-4 transition-transform" :class="{ 'rotate-180': sortOrder === 'asc' }" />
                </Button>
                <Button
                    variant="outline"
                    @click="toggleSort('surface')"
                    :class="{
                        'border-indigo-200 bg-indigo-50 text-indigo-700 dark:border-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300':
                            sortBy === 'surface',
                    }"
                >
                    Surface
                    <ChevronDown
                        v-if="sortBy === 'surface'"
                        class="ml-1 h-4 w-4 transition-transform"
                        :class="{ 'rotate-180': sortOrder === 'asc' }"
                    />
                </Button>
                <Button
                    variant="outline"
                    @click="toggleSort('price_m2')"
                    :class="{
                        'border-indigo-200 bg-indigo-50 text-indigo-700 dark:border-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300':
                            sortBy === 'price_m2',
                    }"
                >
                    Price/m²
                    <ChevronDown
                        v-if="sortBy === 'price_m2'"
                        class="ml-1 h-4 w-4 transition-transform"
                        :class="{ 'rotate-180': sortOrder === 'asc' }"
                    />
                </Button>
                <Button
                    variant="outline"
                    @click="toggleSort('score')"
                    :class="{
                        'border-indigo-200 bg-indigo-50 text-indigo-700 dark:border-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300':
                            sortBy === 'score',
                    }"
                >
                    AI Score
                    <ChevronDown v-if="sortBy === 'score'" class="ml-1 h-4 w-4 transition-transform" :class="{ 'rotate-180': sortOrder === 'asc' }" />
                </Button>
                <Button
                    variant="outline"
                    @click="toggleSort('profit')"
                    :class="{
                        'border-indigo-200 bg-indigo-50 text-indigo-700 dark:border-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300':
                            sortBy === 'profit',
                    }"
                >
                    Profit
                    <ChevronDown
                        v-if="sortBy === 'profit'"
                        class="ml-1 h-4 w-4 transition-transform"
                        :class="{ 'rotate-180': sortOrder === 'asc' }"
                    />
                </Button>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="terrain in filteredTerrains"
                    :key="terrain.id"
                    class="flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg transition-all hover:shadow-xl dark:border-gray-800 dark:bg-gray-900"
                    :class="{ 'ring-2 ring-indigo-500': canCompare && selectedTerrains.includes(terrain.id) }"
                >
                    <div class="flex flex-1 flex-col p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div v-if="canCompare && terrain.analysis" class="flex items-center">
                                    <input
                                        type="checkbox"
                                        :id="`terrain-${terrain.id}`"
                                        :checked="selectedTerrains.includes(terrain.id)"
                                        @change="handleTerrainCheckboxChange($event, terrain.id)"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-indigo-500"
                                    />
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ terrain.title }}</h3>
                            </div>
                            <span
                                v-if="terrain.analysis"
                                :class="[
                                    terrain.analysis.profitability_label === 'Excellent'
                                        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100'
                                        : terrain.analysis.profitability_label === 'Good'
                                          ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100'
                                          : terrain.analysis.profitability_label === 'Average'
                                            ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100'
                                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100',
                                    'rounded-full px-2.5 py-0.5 text-xs font-medium',
                                ]"
                            >
                                {{ terrain.analysis.profitability_label }}
                            </span>
                        </div>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ terrain.city }}, {{ terrain.zip_code }}</p>
                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Price</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ formatPrice(terrain.price) }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Surface</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ formatSurface(terrain.surface_m2) }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Price/m²</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ terrain.analysis ? formatPricePerM2(terrain.analysis.price_m2) : 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">AI Score</p>
                                <div class="flex items-center">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ terrain.analysis ? Number(terrain.analysis.ai_score).toFixed(1) : 'N/A' }}
                                    </span>
                                    <div v-if="terrain.analysis" class="ml-2 h-2 w-16 rounded-full bg-gray-200 dark:bg-gray-700">
                                        <div
                                            class="h-2 rounded-full"
                                            :class="[
                                                terrain.analysis.ai_score >= 80
                                                    ? 'bg-green-500'
                                                    : terrain.analysis.ai_score >= 60
                                                      ? 'bg-blue-500'
                                                      : terrain.analysis.ai_score >= 40
                                                        ? 'bg-yellow-500'
                                                        : 'bg-red-500',
                                            ]"
                                            :style="{ width: `${terrain.analysis.ai_score}%` }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Status</p>
                            <div class="flex items-center">
                                <span
                                    :class="[
                                        terrain.viabilised
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100'
                                            : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100',
                                        'mt-1 inline-flex rounded-full px-2 py-0.5 text-xs font-medium',
                                    ]"
                                >
                                    {{ terrain.viabilised ? 'Viabilised' : 'Not Viabilised' }}
                                </span>
                            </div>
                        </div>
                        <div v-if="terrain.analysis && terrain.analysis.net_margin_estimate" class="mt-4">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Estimated Profit</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ formatPrice(terrain.analysis.net_margin_estimate) }}
                            </p>
                        </div>
                    </div>
                    <div class="flex border-t border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-900/50">
                        <Link
                            :href="route('terrains.show', terrain.id)"
                            class="flex flex-1 items-center justify-center p-3 text-sm font-medium text-indigo-600 hover:bg-gray-100 dark:text-indigo-400 dark:hover:bg-gray-800"
                        >
                            View Details
                        </Link>
                        <Link
                            :href="route('terrains.edit', terrain.id)"
                            class="flex flex-1 items-center justify-center border-l border-gray-200 p-3 text-sm font-medium text-indigo-600 hover:bg-gray-100 dark:border-gray-800 dark:text-indigo-400 dark:hover:bg-gray-800"
                        >
                            Edit
                        </Link>
                    </div>
                </div>

                <div
                    v-if="filteredTerrains.length === 0"
                    class="col-span-full flex flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 bg-gray-50 p-12 text-center dark:border-gray-700 dark:bg-gray-800"
                >
                    <template v-if="searchQuery">
                        <Frown class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" />
                        <h3 class="mt-2 text-base font-semibold text-gray-900 dark:text-white">No terrains found</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">No terrains match your search criteria.</p>
                    </template>
                    <template v-else>
                        <Blocks class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" />
                        <h3 class="mt-2 text-base font-semibold text-gray-900 dark:text-white">No terrains available</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Get started by adding a new terrain.</p>
                        <div class="mt-6">
                            <Link :href="route('terrains.create')">
                                <Button class="bg-indigo-600 text-white hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600">
                                    <PlusCircle class="mr-2 h-4 w-4" /> Add New Terrain
                                </Button>
                            </Link>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

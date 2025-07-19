<script setup lang="ts">
import { type BreadcrumbItem } from '@/types';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import LeafletMap, { type MapTerrain } from '@/components/LeafletMap.vue';
import { Link } from '@inertiajs/vue3';
import { defineProps } from 'vue';

interface DashboardProps {
    mapTerrains: MapTerrain[];
    stats: {
        totalTerrains: number;
        profitableTerrains: number;
        aiScoreAvg: number;
        analysesRemaining: string; // <-- S'assurer que le type est 'string'
    };
    latestTerrains: {
        id: number;
        title: string | null;
        city: string | null;
        ai_score: number | null;
        profitability_label: string | null;
    }[];
}

const props = defineProps<DashboardProps>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
];
</script>

<template>
    <AuthenticatedLayout title="Dashboard" :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">

            <div class="relative min-h-[500px] rounded-xl border border-border shadow-lg">
                <LeafletMap :terrains="props.mapTerrains" :initial-zoom="6" />
                <div v-if="props.mapTerrains.length === 0" class="absolute inset-0 flex items-center justify-center bg-card rounded-xl bg-opacity-90 z-10">
                    <p class="text-muted-foreground text-lg">Aucun terrain avec localisation disponible à afficher.</p>
                </div>
            </div>

            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative p-6 bg-card rounded-xl shadow-md border border-border">
                    <h3 class="text-lg font-semibold text-card-foreground mb-2">Terrains Analysés</h3>
                    <p class="text-4xl font-bold text-land-green">{{ props.stats.totalTerrains }}</p>
                    <p class="text-sm text-muted-foreground mt-2">Nombre total de terrains traités par votre équipe.</p>
                </div>

                <div class="relative p-6 bg-card rounded-xl shadow-md border border-border">
                    <h3 class="text-lg font-semibold text-card-foreground mb-2">Terrains Rentables</h3>
                    <p class="text-4xl font-bold text-data-green">{{ props.stats.profitableTerrains }}</p>
                    <p class="text-sm text-muted-foreground mt-2">Terrains avec une marge nette estimée positive.</p>
                </div>

                <div class="relative p-6 bg-card rounded-xl shadow-md border border-border">
                    <h3 class="text-lg font-semibold text-card-foreground mb-2">Score AI Moyen</h3>
                    <p class="text-4xl font-bold text-land-green">{{ props.stats.aiScoreAvg }} / 100</p>
                    <p class="text-sm text-muted-foreground mt-2">Moyenne des scores AI de vos terrains.</p>
                </div>
            </div>

            <div class="grid auto-rows-min gap-4 md:grid-cols-2"> <div class="relative p-6 bg-card rounded-xl shadow-md border border-border">
                <h3 class="text-lg font-semibold text-card-foreground mb-2">Analyses Disponibles</h3>
                <p class="text-4xl font-bold text-data-blue">{{ props.stats.analysesRemaining }}</p> <p class="text-sm text-muted-foreground mt-2">Analyses d'IA restantes cette semaine selon votre plan.</p>
            </div>

                <div class="relative p-6 bg-card rounded-xl shadow-md border border-border">
                    <h3 class="text-lg font-semibold text-card-foreground mb-4">Dernières Activités</h3>
                    <ul v-if="props.latestTerrains.length > 0" class="divide-y divide-border">
                        <li v-for="terrain in props.latestTerrains" :key="terrain.id" class="py-3 flex justify-between items-center">
                            <div>
                                <Link :href="route('terrains.show', terrain.id)" class="text-primary hover:text-primary/80 font-medium">
                                    {{ terrain.title || `Terrain à ${terrain.city}` }}
                                </Link>
                                <p class="text-sm text-muted-foreground">{{ terrain.city }}</p>
                            </div>
                            <div class="text-right">
                                <span class="block text-sm font-semibold text-foreground">Score AI: {{ terrain.ai_score !== null ? terrain.ai_score : 'N/A' }}</span>
                                <span class="block text-xs text-muted-foreground">{{ terrain.profitability_label || 'En cours...' }}</span>
                            </div>
                        </li>
                    </ul>
                    <div v-else class="text-center text-muted-foreground py-4">
                        Aucune activité récente.
                    </div>
                </div>
            </div>


            <div class="flex justify-center mt-6">
                <Link :href="route('terrains.create')"
                      class="inline-flex items-center px-6 py-3 bg-primary border border-transparent rounded-md font-semibold text-base text-primary-foreground uppercase tracking-widest hover:bg-opacity-90 focus:outline-none focus:border-land-green focus:ring ring-land-green transition ease-in-out duration-150">
                    Analyser un Nouveau Terrain
                </Link>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

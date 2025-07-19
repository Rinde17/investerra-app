<template>
    <div class="leaflet-map-container">
        <LMap
            ref="mapRef"
            v-model:zoom="zoom"
            :center="currentCenter"
            :useGlobalLeaflet="false"
            :options="{ zoomControl: true, attributionControl: true }"
        >
            <LTileLayer
                url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                attribution="&copy; <a href='https://www.openstreetmap.org/copyright'>OpenStreetMap</a> contributors"
                layer-type="base"
                name="OpenStreetMap"
            ></LTileLayer>

            <LMarker
                v-for="terrain in effectiveTerrains"
                :key="terrain.id"
                :lat-lng="[terrain.latitude, terrain.longitude]"
            >
                <LIcon :icon-url="markerIconUrl" :icon-size="iconSize" :icon-anchor="iconAnchor" />
                <LPopup>
                    <div class="popup-content">
                        <h4 class="font-bold text-foreground">{{ terrain.title || `Terrain à ${terrain.city}` }}</h4>
                        <p class="text-sm text-muted-foreground">{{ terrain.city }}</p>
                        <p v-if="terrain.surface_m2" class="text-xs text-muted-foreground">Surface: {{ terrain.surface_m2 }} m²</p>
                        <p v-if="terrain.price" class="text-xs text-muted-foreground">Prix: {{ new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(terrain.price) }}</p>
                        <Link v-if="terrain.id" :href="route('terrains.show', terrain.id)" class="text-primary hover:text-primary-foreground text-sm mt-2 block">
                            Voir les détails
                        </Link>
                    </div>
                </LPopup>
            </LMarker>

        </LMap>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue'; // Ajout de onMounted ici
import { LMap, LTileLayer, LMarker, LPopup, LIcon } from '@vue-leaflet/vue-leaflet';
import 'leaflet/dist/leaflet.css';
import * as L from 'leaflet';
import { Link } from '@inertiajs/vue3';

// Définition de l'interface pour un terrain sur la carte
export interface MapTerrain {
    id: number;
    title: string;
    city: string;
    latitude: number;
    longitude: number;
    surface_m2: number;
    price: number;
}

const props = defineProps<{
    terrains?: MapTerrain[];
    terrain?: MapTerrain | null;
    initialZoom?: number;
    markerIconUrl?: string;
    iconSize?: [number, number];
    iconAnchor?: [number, number];
}>();

const defaultZoom = 6;
const defaultCenter: [number, number] = [46.603354, 1.888334]; // Centre de la France

const mapRef = ref(null); // Référence à l'instance de la carte Leaflet

const defaultMarkerIconUrl = 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png';
const defaultMarkerIconRetinaUrl = 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png';
const defaultShadowUrl = 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png';

// Configuration des icônes par défaut de Leaflet
L.Marker.prototype.options.icon = L.icon({
    iconUrl: defaultMarkerIconUrl,
    iconRetinaUrl: defaultMarkerIconRetinaUrl,
    shadowUrl: defaultShadowUrl,
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    tooltipAnchor: [16, -28],
    shadowSize: [41, 41]
});

// Icone personnalisée ou par défaut
const markerIconUrl = computed(() => props.markerIconUrl || defaultMarkerIconUrl);
const iconSize = computed<[number, number]>(() => props.iconSize || [25, 41]);
const iconAnchor = computed<[number, number]>(() => props.iconAnchor || [12, 41]);


// Utiliser soit le tableau 'terrains' soit l'objet 'terrain'
const effectiveTerrains = computed<MapTerrain[]>(() => {
    const filteredTerrains = (props.terrains || []).filter(t => t.latitude !== null && t.longitude !== null);

    if (filteredTerrains.length > 0) {
        return filteredTerrains;
    } else if (props.terrain && props.terrain.latitude !== null && props.terrain.longitude !== null) {
        return [props.terrain];
    }
    return [];
});

// Calcul de l'état initial de la carte (centre et zoom)
// Cette fonction est appelée une seule fois à l'initialisation du composant.
const getInitialMapState = () => {
    if (effectiveTerrains.value.length === 1) {
        return {
            center: [effectiveTerrains.value[0].latitude, effectiveTerrains.value[0].longitude] as [number, number],
            zoom: props.initialZoom || 13 // Zoom plus serré pour un seul point
        };
    } else if (effectiveTerrains.value.length > 1) {
        // Pour plusieurs terrains, on ne peut pas calculer les 'bounds' précis sans l'objet carte Leaflet
        // On utilise donc un centre/zoom par défaut pour l'initialisation, qui sera ajusté par fitMapToMarkers.
        return {
            center: defaultCenter,
            zoom: props.initialZoom || defaultZoom
        };
    }
    // Aucun terrain valide, utilise les valeurs par défaut
    return {
        center: defaultCenter,
        zoom: props.initialZoom || defaultZoom
    };
};

const initialMapState = getInitialMapState();
const zoom = ref(initialMapState.zoom);
const currentCenter = ref(initialMapState.center);


// Fonction pour ajuster la vue de la carte
const fitMapToMarkers = () => {
    // Vérifie si la référence de la carte et s'il y a des terrains effectifs
    if (!mapRef.value || effectiveTerrains.value.length === 0) {
        // Si non, revient au centre/zoom par défaut
        currentCenter.value = defaultCenter;
        zoom.value = props.initialZoom || defaultZoom;
        return;
    }

    const leafletMap = (mapRef.value as any).leafletObject; // Accéder à l'instance Leaflet native

    if (effectiveTerrains.value.length === 1) {
        // Un seul marqueur : centre sur ce marqueur avec le zoom initial ou un zoom plus serré
        currentCenter.value = [effectiveTerrains.value[0].latitude, effectiveTerrains.value[0].longitude];
        zoom.value = props.initialZoom || 13;
        leafletMap.setView(currentCenter.value, zoom.value);
    } else {
        // Plusieurs marqueurs : calculer les limites et ajuster la vue
        const latLngs = effectiveTerrains.value.map(t => new L.LatLng(t.latitude, t.longitude));
        const bounds = new L.LatLngBounds(latLngs);

        if (bounds.isValid()) {
            // Si les limites sont valides, ajuster la vue pour les contenir
            leafletMap.fitBounds(bounds, { padding: [50, 50], maxZoom: props.initialZoom || 15 });
            currentCenter.value = leafletMap.getCenter().toArray() as [number, number];
            zoom.value = leafletMap.getZoom();
        } else if (effectiveTerrains.value.length > 0) {
            // Si les limites ne sont pas valides (ex: tous les points sont les mêmes), centre sur le premier point
            currentCenter.value = [effectiveTerrains.value[0].latitude, effectiveTerrains.value[0].longitude];
            zoom.value = props.initialZoom || 13;
            leafletMap.setView(currentCenter.value, zoom.value);
        } else {
            // Aucun terrain valide, revenir aux valeurs par défaut
            currentCenter.value = defaultCenter;
            zoom.value = props.initialZoom || defaultZoom;
        }
    }
};

// Exécute fitMapToMarkers après que la carte Leaflet est montée et prête
onMounted(() => {
    // Un petit setTimeout est souvent nécessaire pour s'assurer que l'objet mapRef.value.leafletObject est bien initialisé
    // et que le DOM de la carte a sa taille finale.
    if (mapRef.value) {
        setTimeout(() => {
            (mapRef.value as any).leafletObject.invalidateSize(); // Invalide la taille pour bien rendre la carte
            fitMapToMarkers(); // Ajuste la vue initiale
        }, 100); // Un délai de 100ms est généralement suffisant
    }
});

// Surveille les changements dans les terrains effectifs pour réajuster la vue
watch(effectiveTerrains, () => {
    if (mapRef.value) {
        setTimeout(() => {
            (mapRef.value as any).leafletObject.invalidateSize();
            fitMapToMarkers();
        }, 100);
    }
}, { deep: true }); // 'deep: true' est important si les propriétés internes des terrains peuvent changer


</script>

<style> /* Suppression de 'scoped' car les styles de popup sont globaux ou gérés dans app.css */
.leaflet-map-container {
    height: 100%;
    width: 100%;
    border-radius: 8px;
    overflow: hidden;
}
/* Styles pour la popup, si vous voulez un style personnalisé */
.popup-content {
    padding: 5px;
}
/* Les styles de popup avec @apply devraient être dans app.css pour éviter les erreurs Tailwind V4 */
</style>

<template>
    <div class="leaflet-map-container">
        <LMap
            ref="map"
            v-model:zoom="zoom"
            :center="center"
            :useGlobalLeaflet="false"
            :options="{ zoomControl: true, attributionControl: true }"
        >
            <LTileLayer
                url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                attribution="&copy; <a href='https://www.openstreetmap.org/copyright'>OpenStreetMap</a> contributors"
                layer-type="base"
                name="OpenStreetMap"
            ></LTileLayer>

            <LMarker v-if="markerPosition" :lat-lng="markerPosition as [number, number]">
                <LPopup>
                    {{ title }}
                </LPopup>
            </LMarker>

        </LMap>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { LMap, LTileLayer, LMarker, LPopup } from '@vue-leaflet/vue-leaflet';
import 'leaflet/dist/leaflet.css';
import * as L from 'leaflet';

const props = defineProps<{
    latitude: number;
    longitude: number;
    title: string;
}>();

const zoom = ref(13);
const center = ref([props.latitude, props.longitude] as [number, number]);
const markerPosition = ref([props.latitude, props.longitude] as [number, number]);

const map = ref(null);

watch(() => [props.latitude, props.longitude], ([newLat, newLng]) => {
    center.value = [newLat, newLng];
    markerPosition.value = [newLat, newLng];

    if (map.value && (map.value as any).leafletObject) {
        (map.value as any).leafletObject.setView(new L.LatLng(newLat, newLng), zoom.value);
    }
}, { immediate: true });

onMounted(() => {
    if (map.value && (map.value as any).leafletObject) {
        setTimeout(() => {
            (map.value as any).leafletObject.invalidateSize();
        }, 100);
    }
});
</script>

<style scoped>
.leaflet-map-container {
    height: 100%;
    width: 100%;
    border-radius: 8px;
    overflow: hidden;
}
</style>

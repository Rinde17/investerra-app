<template>
    <div id="map" class="leaflet-map"></div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import L from 'leaflet';

const props = defineProps<{
    latitude: number;
    longitude: number;
}>();

onMounted(() => {
    const map = L.map('map', {
        center: [props.latitude, props.longitude],
        zoom: 13,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution:
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(map);

    L.marker([props.latitude, props.longitude]).addTo(map);
});
</script>

<style scoped>
.leaflet-map {
    height: 400px;
    width: 100%;
    border-radius: 8px;
}
</style>

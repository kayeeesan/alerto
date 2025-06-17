<script setup>
import "leaflet/dist/leaflet.css";
import L from "leaflet";

// FIX LEAFLET ICON ISSUE
delete L.Icon.Default.prototype._getIconUrl;

L.Icon.Default.mergeOptions({ 
  iconRetinaUrl: new URL('leaflet/dist/images/marker-icon-2x.png', import.meta.url).href,
  iconUrl: new URL('leaflet/dist/images/marker-icon.png', import.meta.url).href,
  shadowUrl: new URL('leaflet/dist/images/marker-shadow.png', import.meta.url).href,
});

// Import components from @vue-leaflet/vue-leaflet
import { LMap, LTileLayer, LMarker, LPopup } from "@vue-leaflet/vue-leaflet";
import { ref } from "vue";

// Define center and zoom
const center = ref([7.1911, 124.9436]);
const zoom = ref(7);
const url = "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png";
const attribution = "&copy; OpenStreetMap contributors";

// Hardcoded sensor data

</script>

<template>
  <l-map style="height: 100vh;" :zoom="zoom" :center="center">
    <l-tile-layer :url="url" :attribution="attribution" />
   
  </l-map>
</template>

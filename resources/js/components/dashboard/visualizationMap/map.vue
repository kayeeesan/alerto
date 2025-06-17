<script setup>
import "leaflet/dist/leaflet.css";
import L from "leaflet";

// Fix Leaflet icon issue
delete L.Icon.Default.prototype._getIconUrl;

L.Icon.Default.mergeOptions({ 
  iconRetinaUrl: new URL('leaflet/dist/images/marker-icon-2x.png', import.meta.url).href,
  iconUrl: new URL('leaflet/dist/images/marker-icon.png', import.meta.url).href,
  shadowUrl: new URL('leaflet/dist/images/marker-shadow.png', import.meta.url).href,
});

// Import components
import { LMap, LTileLayer, LCircle, LPopup } from "@vue-leaflet/vue-leaflet";
import { ref, onMounted } from "vue";

// Import your composable to fetch sensors
import useSensorsUnderAlerto from "../../../composables/SensorsUnderAlerto";
import useSensorsUnderPh from "../../../composables/SensorsUnderPh";

// Access data from both
const { sensors_under_alerto, getSensorsUnderAlerto } = useSensorsUnderAlerto();
const { sensors_under_ph, getSensorsUnderPh } = useSensorsUnderPh();

const center = ref([6.9214, 122.0790]);
const zoom = ref(13);
const url = "https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png";
const attribution = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>';

// Determine color based on sensor's status
function getColorForStatus(status) {
  if (status === "warning") return "yellow";
  if (status === "alert") return "orange";
  if (status === "critical") return "red";
  return "blue";
}

onMounted(async () => {
  await getSensorsUnderAlerto({});
  await getSensorsUnderPh({});
});

</script>

<template>
  <l-map style="height: 100vh;" :zoom="zoom" :center="center">
    <l-tile-layer :url="url" :attribution="attribution" />

    <!-- Display Alerto Sensors -->
    <l-circle
      v-for="sensor in sensors_under_alerto"
      :key="sensor.device_id"
      :lat-lng="[parseFloat(sensor.lat), parseFloat(sensor.long)]"
      :radius="100"
      :color="getColorForStatus(sensor.status)"
      :fill-color="getColorForStatus(sensor.status)"
      fill-opacity="0.5">
      <l-popup>
        <div>
          <h3>{{ sensor.name }}</h3>
          <p><strong>Device ID:</strong> {{ sensor.device_id }}</p>
          <p><strong>Status:</strong> {{ sensor.status }}</p>
          <p><strong>Location:</strong> {{ sensor.lat }}, {{ sensor.long }}</p>
        </div>
      </l-popup>
    </l-circle>

    <!-- Display pH Sensors -->
    <l-circle
      v-for="sensor in sensors_under_ph"
      :key="sensor.device_id"
      :lat-lng="[parseFloat(sensor.lat), parseFloat(sensor.long)]"
      :radius="100"
      :color="getColorForStatus(sensor.status)"
      :fill-color="getColorForStatus(sensor.status)"
      fill-opacity="0.5">
      <l-popup>
        <div>
          <h3>{{ sensor.name }}</h3>
          <p><strong>Device ID:</strong> {{ sensor.device_id }}</p>
          <p><strong>Status:</strong> {{ sensor.status }}</p>
          <p><strong>Location:</strong> {{ sensor.lat }}, {{ sensor.long }}</p>
        </div>
      </l-popup>
    </l-circle>
  </l-map>
</template>

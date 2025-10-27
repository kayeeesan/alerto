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
import { ref, onMounted, computed } from "vue";

// Import your composables
import useSensorsUnderAlerto from "../../../composables/sensorsUnderAlerto";
import useSensorsUnderPh from "../../../composables/sensorsUnderPh";

const { sensors_under_alerto, getSensorsUnderAlerto } = useSensorsUnderAlerto();
const { sensors_under_ph, getSensorsUnderPh } = useSensorsUnderPh();

const center = ref([6.9214, 122.0790]);
const zoom = ref(13);
const url = "https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png";
const attribution = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>';

// Color based on status
function getColorForStatus(status) {
  if (status === "warning") return "yellow";
  if (status === "alert") return "orange";
  if (status === "critical") return "red";
  return "blue";
}

// Helpers to filter valid coords
function hasValidCoords(sensor) {
  return (
    sensor.lat &&
    sensor.long &&
    !isNaN(parseFloat(sensor.lat)) &&
    !isNaN(parseFloat(sensor.long))
  );
}

// Filtered lists (no NaN errors)
const valid_alerto_sensors = computed(() =>
  sensors_under_alerto.value.filter(hasValidCoords)
);
const valid_ph_sensors = computed(() =>
  sensors_under_ph.value.filter(hasValidCoords)
);

onMounted(async () => {
  await getSensorsUnderAlerto({});
  await getSensorsUnderPh({});
});
</script>

<template>
  <l-map style="height: 100vh;" :zoom="zoom" :center="center">
    <l-tile-layer :url="url" :attribution="attribution" />

    <!-- Alerto Sensors -->
    <l-circle
      v-for="sensor in valid_alerto_sensors"
      :key="sensor.device_id"
      :lat-lng="[parseFloat(sensor.lat), parseFloat(sensor.long)]"
      :radius="100"
      :color="getColorForStatus(sensor.status)"
      :fill-color="getColorForStatus(sensor.status)"
      fill-opacity="0.5"
    >
      <l-popup>
        <div>
          <h3>{{ sensor.name }}</h3>
          <p><strong>Device ID:</strong> {{ sensor.device_id }}</p>
          <p><strong>Status:</strong> {{ sensor.status }}</p>
          <p><strong>Location:</strong> {{ sensor.lat }}, {{ sensor.long }}</p>
        </div>
      </l-popup>
    </l-circle>

    <!-- pH Sensors -->
    <l-circle
      v-for="sensor in valid_ph_sensors"
      :key="sensor.device_id"
      :lat-lng="[parseFloat(sensor.lat), parseFloat(sensor.long)]"
      :radius="100"
      :color="getColorForStatus(sensor.status)"
      :fill-color="getColorForStatus(sensor.status)"
      fill-opacity="0.5"
    >
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

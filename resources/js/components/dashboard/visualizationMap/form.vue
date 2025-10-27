<script setup>
import { ref } from 'vue';
import { useRoute } from 'vue-router';
import Map from '@/components/dashboard/visualizationMap/map.vue';
import "leaflet/dist/leaflet.css";


</script>

<template>
  <div class="map-container">
      <Map/>
  </div>

</template>

<style scoped>
.map-container {
  margin-top: -60px; /* Counteracts the parent's margin */
  margin-right: -50px; /* Counteracts the parent's margin */
}
</style>
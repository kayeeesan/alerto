<script setup>
import { ref } from 'vue';
import weatherForecast from './weatherForecast.vue';
import weatherDegree from './weatherDegree.vue';
import typesOfTemp from './typesOfTemp.vue';
import localRainfall from './localRainfall.vue';
import localThunderStorm from './localThunderStorm.vue';
import pagasa from './pagasa.vue';
</script>

<template >
  <div class="mt-24 mr-12 animate__animated animate__fadeIn pb-10">
    <!-- Title Section -->
    <v-row>
      <span class="text-h4 font-weight-bold text-blue-darken-4 mt-3 ml-6">
        PAGASA - Weather Updates
      </span>

    </v-row>

    <!-- Main Content Section -->
    <v-row class="flex justify-between items-start">
      <v-col cols="12" lg="9" class="pr-5">
        <weatherForecast />
      </v-col>

      <v-col cols="12" lg="3" class="pl-5 space-y-4">
        <localRainfall />
        <localThunderStorm />
        <pagasa />
      </v-col>
    </v-row>
  </div>
</template>

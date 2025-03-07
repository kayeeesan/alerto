<script setup>
import { ref } from 'vue';
import weatherData from './weatherData.vue';
import weatherStation from './weatherStation.vue';

</script>

<template>
  <div class="mt-24 mr-12 animate__animated animate__fadeIn">
    <!-- Title Section -->
    <v-row>
      <span class="text-h4 font-weight-bold text-blue-darken-4 mt-3 ml-6">
        Ateneo de Zamboanga University Weather Station
      </span>
    </v-row>

    <!-- Main Content Section -->
     <v-row>
        <v-col cols="12" lg="7">
          <weatherData class="animate__animated animate__fadeIn"/>
        </v-col>
        <v-col>
          <weatherStation class="animate__animated animate__fadeIn"/>
        </v-col>
     </v-row>
   
    
  </div>
</template>

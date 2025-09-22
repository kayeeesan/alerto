<script setup>
import { ref } from 'vue';
import phivolcs from './phivolcs.vue';

</script>

<template>
  <div class="mt-24 mr-12 ml-5 animate__animated animate__fadeIn">
    <!-- Title Section -->
    <v-row>
      <p class="text-h4 font-weight-bold text-blue-darken-4 ml-6">Earthquake Monitoring</p>
    </v-row>

    <!-- Main Content Section -->
     <v-row>
      <v-col cols="12" lg="12" class="pr-5">
        <phivolcs />
      </v-col>
     </v-row>
    
  </div>
</template>

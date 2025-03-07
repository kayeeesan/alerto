<script setup>
import { ref } from 'vue';
import dataExtraction from './dataExtraction.vue';

</script>

<template>
  <div class="mt-24 mr-12 animate__animated animate__fadeIn">
    <!-- Title Section -->
    <v-row>
      <p class="text-h4 font-weight-bold text-blue-darken-4 mt-3 ml-6">History Data Extraction</p>
    </v-row>

    <!-- Main Content Section -->
     <v-row>
        <v-col>
          <dataExtraction/>
        </v-col>
 
     </v-row>
   
    
  </div>
</template>

<script setup>
import { ref } from 'vue';
import getInTouch from '../../components/contactUs/getInTouch.vue';

</script>

<template>
  <div class="mt-24 mr-12 animate__animated animate__fadeIn">
    <!-- Title Section -->
    <v-row>
      <p class="text-h4 font-weight-bold text-blue-darken-4 mt-3 ml-6">Contact Us</p>
    </v-row>

    <!-- Main Content Section -->
    
   <v-row>
    <getInTouch/>
   </v-row>


    
  </div>
</template>

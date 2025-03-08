<script setup>
import { ref } from 'vue';
import aboutUs from '../../components/aboutUs/aboutUs.vue';
import partners from '../../components/aboutUs/partners.vue';

</script>

<template>
  <div class="ml-5  animate__animated animate__fadeIn">
    <!-- Title Section -->
    <v-row>
      <p class="text-h4 font-weight-bold text-blue-darken-4 mt-3 ml-6" >About Us</p>
    </v-row>

    <!-- Main Content Section -->
    
   <v-row>
        <aboutUs/>
   </v-row>

   <v-row>
    <partners/>
   </v-row>
    
  </div>
</template>

<template>
  <v-col cols="9" class="mt-2" style="padding: 0 !important;">
    <v-sheet
      class="pa-6 rounded-lg shadow-md"
      style="position: relative; background: #F8FAF0; border: 1px solid #E0E0E0;"
    >
      <!-- Top Bar -->
      <span
        style="background: var(--primary-color); position: absolute; left: 0; right: 0; top: 0; border-top-left-radius: 11px; border-top-right-radius: 11px; height: 8px;"
      ></span>

      <!-- Title Section -->
      <v-row justify="center" class="mb-2">
        <p class="text-h6 font-weight-medium text-grey-darken-2">
          Weather Report
        </p>
      </v-row>

      <v-row align="center" justify="center">
        <!-- Weather Icon -->
        <v-col cols="4" class="d-flex justify-center">
          
          <img src="../../../imgs/weatherIcon/sunny.svg" alt="">
        </v-col>
       
        <!-- Weather Details -->
        <v-col cols="8" class="d-flex flex-column align-end">
          <p class="text-h6 font-weight-bold text-blue-darken-3">Sunday</p>
          <p class="text-h5 font-weight-medium text-grey-darken-2">Sunny</p>
          <p class="text-h4 font-weight-bold text-blue-darken-3 mt-2">22°C</p>
          <p class="text-body-1 text-grey-darken-1">71.6°F</p>
        </v-col>
      </v-row>
      <!-- Divider -->
      <hr style="border: 2px solid var(--primary-color); margin: 10px 0;" />

   
    </v-sheet>
  </v-col>
</template>
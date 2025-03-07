<template>
    <v-col cols="12" class="mt-2" style="padding: 0 !important;">
      <v-sheet
        class="pa-6 rounded-lg shadow-md"
        style="position: relative; background: #F8FAF0; border: 1px solid #E0E0E0;"
      >
        <!-- Top Bar -->
        <span
          style="background: var(--primary-color); position: absolute; left: 0; right: 0; top: 0; border-top-left-radius: 11px; border-top-right-radius: 11px; height: 8px;"
        ></span>
  
        <!-- Title Section -->
        <div class="mb-4">
          <p class="text-2xl font-semibold text-blue-800">What's the Weather Like?</p>
        </div>
  
        <!-- Divider -->
        <hr style="border: 2px solid var(--primary-color); margin: 10px 0;" />
  
        <!-- First Row of Forecast Items -->
        <v-row class="d-flex justify-between items-center" style="margin: 10px 5px;">
          <!-- Forecast Item 1 -->
          <v-col cols="6" class="p-3 text-center">
            <div class="bg-blue-100 rounded-lg p-4 shadow-sm">
              <p class="text-lg font-semibold text-blue-600">12°C</p>
              <p class="text-sm text-gray-600">Cloudy</p>
            </div>
          </v-col>
  
          <!-- Forecast Item 2 -->
          <v-col cols="6" class="p-3 text-center">
            <div class="bg-yellow-100 rounded-lg p-4 shadow-sm">
              <p class="text-lg font-semibold text-yellow-600">15°C</p>
              <p class="text-sm text-gray-600">Sunny</p>
            </div>
          </v-col>
        </v-row>
  
        <!-- Second Row of Forecast Items -->
        <v-row class="d-flex justify-between items-center" style="margin: 10px 5px;">
          <!-- Forecast Item 3 -->
          <v-col cols="6" class="p-3 text-center">
            <div class="bg-gray-100 rounded-lg p-4 shadow-sm">
              <p class="text-lg font-semibold text-gray-600">18°C</p>
              <p class="text-sm text-gray-600">Partly Cloudy</p>
            </div>
          </v-col>
  
          <!-- Forecast Item 4 -->
          <v-col cols="6" class="p-3 text-center">
            <div class="bg-green-100 rounded-lg p-4 shadow-sm">
              <p class="text-lg font-semibold text-green-600">20°C</p>
              <p class="text-sm text-gray-600">Rainy</p>
            </div>
          </v-col>
        </v-row>
      </v-sheet>
    </v-col>
</template>
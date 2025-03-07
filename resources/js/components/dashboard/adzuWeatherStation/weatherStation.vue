<template>
    <v-col cols="12" class="p-0">
      <v-sheet class="pa-6 rounded-lg shadow-md bg-gray-100 border border-gray-300 relative"
      style="background: #F8FAF0;">
        <!-- Top Bar -->
        <span
          style="background: var(--primary-color); position: absolute; left: 0; right: 0; top: 0; border-top-left-radius: 11px; border-top-right-radius: 11px; height: 8px;"
        ></span>
  
        <!-- Title -->
        <div class="mb-4">
          <p class="text-xl font-semibold text-gray-500">WEATHER STATION</p>
        </div>
  
        
        <hr style="border: 2px solid var(--primary-color); margin: 10px 0;" />
  
        <!-- Image Section -->
        <v-row class="justify-center mt-2">
          <v-img
            src="https://rdrrmc9-alerto.com/assets/images/ws.jpg"
            alt="Weather Station"
            class="rounded-md shadow-md"
            max-width="70%"
          ></v-img>
        </v-row>
  
        <v-divider class="border-t-2 border-blue-900 my-6"></v-divider>
  
        <!-- Location Details -->
        <v-row class="justify-center text-center">
          <p class="text-gray-700 font-medium">Fr Ernesto Carretero SJ Building</p>
        </v-row>
        <v-row class="justify-center text-center">
          <p class="text-gray-700 font-medium">Ateneo de Zamboanga University</p>
        </v-row>
      </v-sheet>
    </v-col>
  </template>
  
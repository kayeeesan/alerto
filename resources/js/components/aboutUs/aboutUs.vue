<template> 
  <v-col cols="12" class="p-0 ">
    <v-sheet
      class="pa-8 rounded-lg shadow-lg elevation-3"
      style="position: relative; background: #F8FAF0; border: 1px solid #E0E0E0;"
    >
      <!-- Top Bar Section -->
      <div
        style="background: var(--primary-color); position: absolute; left: 0; right: 0; top: 0; height: 8px; border-top-left-radius: 11px; border-top-right-radius: 11px;"
      ></div>

      <!-- Title Section -->
      <v-row class="mb-6 mt-6 text-center">
        <v-col>
          <h2 class="text-3xl font-semibold text-gray-600 leading-tight">
            Automated Water-Level and Rain Monitoring <br />
            using Near-Real Time Observation (ALeRTO)
          </h2>
        </v-col>
      </v-row>

      <!-- Description Section -->
      <v-row class="mb-6">
        <v-col cols="12">
          <p class="text-lg font-medium text-gray-500">
            The Automated Water-Level and Rain Monitoring Using Near-Real Time Observation (ALeRTO) is an early warning system developed by the Ateneo de Zamboanga University-GeoSAFER Mindanao ZAMBASULTA Program. The system makes use of the Automated Rain Gauges (ARGs) and Automated Water-Level Sensors (AWLS) installed in various river systems in the region. It monitors data from DOST-ASTI and PAGASA to interpret hydro-meteorological thresholds. Once a threshold is reached, the system sends alerts via SMS to Local Government Units (LGUs) and provides updates through the ALeRTO website, enhancing preparedness and aiding decision-making to mitigate flood risks.
          </p>
        </v-col>
      </v-row>

      <v-divider class="border-opacity-75 my-6"></v-divider>

      <!-- Image Section -->
      <v-row justify="center">
        <v-img
          src="https://rdrrmc9-alerto.com/assets/images/AlertoMap.jpg"
          alt="ALeRTO Map"
          class="rounded-lg shadow-md"
          max-width="70%"
        ></v-img>
      </v-row>

      <v-divider class="border-opacity-75 my-6"></v-divider>

      <!-- Pilot Areas Section -->
      <v-row class="mb-4">
        <v-col>
          <h3 class="text-2xl font-semibold text-gray-600">10 Pilot Areas</h3>
        </v-col>
      </v-row>

      <v-row>
        <v-col cols="12" md="6">
          <h4 class="text-xl text-gray-600">Zamboanga del Norte</h4>
          <ul class="text-gray-500 ml-4">
            <li>Siocon River - Siocon</li>
            <li>Kipit River - Labason</li>
            <li>Salug River - Salug</li>
            <li>Sindangan River - Sindangan</li>
            <li>Tangian River - Pres. Manuel A. Roxas</li>
          </ul>
          <h4 class="text-xl text-gray-600 mt-4">Zamboanga City</h4>
          <ul class="text-gray-500 ml-4">
            <li>Manicahan River</li>
            <li>Tumaga River</li>
            <li>Lamitan City - Basilan, ARMM</li>
            <li>Gubauan River</li>
          </ul>
        </v-col>

        <v-col cols="12" md="6">
          <h4 class="text-xl text-gray-600">Zamboanga Sibugay</h4>
          <h4 class="text-xl text-gray-600">Sibuguey River</h4>
          <ul class="text-gray-500 ml-4">
            <li>Bayog</li>
            <li>Diplahan</li>
            <li>Imelda</li>
            <li>Siay</li>
            <li>Payao</li>
          </ul>
          <h4 class="text-xl text-gray-600 mt-4">Kabasalan River</h4>
          <ul class="text-gray-500 ml-4">
            <li>Kabasalan</li>
          </ul>
        </v-col>
      </v-row>

      <v-divider class="border-opacity-75 my-10"></v-divider>

      <!-- Device Image Section -->
      <v-row justify="center">
        <v-img
          src="https://rdrrmc9-alerto.com/assets/images/device/alerto_dev1.jpg"
          class="rounded-lg shadow-md"
          max-width="40%"
        ></v-img>
      </v-row>
      <v-row justify="center" class="mt-2">
        <p>Image of ALeRTO v1 Device</p>
      </v-row>

      <v-divider class="border-opacity-75 my-10"></v-divider>
      
      <!-- YouTube Video Section -->
      <v-row justify="center">
        <v-col cols="12" sm="8" style="max-width: 42%">
          <div class="embed-responsive embed-responsive-16by9">
            <iframe
              src="https://www.youtube.com/embed/riJtws5mvFA?t=26s"
              frameborder="0"
              allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen
              class="rounded-lg shadow-md"
            ></iframe>
          </div>
        </v-col>
      </v-row>
    </v-sheet>
  </v-col>
</template>

<script setup>
</script>

<style scoped>
.embed-responsive {
  position: relative;
  overflow: hidden;
  padding-bottom: 56.25%;
  height: 0;
  max-width: 100%;
}
.embed-responsive iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}
</style>

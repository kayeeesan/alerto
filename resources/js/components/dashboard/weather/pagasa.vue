<template>
    <v-col cols="12" class="mt-2" style="padding: 0 !important;">
      <v-sheet
        class="pa-6 rounded-lg shadow-md elevation-3"
        style="position: relative; background: #F8FAF0; border: 1px solid #E0E0E0;"
      >
        <!-- Top Bar -->
        <span
          style="background: var(--primary-color); position: absolute; left: 0; right: 0; top: 0; border-top-left-radius: 11px; border-top-right-radius: 11px; height: 8px;"
        ></span>
  
        <!-- Title Section -->
        <v-row align="center" justify="center" class="mb-4">
          <v-icon color="primary">mdi-web</v-icon>
          <p class="text-h6 font-weight-medium text-grey-darken-2">PAGASA WEBSITE</p>
        </v-row>
  
        <!-- Divider -->
        <hr style="border: 2px solid var(--primary-color); margin: 10px 0;" />
        
        <v-img
          src="https://rdrrmc9-alerto.com/assets/images/partners/pagasa.png"
          max-height="200"
          contain
          class="mb-4"
        ></v-img>

        <p class="text-body-1 text-grey-darken-2 text-center">
          Philippine Atmospheric, Geophysical and Astronomical Services Administration
        </p>

        <hr style="border: 2px solid var(--primary-color); margin: 10px 0;" />

          <v-row justify="center" class="mt-2 p-1">
          <v-btn
            variant="tonal"
            color="primary"
            href="https://bagong.pagasa.dost.gov.ph/index.php"
            target="_blank"
            class="m-1"
          >
            Visit Bagong PAGASA
          </v-btn>
          <v-btn
            variant="tonal"
            color="primary"
            href="https://www.panahon.gov.ph/"
            target="_blank"
            class="m-1"
          >
            Panahon Website
          </v-btn>
        </v-row>

      </v-sheet>
    </v-col>
</template>
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
      <v-row justify="center" class="mb-2">
        <p class="text-h6 font-weight-medium text-grey-darken-2">
          Local ThunderStorm Warning
        </p>
      </v-row>

      <!-- Divider -->
      <hr style="border: 2px solid var(--primary-color); margin: 10px 0;" />

      <v-row justify="center" align="center" class="py-4">
      <v-icon size="48" color="grey-darken-1">mdi-message-bulleted-off</v-icon>
    </v-row>

    <v-row justify="center">
      <p class="text-body-1 text-grey-darken-2">No Event</p>
    </v-row>
    </v-sheet>
  </v-col>
</template>
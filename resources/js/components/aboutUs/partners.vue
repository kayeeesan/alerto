<script setup>
const partners = [
  { name: "RDRRMC 9", src: "https://rdrrmc9-alerto.com/assets/images/partners/rdrrmc9.png" },
  { name: "OCD 9", src: "https://rdrrmc9-alerto.com/assets/images/partners/ocd.png" },
  { name: "DOST 9", src: "https://rdrrmc9-alerto.com/assets/images/partners/dost9.png" },
  { name: "DILG 9", src: "https://rdrrmc9-alerto.com/assets/images/partners/dilg.png" },
];
</script>

<template>
  <v-container class="py-10">
    <v-sheet class="pa-8 rounded-lg elevation-3" color="#F8FAF0" border>
      <!-- Section Title -->
      <v-row justify="center" class="mb-6">
        <v-col cols="12" class="text-center">
          <h2 class="text-h4 font-weight-bold text-grey-darken-3">Partners</h2>
        </v-col>
      </v-row>
      
      <v-divider class="mb-6"></v-divider>
      
      <!-- Partners Grid -->
      <v-row justify="center" class="mt-4">
        <v-col
          v-for="(partner, index) in partners"
          :key="index"
          cols="6" sm="3"
          class="d-flex flex-column align-center text-center"
        >
          <v-avatar size="80">
            <v-img :src="partner.src" :alt="partner.name" contain></v-img>
          </v-avatar>
          <p class="mt-3 text-subtitle-1 font-weight-medium text-grey-darken-2">{{ partner.name }}</p>
        </v-col>
      </v-row>
    </v-sheet>
  </v-container>
</template>

<style scoped>
.v-container {
  margin: auto;
}
</style>

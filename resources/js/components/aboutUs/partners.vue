<script setup>
import ndrrmcLogo from '../../../img/logo/ndrrmc.svg';
import ocdLogo from '../../../img/logo/ocd.svg';
import dostLogo from '../../../img/logo/dost.svg';
import diglLogo from '../../../img/logo/dilg.svg';

const partners = [
  { name: "RDRRMC 9", src: ndrrmcLogo },
  { name: "OCD 9", src: ocdLogo },
  { name: "DOST 9", src: dostLogo },
  { name: "DILG 9", src: diglLogo },
];
</script>

<template>
  <v-col class="partners-container ml-5 mt-5">
    <v-sheet class="partners-sheet" rounded="lg">
      <!-- Header -->
      <div class="header-container">
        <div class="alert-indicator"></div>
        <h1 class="section-title">Partners</h1>
      </div>

      <v-divider class="divider"></v-divider>

      <!-- Content -->
      <div class="content-container">
        <v-row justify="center" align="center" class="partner-row">
          <v-col
            v-for="(partner, index) in partners"
            :key="index"
            cols="6" sm="3"
            class="d-flex flex-column align-center text-center"
          >
            <v-avatar size="90" class="partner-avatar">
              <v-img :src="partner.src" :alt="partner.name" contain></v-img>
            </v-avatar>
            <p class="partner-name mt-3">{{ partner.name }}</p>
          </v-col>
        </v-row>
      </div>
    </v-sheet>
  </v-col>
</template>

<style scoped>
.partners-container {
  padding: 0 !important;
}

.partners-sheet {
  padding: 0;
  border: 1px solid #E0E0E0;
  background: #F8FAF0;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
}

.header-container {
  padding: 24px 24px 16px;
  position: relative;
  background: #F5F5F5;
  border-bottom: 1px solid #E0E0E0;
}

.alert-indicator {
  position: absolute;
  left: 0;
  top: 0;
  height: 100%;
  width: 4px;
  background: var(--primary-color);
}

.section-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #333;
  text-align: center;
  line-height: 1.4;
}

.divider {
  margin: 0;
  border-color: rgba(0, 0, 0, 0.1) !important;
}

.content-container {
  padding: 24px;
}

.partner-row {
  row-gap: 32px;
}

.partner-avatar {
  border: 1px solid #E0E0E0;
  background: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.partner-name {
  font-size: 1rem;
  font-weight: 500;
  color: #555;
}
</style>

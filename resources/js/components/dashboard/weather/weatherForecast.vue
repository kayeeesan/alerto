<template>
  <v-col cols="12" class="p-0">
    <v-sheet class="pa-6 rounded-lg shadow-md forecast-container elevation-3">
      <span class="header-bar"></span>
      
      <div class="forecast-header">
        <p class="forecast-title">24-HOUR FORECAST</p>
        <p class="forecast-subtitle">Weather Updates</p>
      </div>
      
      <hr class="section-divider" />
      
      <v-row class="forecast-image-container">
        <v-img 
          src="https://src.meteopilipinas.gov.ph/repo/mtsat-colored/24hour/1-him-colored.png" 
          alt="24-Hour Weather Forecast" 
          class="forecast-image"
        ></v-img>
      </v-row>
    </v-sheet>
  </v-col>
</template>

<style scoped>
.forecast-container {
  position: relative;
  background: #F8FAF0;
  border: 1px solid #E0E0E0;
}
.header-bar {
  background: var(--primary-color);
  position: absolute;
  left: 0;
  right: 0;
  top: 0;
  border-top-left-radius: 11px;
  border-top-right-radius: 11px;
  height: 8px;
}
.forecast-header {
  text-align: center;
  margin-bottom: 10px;
}
.forecast-title {
  font-size: 1.25rem;
  font-weight: bold;
  color: var(--primary-color);
}
.forecast-subtitle {
  font-size: 1rem;
  font-weight: medium;
  color: #6B7280;
}
.section-divider {
  border: 2px solid var(--primary-color);
  margin: 10px 0;
}
.forecast-image-container {
  display: flex;
  justify-content: center;
  align-items: center;
}
.forecast-image {
  max-width: 100%;
  border-radius: 10px;
}
</style>

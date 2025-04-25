<template>
  <v-col class="threshold-container">
    <v-sheet class="threshold-sheet" rounded="lg">
      <div class="header-container">
        <div class="alert-indicator"></div>
        <h1 class="section-title">24-HOUR FORECAST</h1>
        <p class="section-subtitle">Weather Updates</p>
      </div>
      
      <v-divider class="divider"></v-divider>
      
      <div class="forecast-image-container">
        <v-img 
          src="https://src.meteopilipinas.gov.ph/repo/mtsat-colored/24hour/1-him-colored.png" 
          alt="24-Hour Weather Forecast" 
          class="forecast-image"
        ></v-img>
      </div>
    </v-sheet>
  </v-col>
</template>

<style scoped>
.threshold-container {
  padding: 0 !important;
}

.threshold-sheet {
  padding: 0;
  border: 1px solid #E0E0E0;
  background: #FFFFFF;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
}

.header-container {
  padding: 16px 24px 8px;
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
  font-size: 1.25rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 4px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.section-subtitle {
  font-size: 0.875rem;
  color: #666;
  margin: 0;
  font-weight: 500;
}

.divider {
  margin: 0;
  border-color: rgba(0, 0, 0, 0.1) !important;
}

.forecast-image-container {
  padding: 16px 24px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.forecast-image {
  max-width: 100%;
  border-radius: 8px;
  border: 1px solid #E0E0E0;
}
</style>
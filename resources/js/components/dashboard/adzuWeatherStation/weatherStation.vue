<template>
  <v-col class="threshold-container" cols="12">
    <v-sheet class="threshold-sheet" rounded="lg">
      <!-- Header Section -->
      <div class="header-container">
        <div class="alert-indicator"></div>
        <h1 class="section-title">WEATHER STATION</h1>
      </div>

      <v-divider class="divider"></v-divider>

      <!-- Content Section -->
      <div class="weather-station-content">
        <v-img
          src="https://rdrrmc9-alerto.com/assets/images/ws.jpg"
          alt="Weather Station"
          class="station-image"
          width="70%"
          cover
        ></v-img>

        <v-divider class="divider my-4"></v-divider>

        <!-- Location Info -->
        <div class="location-details">
          <p class="location-line">Fr Ernesto Carretero SJ Building</p>
          <p class="location-line">Ateneo de Zamboanga University</p>
        </div>
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

.divider {
  margin: 0;
  border-color: rgba(0, 0, 0, 0.1) !important;
}

.weather-station-content {
  padding: 16px 24px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.station-image {
  border-radius: 8px;
  border: 1px solid #E0E0E0;
  margin: 16px 0;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.location-details {
  text-align: center;
  width: 100%;
}

.location-line {
  font-size: 0.9375rem;
  color: #666;
  font-weight: 500;
  margin: 4px 0;
}
</style>

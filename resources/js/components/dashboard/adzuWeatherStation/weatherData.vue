<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const weatherData = ref(null);
const loading = ref(true);
const error = ref(null);

onMounted(async () => {
  try {
    // ✅ Call your Laravel API, not WeatherLink directly
    const res = await axios.get("/api/adzu-weather"); 
    weatherData.value = res.data;

  } catch (err) {
    error.value = "Failed to fetch weather data";
    console.error(err);
  } finally {
    loading.value = false;
  }
});
</script>


<template>
  <v-col class="threshold-container">
    <v-sheet class="threshold-sheet" rounded="lg">
      <!-- keep your header intact -->
      <div class="header-container">
        <div class="alert-indicator"></div>
        <h1 class="section-title">ADZU WEATHER DATA</h1>
        <p class="section-subtitle">Weather Updates</p>
      </div>

      <v-divider class="divider"></v-divider>

      <div class="weather-data-content">
        <div v-if="loading" class="empty-state">
          <v-progress-circular indeterminate color="primary"></v-progress-circular>
          <p class="empty-text">Loading weather data...</p>
        </div>

        <div v-else-if="error" class="empty-state">
          <v-icon size="64" color="red">mdi-alert-circle</v-icon>
          <p class="empty-text">{{ error }}</p>
        </div>

        <div v-else class="weather-info">
          <p><strong>Temperature:</strong> {{ weatherData.temp_f }} °F</p>
          <p><strong>Humidity:</strong> {{ weatherData.relative_humidity }} %</p>
          <p><strong>Wind:</strong> {{ weatherData.wind_mph }} mph</p>
          <p><strong>Rain (24hr):</strong> {{ weatherData.rain_24_in }} in</p>
          <p><strong>Pressure:</strong> {{ weatherData.pressure_in }} inHg</p>
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

.weather-data-content {
  padding: 24px;
  min-height: 200px;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  padding: 32px 0;
}

.empty-text {
  font-size: 1rem;
  color: #666;
  margin-top: 16px;
  font-weight: 500;
}
</style>
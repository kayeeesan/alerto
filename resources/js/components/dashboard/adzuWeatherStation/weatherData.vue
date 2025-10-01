<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const weatherData = ref(null);
const loading = ref(true);
const error = ref(null);

onMounted(async () => {
  try {
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
      <!-- ✅ Keep header as is -->
      <div class="header-container">
        <div class="alert-indicator"></div>
        <h1 class="section-title">ADZU WEATHER DATA</h1>
        <p class="section-subtitle">Weather Updates</p>
      </div>

      <v-divider class="divider"></v-divider>

      <div class="weather-data-content">
        <div v-if="loading" class="state-container">
          <v-progress-circular indeterminate color="primary"></v-progress-circular>
          <p class="state-text">Loading weather data...</p>
        </div>

        <div v-else-if="error" class="state-container">
          <v-icon size="64" color="red">mdi-alert-circle</v-icon>
          <p class="state-text">{{ error }}</p>
        </div>

        <div v-else class="weather-container">
          <div class="main-weather-card">
            <div class="location-info">
              <h2 class="location">{{ weatherData.location }}</h2>
              <p class="observation-time">{{ weatherData.observation_time }}</p>
            </div>
            
            <div class="primary-weather">
              <div class="temperature-display">
                <div class="temp-value">{{ weatherData.temp_f }}</div>
                <div class="temp-unit">°F</div>
              </div>
              <div class="weather-icon">
                <v-icon size="64" color="#FFA500">mdi-weather-sunny</v-icon>
              </div>
            </div>
            
            <div class="weather-description">
              <p>Feels like {{ weatherData.heat_index_f }}°F</p>
            </div>
          </div>

          <div class="secondary-metrics">
            <div class="metric-card">
              <div class="metric-icon">
                <v-icon color="#4ECDC4">mdi-water-percent</v-icon>
              </div>
              <div class="metric-data">
                <div class="metric-value">{{ weatherData.relative_humidity }}%</div>
                <div class="metric-label">Humidity</div>
              </div>
            </div>
            
            <div class="metric-card">
              <div class="metric-icon">
                <v-icon color="#45B7D1">mdi-weather-windy</v-icon>
              </div>
              <div class="metric-data">
                <div class="metric-value">{{ weatherData.wind_mph }} mph</div>
                <div class="metric-label">Wind</div>
              </div>
            </div>
            
            <div class="metric-card">
              <div class="metric-icon">
                <v-icon color="#96CEB4">mdi-weather-rainy</v-icon>
              </div>
              <div class="metric-data">
                <div class="metric-value">{{ weatherData.rain_24_in }} in</div>
                <div class="metric-label">Rain (24hr)</div>
              </div>
            </div>
            
            <div class="metric-card">
              <div class="metric-icon">
                <v-icon color="#FFA07A">mdi-gauge</v-icon>
              </div>
              <div class="metric-data">
                <div class="metric-value">{{ weatherData.pressure_in }} inHg</div>
                <div class="metric-label">Pressure</div>
              </div>
            </div>
          </div>

          <div class="credit-section">
            <div class="credit-content">
              <a href="https://www.weatherlink.com/" target="_blank" class="image-credit">
                <img src="https://www.weatherlink.com/static/img/home/davis-logo.png" alt="Davis Logo" height="30"/>
              </a>

              <p class="credit-text">
                Data provided by 
                <a :href="weatherData.credit_URL" target="_blank" class="credit-link">{{ weatherData.credit }}</a>
              </p>
            </div>
          </div>

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

/* Loading / Error states */
.state-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 32px 0;
}
.state-text {
  font-size: 1rem;
  color: #666;
  margin-top: 12px;
  font-weight: 500;
}

/* Weather Container */
.weather-container {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

/* Main Weather Card */
.main-weather-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 16px;
  padding: 24px;
  color: white;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
  position: relative;
  overflow: hidden;
}

.main-weather-card::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -50%;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  transform: scale(0);
  transition: transform 0.5s ease;
}

.main-weather-card:hover::before {
  transform: scale(1.5);
}

.location-info {
  margin-bottom: 16px;
}

.location {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 4px;
}

.observation-time {
  font-size: 0.9rem;
  opacity: 0.9;
}

.primary-weather {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.temperature-display {
  display: flex;
  align-items: flex-start;
}

.temp-value {
  font-size: 4rem;
  font-weight: 700;
  line-height: 1;
}

.temp-unit {
  font-size: 1.5rem;
  margin-top: 8px;
  margin-left: 4px;
}

.weather-description p {
  font-size: 1rem;
  opacity: 0.9;
}

/* Secondary Metrics */
.secondary-metrics {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
}

.metric-card {
  display: flex;
  align-items: center;
  padding: 16px;
  background: #f8f9fa;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.metric-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.metric-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  background: white;
  border-radius: 50%;
  margin-right: 16px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

.metric-data {
  flex: 1;
}

.metric-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 4px;
}

.metric-label {
  font-size: 0.9rem;
  color: #666;
}

/* Credit Section */
.credit-section {
  display: flex;
  justify-content: center;
  padding: 16px 0;
  border-top: 1px solid #e9ecef;
}

.credit-content {
  display: flex;
  align-items: center;
  gap: 12px;
}

.image-credit {
  display: flex;
  align-items: center;
}

.credit-text {
  font-size: 0.9rem;
  color: #666;
  margin: 0;
}

.credit-link {
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
}

.credit-link:hover {
  text-decoration: underline;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .primary-weather {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }
  
  .secondary-metrics {
    grid-template-columns: 1fr;
  }
  
  .credit-content {
    flex-direction: column;
    text-align: center;
  }
  
  .temp-value {
    font-size: 3rem;
  }
}

</style>
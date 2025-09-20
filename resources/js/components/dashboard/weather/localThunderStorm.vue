<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const advisory = ref("Loading...");

onMounted(async () => {
  try {
    const { data } = await axios.get("/api/thunderstorm-advisory");
    advisory.value = data.message;
  } catch (err) {
    advisory.value = "Failed to fetch advisory.";
  }
});
</script>

<template>
  <v-col class="threshold-container mb-4">
    <v-sheet class="threshold-sheet" rounded="lg">
      <div class="header-container">
        <div class="alert-indicator"></div>
        <h1 class="section-title">LOCAL THUNDERSTORM WARNING</h1>
      </div>
      
      <v-divider class="divider"></v-divider>
      
      <div class="warning-content">
        <v-icon size="64" color="grey-darken-1" class="warning-icon">
          mdi-weather-lightning-rainy
        </v-icon>
        <p class="warning-text">{{ advisory }}</p>
      </div>
    </v-sheet>
  </v-col>
</template>


<style scoped>
/* Same styles as localRainFall.vue */
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

.warning-content {
  padding: 32px 24px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.warning-icon {
  margin-bottom: 16px;
}

.warning-text {
  font-size: 1rem;
  color: #666;
  font-weight: 500;
  margin: 0;
}
</style>
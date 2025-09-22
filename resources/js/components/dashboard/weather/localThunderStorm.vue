<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import axios from "axios";

const advisory = ref("");
const loading = ref(true);
let refreshInterval = null;

async function refreshThunderstorm() {
  loading.value = true;
  try {
    const res = await axios.get("/api/thunderstorm-advisory");
    advisory.value = res.data.message;
  } catch (e) {
    advisory.value = "Unable to load thunderstorm advisory.";
  } finally {
    loading.value = false;
  }
}

onMounted(async () => {
  refreshThunderstorm(); // initial fetch
  refreshInterval = setInterval(refreshThunderstorm, 600000); // every 10 min
});
onBeforeUnmount(() => {
  clearInterval(refreshInterval);
});
</script>

<template>
  <v-col class="threshold-container mb-4">
    <v-sheet class="threshold-sheet" rounded="lg">
      <!-- Header -->
      <div class="header-container">
        <div class="alert-indicator"></div>
        <h1 class="section-title">LOCAL THUNDERSTORM WARNING</h1>
      </div>
      
      <v-divider class="divider"></v-divider>
      
      <!-- Loading -->
      <div class="warning-content" v-if="loading">
        <v-progress-circular indeterminate color="primary"></v-progress-circular>
        <p class="warning-text">Fetching advisory...</p>
      </div>

      <!-- Advisory -->
      <div class="warning-scroll" v-else>
        <template v-if="advisory">
          <!-- <v-icon size="48" color="grey-darken-1" class="warning-icon">
            mdi-weather-lightning-rainy
          </v-icon> -->
          <p class="warning-text">{{ advisory }}</p>
        </template>
        <template v-else>
          <v-icon size="64" color="grey-darken-1" class="warning-icon">
            mdi-message-bulleted-off
          </v-icon>
          <p class="warning-text">No Event</p>
        </template>
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
  height: 320px; /* matches Rainfall card */
  display: flex;
  flex-direction: column;
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
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.warning-scroll {
  flex: 1;
  overflow-y: auto;    /* only vertical scroll */
  overflow-x: hidden;  /* hide horizontal scroll */
  padding: 16px 20px;  /* add space around text */
  text-align: start;
}

.warning-icon {
  margin: 0 auto 12px;
  display: block;
}

.warning-text {
  font-size: 1rem;
  color: #444;
  font-weight: 500;
  margin: 0;
  white-space: pre-wrap;
  line-height: 1.5;
}
</style>

<template>
  <v-col class="threshold-container mb-4">
    <v-sheet class="threshold-sheet" rounded="lg">
      <!-- Header (unchanged) -->
      <div class="header-container">
        <div class="alert-indicator"></div>
        <h1 class="section-title">LOCAL RAINFALL WARNING</h1>
      </div>
      
      <v-divider class="divider"></v-divider>
      
      <!-- Loading state -->
      <div class="warning-content" v-if="loading">
        <v-progress-circular indeterminate color="primary"></v-progress-circular>
        <p class="warning-text">Fetching advisory...</p>
      </div>

      <!-- Advisory content -->
      <div class="warning-scroll" v-else>
        <template v-if="advisory">
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

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const advisory = ref("");
const loading = ref(true);

onMounted(async () => {
  try {
    const res = await axios.get("/api/rain-advisory");
    advisory.value = res.data.message;
  } catch (e) {
    advisory.value = "Unable to load rainfall advisory.";
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.threshold-container {
  padding: 0 !important;
}

.threshold-sheet {
  padding: 0;
  border: 1px solid #E0E0E0;
  background: #FFFFFF;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
  height: 320px; /* Fixed height for consistent card size */
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
  overflow-y: auto;       /* scroll only vertically */
  overflow-x: hidden;     /* ❌ hide bottom scroll */
  padding: 16px 20px;     /* ✅ add nice padding */
  text-align: left;
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
  white-space: pre-wrap; /* preserve line breaks */
  line-height: 1.5;      /* better readability */
}
</style>

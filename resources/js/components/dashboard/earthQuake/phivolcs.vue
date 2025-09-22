<script setup>
import { ref, onMounted, onBeforeMount } from "vue";
import axios from "axios";

const quakes = ref([]);
const loading = ref(true);
let refreshInterval = null;

async function refreshEarthquake()
{
  loading.value = true;
  try {
    const res = await axios.get("/api/earthquakes");
    quakes.value = res.data.data;
  } catch (e) {
    console.error(e);
    quakes.value = [];
  } finally {
    loading.value = false;
  }
}

onMounted(async () => {
  refreshEarthquake();
  refreshInterval = setInterval(refreshEarthquake, 600000); // every 10 min
});
onBeforeMount(() => {
  clearInterval(refreshInterval);
});
</script>

<template>
  <v-col class="threshold-container mb-4">
    <v-sheet class="threshold-sheet" rounded="lg">
      <!-- Header -->
      <div class="header-container">
        <div class="alert-indicator"></div>
        <h1 class="section-title">PHIVOLCS LATEST EARTHQUAKE INFORMATION</h1>
      </div>

      <v-divider class="divider"></v-divider>

      <!-- Intro text -->
      <div class="intro-text">
        <p>
          PHIVOLCS Earthquake Bulletins of latest seismic events in the Philippines are listed below.
          The event parameters (hypocenter, time and magnitude) are determined using incoming data
          from the Philippine Seismic Network.
        </p>
        <p>
          Philippine Standard Time (PST) is eight hours ahead of Coordinated Universal Time (UTC).
          (PST = UTC + 8H) UTC is the time standard for which the world regulates clocks and time.
        </p>
        <p>
          Earthquakes in this list with their date and time in <span class="highlight">blue</span> 
          have reported and recorded intensities. Intensity ratings are based on the PHIVOLCS Earthquake Intensity Scale.
        </p>
      </div>

      <v-divider class="divider"></v-divider>

      <!-- Loading state -->
      <div class="warning-content" v-if="loading">
        <v-progress-circular indeterminate color="primary"></v-progress-circular>
        <p class="warning-text">Fetching earthquakes...</p>
      </div>

      <!-- Earthquake table -->
      <div class="warning-scroll" v-else>
        <template v-if="quakes.length">
          <table class="quake-table">
            <thead>
              <tr>
                <th>Date - Time (PST)</th>
                <th>Latitude (ºN)</th>
                <th>Longitude (ºE)</th>
                <th>Depth (km)</th>
                <th>Mag</th>
                <th>Location</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(q, index) in quakes" :key="index">
                <td>{{ q.datetime }}</td>
                <td>{{ q.latitude }}</td>
                <td>{{ q.longitude }}</td>
                <td>{{ q.depth }}</td>
                <td><strong>M{{ q.magnitude }}</strong></td>
                <td>{{ q.location }}</td>
              </tr>
            </tbody>
          </table>
        </template>
        <template v-else>
          <v-icon size="64" color="grey-darken-1" class="warning-icon">
            mdi-earth-off
          </v-icon>
          <p class="warning-text">No Recent Earthquakes</p>
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
  height: 820px;
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

.intro-text {
  padding: 16px 20px;
  font-size: 0.9rem;
  color: #444;
  line-height: 1.4;
}

.intro-text .highlight {
  color: #1565c0;
  font-weight: 600;
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
  overflow-y: auto;
  overflow-x: hidden;
  padding: 16px 20px;
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
  text-align: center;
}

.quake-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.9rem;
}

.quake-table th {
  background: #f5f5f5;
  text-align: left;
  padding: 8px;
  border-bottom: 2px solid #ddd;
  font-weight: 600;
}

.quake-table td {
  padding: 8px;
  border-bottom: 1px solid #eee;
}

.quake-table tr:hover {
  background-color: #fafafa;
}
</style>

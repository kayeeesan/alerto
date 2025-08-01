<script setup>
import { ref, onMounted, watch } from "vue";
import useSensorsUnderAlerto from "../../../composables/sensorsUnderAlerto";
import { Line } from "vue-chartjs";
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement
} from "chart.js";

ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement);

// Composables
const {
  sensors_under_alerto,
  query,
  is_loading: alertoLoading,
  getSensorsUnderAlerto
} = useSensorsUnderAlerto();

// UI State
const options = ref([]);
const selectedOption = ref(null);
const fromDate = ref(null);
const toDate = ref(null);
const chartDataRef = ref({ labels: [], datasets: [] });

// Chart options
const chartOptions = {
  responsive: true,
  scales: {
    x: {
      title: { display: true, text: "Date/Time" }
    },
    yRain: {
      type: "linear",
      position: "left",
      title: { display: true, text: "Rain (mm)" }
    },
    yWater: {
      type: "linear",
      position: "right",
      title: { display: true, text: "Water Level (m)" },
      grid: { drawOnChartArea: false }
    }
  },
  plugins: {
    legend: {
      display: true,
      position: "bottom"
    },
    tooltip: {
      mode: "index",
      intersect: false
    }
  }
};

// Load sensors on mount
onMounted(async () => {
  await getSensorsUnderAlerto();
  populateSensorOptions();
});

watch(sensors_under_alerto, populateSensorOptions);

function populateSensorOptions() {
  options.value = sensors_under_alerto.value.map(sensor => ({
    label: sensor.name,
    value: sensor.uuid
  }));
}

// Simulated chart population based on selected sensor
function populateChart() {
  const selectedSensor = sensors_under_alerto.value.find(
    s => s.uuid === selectedOption.value?.value
  );

  if (!selectedSensor) return;

  // Simulate 3 datapoints
  const labels = ["2025-07-01 12:00", "2025-07-01 13:00", "2025-07-01 14:00"];
  const rainData = [0.02, 0.04, parseFloat(selectedSensor.device_rain_amount || 0)];
  const waterData = [0.03, 0.06, parseFloat(selectedSensor.device_water_level || 0)];

  chartDataRef.value = {
    labels,
    datasets: [
      {
        label: "Rain (mm)",
        data: rainData,
        borderColor: "#FF9800",
        backgroundColor: "rgba(255, 152, 0, 0.3)",
        yAxisID: "yRain",
        tension: 0.3
      },
      {
        label: "Water Level (m)",
        data: waterData,
        borderColor: "#2196F3",
        backgroundColor: "rgba(33, 150, 243, 0.3)",
        yAxisID: "yWater",
        tension: 0.3
      }
    ]
  };
}
</script>

<template>
  <v-col cols="12" class="p-0">
    <v-sheet class="pa-6 rounded-lg" style="border: 1px solid #E0E0E0;">
      <div class="header-accent"></div>

      <v-row class="filter-row mb-6">
        <v-col cols="12" md="4">
          <vue-multiselect
            v-model="selectedOption"
            :options="options"
            placeholder="Select a sensor"
            label="label"
            track-by="value"
          />
        </v-col>

        <v-col cols="12" md="2">
          <v-text-field
            v-model="fromDate"
            label="From date"
            type="date"
            prepend-icon="mdi-calendar"
            density="comfortable"
            variant="outlined"
            class="date-field"
            bg-color="#f5f7fa"
          />
        </v-col>

        <v-col cols="12" md="2">
          <v-text-field
            v-model="toDate"
            label="To date"
            type="date"
            prepend-icon="mdi-calendar"
            density="comfortable"
            variant="outlined"
            class="date-field"
            bg-color="#f5f7fa"
          />
        </v-col>

        <v-col cols="12" md="2" class="d-flex">
          <v-btn
            color="primary"
            class="search-btn"
            size="large"
            elevation="0"
            @click="populateChart"
          >
            <v-icon left>mdi-magnify</v-icon>
            <span>Search</span>
          </v-btn>
        </v-col>

        <v-col cols="12" md="2" class="d-flex justify-end">
          <v-btn
            color="secondary"
            variant="outlined"
            class="export-btn"
            size="large"
          >
            <v-icon left>mdi-download</v-icon>
            <span>Export</span>
          </v-btn>
        </v-col>
      </v-row>

      <v-card class="data-table-container" elevation="0">
        <Line :data="chartDataRef" :options="chartOptions" />
      </v-card>
    </v-sheet>
  </v-col>
</template>

<style scoped>
.header-accent {
  position: absolute;
  left: 0;
  right: 0;
  top: 0;
  height: 6px;
  border-top-left-radius: 11px;
  border-top-right-radius: 11px;
  background: linear-gradient(90deg, #3f51b5, #2196f3);
}

.filter-row {
  background: #ffffff;
  padding: 16px;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.date-field {
  border-radius: 8px;
}

.search-btn {
  background: linear-gradient(135deg, #3f51b5, #2196f3);
  color: white;
  font-weight: 600;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  border-radius: 8px;
  padding: 0 24px;
  height: 48px;
  box-shadow: 0 2px 4px rgba(63, 81, 181, 0.3);
  transition: all 0.3s ease;
}

.search-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(63, 81, 181, 0.3);
}

.export-btn {
  border: 1px solid #e0e0e0;
  color: #3f51b5;
  font-weight: 600;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  border-radius: 8px;
  padding: 0 24px;
  height: 48px;
  transition: all 0.3s ease;
}

.export-btn:hover {
  background-color: #f5f7fa;
  border-color: #bdbdbd;
}

.data-table-container {
  border-radius: 10px;
  overflow: hidden;
  border: 1px solid #e0e0e0;
}
</style>

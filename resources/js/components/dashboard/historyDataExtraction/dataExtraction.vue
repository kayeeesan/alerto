<script setup>
import { ref, onMounted, watch } from "vue";
import { Line } from "vue-chartjs";
import * as XLSX from 'xlsx';
import useSensorHistories from "../../../composables/SensorHistory";
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
const { sensor_histories, getSensorHistories, is_loading } = useSensorHistories();
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

onMounted(async() => {
  await getSensorHistories();
});

watch(sensor_histories, (newVal) => {
  const uniqueMap = new Map();

  newVal.forEach(item => {
    if (!uniqueMap.has(item.sensor_uuid)) {
      uniqueMap.set(item.sensor_uuid, {
        label: item.sensor_name,
        value: item.sensor_uuid
      });
    }
  });

  // Optional: Sort alphabetically by label
  options.value = Array.from(uniqueMap.values()).sort((a, b) =>
    a.label.localeCompare(b.label)
  );
});

const populateChart = () => {
  if (!selectedOption.value || !fromDate.value || !toDate.value) {
    chartDataRef.value = { labels: [], datasets: [] };
    return;
  }

  const filtered = sensor_histories.value.filter(item => {
    const recordedAt = new Date(item.recorded_at);
    return (
      item.sensor_uuid === selectedOption.value.value &&
      recordedAt >= new Date(fromDate.value) &&
      recordedAt <= new Date(toDate.value + 'T23:59:59') // include full day
    );
  });

  // Sort by recorded_at
  filtered.sort((a, b) => new Date(a.recorded_at) - new Date(b.recorded_at));

  const labels = filtered.map(item =>
    new Date(item.recorded_at).toLocaleString()
  );

  const rainData = filtered.map(item => item.device_rain_amount ?? 0);
  const waterData = filtered.map(item => item.device_water_level ?? 0);

  chartDataRef.value = {
    labels,
    datasets: [
      {
        label: "Rain (mm)",
        data: rainData,
        yAxisID: "yRain",
        borderWidth: 2,
        fill: false
      },
      {
        label: "Water Level (m)",
        data: waterData,
        yAxisID: "yWater",
        borderWidth: 2,
        fill: false
      }
    ]
  };
};

const exportToExcel = () => {
  if (!selectedOption.value || !fromDate.value || !toDate.value) {
    alert('Please select a sensor and date range first');
    return;
  }

  const filtered = sensor_histories.value.filter(item => {
    const recordedAt = new Date(item.recorded_at);
    return (
      item.sensor_uuid === selectedOption.value.value &&
      recordedAt >= new Date(fromDate.value) &&
      recordedAt <= new Date(toDate.value + 'T23:59:59')
    );
  });

  if (filtered.length === 0) {
    alert('No data availbale for export');
    return;
  }

  filtered.sort((a, b) => new Date(a.recorded_at) - new Date(b.recorded_at));

    const excelData = filtered.map(item => ({
      'Sensor Name': item.sensor_name,
      'Recorded At': new Date(item.recorded_at).toLocaleString(),
      'Rain (mm)': item.device_rain_amount ?? 0,
      'Water Level (m)': item.device_water_level ?? 0,
      'Battery Level': item.device_battery_level ?? 'N/A',
      'Signal Strength': item.device_signal_strength ?? 'N/A'
    }));

    const worksheet = XLSX.utils.json_to_sheet(excelData);

    const workbook = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(
      workbook, 
      worksheet, 
      `${selectedOption.value.label} Data`
    );

    // Generate file name
    const fileName = `SensorData_${selectedOption.value.label}_${fromDate.value}_to_${toDate.value}.xlsx`;

    // Export the file
    XLSX.writeFile(workbook, fileName);
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
            @click="exportToExcel"
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

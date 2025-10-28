<script setup>
import { ref, onMounted, watch } from "vue";
import Chart from 'chart.js/auto';
import useSensorsUnderAlerto from "../../../composables/sensorsUnderAlerto";
import useSensorsUnderPh from "../../../composables/sensorsUnderPh";

const {
  sensors_under_alerto,
  is_loading: alertoLoading,
  getSensorsUnderAlerto
} = useSensorsUnderAlerto();

const {
  sensors_under_ph,
  is_loading: phLoading,
  getSensorsUnderPh
} = useSensorsUnderPh();

const tab = ref(0);
const chartRefs = [ref(null), ref(null)];
const chartInstances = [null, null];

const statusLabels = ['NORMAL', 'ALERT', 'CRITICAL', 'WARNING'];

// Count how many sensors per status
const countStatus = (items) => {
  const counts = {
    WARNING: 0,
    ALERT: 0,
    CRITICAL: 0,
    NORMAL: 0
  };

  items.forEach(item => {
    const status = item.status?.toUpperCase();
    if (counts[status] !== undefined) {
      counts[status]++;
    }
  });

  return [counts.NORMAL, counts.WARNING, counts.ALERT, counts.CRITICAL];
};

// Format labels to include counts
const labelWithCount = (counts) =>
  statusLabels.map((label, i) => `${label} (${counts[i]})`);

// Initial chart configs (Rain first, Water second)
const chartConfigs = [
  {
    type: 'doughnut',
    data: {
      labels: [],
      datasets: [{
        label: 'Rain Status',
        data: [],
        backgroundColor: ['#6D94C5', '#f3fc47', '#f98023', '#fd361e'],
        hoverOffset: 4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '70%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            boxWidth: 12,
            padding: 20,
            font: { size: 12 }
          }
        }
      }
    }
  },
  {
    type: 'doughnut',
    data: {
      labels: [],
      datasets: [{
        label: 'Water Status',
        data: [],
        backgroundColor: ['#6D94C5', '#8ecae6', '#219ebc', '#023047'],
        hoverOffset: 4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '70%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            boxWidth: 12,
            padding: 20,
            font: { size: 12 }
          }
        }
      }
    }
  }
];

// Create chart instance
const createChart = (index) => {
  if (chartRefs[index].value && !chartInstances[index]) {
    chartInstances[index] = new Chart(chartRefs[index].value, chartConfigs[index]);
  }
};

// Update chart data and labels dynamically
const updateChart = (index, dataCounts) => {
  if (chartInstances[index]) {
    chartInstances[index].data.datasets[0].data = dataCounts;
    chartInstances[index].data.labels = labelWithCount(dataCounts);
    chartInstances[index].update();
  }
};

onMounted(async () => {
  await getSensorsUnderPh();
  await getSensorsUnderAlerto();

  // Rain chart first
  const alertoCounts = countStatus(sensors_under_alerto.value);
  const phCounts = countStatus(sensors_under_ph.value);

  chartConfigs[0].data.datasets[0].data = alertoCounts;
  chartConfigs[0].data.labels = labelWithCount(alertoCounts);

  chartConfigs[1].data.datasets[0].data = phCounts;
  chartConfigs[1].data.labels = labelWithCount(phCounts);

  createChart(0); // Create first (Rain)
});

// Create chart for other tab when selected
watch(tab, (newIndex) => {
  createChart(newIndex);
});

// Watch data for real-time updates
watch(sensors_under_alerto, (newData) => {
  const counts = countStatus(newData);
  updateChart(0, counts); // Rain
});

watch(sensors_under_ph, (newData) => {
  const counts = countStatus(newData);
  updateChart(1, counts); // Water
});
</script>

<template>
  <v-col class="threshold-container">
    <v-sheet class="threshold-sheet" rounded="lg">
      <div class="header-container">
        <div class="alert-indicator"></div>
        <h1 class="section-title">WARNING OVERVIEW</h1>
      </div>

      <v-divider class="divider"></v-divider> 

      <!-- Swap tab order -->
      <v-tabs v-model="tab" background-color="white" grow>
        <v-tab>Rain</v-tab>
        <v-tab>Water</v-tab>
      </v-tabs>

      <v-window v-model="tab">
        <v-window-item v-for="(refEl, index) in chartRefs" :key="index">
          <div class="doughnut-container">
            <canvas :ref="refEl" class="doughnut-chart"></canvas>
          </div>
        </v-window-item>
      </v-window>
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
  height: 100%;
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

.doughnut-container {
  padding: 8px 16px 16px;
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  min-height: 400px;
}

.doughnut-chart {
  width: 100% !important;
  height: 100% !important;
  max-width: 350px;
  max-height: 350px;
}
</style>

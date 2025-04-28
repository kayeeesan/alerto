<script setup>
import { ref, onMounted } from "vue";
import Chart from 'chart.js/auto';

const chartRef = ref(null);
let chartInstance = null;

const data = {
  labels: ['EVACUATION', 'WLMS', 'TANDEM'],
  datasets: [{
    label: 'My First Dataset',
    data: [300, 50, 100],
    backgroundColor: [
      'rgb(243,252,71)',
      'rgb(249,128,35)',
      'rgb(253,54,30)'
    ],
    hoverOffset: 4
  }]
};

const config = {
  type: 'doughnut',
  data: data,
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'bottom',
        labels: {
          boxWidth: 12,
          padding: 20,
          font: {
            size: 12
          }
        }
      }
    },
    cutout: '70%'
  }
};

onMounted(() => {
  if (chartRef.value) {
    chartInstance = new Chart(chartRef.value, config);
  }
});
</script>

<template>
  <v-col class="threshold-container">
    <v-sheet class="threshold-sheet" rounded="lg">
      <div class="header-container">
        <div class="alert-indicator"></div>
        <h1 class="section-title">MONITORING OVERVIEW</h1>
      </div>
      
      <v-divider class="divider"></v-divider>
      
      <div class="doughnut-container">
        <canvas ref="chartRef" class="doughnut-chart"></canvas>
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
  justify-content: center; /* center it vertically */
  align-items: center; /* center it horizontally */
  min-height: 400px; /* <-- ADD THIS */
}

.doughnut-chart {
  width: 100% !important;
  height: 100% !important;
  max-width: 350px; /* optional: limit max size */
  max-height: 350px; /* optional: limit max size */
}

</style>
<script setup>
import { ref, onMounted } from 'vue';
import Chart from 'chart.js/auto';

const chartRef = ref(null);

const labels = ref(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);

const data = ref({
  labels: labels.value,
  datasets: [
    {
      label: 'Rain Probability (%)',
      data: [80, 50, 90, 20, 70, 10, 60], 
      fill: false,
      borderColor: 'rgb(75, 192, 192)',
      tension: 0.1
    }
  ]
});

const config = ref({
  type: 'line',
  data: data.value,
  options: {
    responsive: true, 
    maintainAspectRatio: false, 
  }
});

onMounted(() => {
  new Chart(chartRef.value, config.value);
});
</script>

<template>
  <v-sheet class="pa-4 mt-4" rounded="lg" style="position: relative; width: 100%; height: 450px; background: #F8FAF0; border: 1px solid #E0E0E0;">
    <span style="background: var(--primary-color); position: absolute; left: 0; right: 0; top: 0; 
    border-top-left-radius: 11px; border-top-right-radius: 11px; height: 11px;">.</span>
    <h1>RAIN LEVEL MONITORING</h1>
    <hr style="border: 2px solid var(--primary-color); margin: 10px 0;">
    
    <v-row>
      <div style="width: 100%; height: 100%; display: flex;">
        <canvas ref="chartRef" style="width: 100%; height: 350px; "></canvas>
      </div>
    </v-row>
  </v-sheet>
</template>

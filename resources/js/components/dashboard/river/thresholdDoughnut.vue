<script setup>
import { ref, onMounted } from "vue";
import Chart from 'chart.js/auto';

// Reference for the canvas element
const chartRef = ref(null);
let chartInstance = null;

// Chart data and config
const data = {
  labels: ['EVACUATION', 'WLMS', 'TANDEM'],
  datasets: [{
    label: 'My First Dataset',
    data: [300, 50, 100],
    backgroundColor: [
      'rgb(255, 99, 132)',
      'rgb(219, 208, 55)',
      'rgb(242, 112, 5)'
    ],
    hoverOffset: 4
  }]
};

const config = {
  type: 'doughnut',
  data: data,
};

onMounted(() => {
  if (chartRef.value) {
    chartInstance = new Chart(chartRef.value, config);
  }
});
</script>
<template>
            <v-col cols="12" style="padding: 0 !important;" class="">
                <v-sheet class="pa-4 elevation-3 " rounded="lg"  style="position: relative; background: #F8FAF0; border: 1px solid #E0E0E0;">
                    <span style="background: var(--primary-color); position: absolute; left: 0; right: 0; top: 0; border-top-left-radius: 11px; border-top-right-radius: 11px; height: 11px;">.</span>
                    <div>
                        <p style="font-size: 20px;"> MONITORING OVERVIEW</p>
                    </div>
                    <hr style="border: 2px solid var(--primary-color); margin: 10px 0;">
                 
                    <div class="d-flex justify-center align-center" style="width: 100%; height: 250px">
                        <canvas ref="chartRef" ></canvas>
                    </div>
                </v-sheet>
            </v-col>
            
</template>




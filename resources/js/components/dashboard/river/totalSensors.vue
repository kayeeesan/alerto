<template>
  <v-col class="threshold-container">
    <v-sheet class="threshold-sheet" rounded="lg">
      <div class="header-container">
        <div class="alert-indicator"></div>
        <h1 class="section-title">TOTAL SENSORS</h1>
      </div>
      
      <v-divider class="divider"></v-divider>
      
      <div class="sensors-count">
        <div class="sensor-row" v-for="sensor in sensorTypes" :key="sensor.type">
          <div class="count-value">{{ getCountByType(sensor.type) }}</div>
          <div class="count-label">{{ sensor.label }}</div>
        </div>
      </div>
    </v-sheet>
  </v-col>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import useSensorsUnderAlerto from '../../../composables/sensorsUnderAlerto';
import useSensorsUnderPh from '../../../composables/sensorsUnderPh';

const {
  sensors_under_alerto,
  getSensorsUnderAlerto
} = useSensorsUnderAlerto();

const {
  sensors_under_ph,
  getSensorsUnderPh
} = useSensorsUnderPh();

const sensorTypes = [
  { type: "ARG", label: "ARG" },
  { type: "WLMS", label: "WLMS" },
  { type: "TANDEM", label: "TANDEM" }
];

onMounted(() => {
  getSensorsUnderAlerto();
  getSensorsUnderPh();
});

const all_sensors = computed(() => [
  ...sensors_under_alerto.value,
  ...sensors_under_ph.value
]);

const getCountByType = (type) => computed(() =>
  Array.isArray(all_sensors.value)
    ? all_sensors.value.filter(sensor => sensor.sensor_type === type).length
    : 0
);
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

.sensors-count {
  padding: 16px 24px;
}

.sensor-row {
  display: flex;
  align-items: center;
  margin-bottom: 12px;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.count-value {
  width: 80px;
  padding: 12px;
  background: #C99D34;
  color: white;
  font-weight: bold;
  text-align: center;
}

.count-label {
  flex: 1;
  padding: 12px;
  background: #FFF9E6;
  border: 1px solid #C99D34;
  font-weight: bold;
  text-align: center;
  border-bottom-right-radius: 11px;
  border-top-right-radius: 11px;
}
</style>
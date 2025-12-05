<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import useSensorsUnderAlerto from "../../../composables/sensorsUnderAlerto";
import useSensorsUnderPh from "../../../composables/sensorsUnderPh";

const {
  sensors_under_alerto,
  pagination,
  query,
  is_loading: alertoLoading,
  getSensorsUnderAlerto
} = useSensorsUnderAlerto();

const {
  sensors_under_ph,
  is_loading: phLoading,
  getSensorsUnderPh
} = useSensorsUnderPh();

const sensor_type = ref("ARG");
const sensor_tabs = ref([
  { id: 1, name: "ARG", value: "ARG" },
  { id: 2, name: "WLMS", value: "WLMS" },
  { id: 3, name: "TANDEM", value: "TANDEM" }
]);

const headers = [
  { key: "river.name", title: "River" },
  { key: "municipality.name", title: "Region" },
  { key: "name", title: "Sensor Name" },
  { key: "sensor_type", title: "Type" },
  { key: "status", title: "Status" },
  { key: "api_last_updated_at", title: "Last Update" },
  { key: "device_water_level", title: "Water Level" },
  { key: "total_acc", width: "8%", title: "Total Acc (mm)" },
  { key: "device_rain_amount", title: "Rain (mm)" }
];

const all_sensors = computed(() => [
  ...sensors_under_alerto.value,
  ...sensors_under_ph.value
]);

const filteredSensors = computed(() => {
  return all_sensors.value.filter(sensor => {
    const matchesType = sensor.sensor_type === sensor_type.value;
    const matchesSearch = query.search
      ? (sensor.name?.toLowerCase().includes(query.search.toLowerCase()) ||
         sensor.river?.name?.toLowerCase().includes(query.search.toLowerCase()) ||
         sensor.municipality?.name?.toLowerCase().includes(query.search.toLowerCase()))
      : true;
    return matchesType && matchesSearch;
  });
});

const isLoading = computed(() => alertoLoading.value || phLoading.value);

onMounted(async () => {
  await getSensorsUnderAlerto({ sensor_type: sensor_type.value });
  await getSensorsUnderPh({ sensor_type: sensor_type.value });
});

watch([() => sensor_type.value, () => query.search], async () => {
  await getSensorsUnderAlerto({ sensor_type: sensor_type.value, query: query.search });
  await getSensorsUnderPh({ sensor_type: sensor_type.value, query: query.search });
});

const formatDateTime = (isoString) => {
    if (!isoString) return '';

    const date = new Date(isoString);
    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    }).format(date);
};

const itemsPerPage = ref(10);

const paginatedSensors = computed(() => {
  const start = (query.value.page - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredSensors.value.slice(start, end);
});

const totalPages = computed(() => {
  return Math.ceil(filteredSensors.value.length / itemsPerPage);
});
</script>

<template>
  <v-col class="threshold-container">
    <v-sheet class="threshold-sheet" rounded="lg">
      <div class="header-container">
        <div class="alert-indicator"></div>
        <h1 class="section-title">SENSORS STATUS</h1>
      </div>
      
      <v-divider class="divider"></v-divider>
      
      <div class="sensors-content">
        <!-- Tabs -->
        <v-tabs v-model="sensor_type" class="sensor-tabs">
          <v-tab v-for="sensorTab in sensor_tabs" :key="sensorTab.id" :value="sensorTab.value">
            {{ sensorTab.name }}
          </v-tab>
        </v-tabs>

        <v-card class="sensor-card">
          <v-card-title class="search-container">
            <v-text-field
              v-model="query.search"
              append-icon="mdi-magnify"
              label="Search"
              single-line
              hide-details
              class="search-field"
            ></v-text-field>
          </v-card-title>

          <v-data-table
            :headers="headers"
            :items="filteredSensors"
            :search="query.search"
            :loading="isLoading"
            loading-text="Loading... Please wait"
            class="sensor-table"
          >
            <template #item.updated_at="{ item }">
                {{ formatDateTime(item.updated_at) }}
            </template>

            <template v-slot:item["river.name"]="{ item }">
              {{ item.river?.name || 'N/A' }}
            </template>

            <template v-slot:item["municipality.name"]="{ item }">
              {{ item.municipality?.name || 'N/A' }}
            </template>

            <template v-slot:item["updated_at"]="{ item }">
              {{ new Date(item.updated_at).toLocaleString() }}
            </template>

            <template v-slot:item["water_level"]="{ item }">
              {{ item.water_level || '0' }}
            </template>


            <template v-slot:item["rain_mm"]="{ item }">
              {{ item.rain_amount || '0' }}
            </template>

            <!-- <template v-slot:bottom>
              <div class="table-footer">
                <span>Showing {{ filteredSensors.length }} sensors</span>
              </div>
            </template> -->

            <template v-slot:bottom>
              <div class="d-flex flex-column flex-md-row justify-space-between align-center pa-4">
                <div class="text-caption text-medium-emphasis mb-2 mb-md-0">
                  <span v-if="filteredSensors.length">
                    Showing {{ (query.page - 1) * itemsPerPage + 1 }}
                    to {{ Math.min(query.page * itemsPerPage, filteredSensors.length) }}
                    of {{ filteredSensors.length }} entries
                  </span>
                  <span v-else>
                    No entries found
                  </span>
                </div>
                <v-pagination
                  v-if="filteredSensors.length"
                  v-model="query.page"
                  :length="pagination.last_page"
                  :total-visible="5"
                  density="comfortable"
                />
              </div>
            </template>

          </v-data-table>
        </v-card>
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

.sensors-content {
  padding: 16px 24px;
}

.sensor-tabs {
  margin-bottom: 16px;
}

.sensor-card {
  box-shadow: none !important;
  border: 1px solid #E0E0E0;
}

.search-container {
  padding: 16px;
}

.search-field {
  max-width: 300px;
}

.sensor-table {
  border-top: 1px solid #E0E0E0;
}

.table-footer {
  padding: 8px 16px;
  font-size: 0.875rem;
  color: #666;
}
</style>
<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import useSensorsUnderAlerto from "../../../composables/sensorsUnderAlerto";

const { sensors_under_alerto, pagination, query, is_loading, getSensorsUnderAlerto } = useSensorsUnderAlerto();

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
  { key: "", title: "Last Update" },
  { key: "rain_mm", title: "Rain (mm)" }, // Assuming "rain_mm" exists in the data
];

const filteredSensors = computed(() => {
  return sensors_under_alerto.value.filter(sensor => sensor.sensor_type === sensor_type.value);
});

// Watch to reload data on sensor type change or search
watch([() => sensor_type.value, () => query.search], async () => {
  await getSensorsUnderAlerto({ sensor_type: sensor_type.value, query: query.search });
});

// Initial load
onMounted(() => {
  getSensorsUnderAlerto({ sensor_type: sensor_type.value });
});
</script>

<template>
  <v-col cols="11.5" style="padding: 0 !important;">
    <v-sheet class="pa-4 elevation-3" rounded="lg" style="position: relative; background: #F8FAF0; border: 1px solid #E0E0E0;">
      <span style="background: var(--primary-color); position: absolute; left: 0; right: 0; top: 0; border-top-left-radius: 11px; border-top-right-radius: 11px; height: 11px;"></span>
      <div>
        <p class="text-black" style="font-size: 20px;">SENSORS STATUS</p>
      </div>
      <hr style="border: 2px solid var(--primary-color); margin: 10px 0;" />

      <!-- Tabs -->
      <v-tabs v-model="sensor_type" vertical>
        <v-tab v-for="sensorTab in sensor_tabs" :key="sensorTab.id" :value="sensorTab.value">
          {{ sensorTab.name }}
        </v-tab>
      </v-tabs>

      <v-card class="mt-4">
        <v-card-title>
          <v-text-field
            v-model="query.search"
            append-icon="mdi-magnify"
            label="Search"
            single-line
            hide-details
          ></v-text-field>
        </v-card-title>

        <v-data-table
          :headers="headers"
          :items="filteredSensors"
          :search="query.search"
          class="elevation-1 p-2"
          :loading="is_loading"
          loading-text="Loading... Please wait"
        >
          <template v-slot:item["river.name"]="{ item }">
            {{ item.river?.name || 'N/A' }}
          </template>

          <template v-slot:item["municipality.name"]="{ item }">
            {{ item.municipality?.name || 'N/A' }}
          </template>

          <template v-slot:item["updated_at"]="{ item }">
            {{ new Date(item.updated_at).toLocaleString() }}
          </template>

          <template v-slot:item["rain_mm"]="{ item }">
            {{ item.rain_mm || '0' }}
          </template>

          <template v-slot:bottom>
            <div class="m-2">
              <span style="color: gray" v-if="pagination">
                Showing {{ pagination.from }} to {{ pagination.to }} out of <b>{{ pagination.total }} records</b>
              </span>
              <div class="text-center">
                <v-pagination
                  v-model="query.page"
                  circle
                  @click="getSensorsUnderAlerto"
                ></v-pagination>
              </div>
            </div>
          </template>
        </v-data-table>
      </v-card>
    </v-sheet>
  </v-col>
</template>

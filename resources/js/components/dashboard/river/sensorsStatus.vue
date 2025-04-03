<script setup>
import { ref } from 'vue';
import useSensorsUnderAlerto from "../../../composables/sensorsUnderAlerto";

const { sensors_under_alerto, getSensorsUnderAlerto} = useSensorsUnderAlerto();

const tab = ref('ARG'); // Default tab should match one of the items
const items = ref([
  { id: 1, name: "ARG" },
  { id: 2, name: "WLMS" },
  { id: 3, name: "TANDEM" }
]);
const search = ref("");

const headers = ref([
  { key: "", title: "River" },
  { key: "", title: "Region" },
  { key: "", title: "Sensor Name" },
  { key: "",title: "Type" },
  { key: "", title: "Status" },
  { key: "", title: "Last Update" },
  { key: "", title: "Rain(mm)" },
]);


</script>
<template>
    <v-col cols="11.5" style="padding: 0 !important; ">
      <v-sheet class="pa-4 elevation-3" rounded="lg" style="position: relative; background: #F8FAF0; border: 1px solid #E0E0E0; ">
        <span style="background: var(--primary-color); position: absolute; left: 0; right: 0; top: 0; border-top-left-radius: 11px; border-top-right-radius: 11px; height: 11px;"></span>
        <div>
          <p class="text-black" style="font-size: 20px;">SENSORS STATUS</p>
        </div>
        <hr style="border: 2px solid var(--primary-color); margin: 10px 0;" />
  
        <!-- TABS -->
        <v-tabs v-model="tab">
          <v-tab v-for="item in items" :key="item" :value="item">
            {{ item.name }}
          </v-tab>
        </v-tabs>
  
        <v-tabs-window v-model="tab">
          <v-tabs-window-item v-for="item in items" :key="item" :value="item">
            <v-card flat>
  
              <v-text-field
                v-model="search"
                label="Search"
                prepend-inner-icon="mdi-magnify"
                variant="outlined"
                hide-details
                single-line
              ></v-text-field>
  
              <v-data-table :headers="headers" :items="desserts" :search="search"></v-data-table>
            </v-card>
          </v-tabs-window-item>
        </v-tabs-window>
      </v-sheet>
    </v-col>
  </template>
<style scoped>
::v-deep(th) {
  font-weight: bold;
  font-size: 16px;
  color: #3E5754;
  text-transform: uppercase;
}
.badge{
    background: lightyellow;
    padding: 10px;
    border: 1px solid #C99D34;
    border-radius: 11px;
    margin-bottom: 10px;
}
</style>

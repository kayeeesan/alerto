<script setup>
import { ref } from 'vue';

const tab = ref('ARG'); // Default tab should match one of the items
const items = ref(['ARG', 'WLMS', 'TANDEM']);
const search = ref("");

const headers = ref([
  { align: "start", key: "name", sortable: false, title: "River", class:'header' },
  { key: "Region", title: "Region" },
  { key: "SensorName", title: "Sensor Name" },
  { key: "Type",title: "Type" },
  { key: "Status", title: "Status" },
  { key: "LastUpdate", title: "Last Update" },
  { key: "Rain", title: "Rain(mm)" },
]);

const desserts = ref([
  { name: "Agusan del Norte", Region: 13, SensorName: 'CABADBARAN', Type: 'ARG', Status: 4.0,LastUpdate: 'Under Preventive Maintenance' },
  { name: "Agusan del Norte", Region: 13, SensorName: 'MAT-I Evacuation and Training Center', Type: 'ARG', Status: 4.3,LastUpdate: 'Under Preventive Maintenance' },
  { name: "Agusan del Norte", Region: 262, SensorName: 'DUGYAMAN', Type: 'ARG', Status: 6.0,LastUpdate: 'Under Preventive Maintenance' },
  { name: "Agusan del Norte", Region: 305, SensorName: 'JAGUPIT', Type: 'ARG', Status: 4.3,LastUpdate: 'Under Preventive Maintenance' },
  { name: "Bolong", Region: 356, SensorName: 'BARREDO PMS', Type: 'ARG', Status: 3.9,LastUpdate: 'Under Preventive Maintenance'},
  { name: "Cagayan Valley", Region: 375, SensorName: 'CAMALANIUGAN POLICE STATION', Type: 'ARG', Status: 0.0,LastUpdate: 'Under Preventive Maintenance' },
  { name: "Cagayan Valley", Region: 392, SensorName: 'BRGY. PINA WESTE', Type: 'ARG', Status: 0,LastUpdate: 'Under Preventive Maintenance' },
  { name: "Cagayan Valley", Region: 408, SensorName: 'LASAM MUNICIPAL HALL', Type: 'ARG', Status: 6.5,LastUpdate: 'Under Preventive Maintenance'},
  { name: "Cagayan Valley", Region: 452, SensorName: 'ALLACAPAN MUNICIPAL HALL', Type: 'ARG', Status: 4.9,LastUpdate: 'Under Preventive Maintenance'},
  { name: "Cagayan Valley", Region: 518, SensorName: 'AMULUNG MUNICIPAL DISASTER OPERATION CENTER', Type: 'ARG', Status: 7,LastUpdate: 'Under Preventive Maintenance' },
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
            {{ item }}
          </v-tab>
        </v-tabs>
  
        <v-tabs-window v-model="tab">
          <v-tabs-window-item v-for="item in items" :key="item" :value="item">
            <v-card flat>
              <!-- <div class="d-flex justify-center align-center" style="color: black;">
                <p class="badge">AUTOMATED RAIN GAUGE</p>
              </div> -->
  
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

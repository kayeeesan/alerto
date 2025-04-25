<template>
  <v-col cols="12" class="p-0">
    <v-sheet class="pa-6 rounded-lg" style="border: 1px solid #E0E0E0;">
      <!-- Header accent bar with gradient -->
      <div class="header-accent"></div>
      
      <!-- Search and filter row -->
      <v-row class="filter-row mb-6">
        <v-col cols="12" md="4">
          <v-text-field
            v-model="search"
            label="Search records"
            prepend-inner-icon="mdi-magnify"
            variant="outlined"
            hide-details
            single-line
            density="comfortable"
            class="search-field"
            bg-color="#f5f7fa"
          ></v-text-field>
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
          ></v-text-field>
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
          ></v-text-field>
        </v-col>
        
        <v-col cols="12" md="2" class="d-flex">
          <v-btn
            color="primary"
            class="search-btn"
            size="large"
            elevation="0"
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
          >
            <v-icon left>mdi-download</v-icon>
            <span>Export</span>
          </v-btn>
        </v-col>
      </v-row>
  
      <!-- Data table container -->
      <v-card class="data-table-container" elevation="0">
        <v-data-table
          :headers="headers"
          :items="items"
          :search="search"
          class="elevation-1"
          :header-props="{ style: 'background-color: #f8f9fa' }"
        >
          <!-- Custom header styling -->
          <template v-slot:header="{ props }">
            <thead class="v-data-table-header">
              <tr>
                <th v-for="header in props.headers" :key="header.title">
                  <span class="header-text">{{ header.title }}</span>
                </th>
              </tr>
            </thead>
          </template>
          
          <!-- Custom row styling -->
          <template v-slot:item="{ item }">
            <tr class="data-table-row">
              <td v-for="header in headers" :key="header.value">
                <span class="cell-content">{{ item[header.value] }}</span>
              </td>
            </tr>
          </template>
        </v-data-table>
      </v-card>
    </v-sheet>
  </v-col>
</template>

<script setup>
import { ref } from "vue";

const search = ref("");
const fromDate = ref(null);
const toDate = ref(null);

// Sample headers and data - replace with your actual data
const headers = [
  { title: 'ID', value: 'id', align: 'start' },
  { title: 'NAME', value: 'name' },
  { title: 'DATE', value: 'date' },
  { title: 'STATUS', value: 'status' },
  { title: 'VALUE', value: 'value' },
];

const items = [

];
</script>

<style scoped>
/* Header accent */
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

/* Filter row styling */
.filter-row {
  background: #ffffff;
  padding: 16px;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.search-field, .date-field {
  border-radius: 8px;
}

.search-field :deep(.v-field__outline) {
  color: #e0e0e0;
}

.search-field :deep(.v-field__outline:hover) {
  color: #bdbdbd;
}

/* Button styling */
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

/* Data table container */
.data-table-container {
  border-radius: 10px;
  overflow: hidden;
  border: 1px solid #e0e0e0;
}

/* Table header styling */
.v-data-table-header {
  background-color: #f8f9fa !important;
}

.header-text {
  font-weight: 700;
  color: #37474f;
  text-transform: uppercase;
  font-size: 0.8rem;
  letter-spacing: 0.5px;
}

/* Table row styling */
.data-table-row {
  transition: background-color 0.2s ease;
}

.data-table-row:hover {
  background-color: #f5f7fa !important;
}

.cell-content {
  font-weight: 500;
  color: #455a64;
}

/* Status badges - example styling */
:deep(.status-active) {
  background-color: #e8f5e9;
  color: #2e7d32;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
}

:deep(.status-inactive) {
  background-color: #ffebee;
  color: #c62828;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
}

:deep(.status-pending) {
  background-color: #fff8e1;
  color: #ff8f00;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
}
</style>
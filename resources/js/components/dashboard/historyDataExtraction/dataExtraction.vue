<template>

<v-col cols="12" class="p-0">
      <v-sheet
        class="pa-6 rounded-lg shadow-md"
        style="position: relative; background: #F8FAF0; border: 1px solid #E0E0E0;"
      >
    
        <span
          style="background: var(--primary-color); position: absolute; left: 0; right: 0; top: 0; border-top-left-radius: 11px; border-top-right-radius: 11px; height: 8px;"
        ></span>
  
       
        <v-row class="p-4">
       
            <v-col cols="12" md="3">
                <v-text-field
                    v-model="search"
                    label="Search"
                    prepend-inner-icon="mdi-magnify"
                    variant="outlined"
                    hide-details
                    single-line
            ></v-text-field>
            </v-col>
    
           
            <v-col cols="12" md="2">
            <v-text-field
                v-model="fromDate"
                label="From"
                type="date"
                prepend-icon="mdi-calendar"
                density="comfortable"
                variant="outlined"
                class="rounded-lg"
            ></v-text-field>
            </v-col>
    
           
            <v-col cols="12" md="2">
            <v-text-field
                v-model="toDate"
                label="To"
                type="date"
                prepend-icon="mdi-calendar"
                density="comfortable"
                variant="outlined"
                class="rounded-lg"
            ></v-text-field>
            </v-col>
  
       
        <v-col cols="12" md="2" class="d-flex justify-start mb-5">
          <v-btn
            color="primary"
            class="px-6 py-3 rounded-lg text-white text-uppercase font-weight-bold"
            size="large"
            elevation="2"
            @click="filterResults"
          >
            <v-icon start>mdi-magnify</v-icon>
            Search
          </v-btn>
        </v-col>
      </v-row>
  
        <v-row style="background: #F8FAF0; border: 1px solid #E0E0E0; border-radius: 11px;">
            <v-data-table :headers="headers" :items="filteredDesserts"></v-data-table>
        </v-row>

      </v-sheet>
    </v-col>
  
  </template>
  
  <script setup>
  import { ref, computed } from "vue";
  
  const search = ref("");
  const fromDate = ref(null);
  const toDate = ref(null);
  
  const headers = [
    { align: "start", key: "name", sortable: false, title: "Dessert (100g serving)" },
    { key: "calories", title: "Calories" },
    { key: "fat", title: "Fat (g)" },
    { key: "carbs", title: "Carbs (g)" },
    { key: "protein", title: "Protein (g)" },
    { key: "iron", title: "Iron (%)" },
  ];
  
  const desserts = ref([
    { name: "Frozen Yogurt", calories: 159, fat: 6.0, carbs: 24, protein: 4.0, iron: 1 },
 
  ]);
  
  const filteredDesserts = computed(() => {
    return desserts.value.filter((item) => {
      const searchTerm = search.value.toLowerCase();
      return (
        item.name.toLowerCase().includes(searchTerm) ||
        item.calories.toString().includes(searchTerm)
      );
    });
  });
  
  function filterResults() {
    console.log("Filtering data...");
  }
  </script>
  
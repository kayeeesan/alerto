<script setup>
// import { defineProps, defineEmits } from "vue";

const props = defineProps(["drawer"]);
const emit = defineEmits();

const toggleDrawer = (val) => {
  emit("update:drawer", val);
};

  // Dashboard items
  const itemsInDashboard = [
  { title: "River Status", icon: "mdi-wave", route: "/river-status" },
    { title: "Weather Updates", icon: "mdi-weather-cloudy", route: "/weather-updates" },
    { title: "AdZU Weather Station", icon: "mdi-weather-sunny", route: "/adzu-weather" },
    { title: "Earthquake Bulletin", icon: "mdi-earth", route: "earthquake" },
    { title: "Visualization Map", icon: "mdi-map", route: "/visualization-map" },
    { title: "Historical Data Extraction", icon: "mdi-database", route: "/history-data-extraction" },
  ];
  
  // Other menu items
  const items = [
  { title: "About Us", icon: "mdi-information", route: "/about-us" },
  { title: "Contact Us", icon: "mdi-phone", route: "/contact-us" }
  ];
  
  // Libraries
  const libraries = [
  { title: "General Actions",icon: "mdi-cog",  route: "" },
    { title: "Threshold",icon: "mdi-arrow-split-horizontal",  route: "/thresholds" },
    { title: "Province", icon: "mdi-city",  route: "/provinces" },
    { title: "Municipality", icon: "mdi-city",  route: "/municipalities" },
    { title: "Rivers", icon: "mdi-waves",  route: "/rivers" },
    { title: "Sensors under ALerTO", icon: "mdi-signal-variant",  route: "/sensors-under-alerto" },
    { title: "Sensors in PH", icon: "mdi-signal-variant",  route: "" },
    { title: "Mobile Prefix", icon: "mdi-cellphone",  route: "" },
    { title: "Recipients Data", icon: "mdi-account-circle-outline",  route: "/recipients-data" },
    { title: "User Restrictions", icon: "mdi-account-cog",  route: "" },

  ];
</script>

<template>
  <v-navigation-drawer
    :model-value="drawer"
    @update:model-value="toggleDrawer"
    width="350"
    style="background: #003092;"
  >
    <v-list style="padding: 0 !important;">
      <v-sheet
        class="d-flex flex-row align-center"
        style="padding: 15px; background: #001A6E;"
        width="100%"
      >
        <RouterLink to="/" class="sidebar-logo">
          <v-avatar
            size="64"
            image="https://rdrrmc9-alerto.com/assets/images/logo3.png"
            class="mr-3"
          ></v-avatar>
          <span class="font-weight-bold" style="font-size: 2em; color: white;">ALERTO</span>
        </RouterLink>
      </v-sheet>
    </v-list>
    <v-list nav dense>
        <v-list-group>
          <template v-slot:activator="{ props }">
            <v-list-item v-bind="props" class="sidebar-item" :to="'/'">
              <v-icon class="sidebar-icon mr-2" style="background: #001A6E; color: #fff; height: 40px; width: 40px; border-radius: 99px;">mdi-view-dashboard</v-icon>
              <span class="sidebar-text" style="color: white;">Dashboard</span>
            </v-list-item>
          </template>
  
          <v-list-item
            v-for="item in itemsInDashboard"
            :key="item.title"
            :to="item.route"
            class="sidebar-subitem"
            link
          >
            <v-icon class="sidebar-icon mr-3" style="background: #001A6E; color: #fff; height: 40px; width: 40px; border-radius: 99px;">{{ item.icon }}</v-icon>
            <span class="sidebar-text" style="color: white;">{{ item.title }}</span>
          </v-list-item>
        </v-list-group>
        <v-list-item
          v-for="item in items"
          :key="item.title"
          :to="item.route"
          class="sidebar-item"
          link
        >
          <v-icon class="sidebar-icon mr-3" style="background: #001A6E; color: #fff; height: 40px; width: 40px; border-radius: 99px;">{{ item.icon }}</v-icon>
          <span class="sidebar-text" style="color: white;">{{ item.title }}</span>
        </v-list-item>
  
        <v-list-group>
          <template v-slot:activator="{ props }">
            <v-list-item v-bind="props" class="sidebar-item">
              <v-icon class="sidebar-icon mr-3" style="background: #001A6E; color: #fff; height: 40px; width: 40px; border-radius: 99px;">mdi-folder</v-icon>
              <span class="sidebar-text" style="color: white;">Libraries</span>
            </v-list-item>
          </template>
  
          <v-list-item
            v-for="item in libraries"
            :key="item.title"
            :to="item.route"
            class="sidebar-subitem"
            link
          >
            <v-icon class="sidebar-icon mr-3" style="background: #001A6E; color: #fff; height: 40px; width: 40px; border-radius: 99px;">{{ item.icon }}</v-icon>
            <span class="sidebar-text" style="color: white;">{{ item.title }}</span>
          </v-list-item>
        </v-list-group>
      </v-list>

      <v-footer class="d-flex flex-column align-center" style="background: #001A6E; padding: 10px; color: white;">
      <p class="text-center mb-2">Partners: Region 9</p>
      <v-row class="d-flex justify-center" no-gutters>
        <v-col cols="3" class="d-flex justify-center">
          <img src="https://rdrrmc9-alerto.com/assets/images/partners/rdrrmc9.png" height="40" contain alt="">
        </v-col>
        <v-col cols="3" class="d-flex justify-center">
          <img src="https://rdrrmc9-alerto.com/assets/images/partners/ocd.png" height="40" contain alt="">
        </v-col>
        <v-col cols="3" class="d-flex justify-center">
          <img src="https://rdrrmc9-alerto.com/assets/images/partners/dost9.png" height="40" contain alt="">
        </v-col>
        <v-col cols="3" class="d-flex justify-center">
          <img src="https://rdrrmc9-alerto.com/assets/images/partners/dilg.png" height="40" contain alt="">
        </v-col>
      </v-row>
    </v-footer>
  </v-navigation-drawer>
</template>


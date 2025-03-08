<script setup>
import { ref, watch } from "vue";

const props = defineProps({
    drawer: {
        type: Boolean,
        default: null
    },
}); 

  // Dashboard items
  const itemsInDashboard = [
  { title: "River Status", icon: "mdi-wave", route: "/river-status" },
    { title: "Weather Updates", icon: "mdi-weather-cloudy", route: "/weather-updates" },
    { title: "AdZU Weather Station", icon: "mdi-weather-sunny", route: "/adzu-weather" },
    { title: "Earthquake Bulletin", icon: "mdi-earth", route: "earthquake" },
    { title: "Visualization Map", icon: "mdi-map", route: "/visualization-map" },
    { title: "Historical Data Extraction", icon: "mdi-database", route: "/history-data-extraction" },
  ];

  const items = [
  { title: "About Us", icon: "mdi-information", route: "/about-us" },
  { title: "Contact Us", icon: "mdi-phone", route: "/contact-us" },
  ];

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
    { title: 'Role', route: "/roles" },
    { title: 'Accounts', route: "/users" },
];

const rail = ref(true);

watch(
    () => props.drawer,
    (value)  => {
        rail.value = value;
        console.log(value);
        
    }
);
</script>
<template>
    <v-navigation-drawer v-model="rail" app style="background: #003092; color: white;"  width="350">
        <v-sheet
                class="d-flex flex-row align-center"
                style="padding: 15px; background: #001A6E;"
                width="100%"
                >
                    <RouterLink to="/home" class="sidebar-logo">
                    <v-avatar
                        size="64"
                        image="https://rdrrmc9-alerto.com/assets/images/logo3.png"
                        class="mr-3"
                    ></v-avatar>
                    <span class="font-weight-bold" style="font-size: 2em; color: white;">ALERTO</span>
                </RouterLink>
            </v-sheet>
        <v-list>
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
                    class="sidebar-subitem pt-2"
                    link
                    style="padding-left: 30px !important;"
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
            <v-list-group value="Actions">
                <template v-slot:activator="{ props }">
                    <v-list-item
                        prepend-icon="mdi-format-list-bulleted-type"
                        v-bind="props"
                        title="Libraries"
                    ></v-list-item>
                </template>

                <v-list-item
                    v-for="library in libraries"
                    :title="library.title"
                    :to="library.route"
                ></v-list-item>
            </v-list-group>
        </v-list>
      </v-navigation-drawer>
</template>
<style>
.v-navigation-drawer--rail.v-navigation-drawer--expand-on-hover:not(.v-navigation-drawer--is-hovering) .v-list-item .v-avatar, .v-navigation-drawer--rail:not(.v-navigation-drawer--expand-on-hover) .v-list-item .v-avatar{
    width: 38px;
    height: 38px;
}
.nav-header {
    margin-top: 10px
}
.nav-header.v-list-item--nav .v-list-item-title {
    font-size: 15px;
    font-weight: bold;
}
</style>
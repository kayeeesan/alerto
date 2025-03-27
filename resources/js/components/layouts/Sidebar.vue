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
    { title: "River Status", icon: "mdi-wave", route: "/home/river-status" },
    { title: "Weather Updates", icon: "mdi-weather-cloudy", route: "/home/weather-updates" },
    { title: "AdZU Weather Station", icon: "mdi-weather-sunny", route: "/home/adzu-weather" },
    { title: "Earthquake Bulletin", icon: "mdi-earth", route: "/home/earthquake" },
    { title: "Visualization Map", icon: "mdi-map", route: "/home/visualization-map" },
    { title: "Historical Data Extraction", icon: "mdi-database", route: "/home/history-data-extraction" },
];

// Items under general settings (About Us, Contact Us, etc.)
const items = [
    { title: "About Us", icon: "mdi-information", route: "/home/about-us" },
    { title: "Contact Us", icon: "mdi-phone", route: "/home/contact-us" },
];

// Libraries section
const libraries = [
    { title: "General Actions", icon: "mdi-cog", route: "/home/responses" },
    { title: "Threshold", icon: "mdi-arrow-split-horizontal", route: "/home/thresholds" },
    { title: "Region", icon: "mdi-city", route: "/home/regions" },
    { title: "Province", icon: "mdi-city", route: "/home/provinces" },
    { title: "Municipality", icon: "mdi-city", route: "/home/municipalities" },
    { title: "Rivers", icon: "mdi-waves", route: "/home/rivers" },
    { title: "Sensors under ALerTO", icon: "mdi-signal-variant", route: "/home/sensors-under-alerto" },
    { title: "Sensors in PH", icon: "mdi-signal-variant", route: "/home/sensors-under-ph" },
    { title: "Mobile Prefix", icon: "mdi-cellphone", route: "" },
    { title: "User Restrictions", icon: "mdi-account-cog", route: "" },
    { title: "Role", icon: "mdi-account", route: "/home/roles" },
    { title: "Recipients Data", icon: "mdi-account-circle-outline", route: "/home/staffs" },
    { title: "Accounts", icon: "mdi-account-circle-outline", route: "/home/users" },
];


// Pendings item (should be the last item)
const alerts = [
    { title: "User Settings", icon: "mdi-cog", route: "/home/alerts" },
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
    <v-navigation-drawer v-model="rail" app style="background: #003092; color: white;" width="350">
        <v-sheet
            class="d-flex flex-row align-center"
            style="padding: 15px; background: #001A6E;"
            width="100%"
        >
            <RouterLink to="/home" class="sidebar-logo d-flex align-center" style="text-decoration: none;">
                <v-avatar
                    size="64"
                    image="https://rdrrmc9-alerto.com/assets/images/logo3.png"
                    class="mr-3"
                ></v-avatar>
                <span class="font-weight-bold" style="font-size: 2em; color: white;">ALERTO</span>
            </RouterLink>
        </v-sheet>

        <v-list>
            <!-- Dashboard Section -->
            <v-list-group>
                <template v-slot:activator="{ props }">
                    <v-list-item v-bind="props" class="sidebar-item" :to="'/home'">
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
                    style="padding-left: 40px !important;"
                >
                    <v-icon class="sidebar-icon mr-3" style="background: #001A6E; color: #fff; height: 40px; width: 40px; border-radius: 99px;">{{ item.icon }}</v-icon>
                    <span class="sidebar-text" style="color: white;">{{ item.title }}</span>
                </v-list-item>
            </v-list-group>

            <!-- About Us and Contact Us Section (Separate from User Settings) -->
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

            <!-- Libraries Section -->
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
                    style="padding-left: 40px !important;"
                >
                    <v-icon class="sidebar-icon mr-3" style="background: #001A6E; color: #fff; height: 40px; width: 40px; border-radius: 99px;">{{ item.icon }}</v-icon>
                    <span class="sidebar-text" style="color: white;">{{ item.title }}</span>
                </v-list-item>
            </v-list-group>

            <v-list-item
                v-for="item in alerts"
                :key="item.title"
                :to="item.route"
                class="sidebar-item"
                link
            >
                <v-icon class="sidebar-icon mr-3" style="background: #001A6E; color: #fff; height: 40px; width: 40px; border-radius: 99px;">{{ item.icon }}</v-icon>
                <span class="sidebar-text" style="color: white;">{{ item.title }}</span>
            </v-list-item>

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
        </v-list>
    </v-navigation-drawer>
</template>

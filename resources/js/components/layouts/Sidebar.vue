<script setup>
import { ref, watch } from "vue";
import store from "@/store";

const props = defineProps({
  drawer: {
    type: Boolean,
    default: null,
  },
});

const user = store.state.auth.user;

const hasRole = (roleSlug) => {
  return user?.roles?.some((role) => role.slug === roleSlug);
};

const itemsInDashboard = [
  { title: "River Status", icon: "mdi-wave", route: "/home/river-status" },
  { title: "Weather Updates", icon: "mdi-weather-cloudy", route: "/home/weather-updates" },
  { title: "AdZU Weather Station", icon: "mdi-weather-sunny", route: "/home/adzu-weather" },
  { title: "Earthquake Bulletin", icon: "mdi-earth", route: "/home/earthquake" },
  { title: "Visualization Map", icon: "mdi-map", route: "/home/visualization-map" },
  { title: "Historical Data Extraction", icon: "mdi-database", route: "/home/history-data-extraction" },
];

const items = [
  { title: "About Us", icon: "mdi-information-outline", route: "/home/about-us" },
  { title: "Contact Us", icon: "mdi-email-outline", route: "/home/contact-us" },
];

const libraries = [
  { title: "General Actions", icon: "mdi-cog-outline", route: "/home/responses" },
  { title: "Threshold", icon: "mdi-arrow-split-horizontal", route: "/home/thresholds" },
  { title: "Region", icon: "mdi-map-outline", route: "/home/regions" },
  { title: "Province", icon: "mdi-city", route: "/home/provinces" },
  { title: "Municipality", icon: "mdi-home-city", route: "/home/municipalities" },
  { title: "Rivers", icon: "mdi-waves", route: "/home/rivers" },
  { title: "Sensors under ALerTO", icon: "mdi-signal", route: "/home/sensors-under-alerto" },
  { title: "Sensors in PH", icon: "mdi-signal-variant", route: "/home/sensors-under-ph" },
  // { title: "Mobile Prefix", icon: "mdi-cellphone", route: "" },
  // { title: "User Restrictions", icon: "mdi-account-cog", route: "" },
  { title: "Role", icon: "mdi-account-group", route: "/home/roles" },
  { title: "Recipients Data", icon: "mdi-account-details", route: "/home/staffs" },
  { title: "Accounts", icon: "mdi-account-box-multiple", route: "/home/users" },
];

const alerts = [
  { title: "Pending", icon: "mdi-cog-outline", route: "/home/alerts-pending" },
  { title: "Responded", icon: "mdi-cog-outline", route: "/home/alerts-responded" },
  { title: "Expired", icon: "mdi-cog-outline", route: "/home/alerts-expired" },
];

const rail = ref(true);

watch(
  () => props.drawer,
  (value) => {
    rail.value = value;
  }
);
</script>

<template>
  <v-navigation-drawer
    v-model="rail"
    app
    style="background: #003092; color: white"
    width="300"
    class="sidebar"
  >
    <!-- Header -->
    <div class="sidebar-header">
      <RouterLink to="/home" class="sidebar-logo">
        <v-avatar
          size="64"
          image="https://rdrrmc9-alerto.com/assets/images/logo3.png"
          class="logo-avatar"
        ></v-avatar>
        <span class="logo-text">ALERTO</span>
      </RouterLink>
    </div>

    <!-- Menu Items -->
    <v-list class="sidebar-list">
      <v-list-group value="Dashboard" class="sidebar-group">
        <template v-slot:activator="{ props }">
          <v-list-item
            v-bind="props"
            class="sidebar-item"
            :to="'/home'"
            prepend-icon="mdi-view-dashboard"
          >
            <template v-slot:prepend>
              <v-icon class="sidebar-icon"></v-icon>
            </template>
            <span class="sidebar-text">Dashboard</span>
          </v-list-item>
        </template>

        <v-list-item
          v-for="item in itemsInDashboard"
          :key="item.title"
          :to="item.route"
          class="sidebar-subitem"
          link
        >
          <template v-slot:prepend>
            <v-icon class="subitem-icon">{{ item.icon }}</v-icon>
          </template>
          <span class="sidebar-text">{{ item.title }}</span>
        </v-list-item>
      </v-list-group>

      <v-list-item
        v-for="item in items"
        :key="item.title"
        :to="item.route"
        class="sidebar-item"
        link
        :prepend-icon="item.icon"
      >
        <template v-slot:prepend>
          <v-icon class="sidebar-icon"></v-icon>
        </template>
        <span class="sidebar-text">{{ item.title }}</span>
      </v-list-item>

      <v-list-group
        v-if="hasRole('administrator')"
        value="Libraries"
        class="sidebar-group"
      >
        <template v-slot:activator="{ props }">
          <v-list-item
            v-bind="props"
            class="sidebar-item"
            prepend-icon="mdi-folder-outline"
          >
            <template v-slot:prepend>
              <v-icon class="sidebar-icon"></v-icon>
            </template>
            <span class="sidebar-text">Libraries</span>
          </v-list-item>
        </template>

        <v-list-item
          v-for="item in libraries"
          :key="item.title"
          :to="item.route"
          class="sidebar-subitem"
          link
        >
          <template v-slot:prepend>
            <v-icon class="subitem-icon">{{ item.icon }}</v-icon>
          </template>
          <span class="sidebar-text">{{ item.title }}</span>
        </v-list-item>
      </v-list-group>

       <v-list-group
        v-if="hasRole('administrator')"
        value="Alerts"
        class="sidebar-group"
      >
        <template v-slot:activator="{ props }">
          <v-list-item
            v-bind="props"
            class="sidebar-item"
            prepend-icon="mdi-folder-outline"
          >
            <template v-slot:prepend>
              <v-icon class="sidebar-icon"></v-icon>
            </template>
            <span class="sidebar-text">Monitoring</span>
          </v-list-item>
        </template>

        <v-list-item
          v-for="item in alerts"
          :key="item.title"
          :to="item.route"
          class="sidebar-subitem"
          link
        >
          <template v-slot:prepend>
            <v-icon class="subitem-icon">{{ item.icon }}</v-icon>
          </template>
          <span class="sidebar-text">{{ item.title }}</span>
        </v-list-item>
      </v-list-group>
     
    </v-list>

    <!-- Footer with Logos -->
    <div class="sidebar-footer">
      <p class="footer-text">Partners: Region 9</p>
      <div class="partner-logos">
        <img src="https://rdrrmc9-alerto.com/assets/images/partners/rdrrmc9.png" alt="RDRRMC9" />
        <img src="https://rdrrmc9-alerto.com/assets/images/partners/ocd.png" alt="OCD" />
        <img src="https://rdrrmc9-alerto.com/assets/images/partners/dost9.png" alt="DOST9" />
        <img src="https://rdrrmc9-alerto.com/assets/images/partners/dilg.png" alt="DILG" />
      </div>
    </div>
  </v-navigation-drawer>
</template>

<style scoped>
.sidebar {
  background: linear-gradient(180deg, #003092 0%, #001a6e 100%);
  border-right: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-header {
  padding: 16px;
  background: #001a6e;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-logo {
  display: flex;
  align-items: center;
  text-decoration: none;
  gap: 12px;
}

.logo-avatar {
  border: 2px solid rgba(255, 255, 255, 0.2);
}

.logo-text {
  font-size: 1.5rem;
  font-weight: 700;
  color: white;
  letter-spacing: 1px;
}

.sidebar-list {
  background: transparent;
  padding: 8px 0;
}

.sidebar-item {
  color: white;
  margin: 4px 8px;
  border-radius: 8px;
  min-height: 48px;
}

.sidebar-item:hover {
  background: rgba(255, 255, 255, 0.1);
}

.sidebar-item.v-list-item--active {
  background: rgba(255, 255, 255, 0.2);
}

.sidebar-icon {
  color: white;
  margin-right: 16px;
}

.sidebar-text {
  font-size: 0.9375rem;
  font-weight: 500;
  letter-spacing: 0.5px;
}

.sidebar-group :deep(.v-list-group__items) {
  background: rgba(0, 0, 0, 0.1);
}

.sidebar-subitem {
  color: white;
  padding-left: 56px !important;
  min-height: 40px;
}

.sidebar-subitem:hover {
  background: rgba(255, 255, 255, 0.05);
}

.subitem-icon {
  font-size: 1.25rem;
  color: rgba(255, 255, 255, 0.7);
}

.sidebar-footer {
  padding: 16px;
  background: #001a6e;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  margin-top: auto;
}

.footer-text {
  color: rgba(255, 255, 255, 0.8);
  text-align: center;
  margin-bottom: 12px;
  font-size: 0.875rem;
}

.partner-logos {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 8px;
  align-items: center;
}

.partner-logos img {
  max-width: 100%;
  height: auto;
  max-height: 40px;
  object-fit: contain;
  opacity: 0.8;
}

.partner-logos img:hover {
  opacity: 1;
}
</style>

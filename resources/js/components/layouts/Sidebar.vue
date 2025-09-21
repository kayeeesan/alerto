<script setup>
import { ref, watch, computed } from "vue";
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
  // { title: "Earthquake Bulletin", icon: "mdi-earth", route: "/home/earthquake" },
  // { title: "Visualization Map", icon: "mdi-map", route: "/home/visualization-map" },
  // { title: "Historical Data Extraction", icon: "mdi-database", route: "/home/history-data-extraction" },
];

const items = [
  { title: "About Us", icon: "mdi-information-outline", route: "/home/about-us" },
  { title: "Contact Us", icon: "mdi-email-outline", route: "/home/contact-us" },
];

const libraries = computed(() => {
  if (hasRole('administrator')) {
      return [
        { title: "General Actions", icon: "mdi-cog-outline", route: "/home/responses" },
        { title: "Threshold", icon: "mdi-arrow-split-horizontal", route: "/home/thresholds" },
        { title: "Region", icon: "mdi-map-outline", route: "/home/regions" },
        { title: "Province", icon: "mdi-city", route: "/home/provinces" },
        { title: "Municipality", icon: "mdi-home-city", route: "/home/municipalities" },
        { title: "Rivers", icon: "mdi-waves", route: "/home/rivers" },
        { title: "Sensors under ALerTO", icon: "mdi-signal", route: "/home/sensors-under-alerto" },
        { title: "Sensors in PH", icon: "mdi-signal-variant", route: "/home/sensors-under-ph" },
        { title: "Role", icon: "mdi-account-group", route: "/home/roles" },
        { title: "Recipients Data", icon: "mdi-account-details", route: "/home/staffs" },
        { title: "Accounts", icon: "mdi-account-box-multiple", route: "/home/users" },
      ];
    } else {
      return [
        { title: "Sensors under ALerTO", icon: "mdi-signal", route: "/home/sensors-under-alerto" }
      ];
    }
  });

const alerts = [
  { title: "Pending", icon: "mdi-timer-sand", route: "/home/alerts-pending" },
  { title: "Responded", icon: "mdi-check-circle-outline", route: "/home/alerts-responded" },
  { title: "Expired", icon: "mdi-alert-circle-outline", route: "/home/alerts-expired" },
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
    style="background: linear-gradient(180deg, #003092 0%, #001a6e 100%); color: white"
    width="300"
    class="sidebar"
  >
    <!-- Header -->
    <div class="sidebar-header">
      <RouterLink to="/home" class="sidebar-logo">
        <div class="logo-container">
          <v-avatar
            size="60"
            image="https://rdrrmc9-alerto.com/assets/images/logo3.png"
            class="logo-avatar"
          ></v-avatar>
          <span class="logo-text">ALERTO</span>
        </div>
      </RouterLink>
    </div>

    <!-- Menu Items -->
    <v-list class="sidebar-list" density="compact">
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
        v-if="hasRole('project-staff') || hasRole('administrator')"
        value="Alerts"
        class="sidebar-group"
      >
        <template v-slot:activator="{ props }">
          <v-list-item
            v-bind="props"
            class="sidebar-item"
            prepend-icon="mdi-alert-circle-outline"
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
      <p class="footer-text">Supported by our partners</p>
      <div class="partner-logos">
        <div class="logo-item">
          <img src="https://rdrrmc9-alerto.com/assets/images/partners/rdrrmc9.png" alt="RDRRMC9" />
        </div>
        <div class="logo-item">
          <img src="https://rdrrmc9-alerto.com/assets/images/partners/ocd.png" alt="OCD" />
        </div>
        <div class="logo-item">
          <img src="https://rdrrmc9-alerto.com/assets/images/partners/dost9.png" alt="DOST9" />
        </div>
        <div class="logo-item">
          <img src="https://rdrrmc9-alerto.com/assets/images/partners/dilg.png" alt="DILG" />
        </div>
      </div>
      <div class="copyright">© 2025 ALERTO System</div>
    </div>
  </v-navigation-drawer>
</template>

<style scoped>
.sidebar {
  background: linear-gradient(180deg, #003092 0%, #001a6e 100%);
  border-right: 1px solid rgba(255, 255, 255, 0.1);
  display: flex;
  flex-direction: column;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
}

.sidebar-header {
  padding: 24px 20px 20px;
  background: rgba(0, 26, 110, 0.5);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
}

.sidebar-logo {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-decoration: none;
  gap: 8px;
}

.logo-container {
  display: flex;
  align-items: center;
  gap: 12px;
}

.logo-avatar {
  border: 2px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  transition: all 0.3s ease;
}

.logo-avatar:hover {
  transform: scale(1.05);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
}

.logo-text {
  font-size: 1.5rem;
  font-weight: 800;
  color: white;
  letter-spacing: 1.5px;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.logo-subtitle {
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.7);
  letter-spacing: 0.5px;
  margin-top: 4px;
}

.sidebar-list {
  background: transparent;
  padding: 16px 8px;
  flex-grow: 1;
}

.sidebar-item {
  color: white;
  margin: 6px 8px;
  border-radius: 10px;
  min-height: 46px;
  transition: all 0.2s ease;
  position: relative;
  overflow: hidden;
}

.sidebar-item::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  height: 100%;
  width: 4px;
  background: rgba(255, 255, 255, 0.5);
  opacity: 0;
  transition: opacity 0.2s ease;
}

.sidebar-item:hover {
  background: rgba(255, 255, 255, 0.12);
  transform: translateX(4px);
}

.sidebar-item:hover::before {
  opacity: 1;
}

.sidebar-item.v-list-item--active {
  background: linear-gradient(90deg, rgba(255, 255, 255, 0.2) 0%, transparent 100%);
}

.sidebar-item.v-list-item--active::before {
  opacity: 1;
  background: #fff;
}

.sidebar-icon {
  color: white;
  margin-right: 16px;
  font-size: 1.25rem;
}

.dropdown-arrow {
  color: rgba(255, 255, 255, 0.7);
  margin-left: auto;
  transition: transform 0.3s ease;
}

.sidebar-group.v-list-group--active .dropdown-arrow {
  transform: rotate(180deg);
}

.sidebar-text {
  font-size: 0.9375rem;
  font-weight: 500;
  letter-spacing: 0.3px;
}

.sidebar-group :deep(.v-list-group__items) {
  background: rgba(0, 0, 0, 0.15);
  margin: 4px 0;
  border-radius: 0 0 10px 10px;
  overflow: hidden;
}

.sidebar-subitem {
  color: white;
  padding-left: 56px !important;
  min-height: 40px;
  border-radius: 0;
  transition: all 0.2s ease;
}

.sidebar-subitem:hover {
  background: rgba(255, 255, 255, 0.08);
  padding-left: 60px !important;
}

.subitem-icon {
  font-size: 1.15rem;
  margin-right: 16px;
  color: rgba(255, 255, 255, 0.7);
}

.sidebar-footer {
  padding: 20px 16px;
  background: rgba(0, 26, 110, 0.5);
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
}

.footer-text {
  color: rgba(255, 255, 255, 0.8);
  text-align: center;
  margin-bottom: 16px;
  font-size: 0.875rem;
  font-weight: 500;
}

.partner-logos {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  margin-bottom: 16px;
}

.logo-item {
  display: flex;
  justify-content: center;
  align-items: center;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  padding: 8px;
  transition: all 0.3s ease;
}

.logo-item:hover {
  background: rgba(255, 255, 255, 0.15);
  transform: translateY(-2px);
}

.logo-item img {
  max-width: 100%;
  height: auto;
  max-height: 36px;
  object-fit: contain;
  filter: grayscale(0.3) brightness(1.2);
  transition: all 0.3s ease;
}

.logo-item:hover img {
  filter: grayscale(0) brightness(1.4);
}

.copyright {
  color: rgba(255, 255, 255, 0.6);
  text-align: center;
  font-size: 0.75rem;
  margin-top: 8px;
}
</style>
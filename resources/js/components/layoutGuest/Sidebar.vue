<script setup>
import { ref, watch } from "vue";
import store from "@/store";

import ndrrmcLogo from '../../../img/logo/ndrrmc.png';
import ocdLogo from '../../../img/logo/ocd.svg';
import dostLogo from '../../../img/logo/dost.png';
import diglLogo from '../../../img/logo/dilg.png';
import alertoLogo from '../../../img/logo/alerto-logo.png';

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
  { title: "River Status", icon: "mdi-wave", route: "/river-status" },
  { title: "Weather Updates", icon: "mdi-weather-cloudy", route: "/weather-updates" },
  { title: "AdZU Weather Station", icon: "mdi-weather-sunny", route: "/adzu-weather" },
  { title: "Earthquake Bulletin", icon: "mdi-earth", route: "/earthquake" },
];

const items = [
  { title: "About Us", icon: "mdi-information-outline", route: "/about-us" },
  { title: "Contact Us", icon: "mdi-email-outline", route: "/contact-us" },
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
    <!-- Background Bubbles -->
    <div class="bubble-container">
      <div class="bubble bubble-1"></div>
      <div class="bubble bubble-2"></div>
      <div class="bubble bubble-3"></div>
      <div class="bubble bubble-4"></div>
      <div class="bubble bubble-5"></div>
    </div>

    <!-- Header -->
    <div class="sidebar-header">
      <RouterLink to="/" class="sidebar-logo">
        <div class="logo-container">
          <div class="logo-bubble">
            <v-avatar
              size="50"
              :image="alertoLogo"
              class="logo-avatar"
            ></v-avatar>
          </div>
          <div class="brand-text">
            <span class="brand-name">ALERTO</span>
            <span class="brand-tagline">Disaster Monitoring System</span>
          </div>
        </div>
      </RouterLink>
    </div>

    <!-- Menu Items -->
    <v-list class="sidebar-list" density="compact">
      <v-list-group value="Dashboard" class="sidebar-group">
        <template v-slot:activator="{ props }">
          <v-list-item
            v-bind="props"
            class="sidebar-item bubble-item"
            :to="'/'"
            prepend-icon="mdi-view-dashboard"
          >
            <template v-slot:prepend>
              <div class="icon-bubble">
                <v-icon class="sidebar-icon"></v-icon>
              </div>
            </template>
            <span class="sidebar-text">Dashboard</span>
          </v-list-item>
        </template>

        <v-list-item
          v-for="item in itemsInDashboard"
          :key="item.title"
          :to="item.route"
          class="sidebar-subitem bubble-subitem"
          link
        >
          <template v-slot:prepend>
            <div class="subitem-icon-bubble">
              <v-icon class="subitem-icon">{{ item.icon }}</v-icon>
            </div>
          </template>
          <span class="sidebar-text">{{ item.title }}</span>
        </v-list-item>
      </v-list-group>

      <v-list-item
        v-for="item in items"
        :key="item.title"
        :to="item.route"
        class="sidebar-item bubble-item"
        link
        :prepend-icon="item.icon"
      >
        <template v-slot:prepend>
          <div class="icon-bubble">
            <v-icon class="sidebar-icon"></v-icon>
          </div>
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
            class="sidebar-item bubble-item"
            prepend-icon="mdi-folder-outline"
          >
            <template v-slot:prepend>
              <div class="icon-bubble">
                <v-icon class="sidebar-icon"></v-icon>
              </div>
            </template>
            <span class="sidebar-text">Libraries</span>
            <div class="dropdown-bubble">
              <v-icon class="dropdown-arrow">mdi-chevron-down</v-icon>
            </div>
          </v-list-item>
        </template>
      </v-list-group>
    </v-list>

    <!-- Footer with Logos -->
    <div class="sidebar-footer">
      <p class="footer-text">Supported by our partners</p>
      <div class="partner-logos">
        <div class="logo-item logo-bubble-item">
          <img :src="ndrrmcLogo" alt="RDRRMC9" />
        </div>
        <div class="logo-item logo-bubble-item">
          <img :src="ocdLogo" alt="OCD" />
        </div>
        <div class="logo-item logo-bubble-item">
          <img :src="dostLogo" alt="DOST9" />
        </div>
        <div class="logo-item logo-bubble-item">
          <img :src="diglLogo" alt="DILG" />
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
  position: relative;
  overflow: hidden;
}

/* Background Bubbles */
.bubble-container {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 0;
  pointer-events: none;
}

.bubble {
  position: absolute;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.05);
  animation: float 15s infinite ease-in-out;
}

.bubble-1 {
  width: 120px;
  height: 120px;
  top: 10%;
  left: -30px;
  animation-delay: 0s;
}

.bubble-2 {
  width: 80px;
  height: 80px;
  top: 40%;
  right: -20px;
  animation-delay: 3s;
}

.bubble-3 {
  width: 60px;
  height: 60px;
  bottom: 30%;
  left: 10%;
  animation-delay: 6s;
}

.bubble-4 {
  width: 100px;
  height: 100px;
  bottom: 10%;
  right: -40px;
  animation-delay: 9s;
}

.bubble-5 {
  width: 70px;
  height: 70px;
  top: 20%;
  right: 20%;
  animation-delay: 12s;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0) translateX(0);
  }
  25% {
    transform: translateY(-10px) translateX(5px);
  }
  50% {
    transform: translateY(5px) translateX(-5px);
  }
  75% {
    transform: translateY(-5px) translateX(3px);
  }
}

.sidebar-header {
  padding: 8px 20px 8px;
  background: rgba(0, 26, 110, 0.5);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  position: relative;
  z-index: 1;
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

.logo-bubble {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(5px);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  transition: all 0.3s ease;
}

.logo-bubble:hover {
  transform: scale(1.05);
  background: rgba(255, 255, 255, 0.15);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
}

.logo-avatar {
  border: 2px solid rgba(255, 255, 255, 0.3);
  transition: all 0.3s ease;
}

.sidebar-list {
  background: transparent;
  padding: 16px 8px;
  flex-grow: 1;
  position: relative;
  z-index: 1;
}

.sidebar-item {
  color: white;
  margin: 6px 8px;
  border-radius: 15px;
  min-height: 46px;
  transition: all 0.2s ease;
  position: relative;
  overflow: hidden;
}

.bubble-item {
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(5px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.bubble-item:hover {
  background: rgba(255, 255, 255, 0.12);
  transform: translateX(4px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.bubble-item.v-list-item--active {
  background: linear-gradient(90deg, rgba(255, 255, 255, 0.2) 0%, transparent 100%);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.icon-bubble {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
  margin-right: 12px;
  transition: all 0.3s ease;
}

.bubble-item:hover .icon-bubble {
  background: rgba(255, 255, 255, 0.2);
  transform: scale(1.1);
}

.sidebar-icon {
  color: white;
  font-size: 1.25rem;
}

.dropdown-bubble {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
  margin-left: auto;
  transition: all 0.3s ease;
}

.dropdown-arrow {
  color: rgba(255, 255, 255, 0.7);
  transition: transform 0.3s ease;
}

.sidebar-group.v-list-group--active .dropdown-arrow {
  transform: rotate(180deg);
}

.sidebar-group.v-list-group--active .dropdown-bubble {
  background: rgba(255, 255, 255, 0.2);
}

.sidebar-text {
  font-size: 0.9375rem;
  font-weight: 500;
  letter-spacing: 0.3px;
}

.sidebar-group :deep(.v-list-group__items) {
  background: rgba(0, 0, 0, 0.15);
  margin: 0 0;
  border-radius: 0 0 15px 15px;
  overflow: hidden;
}

.sidebar-subitem {
  color: white;
  padding-left: 56px !important;
  min-height: 40px;
  border-radius: 0;
  transition: all 0.2s ease;
}

.bubble-subitem {
  background: rgba(255, 255, 255, 0.03);
}

.bubble-subitem:hover {
  background: rgba(255, 255, 255, 0.08);
  padding-left: 60px !important;
}

.subitem-icon-bubble {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.08);
  margin-right: 16px;
  transition: all 0.3s ease;
}

.bubble-subitem:hover .subitem-icon-bubble {
  background: rgba(255, 255, 255, 0.15);
  transform: scale(1.05);
}

.subitem-icon {
  font-size: 1.15rem;
  color: rgba(255, 255, 255, 0.7);
}

.sidebar-footer {
  padding: 20px 16px;
  background: rgba(0, 26, 110, 0.5);
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  position: relative;
  z-index: 1;
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
  border-radius: 12px;
  padding: 8px;
  transition: all 0.3s ease;
}

.logo-bubble-item {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(5px);
}

.logo-bubble-item:hover {
  background: rgba(255, 255, 255, 0.15);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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

.brand-text {
  display: flex;
  flex-direction: column;
  margin-left: 8px;
}

.brand-name {
  font-size: 1.5rem;
  font-weight: 800;
  color: white;
  letter-spacing: 1px;
  line-height: 1;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.brand-tagline {
  font-size: 0.7rem;
  color: rgba(255, 255, 255, 0.8);
  letter-spacing: 0.5px;
  margin-top: 2px;
}

.sidebar :deep(.v-list-item__prepend) {
  margin-inline-end: 0 !important;
}
</style>
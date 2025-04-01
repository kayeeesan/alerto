<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import Sidebar from "../components/layouts/Sidebar.vue";
import useAuth from "../composables/auth.js";
import store from "@/store";

const { logout } = useAuth();
const user = store.state.auth.user;

const drawer = ref(true); // Controls the sidebar visibility
let interval = null; // Define interval variable

const cleanUpExpiredItems = () => {
    const expiry = localStorage.getItem("expiry");

    if (!expiry) return;

    const now = new Date().getTime();

    if (now > expiry) {
        logout();
    }
};

onMounted(() => {
    interval = setInterval(cleanUpExpiredItems, 28800000); // Restart every 8 hours
});

onUnmounted(() => {
    clearInterval(interval);
});

// Dynamically adjust main content margin
const mainContentClass = computed(() => ({
  "expanded": drawer.value, // Add 'expanded' class when sidebar is open
}));
</script>

<template>
  <v-app>
    <div class="layout-wrapper">
      <!-- Sidebar -->
      <Sidebar v-model:drawer="drawer" class="sidebar" />

      <!-- Main Content (Responsive) -->
      <div class="main-content" :class="mainContentClass">
        <v-app-bar app style="background: #003092;">
            <v-app-bar-nav-icon @click="drawer = !drawer" color="white"></v-app-bar-nav-icon>
            <v-spacer></v-spacer>
            
            <v-badge
                color="blue-grey-lighten-5"
                :content="user.full_name"
                inline
            ></v-badge>
            <v-btn icon="mdi-logout" variant="text" @click="logout()" color="white"></v-btn>
        </v-app-bar>

        <v-main style="margin-left: 0 !important;">
          <v-container fluid>
            <router-view />
          </v-container>
        </v-main>
      </div>
    </div>
  </v-app>
</template>

<style scoped>
/* Base layout */
.layout-wrapper {
  display: flex;
  height: 100vh;
}

/* Sidebar styling */
.sidebar {
  transition: width 0.3s ease-in-out;
}

/* Main content responsiveness */
.main-content {
  flex-grow: 1;
  transition: margin-left 0.3s ease-in-out;
}

/* When Sidebar is expanded */
.main-content.expanded {
  margin-left: 270px; /* Same width as sidebar */
}

/* Responsive for small screens */
@media (max-width: 768px) {
  .main-content {
    margin-left: 0 !important; /* No offset when sidebar is temporary */
  }
}
</style>

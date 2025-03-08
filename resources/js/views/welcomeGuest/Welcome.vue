<script setup>
import { ref, computed } from "vue";
import Sidebar from "../../components/layoutGuest/Sidebar.vue";
import Header from "../../components/layoutGuest/Header.vue";

const drawer = ref(true); // Controls the sidebar visibility

// Dynamically adjust main content width
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
        <Header
          @toggle-sidebar="drawer = !drawer"
          class="toggle-button"
        />
          <v-main>
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
  margin-left: 250px; /* Same width as sidebar */
}

/* Responsive for small screens */
@media (max-width: 768px) {
  .main-content {
    margin-left: 0 !important; /* No offset when sidebar is temporary */
  }
}
</style>

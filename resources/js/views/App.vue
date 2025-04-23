<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import Sidebar from "../components/layouts/Sidebar.vue";
import UserProfile from "../components/users/ProfilePage.vue";
import useAuth from "../composables/auth.js";
import store from "@/store";
import useStaffs from "../composables/staff.js";

const { staffs, getStaffs } = useStaffs();

const staff = ref({});

const { logout } = useAuth();
const user = store.state.auth.user;

const drawer = ref(true); // Controls the sidebar visibility
let interval = null; // Define interval variable
const show_form_modal = ref(false);

const ShowModalForm = () => {
  show_form_modal.value = true;
}


const cleanUpExpiredItems = () => {
    const expiry = localStorage.getItem("expiry");

    if (!expiry) return;

    const now = new Date().getTime();

    if (now > expiry) {
        logout();
    }
};

const localTime = ref('');

const updatedLocalTime = () => {
  const now = new Date();
  localTime.value = now.toLocaleTimeString();
};

onMounted(async () => {
    interval = setInterval(cleanUpExpiredItems, 28800000); // Restart every 8 hours
    updatedLocalTime();
    setInterval(updatedLocalTime, 1000);

    await getStaffs();

    const matched = staffs.value.find(
    (s) => s.username?.toLowerCase() === user.username?.toLowerCase()
  );

    if (matched) {
      staff.value = matched;
    }
  console.log(matched);
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
      <div class="main-content" :class="mainContentClass" style="background: #F8FAF0;">
        <v-app-bar app style="background: #003092;">
            <v-app-bar-nav-icon @click="drawer = !drawer" color="white"></v-app-bar-nav-icon>
            <v-spacer></v-spacer>
            

            <!-- <v-badge
                color="blue-grey-lighten-5"
                :content="user.full_name"
                inline
            ></v-badge> -->
            <div class="d-flex align-center pr-6">
              <v-menu transition="scale-transition" offset-y open-on-hover>
                <template v-slot:activator="{ props }">
                  <v-btn
                    v-bind="props"
                    variant="text"
                    color="white"
                    class="text-capitalize"
                    rounded="lg"
                  >
                    <v-avatar class="mr-2" size="32" color="primary">
                      <span class="white--text text-subtitle-2">
                        {{ user.full_name.charAt(0) }}
                      </span>
                    </v-avatar>
                    {{ user.full_name }}
                    <v-icon end>mdi-menu-down</v-icon>
                  </v-btn>
                </template>

                <v-card class="pa-4" width="320">
                  <div class="d-flex align-center mb-4">
                    <v-avatar size="56" color="primary">
                      <span class="white--text text-h6">
                        {{ user.full_name.charAt(0) }}
                      </span>
                    </v-avatar>
                    <div class="ml-4">
                      <div class="text-subtitle-1 font-weight-bold">
                        Hi, {{ user.first_name.split(" ")[0].toUpperCase() }}!
                      </div>
                      <div class="text-caption">{{ user.email }}</div>
                      <div class="text-caption grey--text">Managed by Alerto Staff</div>
                    </div>
                  </div>

                  <v-divider class="mb-3"></v-divider>

                  <v-btn
                    variant="flat"
                    block
                    class="mb-2 text-none"
                    @click="ShowModalForm"
                    color="#003092"
                    style="color: white; letter-spacing: 0.5px;"
                  >
                    <v-icon left>mdi-account</v-icon>
                    View Profile
                  </v-btn>

                  <v-btn
                    variant="outlined"
                    block
                    class="text-none"
                    @click="logout"
                    color="#d32f2f"
                    style="letter-spacing: 0.5px;"
                  >
                    <v-icon left>mdi-logout</v-icon>
                    Sign out
                  </v-btn>

                  <v-divider class="my-3"></v-divider>

                  <div class="text-caption d-flex justify-space-between grey--text">
                    <span style="cursor: pointer; display: flex; align-items: center;">
                      <v-icon small class="mr-1">mdi-clock-outline</v-icon>
                      {{ localTime }}
                    </span>
                    <span style="cursor: pointer; display: flex; align-items: center;">
                      <v-icon small class="mr-1">mdi-account-circle</v-icon>
                      {{ user.username }}
                    </span>
                  </div>
                </v-card>
              </v-menu>

            </div>

        </v-app-bar>

        <v-main style="margin-left: 0 !important; background: #F8FAF0; ">
          <v-container fluid>
            <router-view />
          </v-container>
        </v-main>
      </div>
    </div>
  </v-app>
  <UserProfile 
  v-model="show_form_modal" 
  :user="user"
  :staff="staff"
  />
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

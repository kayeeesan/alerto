<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import Sidebar from "../components/layouts/Sidebar.vue";
import UserProfile from "../components/users/ProfilePage.vue";
import useAuth from "../composables/auth.js";
import store from "@/store";
import useStaffs from "../composables/staff.js";
import useNotifications from "../composables/notification.js";
import { RouterLink } from "vue-router";



const { staffs, getStaffs } = useStaffs();
const {
  notification, notifications, is_loading, unread_count, getNotifications, markAsRead, markAllAsRead
} = useNotifications();

let notifInterval = null;
const staff = ref({});
const { logout } = useAuth();
const user = store.state.auth.user;
const drawer = ref(true);
const isMobile = ref(false);
const show_form_modal = ref(false);
const localTime = ref('');
let interval = null;
let resizeListener = null;

const isAdmin = computed(() => user?.roles?.some(role => role.slug === 'administrator'));
const userRiverId = computed(() => user?.river?.id);

const filteredNotifications = computed(() => {
  // Ensure we have notifications and they're loaded
  if (!notifications.value || notifications.value.length === 0) {
    return [];
  }

  // If admin, return all notifications
  if (isAdmin.value) {
    return [...notifications.value].reverse(); // Show newest first
  }

  // For non-admin users with river_id
  if (userRiverId.value) {
    return notifications.value.filter(notif => {
      // Get the river ID from either direct property or nested object
      const notifRiverId = notif.river_id || (notif.river && notif.river.id);
      
      // Debug each comparison
      console.log(`Comparing - Notification River: ${notifRiverId}, User River: ${userRiverId.value}`);
      
      // Compare as numbers to avoid type issues
      return notifRiverId != null && Number(notifRiverId) === Number(userRiverId.value);
    }).reverse(); // Show newest first
  }

  return [];
});

const filteredUnreadCount = computed(() => {
  return filteredNotifications.value.filter(notif => !notif.read_at).length;
});

const ShowModalForm = () => {
  show_form_modal.value = true;
};

const cleanUpExpiredItems = () => {
  const expiry = localStorage.getItem("expiry");
  if (!expiry) return;
  const now = new Date().getTime();
  if (now > expiry) {
    logout();
  }
};

const updatedLocalTime = () => {
  const now = new Date();
  localTime.value = now.toLocaleTimeString();
};

const checkMobile = () => {
  isMobile.value = window.innerWidth <= 768;
  if (isMobile.value) {
    drawer.value = false;
  } else {
    drawer.value = true;
  }
};

onMounted(async () => {
  interval = setInterval(cleanUpExpiredItems, 28800000);
  updatedLocalTime();
  setInterval(updatedLocalTime, 1000);
  
  checkMobile();
  resizeListener = () => checkMobile();
  window.addEventListener('resize', resizeListener);

  await getNotifications();
  notifInterval = setInterval(async () => {
    await getNotifications();
  }, 3000);

  await getStaffs();
  const matched = staffs.value.find(
    (s) => s.username?.toLowerCase() === user.username?.toLowerCase()
  );
  if (matched) {
    staff.value = matched;
  }
});


onUnmounted(() => {
  clearInterval(interval);
  if (notifInterval) {
    clearInterval(notifInterval);
  }
  if (resizeListener) {
    window.removeEventListener('resize', resizeListener);
  }
});

const mainContentClass = computed(() => ({
  "expanded": drawer.value && !isMobile.value,
}));
</script>

<template>
  <v-app>
    <div class="layout-wrapper">
      <!-- Sidebar -->
      <Sidebar 
        v-model:drawer="drawer" 
        class="sidebar" 
        :temporary="isMobile"
      />

      <!-- Main Content -->
      <div class="main-content" :class="mainContentClass" style="background: #F8FAF0;">
        <v-app-bar app style="background: #003092;">
          <v-app-bar-nav-icon @click="drawer = !drawer" color="white"></v-app-bar-nav-icon>
          <v-spacer></v-spacer>

          <div class="d-flex align-center">
            <v-menu offset-y :close-on-content-click="false" open-on-hover>
              <template v-slot:activator="{ props }">
                <v-badge
                  :content="filteredUnreadCount"
                  :model-value="filteredUnreadCount > 0"
                  color="red"
                  overlap
                  offset-x="8"
                  offset-y="8"
                >
                  <v-btn
                    v-bind="props"
                    icon
                    color="primary"
                    variant="flat"
                    style="width: 40px; height: 40px; min-width: 40px; border-radius: 50%;"
                    :loading="is_loading"
                  >
                    <v-icon>mdi-bell</v-icon>
                  </v-btn>
                </v-badge>
              </template>

              <v-card width="400" >
                <v-toolbar color="primary" density="compact">
                  <v-toolbar-title>Notifications</v-toolbar-title>
                  <v-spacer></v-spacer>
                  <v-btn
                    v-if="filteredNotifications.length > 0"
                    variant="text"
                    size="small"
                    @click="markAllAsRead"
                  >
                    Mark all as read
                  </v-btn>
                </v-toolbar>

                <v-list v-if="filteredNotifications.length > 0">
                  <RouterLink to="/home/alerts" class="text-decoration-none">
                    <v-list-item
                    v-for="notif in filteredNotifications"
                    :key="notif.id"
                    @click="markAsRead(notif.id)"
                    :class="{ 'bg-grey-lighten-4': !notif.read_at }"
                  >
                    <template v-slot:prepend>
                      <v-icon
                        :color="notif.read_at ? 'grey' : 'primary'"
                        :icon="notif.read_at ? 'mdi-alert-circle-check' : 'mdi-alert'"
                      ></v-icon>
                    </template>
                    
                    <v-list-item-title>{{ notif.data?.message || notif.text || 'New notification' }}</v-list-item-title>
                    <v-list-item-subtitle>
                      {{ new Date(notif.created_at).toLocaleString() }}
                    </v-list-item-subtitle>
                  </v-list-item>
                  </RouterLink>
                 
                </v-list>

                <v-card-text v-else class="text-center py-4" >
                  <v-icon size="48" color="grey">mdi-bell-off</v-icon>
                  <div class="text-h6 mt-2">No notifications</div>
                </v-card-text>
              </v-card>
            </v-menu>
          </div>
          
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

              <v-card class="pa-4" width="380">
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
.layout-wrapper {
  display: flex;
  height: 100vh;
}

.sidebar {
  transition: width 0.3s ease-in-out;
}

.main-content {
  flex-grow: 1;
  transition: margin-left 0.3s ease-in-out;
  width: 100%;
  margin-left: 0;
}

.main-content.expanded {
  margin-left: 270px;
  width: calc(100% - 270px);
}

@media (max-width: 768px) {
  .main-content {
    margin-left: 0 !important;
    width: 100% !important;
  }
  
  .main-content.expanded {
    margin-left: 0 !important;
    width: 100% !important;
  }
}
</style>
<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import Sidebar from "../components/layouts/Sidebar.vue";
import UserProfile from "../components/users/ProfilePage.vue";
import useAuth from "../composables/auth.js";
import store from "@/store";
import useStaffs from "../composables/staff.js";
import useNotifications from "../composables/notification.js";
import { RouterLink } from "vue-router";
import Alert from "../components/dashboard/dashboard/alert.vue";

const { staffs, getStaffs } = useStaffs();
const {
  notification, notifications, is_loading, unread_count, getNotifications, markAsRead, markAllAsRead, echo, reloadNotifications
} = useNotifications();

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
  if (!notifications.value || notifications.value.length === 0) {
    return [];
  }
  let filtered = [...notifications.value];

  if (!isAdmin.value && userRiverId.value) {
    filtered = filtered.filter(notif => {
      const notifRiverId = notif.river_id || (notif.river && notif.river.id);
      return notifRiverId != null && Number(notifRiverId) === Number(userRiverId.value);
    });
  }
  return filtered.sort((a, b) => {
    return new Date(b.created_at) - new Date(a.created_at);
  });
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
 
  if (resizeListener) {
    window.removeEventListener('resize', resizeListener);
  }
});

const mainContentClass = computed(() => ({
  "expanded": drawer.value && !isMobile.value,
}));

// Notification type styling
const getNotificationColor = (type) => {
  switch (type) {
    case 'critical': return 'error';
    case 'alert': return 'warning';
    default: return 'info';
  }
};

const getNotificationIcon = (type) => {
  switch (type) {
    case 'critical': return 'mdi-alert-octagon';
    case 'alert': return 'mdi-alert';
    default: return 'mdi-information';
  }
};

const getAlertRoute = (notification) => {
  if (notification.alert && notification.alert.status) {
    switch (notification.alert.status.toLowerCase()) {
       case 'pending':
        return '/home/alerts-pending';
      case 'responded':
        return '/home/alerts-responded';
      case 'expired':
        return '/home/alerts-expired';
      default:
        return '/home';
    }
  }
}
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

          <!-- Notifications Bell -->
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
                    color="white"
                    variant="text"
                    style="width: 40px; height: 40px; min-width: 40px;"
                    :loading="is_loading"
                  >
                    <v-icon>mdi-bell</v-icon>
                  </v-btn>
                </v-badge>
              </template>

              <v-card width="420" class="elevation-12 rounded-xl">
                <v-toolbar color="#003092" density="compact" class="rounded-t-xl">
                  <v-toolbar-title class="text-white">Notifications</v-toolbar-title>
                  <v-spacer></v-spacer>
                  <v-btn
                    v-if="filteredNotifications.length > 0"
                    variant="text"
                    size="small"
                    color="white"
                    @click="markAllAsRead"
                  >
                    Mark All Read
                  </v-btn>
                </v-toolbar>

                <div style="max-height: 400px; overflow-y: auto;">
                 <v-list v-if="filteredNotifications.length > 0" class="pa-2">
                    <template v-for="notif in filteredNotifications" :key="notif.id">
                      <RouterLink 
                        :to="getAlertRoute(notif)" 
                        class="text-decoration-none"
                        @click="markAsRead(notif.id)"
                      >
                        <v-list-item
                          class="rounded-lg mb-2 pa-3"
                          :class="{
                            'bg-blue-lighten-5': !notif.read_at,
                            'bg-grey-lighten-4': notif.read_at
                          }"
                        >
                          <template v-slot:prepend>
                            <v-avatar
                              size="36"
                              :color="getNotificationColor(notif.type)"
                            >
                              <v-icon
                                :icon="getNotificationIcon(notif.type)"
                                color="white"
                                size="20"
                              ></v-icon>
                            </v-avatar>
                          </template>

                          <v-list-item-title class="font-weight-medium">
                            {{ notif.data?.message || notif.text || 'New notification' }}
                          </v-list-item-title>
                          <v-list-item-subtitle class="text-caption grey--text text--darken-2 mt-1">
                            {{ new Date(notif.created_at).toLocaleString() }}
                          </v-list-item-subtitle>
                        </v-list-item>
                      </RouterLink>
                    </template>
                  </v-list>

                  <v-card-text v-else class="text-center py-6">
                    <v-icon size="48" color="grey">mdi-bell-off-outline</v-icon>
                    <div class="text-h6 mt-2">You're all caught up!</div>
                    <div class="text-caption grey--text">No new notifications</div>
                  </v-card-text>
                </div>
              </v-card>

            </v-menu>
          </div>

          
          <!-- User Profile Menu -->
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

              <v-card class="pa-4 rounded-xl" width="380">
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
            <v-row>
              <v-col cols="12">
                <Alert/>
              </v-col>
            </v-row>
            <router-view />
          </v-container>
        </v-main>
      </div>
    </div>
    
    <UserProfile 
      v-model="show_form_modal" 
      :user="user"
      :staff="staff"
    />
  </v-app>
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

/* Notification Styles */
.notification-dropdown {
  border-radius: 8px;
  overflow: hidden;
}

.notification-header {
  background: #003092;
}

.notification-list {
  padding: 0;
  max-height: 400px;
  overflow-y: auto;
}

.notification-item {
  padding: 12px 16px;
  border-left: 4px solid;
  transition: all 0.2s ease;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.notification-item.unread {
  background-color: rgba(0, 48, 146, 0.05);
  border-left-color: #003092;
}

.notification-item:hover {
  background-color: rgba(0, 48, 146, 0.1);
}

.notification-title {
  font-weight: 500;
  line-height: 1.3;
  margin-bottom: 4px;
}

.notification-time {
  font-size: 0.75rem;
  color: #616161;
}

.empty-notifications {
  color: #757575;
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
  
  .notification-dropdown {
    width: 100vw !important;
    max-width: 100%;
    margin-right: -16px;
  }
}

.v-list-item {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.v-list-item:hover {
  transform: scale(1.01);
  box-shadow: 0 2px 12px rgba(0, 48, 146, 0.12);
}

</style>
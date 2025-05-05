<script setup>
import { ref, computed, onMounted } from "vue";
import store from "@/store";
import useNotifications from "../../../composables/notification";

const {
  notifications, 
  is_loading, 
  getNotifications, 
  markAsSeen  // Added markAsSeen
} = useNotifications();

const user = store.state.auth.user;
const visibleAlerts = ref([]);

const isAdmin = computed(() => user?.roles?.some(role => role.slug === 'administrator'));
const userRiverId = computed(() => user?.river?.id);

const filteredPopUpAlerts = computed(() => {
  if (!notifications.value || notifications.value.length === 0) {
    return [];
  }
  
  let filtered = [...notifications.value];

  // Filter by river if not admin
  if (!isAdmin.value && userRiverId.value) {
    filtered = filtered.filter(notif => {
      const notifRiverId = notif.river_id || (notif.river && notif.river.id);
      return notifRiverId != null && Number(notifRiverId) === Number(userRiverId.value);
    });
  }

  // Only show unseen notifications
  return filtered
    .filter(notif => !notif.seen_at)
    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
});

const showAlert = async (alert) => {
  // Add to visible alerts
  visibleAlerts.value.push(alert);
  
  // Mark as seen in backend
  await markAsSeen(alert.id);
  
  // Auto-dismiss after 5 seconds
  setTimeout(() => {
    visibleAlerts.value = visibleAlerts.value.filter(a => a.id !== alert.id);
  }, 5000);
};

onMounted(async () => {
  await getNotifications();
  
  // Show alerts with staggered appearance
  filteredPopUpAlerts.value.forEach((alert, index) => {
    setTimeout(() => showAlert(alert), index * 100);
  });
});
</script>

<template>
  <div class="alert-container">
    <transition-group name="slide-up">
      <v-alert
        v-for="notif in visibleAlerts"
        :key="notif.id"
        border="start"
        :color="notif.type === 'critical' ? 'error' : notif.type === 'alert' ? 'warning' : 'info'"
        title="ALERT"
        variant="tonal"
        closable
        class="alert-item"
        :icon="notif.type === 'critical' ? 'mdi-alert-octagon' : 'mdi-alert'"
        @click:close="visibleAlerts = visibleAlerts.filter(a => a.id !== notif.id)"
      >
        <strong>{{ notif.type.toUpperCase() }}:</strong> {{ notif.text }}
      </v-alert>
    </transition-group>
  </div>
</template>

<style scoped>
.alert-container {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 999;
  width: 550px;
}

.alert-item {
  margin-bottom: 10px;
  background-color: white;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
  padding: 12px 16px;
  transition: all 0.3s ease;
  cursor: pointer;
}

.alert-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
}

/* Animation for notifications */
.slide-up-enter-active {
  transition: all 0.3s ease-out;
}
.slide-up-leave-active {
  transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
}
.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(20px);
  opacity: 0;
}
</style>
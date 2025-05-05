<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { RouterLink } from "vue-router";
import store from "@/store";
import useNotifications from "../../../composables/notification";

const {
  notification, notifications, is_loading, unread_count, getNotifications, markAsRead, markAllAsRead
} = useNotifications();

let notifInterval = null;
let interval = null;
const user = store.state.auth.user;
const visibleAlerts = ref([]);

const isAdmin = computed(() => user?.roles?.some(role => role.slug === 'administrator'));
const userRiverId = computed(() => user?.river?.id);

const filteredPopUpAlerts = computed(() => {
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

onMounted(async () => {
  await getNotifications();
  
  // Show alerts and auto-hide them
  filteredPopUpAlerts.value.forEach((alert, index) => {
    setTimeout(() => {
      visibleAlerts.value.push(alert);
      
      // Hide after 3 seconds
      setTimeout(() => {
        visibleAlerts.value = visibleAlerts.value.filter(a => a.id !== alert.id);
      }, 5000);
    }, index * 100); // Stagger appearance slightly
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
        color="error"
        title=ALERT
        variant="tonal"
        closable
        class="alert-item"
        icon="mdi-alert"
      >
        {{notif.data?.message || notif.text}}
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
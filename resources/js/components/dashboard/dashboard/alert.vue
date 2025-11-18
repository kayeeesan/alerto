<script setup>
import { ref, computed, onMounted, watch } from "vue";
import store from "@/store";
import useNotifications from "../../../composables/notification";

const { notifications, getNotifications, markAsSeen, reloadNotifications } = useNotifications();
const user = store.state.auth.user;
const visibleAlerts = ref([]);
const alertTimeouts = ref({}); // To track timeouts for each alert

const isAdmin = computed(() => user?.roles?.some(role => role.slug === 'administrator'));
const userRiverId = computed(() => user?.river?.id);

const filteredPopUpAlerts = computed(() => {
  if (!notifications.value?.length) return [];
  let filtered = [...notifications.value];

  if (!isAdmin.value && userRiverId.value) {
    filtered = filtered.filter(notif => {
      const notifRiverId = notif.river_id || notif.river?.id;
      return notifRiverId && Number(notifRiverId) === Number(userRiverId.value);
    });
  }

  // Only show the latest unseen notification per river
  const unseen = filtered
    .filter(notif => !notif.seen_at)
    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

  const seenRivers = new Set();
  const latestPerRiver = [];

  for (const n of unseen) {
    const rid = n.river_id || n.river?.id;
    if (!rid) continue;
    const key = String(rid);
    if (!seenRivers.has(key)) {
      seenRivers.add(key);
      latestPerRiver.push(n);
    }
  }

  return latestPerRiver;
});

const showAlert = async (alert) => {
  // Don't show if already visible
  if (visibleAlerts.value.some(a => a.id === alert.id)) return;

  const audio = new Audio("/sounds/notification.mp3");
  audio.play().catch((e) => console.error("Audio error:", e));

  visibleAlerts.value.push(alert);
  await markAsSeen(alert.id);
  // Also mark any older unseen notifications for the same river as seen
  await markOlderSameRiverAsSeen(alert);

  // Set timeout to auto-remove after 4 seconds
  alertTimeouts.value[alert.id] = setTimeout(() => {
    closeAlert(alert.id);
    delete alertTimeouts.value[alert.id];
  }, 4000);

};

const closeAlert = (id) => {
  // Clear the timeout if alert is manually closed
  if (alertTimeouts.value[id]) {
    clearTimeout(alertTimeouts.value[id]);
    delete alertTimeouts.value[id];
  }

  const index = visibleAlerts.value.findIndex(a => a.id === id);
  if (index !== -1) {
    visibleAlerts.value.splice(index, 1);
  }
};

// Mark any older unseen notifications for the same river as seen
const markOlderSameRiverAsSeen = async (current) => {
  const rid = current.river_id || current.river?.id;
  if (!rid) return;
  const currentTime = new Date(current.created_at).getTime();
  const toMark = notifications.value.filter(n => {
    const nrid = n.river_id || n.river?.id;
    if (!nrid) return false;
    return !n.seen_at && Number(nrid) === Number(rid) && n.id !== current.id && new Date(n.created_at).getTime() <= currentTime;
  });
  for (const n of toMark) {
    await markAsSeen(n.id);
  }
};

onMounted(async () => {
  await getNotifications();
});

watch(filteredPopUpAlerts, (newAlerts) => {
  newAlerts.forEach((alert, index) => {
    if (!visibleAlerts.value.some(a => a.id === alert.id)) {
      setTimeout(() => showAlert(alert), index * 100);
    }
  });
});
</script>

<template>
  <div class="alert-container">
    <transition-group name="alert-fade" tag="div">
      <v-alert
        v-for="notif in visibleAlerts"
        :key="notif.id"
        border="start"
        :color="notif.type === 'critical' ? 'error' : notif.type === 'alert' ? 'warning' : 'yellow-lighten-1'"
        title="ALERT"
        variant="flat"
        closable
        class="alert-item"
        :icon="notif.type === 'critical' ? 'mdi-alert-octagon' : 'mdi-alert'"
        @click:close="closeAlert(notif.id)"
      >
        <strong>{{ notif.type?.toUpperCase() }}:</strong> {{ notif.text }}
      </v-alert>
    </transition-group>
  </div>
</template>


<style scoped>
.alert-container {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 9999;
  width: 550px;
  pointer-events: none;
}


.alert-item {
  margin-bottom: 10px;
  background-color: white;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
  padding: 12px 16px;
  pointer-events: auto;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}


/* Transition effects */
.alert-fade-enter-active,
.alert-fade-leave-active {
  transition: all 0.4s ease;
}
.alert-fade-enter-from {
  opacity: 0;
  transform: translateX(100px);
}
.alert-fade-leave-to {
  opacity: 0;
  transform: translateY(-20px) scale(0.95);
}
</style>



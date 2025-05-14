import { ref } from 'vue';
import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

export default function useNotifications() {
  const notification = ref(null);
  const notifications = ref([]);
  const is_loading = ref(false);
  const unread_count = ref(0);
  const query = ref({
    page: 1,
    per_page: 10
  });
  const onNewNotification = ref(null);

  const getNotifications = async (params = {}) => {
    is_loading.value = true;
    try {
      const query_str = { ...query.value, ...params };
      const response = await axios.get("/api/notifications", { params: query_str });
      notifications.value = response.data.data;
      unread_count.value = notifications.value.filter(n => !n.read_at).length;
    } catch (error) {
      notifications.value = [];
    } finally {
      is_loading.value = false;
    }
  };

  const markAsRead = async (id) => {
    try {
      await axios.patch(`/api/notifications/${id}/read`);
      const notif = notifications.value.find((n) => n.id === id);
      if (notif) {
        notif.read_at = new Date().toISOString();
        unread_count.value = Math.max(0, unread_count.value - 1);
      }
    } catch (error) {
      console.error("Failed to mark as read", error);
    }
  };

  // Added markAsSeen function (similar to markAsRead but uses seen_at instead of read_at)
  const markAsSeen = async (id) => {
    try {
      // Change from PATCH to POST to match your route definition
      await axios.post(`/api/notifications/${id}/seen`);
      const notif = notifications.value.find((n) => n.id === id);
      if (notif) {
        notif.seen_at = new Date().toISOString();
        if (!notif.read_at) {
          unread_count.value = Math.max(0, unread_count.value - 1);
        }
      }
    } catch (error) {
      console.error("Failed to mark as seen", error);
    }
  };

  const markAllAsRead = async () => {
    try {
      await axios.patch('/api/notifications/mark-all-read');
      notifications.value.forEach(n => n.read_at = new Date().toISOString());
      unread_count.value = 0;
    } catch (error) {
      console.error("Failed to mark all as read", error);
    }
  };

  const echo = new Echo({
    broadcaster: 'pusher',
    key: '57206333aea283adecc8', // Your Pusher key
    cluster: 'ap1',
    forceTLS: true,
  });

    //  echo.connector.pusher.connection.bind('state_change', (states) => {
    //   console.log('Pusher connection state changed:', states);
    // });

    // echo.connector.pusher.connection.bind('connected', () => {
    //   console.log('Pusher connected!');
    // });

    // echo.connector.pusher.connection.bind('error', (err) => {
    //   console.error('Pusher error:', err);
    // });

  // Listen for new notifications
  echo.channel('public-alerts')
      .listen('AlertCreated', (event) => {
          console.log('Event received on public-alerts:', event);
          notifications.value.push(event.notification); // Add the new notification
          unread_count.value += 1; // Increment the unread count
          if (onNewNotification.value) {
            onNewNotification.value(event.notification); // Trigger callback
          }
            notifications.value = [...notifications.value];
      });

  return {
    notification,
    notifications,
    query,
    is_loading,
    unread_count,
    getNotifications,
    markAsRead,
    markAsSeen, // Now included in the return object
    markAllAsRead,
    onNewNotification,
    echo
  };
}
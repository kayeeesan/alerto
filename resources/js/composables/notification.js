import { ref } from 'vue';
import axios from 'axios';

export default function useNotifications() {
  const notification = ref(null);
  const notifications = ref([]);
  const is_loading = ref(false);
  const unread_count = ref(0);
  const query = ref({
    page: 1,
    per_page: 10
  });

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

  const markAllAsRead = async () => {
    try {
      await axios.patch('/api/notifications/mark-all-read');
      notifications.value.forEach(n => n.read_at = new Date().toISOString());
      unread_count.value = 0;
    } catch (error) {
      console.error("Failed to mark all as read", error);
    }
  };

  return {
    notification,
    notifications,
    query,
    is_loading,
    unread_count,
    getNotifications,
    markAsRead,
    markAllAsRead
  };
}
import { onMounted, ref, onUnmounted } from 'vue';
import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { eventBus } from './eventBus'; 

export default function useNotifications() {
  const notification = ref(null);
  const notifications = ref([]);
  const is_loading = ref(false);
  const unread_count = ref(0);
  const query = ref({
    page: 1,
    per_page: 10
  });


    const echo = new Echo({
        broadcaster: 'pusher',
        key: '57206333aea283adecc8',
        cluster: 'ap1',
        forceTLS: true,
    });


    // const echo = new Echo({
    //     broadcaster: 'reverb',
    //     key: import.meta.env.VITE_REVERB_APP_KEY,
    //     wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
    //     wsPort: Number(import.meta.env.VITE_REVERB_PORT) || 6001,
    //     wssPort: Number(import.meta.env.VITE_REVERB_PORT) || 6001,
    //     forceTLS: import.meta.env.VITE_REVERB_SCHEME === 'https',
    //     enabledTransports: import.meta.env.VITE_REVERB_SCHEME === 'https' ? ['wss'] : ['ws'],
    // });

    //   const echo = new Echo({
    //     broadcaster: 'reverb',
    //     key: '57206333aea283adecc8',
    //     wsHost: '127.0.0.1',
    //     wsPort: 6001,
    //     wssPort: 6001,
    //     forceTLS: false,
    //     enabledTransports: ['ws']
    // });


      echo.channel('public-alerts')
        .listen('.AlertCreated', (event) => {
            getNotifications();
            // console.log('Event received on public-alerts', event);
            eventBus.$emit('alert-received', event);
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

  const reloadNotifications = async () => {
    await getNotifications();
  }


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


  const markAsSeen = async (id) => {
    try {
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




  return {
    notification,
    notifications,
    query,
    is_loading,
    unread_count,
    getNotifications,
    reloadNotifications,
    markAsRead,
    markAsSeen, 
    markAllAsRead,
  };
}

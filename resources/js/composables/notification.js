import { ref } from 'vue';
import axios from 'axios';

export default function useNotifications() {
    const notification = ref(null);
    const notifications = ref([]);
    const is_loading = ref(false);
    

    const getNotifications = async (params = {}, type = "") => {
        is_loading.value = true;

        let query_str = { ...query.value, ...params};
        let url = type === "/notifications" ? "/api/notifications" : "/api/form/notifications";
        await axios 
        .get(`${url}?page=${query.value.page}`, { params: query_str })
        .then((response) => {
            notifications.value = response.data.data;
            is_loading.value = false;
        })
    }

    const markAsRead = async (id) => {
        try {
          await axios.patch(`/api/notifications/${id}/read`)
          const notif = notifications.value.find(n => n.id === id)
          if (notif) notif.read_at = new Date().toISOString()
        } catch (error) {
          console.error('Failed to mark as read', error)
        }
      }
}
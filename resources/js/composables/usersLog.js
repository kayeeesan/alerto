import { ref } from "vue";
import axios from "axios";

export default function useLogs() {
    const user_logs = ref([]);
    const is_loading = ref(false);
    
    const getUserLogs = async (user_id) => {
        if (!user_id) return; // Ensure user_id is provided
    
        is_loading.value = true;
        try {
            let response = await axios.get(`/api/user-logs`, {
                params: { user_id } // Pass the user_id as a query param
            });
            user_logs.value = response.data.data;
        } catch (error) {
            console.error("Error fetching user logs:", error);
        } finally {
            is_loading.value = false;
        }
    };
    
    
    return {
        user_logs,
        is_loading,
        getUserLogs,
    };
}

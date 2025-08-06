import { ref } from 'vue';
import axios from 'axios';

export default function useSensorHistories() {
    const sensor_histories = ref([]);
    const sensor_history = ref(null);
    const is_loading = ref(false);
    const error = ref(null);

    const getSensorHistories = async (params = {}) => {
        is_loading.value = true;
        error.value = null;

        try {
            const response = await axios.get('/api/form/sensor-histories', { params });
            sensor_histories.value = response.data.data; // Laravel Resource collections are inside `data`
        } catch (err) {
            error.value = err;
            console.error('Error fetching sensor histories:', err);
        } finally {
            is_loading.value = false;
        }
    };

    return {
        sensor_histories,
        sensor_history,
        is_loading,
        error,
        getSensorHistories
    };
}

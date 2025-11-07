import { ref } from 'vue';
import axios from 'axios';

export default function useSensorHistories() {
    const sensor_histories = ref([]);
    const sensor_history = ref(null);
    const is_loading = ref(false);
    const error = ref(null);

    /**
     * @param {Object} params - Query parameters for the request
     * @param {String} type - Optional type. If "auth", it will use the authenticated URL
     */
    const getSensorHistories = async (params = {}) => {
        is_loading.value = true;
        error.value = null;

        // Set URL depending on whether it's for authenticated or public access
        const url = type === '/sensor-histories' ? '/api/sensor-histories' : '/api/form/sensor-histories';

        try {
            const response = await axios.get(url, { params }, type = '');
            sensor_histories.value = response.data.data;
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

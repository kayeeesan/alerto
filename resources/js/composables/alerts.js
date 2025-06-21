import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import Swal from "sweetalert2";
import { eventBus } from './eventBus';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';


export default function useAlerts() {
    const alert = ref(null);
    const alerts = ref([]);
    const is_loading = ref(false);
    const is_success = ref(false);
    const errors = ref({});
    const pagination = ref({});
    const query = ref({
        search: null,
    });

    // const echo = new Echo({
    //     broadcaster: 'pusher',
    //     key: '57206333aea283adecc8',
    //     cluster: 'ap1',
    //     forceTLS: true,
    // });

      const echo = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.REVERB_APP_KEY  || '57206333aea283adecc8',
            wsHost: import.meta.env.REVERB_HOST || window.location.hostname,
            wsPort: Number(import.meta.env.REVERB_PORT) || 6001,
            forceTLS: import.meta.env.REVERB_SCHEME === 'http',
            enabledTransports: ['ws', 'wss'],
        });

    echo.connector.pusher.connection.bind('connected', () => {
        console.log('✅ Pusher for alert connected successfully');
    });

    const handleNewAlert = () => {
        console.log('New alert received, refreshing notifications...');
        getAlerts();
    };

  onMounted(() => {

    echo.channel('alerts-updated')
    .listen('.AlertUpdated', (e) => {
        console.log('✅ Received AlertUpdated:', e.alert); // ✅ Should now be populated
        getAlerts(); // refresh the UI
    });

    eventBus.$on('alert-received', handleNewAlert);
  });

  onUnmounted(() => {
    eventBus.$off('alert-received', handleNewAlert);
  });

    const pendingPage = ref(1);
    const respondedPage = ref(1);
    const expiredPage = ref(1);

    
    const pendingAlerts = ref([]);
    const respondedAlerts = ref([]);
    const expiredAlerts = ref([]);

    const pendingPagination = ref({});
    const respondedPagination = ref({});
    const expiredPagination = ref({});
   
    const getAlerts = async () => {
        is_loading.value = true;

        try {
            const [pending, responded, expired] = await Promise.all([
                axios.get('/api/alerts-pending', { params: { ...query.value, page: pendingPage.value } }),
                axios.get('/api/alerts-responded', { params: { ...query.value, page: respondedPage.value } }),
                axios.get('/api/alerts-expired', { params: { ...query.value, page: expiredPage.value } }),
            ]);

            pendingAlerts.value = pending.data.data;
            pendingPagination.value = pending.data.meta;

            respondedAlerts.value = responded.data.data;
            respondedPagination.value = responded.data.meta;

            expiredAlerts.value = expired.data.data;
            expiredPagination.value = expired.data.meta;

            alerts.value = [
            ...pending.data.data,
            ...responded.data.data,
            ...expired.data.data
            ];

        } catch (error) {
            console.error('Error fetching alerts:', error);
        } finally {
            is_loading.value = false;
        }
    };



    // Update an alert by its ID
    const updateAlert = async (data) => {
        errors.value = {};
        is_loading.value = true;
        alert.value = data;

        try {
            await axios
                .patch(`/api/alerts/${alert.value.id}`, alert.value)
                .then((response) => {
                    Swal.fire({
                        title: "Success",
                        icon: "success",
                        text: response.data.message,
                    });
                    errors.value = {};
                    is_loading.value = false;
                    is_success.value = true;
                });
        } catch (e) {
            if (e.response.status === 422) {
                errors.value = e.response.data;
                is_success.value = false;
                is_loading.value = false;
            }
        }
    };

    // Delete an alert
    const destroyAlert = async (id) => {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.value) {
                axios
                    .delete(`/api/alerts/${id}`)
                    .then((response) => {
                        getAlerts(); // Refresh the list of alerts
                        Swal.fire("Deleted", response.data.message, "success");
                    })
                    .catch(() => {
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Something went wrong!",
                        });
                    });
            }
        });
    };

    // Mark an alert as responded (update response_id)
    const respondToAlert = async (alertId, responseId) => {
        const alertData = {
            response_id: responseId,
        };
        return updateAlert({ ...alertData, id: alertId });
    };

    return {
        alert,
        alerts,
        is_loading,
        is_success,
        errors,
        pagination,
        query,
        updateAlert,
        destroyAlert,
        getAlerts,
        respondToAlert,
        pendingAlerts,
        expiredAlerts,
        respondedAlerts,
        pendingPage,
        respondedPage,
        expiredPage,
        pendingPagination,
        respondedPagination,
        expiredPagination
    };
}

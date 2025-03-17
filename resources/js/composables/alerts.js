import { ref } from 'vue';
import axios from 'axios';
import Swal from "sweetalert2";

export default function useAlerts() {
    const alert = ref(null);
    const alerts = ref([]);
    const is_loading = ref(false);
    const is_success = ref(false);
    const errors = ref({});
    const pagination = ref({});
    const query = ref({
        search: null,
        page: 1,
    });

    // Fetch the alerts with pagination and search functionality
    const getAlerts = async (params = {}) => {
        is_loading.value = true;
        
        let query_str = { ...query.value, ...params };
        await axios
            .get('/api/alerts?page=' + query.value.page, { params: query_str })
            .then((response) => {
                alerts.value = response.data.data;
                pagination.value = response.data.meta;
                is_loading.value = false;
            })
            .catch(() => {
                is_loading.value = false;
            });
    };

    // Store a new alert
    const storeAlert = async (data) => {
        is_loading.value = true;
        errors.value = {};

        try {
            await axios
                .post('/api/alerts', data)
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
        storeAlert,
        updateAlert,
        destroyAlert,
        getAlerts,
        respondToAlert,
    };
}

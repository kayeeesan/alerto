import { ref } from 'vue';
import axios from 'axios';
import Swal from "sweetalert2";

export default function useSensorsUnderAlerto() {
    const sensor_under_alerto = ref(null);
    const sensors_under_alerto = ref([]);
    const is_loading = ref(false);
    const is_success = ref(false);
    const errors = ref({});
    const pagination = ref({});
    const query = ref({
        search: null,
        page: 1,
    });

    const getSensorsUnderAlerto = async (params = {}) => {
        is_loading.value = true;

        let query_str = { ...query.value, ...params };
        await axios
            .get('/api/sensors_under_alerto?page=' + query.value.page, query_str)
            .then((response) => {
                sensors_under_alerto.value = response.data.data;
                pagination.value = response.data.meta;
                is_loading.value = false;
            })
    }

    const storeSensorUnderAlerto = async (data) => {
        is_loading.value = true;
        errors.value = "";
        
        try{
            await axios
                .post(`/api/sensors_under_alerto`, data)
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
            if(e.response.status == 422) {
                errors.value = e.response.data;
                is_success.value = false;
                is_loading.value = false;
            }
        }
    }

    const updateSensorUnderAlerto = async (data) => {
        errors.value = "";
        is_loading.value = true;
        sensor_under_alerto.value = data;
        
        try{
            await axios
                .patch(`/api/sensors_under_alerto/${sensor_under_alerto.value.id}`, sensor_under_alerto.value)
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
            if(e.response.status == 422) {
                errors.value = e.response.data;
                is_success.value = false;
                is_loading.value = false;
            }
        }
    }

    const destorySensorUnderAlerto = async (id) => {
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
                    .delete(`/api/sensors_under_alerto/${id}`)
                    .then((response) => {
                        getSensorsUnderAlerto();
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
    }


    return {
        sensor_under_alerto,
        sensors_under_alerto,
        is_loading,
        is_success,
        errors,
        pagination, 
        query,
        storeSensorUnderAlerto,
        updateSensorUnderAlerto,
        destorySensorUnderAlerto,
        getSensorsUnderAlerto,
    }
}
import { ref } from 'vue';
import axios from 'axios';
import Swal from "sweetalert2";
import Pusher from 'pusher-js';
import Echo from 'laravel-echo';
import { eventBus } from './eventBus';

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

     const echo = new Echo({
        broadcaster: 'pusher',
        key: '57206333aea283adecc8',
        cluster: 'ap1',
        forceTLS: true,
    });

    echo.channel('public-sensors')
        .listen('.SensorUpdated', (event) => {
            getSensorsUnderAlerto();
            eventBus.$emit('sensor-updated', event);
        })
        
    const getSensorsUnderAlerto = async (params = {}, type = "") => {
        is_loading.value = true;
    
        let query_str = { ...query.value, ...params };
        let url = type === "/sensors_under_alerto" ? "/api/sensors_under_alerto" : "/api/form/sensors_under_alerto"
    
    
        await axios
            .get(`${url}?page=${query.value.page}`, { params: query_str })
            .then((response) => {
                sensors_under_alerto.value = response.data.data;
                pagination.value = response.data.meta || { last_page: 1 };
                is_loading.value = false;
            })
            .catch(() => {
                pagination.value = { last_page: 1 };
                is_loading.value = false;
            });
    };
    
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
                  Swal.fire({
                    title: "Error",
                    icon: "error",
                    text: "There was a problem with the information you provided. Please check and try again.",
                    });
            } else if (e.response.status === 409) {
                Swal.fire({
                    title: "Error",
                    icon: "error",
                    text: "A sensor with the same name already exists. Please choose a different device name.",
                });
                is_loading.value = false;
            } else {
                            // Handle other types of errors
                Swal.fire({
                    title: "Error",
                    icon: "error",
                    text: "An unexpected error occurred. Please try again later.",
                });
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
import { ref } from 'vue';
import axios from 'axios';
import Swal from "sweetalert2";
import Pusher from 'pusher-js';
import Echo from 'laravel-echo';
import { eventBus } from './eventBus';

export default function useSensorsUnderPh() {
    const sensor_under_ph = ref(null);
    const sensors_under_ph = ref([]);
    const is_loading = ref(false);
    const is_success = ref(false);
    const errors = ref({});
    const pagination = ref({});
    const query = ref({
        search: null,
        page: 1,
    });

    //  const echo = new Echo({
    //     broadcaster: 'pusher',
    //     key: '57206333aea283adecc8',
    //     cluster: 'ap1',
    //     forceTLS: true,
    // });

        const echo = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
            wsPort: Number(import.meta.env.VITE_REVERB_PORT) || 6001,
            wssPort: Number(import.meta.env.VITE_REVERB_PORT) || 6001,
            forceTLS: import.meta.env.VITE_REVERB_SCHEME === 'https',
            enabledTransports: import.meta.env.VITE_REVERB_SCHEME === 'https' ? ['wss'] : ['ws'],
          });
    

    // const echo = new Echo({
    //     broadcaster: 'reverb',
    //     key: '57206333aea283adecc8',
    //     wsHost: '127.0.0.1',
    //     wsPort: 6001,
    //     wssPort: 6001,
    //     forceTLS: false,
    //     enabledTransports: ['ws']
    // });

    echo.channel('public-sensors')
        .listen('.SensorUpdated', (event) => {
            getSensorsUnderPh();
            eventBus.$emit('sensor-updated', event);
        })

    const getSensorsUnderPh = async (params = {}, type = "") => {
        is_loading.value = true;

        let query_str = { ...query.value, ...params };
        let url = type === "/sensors_under_ph" ? "/api/sensors_under_ph" : "/api/form/sensors_under_ph"
        await axios
            // .get('/api/sensors_under_ph?page=' + query.value.page, query_str)
            .get(`${url}?page=${query.value.page}`, { params: query_str })
            .then((response) => {
                sensors_under_ph.value = response.data.data;
                pagination.value = response.data.meta;
                is_loading.value = false;
            })
    }

    const storeSensorUnderPh = async (data) => {
        is_loading.value = true;
        errors.value = "";
        
        try{
            await axios
                .post(`/api/sensors_under_ph`, data)
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
            }  else if (e.response.status === 409) {
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

    const updateSensorUnderPh = async (data) => {
        errors.value = "";
        is_loading.value = true;
        sensor_under_ph.value = data;
        
        try{
            await axios
                .patch(`/api/sensors_under_ph/${sensor_under_ph.value.id}`, sensor_under_ph.value)
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

    const destorySensorUnderPh = async (id) => {
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
                    .delete(`/api/sensors_under_ph/${id}`)
                    .then((response) => {
                        getSensorsUnderPh();
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
        sensor_under_ph,
        sensors_under_ph,
        is_loading,
        is_success,
        errors,
        pagination, 
        query,
        storeSensorUnderPh,
        updateSensorUnderPh,
        destorySensorUnderPh,
        getSensorsUnderPh,
    }
}
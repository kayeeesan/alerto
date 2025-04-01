import { ref } from 'vue';
import axios from 'axios';
import Swal from "sweetalert2";

export default function useRivers() {
    const river = ref(null);
    const rivers = ref([]);
    const is_loading = ref(false);
    const is_success = ref(false);
    const errors = ref({});
    const pagination = ref({});
    const query = ref({
        search: null,
        page: 1,
    });

    const getRivers = async (params = {}, type = "") => {
        is_loading.value = true;

        let query_str = { ...query.value, ...params };
        let url = type === "/rivers" ? "/api/rivers" : "/api/form/rivers";
        await axios
            // .get('/api/rivers?page=' + query.value.page, query_str)
            .get(`${url}?page=${query.value.page}`, { params: query_str })
            .then((response) => {
                rivers.value = response.data.data;
                pagination.value = response.data.meta;
                is_loading.value = false;
            });
    };

    const storeRiver = async (data) => {
        is_loading.value = true;
        errors.value = "";
        
        try{
            await axios
                .post(`/api/rivers`, data)
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

    const updateRiver = async (data) => {
        errors.value = "";
        is_loading.value = true;
        river.value = data;
        
        try{
            await axios
                .patch(`/api/rivers/${river.value.id}`, river.value)
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

    const destoryRiver = async (id) => {
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
                    .delete(`/api/rivers/${id}`)
                    .then((response) => {
                        getRivers();
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
        river,
        rivers,
        is_loading,
        is_success,
        errors,
        pagination, 
        query,
        storeRiver,
        updateRiver,
        destoryRiver,
        getRivers,
    }
}
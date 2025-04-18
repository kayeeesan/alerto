import { ref } from 'vue';
import axios from 'axios';
import Swal from "sweetalert2";

export default function useResponses() {
    const response = ref(null);
    const responses = ref([]);
    const is_loading = ref(false);
    const is_success = ref(false);
    const errors = ref({});
    const pagination = ref({});
    const query = ref({
        search: null,
        page: 1,
    });

    const getResponses = async (params = {}) => {
        is_loading.value = true;

        let query_str = { ...query.value, ...params };
        await axios
            .get('/api/responses?page=' + query.value.page, query_str)
            .then((response) => {
                responses.value = response.data.data;
                pagination.value = response.data.meta;
                is_loading.value = false;
            })
    }

    const storeResponse = async (data) => {
        is_loading.value = true;
        errors.value = "";
        
        try{
            await axios
                .post(`/api/responses`, data)
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

    const updateResponse = async (data) => {
        errors.value = "";
        is_loading.value = true;
        response.value = data;
        
        try{
            await axios
                .patch(`/api/responses/${response.value.id}`, response.value)
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

    const destoryResponse = async (id) => {
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
                    .delete(`/api/responses/${id}`)
                    .then((response) => {
                        getResponses();
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
        response,
        responses,
        is_loading,
        is_success,
        errors,
        pagination, 
        query,
        storeResponse,
        updateResponse,
        destoryResponse,
        getResponses,
    }
}
import { ref } from 'vue';
import axios from 'axios';
import Swal from "sweetalert2";

export default function useRegions() {
    const region = ref(null);
    const regions = ref([]);
    const is_loading = ref(false);
    const is_success = ref(false);
    const errors = ref({});
    const pagination = ref({});
    const query = ref({
        search: null,
        page: 1,
    });

    const getRegions = async (params = {}, type = "") => {
        is_loading.value = true;

        let query_str = { ...query.value, ...params };
        let url = type === "/regions" ? "/api/regions" : "/api/form/regions";
        await axios
            .get(`${url}?page=${query.value.page}`, { params: query_str})
            .then((response) => {
                regions.value = response.data.data;
                pagination.value = response.data.meta;
                is_loading.value = false;
            });
    };

    const getMultiselectRegions = async (params = {}, type = "") => {
        is_loading.value = true;

        try {
            let query_str = { ...query.value, ...params };
            let url = type === "/multiselect/regions" ? "/api/multiselect/regions" : "/api/form/multiselect/regions";

            const response = await axios.get(url, { params: query_str});
            regions.value = response.data.data;
        } finally {
            is_loading.value = false;
        }
    }

   const storeRegion = async (data) => {
        is_loading.value = true;
        errors.value = "";

        try {
            await axios.post(`/api/regions`, data)
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

                Swal.fire({
                    title: "Error",
                    icon: "error",
                    text: "There was a problem with the information you provided. Please check and try again.",
                });
            } else if (e.response.status === 409) { // Handle conflict (duplicate)
                Swal.fire({
                    title: "Duplicate Data",
                    icon: "error",
                    text: e.response.data.message || "This region already exists.",
                });
                is_loading.value = false;
                is_success.value = false;
            } else {
                Swal.fire({
                    title: "Error",
                    icon: "error",
                    text: "An unexpected error occurred. Please try again later.",
                });
                is_loading.value = false;
            }
        }
    };


    const updateRegion = async (data) => {
        errors.value = "";
        is_loading.value = true;
        region.value = data;
        
        try{
            await axios
                .patch(`/api/regions/${region.value.id}`, region.value)
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

    const destoryRegion = async (id) => {
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
                    .delete(`/api/regions/${id}`)
                    .then((response) => {
                        getRegions();
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
        region,
        regions,
        is_loading,
        is_success,
        errors,
        pagination, 
        query,
        storeRegion,
        updateRegion,
        destoryRegion,
        getRegions,
        getMultiselectRegions
    }
}
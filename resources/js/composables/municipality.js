import { ref } from 'vue';
import axios from 'axios';
import Swal from "sweetalert2";

export default function useMunicipalities() {
    const municipality = ref(null);
    const municipalities = ref([]);
    const is_loading = ref(false);
    const is_success = ref(false);
    const errors = ref({});
    const pagination = ref({});
    const query = ref({
        search: null,
        page: 1,
    });

    const getMunicipalities = async (params = {}, type = "") => {
        is_loading.value = true;

        let query_str = { ...query.value, ...params };
        let url = type === "/municipalities" ? "/api/municipalities" : "/api/form/municipalities"; 

        await axios
            .get(`${url}?page=${query.value.page}`, { params: query_str })
            .then((response) => {
                municipalities.value = response.data.data;
                pagination.value = response.data.meta;
                is_loading.value = false;
            })
    }

    const storeMunicipality = async (data) => {
        is_loading.value = true;
        errors.value = "";

        try {
            await axios
                .post(`/api/municipalities`, data)
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
            } else if (e.response.status === 409) {
                // Handle duplicate data error
                Swal.fire({
                    title: "Duplicate Data",
                    icon: "error",
                    text: e.response.data.message || "Municipality with this name already exists in the selected province.",
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


    const updateMunicipality = async (data) => {
        errors.value = "";
        is_loading.value = true;
        municipality.value = data;
        
        try{
            await axios
                .patch(`/api/municipalities/${municipality.value.id}`, municipality.value)
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
            }
        }
    }

    const destoryMunicipality = async (id) => {
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
                    .delete(`/api/municipalities/${id}`)
                    .then((response) => {
                        getMunicipalities();
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
        municipality,
        municipalities,
        is_loading,
        is_success,
        errors,
        pagination, 
        query,
        storeMunicipality,
        updateMunicipality,
        destoryMunicipality,
        getMunicipalities,
    }
}
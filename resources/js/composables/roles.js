import { ref } from 'vue';
import axios from 'axios';
import Swal from "sweetalert2";

export default function useRoles() {
    const role = ref(null);
    const roles = ref([]);
    const is_loading = ref(false);
    const is_success = ref(false);
    const errors = ref({});
    const pagination = ref({});
    const query = ref({
        search: null,
        page: 1,
    });

    // const getRoles = async (params = {}) => {
    //     is_loading.value = true;

    //     let query_str = { ...query.value, ...params };
    //     let url = type == "/roles" ? "/roles" : "/form/roles"
    //     // let url = params.type && params.type === "/roles" ? "/roles" : "/form/roles";

    //     await axios
    //         // .get('/api/roles?page=' + query.value.page, query_str)
    //         .get(`${url}?page=${query.value.page}` , {params: query_str})
    //         .then((response) => {
    //             roles.value = response.data.data;
    //             pagination.value = response.data.meta;
    //             is_loading.value = false;
    //         });
    // };

    // const getRoles = async (params = {}, type = "") => {
    //     is_loading.value = true;

    //     let query_str = { ...query.value, ...params};
    //     let url = type == "/roles" ? "/roles" : "/form/roles";

    //     await axios
    //         .get(`${url}?page=${query.value.page}`, { params: query_str})
    //         .then((response) => {
    //             roles.value = response.data.data;
    //             pagination.value = response.data.meta;
    //             is_loading.value = false;
    //         });
    // };

    const getRoles = async (params = {}, type = "") => {
        is_loading.value = true;
    
        let query_str = { ...query.value, ...params };
        let url = type === "/roles" ? "/api/roles" : "/api/form/roles";  // Make sure this is correct
    
    
        await axios
            .get(`${url}?page=${query.value.page}`, { params: query_str })
            .then((response) => {
                roles.value = response.data.data;
                pagination.value = response.data.meta;
                is_loading.value = false;
            })
            .catch((error) => {
                is_loading.value = false;
            });
    };
    

    const storeRole = async (data) => {
        is_loading.value = true;
        errors.value = "";
        
        try{
            await axios
                .post(`/api/roles`, data)
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

    const updateRole = async (data) => {
        errors.value = "";
        is_loading.value = true;
        role.value = data;
        
        try{
            await axios
                .patch(`/api/roles/${role.value.id}`, role.value)
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

    const destoryRole = async (id) => {
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
                    .delete(`/api/roles/${id}`)
                    .then((response) => {
                        getRoles();
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
        role,
        roles,
        is_loading,
        is_success,
        errors,
        pagination, 
        query,
        storeRole,
        updateRole,
        destoryRole,
        getRoles,
    }
}
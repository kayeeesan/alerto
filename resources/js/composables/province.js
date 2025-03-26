import { ref } from 'vue';
import axios from 'axios';
import Swal from "sweetalert2";

export default function useProvinces() {
    const province = ref(null);
    const provinces = ref([]);
    const is_loading = ref(false);
    const is_success = ref(false);
    const errors = ref({});
    const pagination = ref({});
    const query = ref({
        search: null,
        page: 1,
    });

    const getProvinces = async (params = {}, type = "") => {
        is_loading.value = true;

        let query_str = { ...query.value, ...params };
        let url = type === "/provinces" ? "/api/provinces" : "/api/form/provinces";
        await axios
            // .get('/api/provinces?page=' + query.value.page, query_str)
            .get(`${url}?page=${query.value.page}`, { params: query_str })
            .then((response) => {
                provinces.value = response.data.data;
                pagination.value = response.data.meta;
                is_loading.value = false;
            })
    }

    const storeProvince = async (data) => {
        is_loading.value = true;
        errors.value = "";
        
        try{
            await axios
                .post(`/api/provinces`, data)
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

    const updateProvince = async (data) => {
        errors.value = "";
        is_loading.value = true;
        province.value = data;
        
        try{
            await axios
                .patch(`/api/provinces/${province.value.id}`, province.value)
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

    const destoryProvince = async (id) => {
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
                    .delete(`/api/provinces/${id}`)
                    .then((response) => {
                        getProvinces();
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
        province,
        provinces,
        is_loading,
        is_success,
        errors,
        pagination, 
        query,
        storeProvince,
        updateProvince,
        destoryProvince,
        getProvinces,
    }
}
import { ref } from 'vue';
import axios from 'axios';
import Swal from "sweetalert2";

export default function useStaffs() {
    const staff = ref(null);
    const staffs = ref([]);
    const is_loading = ref(false);
    const is_success = ref(false);
    const errors = ref({});
    const pagination = ref({});
    const query = ref({
        search: null,
        page: 1,
    });

    const getStaffs = async (params = {}, type = "") => {
        is_loading.value = true;

        let query_str = { ...query.value, ...params };
        let url = type === "/staffs" ? "/api/staffs" : "/api/form/staffs";
        await axios
            .get(`${url}?page=${query.value.page}`, { params: query_str })
            .then((response) => {
                staffs.value = response.data.data;
                pagination.value = response.data.meta;
                is_loading.value = false;
            })
    }

    const storeStaff = async (data) => {
        is_loading.value = true;
        errors.value = "";
        
        try{
            await axios
                .post(`/api/staffs`, data)
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

    const updateStaff = async (data) => {
        errors.value = "";
        is_loading.value = true;
        staff.value = data;
        
        try{
            await axios
                .patch(`/api/staffs/${staff.value.id}`, staff.value)
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

    const destoryStaff = async (id) => {
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
                    .delete(`/api/staffs/${id}`)
                    .then((response) => {
                        getStaffs();
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
        staff,
        staffs,
        is_loading,
        is_success,
        errors,
        pagination, 
        query,
        storeStaff,
        updateStaff,
        destoryStaff,
        getStaffs,
    }
}
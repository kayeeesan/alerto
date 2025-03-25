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
    const querySearch = ref({
        search: null,
        page: 1,
    });

    const getStaffs = async (params = {}) => {
        is_loading.value = true;

        let query_str = { ...querySearch.value, ...params };
        await axios
            .get('/api/staffs', { params: query_str })
            .then((response) => {
                staffs.value = response.data.data;
                pagination.value = response.data.meta;
            })
            .catch((error) => {
                console.error("Error fetching staffs:", error);
            })
            .finally(() => {
                is_loading.value = false;
            });
    };

    const storeStaff = async (data) => {
        is_loading.value = true;
        errors.value = {};

        try {
            await axios.post(`/api/staffs`, data).then((response) => {
                Swal.fire({
                    title: "Success",
                    icon: "success",
                    text: response.data.message,
                });
                errors.value = {};
                is_success.value = true;
                getStaffs(); // Refresh list
            });
        } catch (e) {
            if (e.response && e.response.status === 422) {
                errors.value = e.response.data.errors;
            } else {
                console.error("Error storing staff:", e);
            }
            is_success.value = false;
        } finally {
            is_loading.value = false;
        }
    };

    const updateStaff = async (data) => {
        is_loading.value = true;
        errors.value = {};

        try {
            await axios.patch(`/api/staffs/${data.id}`, data).then((response) => {
                Swal.fire({
                    title: "Success",
                    icon: "success",
                    text: response.data.message,
                });
                errors.value = {};
                is_success.value = true;
                getStaffs(); // Refresh list
            });
        } catch (e) {
            if (e.response && e.response.status === 422) {
                errors.value = e.response.data.errors;
            } else {
                console.error("Error updating staff:", e);
            }
            is_success.value = false;
        } finally {
            is_loading.value = false;
        }
    };

    const destroyStaff = async (id) => {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.isConfirmed) {
                axios
                    .delete(`/api/staffs/${id}`)
                    .then((response) => {
                        Swal.fire("Deleted", response.data.message, "success");
                        getStaffs(); // Refresh list
                    })
                    .catch((error) => {
                        console.error("Error deleting staff:", error);
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Something went wrong!",
                        });
                    });
            }
        });
    };

    return {
        staff,
        staffs,
        is_loading,
        is_success,
        errors,
        pagination,
        querySearch,
        getStaffs,
        storeStaff,
        updateStaff,
        destroyStaff,
    };
}

import { ref } from 'vue';
import axios from 'axios';
import Swal from "sweetalert2";

export default function useContactMessages() {
    const contact_message = ref(null);
    const contact_messages = ref([]);
    const is_loading = ref(false);
    const is_success = ref(false);
    const errors = ref({});
    const pagination = ref({});
    const query = ref({
        search: null,
        page: 1,
    });

    const getContactMessages = async (params = {}) => {
        is_loading.value = true;

        let url = "/api/messages";

        try {
            const response = await axios.get(url, { params: { ...query.value, ...params } });

            if (response.data.data) {
                contact_messages.value = response.data.data;
                pagination.value = response.data.meta || {};
            } else {
                contact_messages.value = response.data;
            }
    
            is_loading.value = false;
        } catch (error) {
            console.error("Error fetching message:", error);
            is_loading.value = false;
        }
    }

    const storeContactMessage = async (data) => {
        is_loading.value = true;
        errors.value = "";

        try {
            await axios
                .post(`/api/form/messages`, data)
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
            }else {
                Swal.fire({
                    title: "Error",
                    icon: "error",
                    text: "An unexpected error occurred. Please try again later.",
                });
                    is_loading.value = false;
                }
        }
    }

    return {
        contact_message,
        contact_messages,
        is_loading,
        is_success,
        errors,
        pagination,
        query,
        storeContactMessage,
        getContactMessages
    }
}
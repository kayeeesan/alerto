import { ref } from "vue";
import axios from "axios";
import Swal from "sweetalert2";

export default function useContactMessages() {
  const errors = ref({});
  const is_loading = ref(false);
  const is_success = ref(false);

    const storeContactMessage = async (data = {}) => {
        is_loading.value = true;
        errors.value = {};

        try {
        // Make API call to store contact message
        const response = await axios.post(`/api/contact-us`, data);

        // Show success alert
        Swal.fire({
            title: "Success",
            icon: "success",
            text: response.data.message,
        });

        is_success.value = true;
        is_loading.value = false;

        } catch (e) {
        if (e.response && e.response.status === 422) {
            errors.value = e.response.data.errors;
        } else {
            Swal.fire({
            title: "Error",
            icon: "error",
            text: "An unexpected error occurred. Please try again later.",
            });
        }

        is_loading.value = false;
        is_success.value = false;
        }
    };

  return {
    errors,
    is_loading,
    is_success,
    storeContactMessage,
  };
}

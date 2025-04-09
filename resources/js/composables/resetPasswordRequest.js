import { ref } from 'vue';
import axios from 'axios';

export default function useResetPasswordRequest() {
    const isLoading = ref(false);

    const sendResetLink = async (username) => {
        isLoading.value = true;
        try {
            await axios.post('/api/forgot-password', {
                username: username
            });
            return true;
        } catch (error) {
            alert(error.response?.data?.message || 'Something went wrong.');
            return false;
        } finally {
            isLoading.value = false;
        }
    };

    return {
        isLoading,
        sendResetLink
    };
}

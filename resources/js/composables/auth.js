import { ref } from 'vue';
import axios from 'axios';
import { useStore } from "vuex";
import { useRouter } from 'vue-router';
import Swal from "sweetalert2";

export default function useAuth() {
    const user = ref(null);
    const is_loading = ref(false);
    const store =  useStore();
    const error = ref("");
    const router = useRouter();

    const login = async (data) => {
        is_loading.value = true;

        try{
            await axios.get('/sanctum/csrf-cookie')
            await axios.post('/api/login', data)
            .then((response) => {
                if(response.data.user){
                    user.value = response.data.user;
        
                    const now = new Date();
                    const expiry = now.getTime() + 8 * 60 * 60 * 1000; // expire every 8 hours

                    localStorage.setItem('expiry', expiry);
                    localStorage.setItem('token', response.data.access_token);

                     // Store the tenant path if provided
                    if (response.data.tenant_db_path) {
                        localStorage.setItem('tenant_db_path', response.data.tenant_db_path);
                    }

                    store.commit('auth/SET_USER', user.value);
                    store.commit('auth/SET_AUTHENTICATED',true);
                    store.commit('auth/SET_PASSWORD_RESET',user.value.password_reset);
                    location.reload();
                }
            })
        } catch (e) {
            error.value = e.response.data;
            is_loading.value = false;
        }
    }

    const logout = async () => {
        Swal.fire({
            title: "Logout",
            text: "Are you sure you want to logout?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, logout!",
        }).then(async (result) => {
            if (result.isConfirmed) {
                is_loading.value = true;
                try{
                    await axios.post('/api/logout');
                    localStorage.clear();
                    store.dispatch('auth/logout');
                    is_loading.value = false;
                        router.push("/");
                } catch (err) {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Something went wrong!",
                    });
                    is_loading.value = false;
                }
            }
        });
    }

    const setPassword = async (data) => {
        error.value = null;
        is_loading.value = true;
        
        try {
            await axios.post("/api/set-password", data)
            .then((response) => {
                Swal.fire({
                    title: "Success",
                    icon: "success",
                    text: response.data.message,
                });
            });
            localStorage.clear();
            store.dispatch('auth/logout');
            
            location.reload();
        } catch (err) {
            error.value = err.response.data.message || "An error occurred.";
        }
        is_loading.value = false;
    }

    return {
        user,
        is_loading,
        error,
        login,
        logout,
        setPassword
    }
}
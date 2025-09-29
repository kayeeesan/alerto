<script setup>
import { ref } from "vue";
import { useRouter } from 'vue-router';
import useResetPasswordRequest from "../../composables/resetPasswordRequest";
import Swal from "sweetalert2";

const email = ref('');
const router = useRouter();
const { isLoading, sendResetLink } = useResetPasswordRequest();

const emit = defineEmits(['close']);

const handleSend = async () => {
    const success = await sendResetLink(email.value);
    if (success) {
        Swal.fire({
            icon: "success",
            title: "Link Sent!",
            text: "We've sent a password reset link to your email.",
            confirmButtonColor: "#011a6e",
        });
        emit('close');
        router.push('/');
    }
};

const handleClose = () => {
    emit('close');
};
</script>

<template>
    <div class="reset-container">
        <div class="reset-card">
            <!-- Header Section -->
            <div class="avatar">
                <img src="https://rdrrmc9-alerto.com/assets/images/logo3.png" alt="Alerto Logo" />
            </div>

            <h2 class="title">Reset Password</h2>
            <p class="subtitle">Enter your email to receive a reset link</p>

            <!-- Email Input Section -->
            <form @submit.prevent="handleSend" class="form">
                <div class="input-box">
                    <i class="mdi mdi-email-outline input-icon"></i>
                    <input
                        v-model="email"
                        type="email"
                        placeholder="Email Address"
                        required
                        :disabled="isLoading"
                    />
                </div>

                <!-- Action Buttons -->
                <div class="button-group">
                    <button 
                        type="button" 
                        class="btn btn-secondary"
                        @click="handleClose"
                        :disabled="isLoading"
                    >
                        <i class="mdi mdi-arrow-left"></i>
                        Back
                    </button>
                    <button 
                        type="submit" 
                        class="btn btn-primary"
                        :disabled="isLoading"
                    >
                        <span v-if="isLoading">Sending...</span>
                        <span v-else>
                            <i class="mdi mdi-email-arrow-right"></i>
                            Send Link
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
.reset-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #011a6e, #5487ca);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.reset-card {
    width: 420px;
    background: #fff;
    border-radius: 20px;
    padding: 40px 30px;
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.25);
    text-align: center;
    animation: fadeInUp 0.6s ease;
}

.avatar {
    width: 110px;
    height: 110px;
    margin: -90px auto 20px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.avatar img {
    width: 100%;
    height: auto;
}

.title {
    font-size: 22px;
    font-weight: bold;
    color: #011a6e;
    margin-bottom: 5px;
}

.subtitle {
    font-size: 14px;
    color: #777;
    margin-bottom: 25px;
}

.form {
    display: flex;
    flex-direction: column;
}

.input-box {
    position: relative;
    margin: 15px 0 25px 0;
}
.input-box input {
    width: 100%;
    padding: 12px 45px 12px 40px;
    border: 1px solid #ccc;
    border-radius: 30px;
    outline: none;
    font-size: 14px;
    transition: 0.3s;
}
.input-box input:focus {
    border-color: #011a6e;
    box-shadow: 0 0 8px rgba(1, 26, 110, 0.2);
}
.input-box input:disabled {
    background-color: #f5f5f5;
    cursor: not-allowed;
}

.input-icon {
    position: absolute;
    top: 12px;
    left: 14px;
    font-size: 18px;
    color: #888;
}

.button-group {
    display: flex;
    gap: 12px;
    justify-content: space-between;
}

.btn {
    flex: 1;
    padding: 14px;
    border: none;
    border-radius: 30px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-primary {
    background: linear-gradient(135deg, #011a6e, #2a5da8);
    color: #fff;
}

.btn-primary:hover:not(:disabled) {
    background: linear-gradient(135deg, #2a5da8, #5487ca);
}

.btn-secondary {
    background: #f5f5f5;
    color: #555;
    border: 1px solid #ddd;
}

.btn-secondary:hover:not(:disabled) {
    background: #e0e0e0;
}

/* Animation */
@keyframes fadeInUp {
    0% {
        transform: translateY(40px);
        opacity: 0;
    }
    100% {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Responsive Design */
@media (max-width: 480px) {
    .reset-card {
        width: 90%;
        margin: 20px;
        padding: 30px 20px;
    }
    
    .button-group {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
}
</style>
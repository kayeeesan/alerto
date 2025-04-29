<script setup>
import { ref } from "vue";
import { useRouter } from 'vue-router';
import useResetPasswordRequest from "../../composables/resetPasswordRequest";
import Swal from "sweetalert2";

const email = ref('');
const router = useRouter();
const { isLoading, sendResetLink } = useResetPasswordRequest();

const handleSend = async () => {
    const success = await sendResetLink(email.value);
    if (success) {
        Swal.fire({
            icon: "success",
            title: "Link Sent!",
            text: "We've sent a password reset link to your email.",
            confirmButtonColor: "#4f46e5",
        });
        router.push('/');
    }
};
</script>

<template>
    <v-app>
        <v-layout class="bg-indigo-darken-1" style="position: relative; overflow: hidden;">
            <!-- Circular background elements matching login page -->
            <div class="circle-bg" style="
                position: absolute;
                top: -100px;
                right: -100px;
                width: 400px;
                height: 400px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.1);
            "></div>
            <div class="circle-bg" style="
                position: absolute;
                bottom: -150px;
                left: -150px;
                width: 500px;
                height: 500px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.08);
            "></div>
            <div class="circle-bg" style="
                position: absolute;
                top: 50%;
                left: 30%;
                width: 300px;
                height: 300px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.05);
            "></div>
            
            <v-container fluid class="fill-height d-flex align-center justify-center" style="position: relative; z-index: 1;">
                <v-card class="pa-8" elevation="8" width="448" rounded="lg">
                    <div class="text-center mb-6">
                        <router-link :to="'/'">
                            <v-img
                                class="mx-auto mb-4"
                                max-width="100"
                                src="https://rdrrmc9-alerto.com/assets/images/logo3.png"
                            ></v-img>
                            </router-link>
                        <v-card-title class="text-h4 font-weight-bold text-primary">Reset Password</v-card-title>
                        <v-card-subtitle class="text-caption">Enter your email to receive a reset link</v-card-subtitle>
                    </div>
                    
                    <v-card-text>
                        <v-form @submit.prevent="handleSend">
                            <v-text-field
                                v-model="email"
                                variant="outlined"
                                label="Email Address"
                                type="email"
                                required
                                prepend-inner-icon="mdi-email-outline"
                                color="primary"
                                :disabled="isLoading"
                                class="mb-4"
                            />
                            
                            <v-row>
                                <v-col>
                                    <v-btn
                                        color="red darken-2"
                                        size="large"
                                        variant="flat"
                                        block
                                        :to="'/login'"
                                    >
                                        Cancel
                                    </v-btn>
                                </v-col>
                                <v-col>
                                    <v-btn
                                        color="primary"
                                        size="large"
                                        variant="flat"
                                        block
                                        @click="handleSend"
                                        :loading="isLoading"
                                    >
                                        Send Link
                                    </v-btn>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-container>
        </v-layout>
    </v-app>
</template>

<style scoped>
/* Matching animation for the circles */
.circle-bg {
    animation: float 15s infinite ease-in-out;
}

.circle-bg:nth-child(1) {
    animation-delay: 0s;
}
.circle-bg:nth-child(2) {
    animation-delay: 3s;
}
.circle-bg:nth-child(3) {
    animation-delay: 6s;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }
    50% {
        transform: translateY(-20px) rotate(5deg);
    }
}
</style>
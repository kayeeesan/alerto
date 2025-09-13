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
            <!-- Background Elements -->
            <div class="bg-circle" style="
                position: absolute;
                top: -100px;
                right: -100px;
                width: 400px;
                height: 400px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.1);
            "></div>
            <div class="bg-circle" style="
                position: absolute;
                bottom: -150px;
                left: -150px;
                width: 500px;
                height: 500px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.08);
            "></div>
            
            <v-container fluid class="fill-height d-flex align-center justify-center">
                <v-card class="pa-8" elevation="8" width="500px" rounded="lg" style="position: relative; z-index: 1;">
                    <!-- Header Section -->
                    <div class="text-center mb-6">
                        <router-link :to="'/'">
                            <v-img
                                class="mx-auto mb-4"
                                max-width="100"
                                src="https://rdrrmc9-alerto.com/assets/images/logo3.png"
                            ></v-img>
                        </router-link>
                        <v-card-title class="text-h5 text-md-h4 font-weight-bold text-primary">
                            Reset Password
                        </v-card-title>
                        <v-card-subtitle class="text-caption">Enter your email to receive a reset link</v-card-subtitle>
                    </div>

                    <v-card-text>
                        <v-form @submit.prevent="handleSend">
                            <!-- Email Input Section -->
                            <v-card variant="outlined" class="mb-6 pa-4" color="blue-darken-4">
                                <v-card-title class="text-subtitle-1 font-weight-bold">Account Recovery</v-card-title>
                                
                                <v-row>
                                    <v-col cols="12">
                                        <div class="d-flex">
                                            <v-icon class="mt-3 mr-3" style="color: #6E92C1;">mdi-email</v-icon>
                                            <v-text-field
                                                v-model="email"
                                                variant="outlined"
                                                label="Email Address*"
                                                type="email"
                                                required
                                                density="comfortable"
                                                :disabled="isLoading"
                                                bg-color="white"
                                                class="dark-input"
                                                :rules="[
                                                    (v) => !!v || 'Email is required',
                                                    (v) => /.+@.+\..+/.test(v) || 'Invalid email address',
                                                ]"
                                            />
                                        </div>
                                    </v-col>
                                </v-row>
                            </v-card>
                        </v-form>
                    </v-card-text>

                    <!-- Action Buttons -->
                    <v-card-actions class="justify-end pt-4">
                        <v-row>
                            <v-col class="d-flex justify-end">
                                <v-btn 
                                    color="red-darken-2" 
                                    variant="flat" 
                                    :to="'/login'"
                                    size="large"
                                    class="mr-2"
                                >
                                    <v-icon>mdi-arrow-left</v-icon>
                                </v-btn>
                          
                                <v-btn 
                                    color="blue-darken-4" 
                                    variant="flat" 
                                    :loading="isLoading" 
                                    @click="handleSend"
                                    size="large"
                                >
                                    <v-icon>mdi-email-arrow-right</v-icon>
                                </v-btn>
                            </v-col>
                        </v-row>
                    </v-card-actions>
                </v-card>
            </v-container>
        </v-layout>
    </v-app>
</template>

<style scoped>
.bg-circle {
    animation: float 15s infinite ease-in-out;
    filter: blur(1px);
}

.bg-circle:nth-child(1) {
    animation-delay: 0s;
}
.bg-circle:nth-child(2) {
    animation-delay: 3s;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }
    50% {
        transform: translateY(-20px) rotate(5deg);
    }
}

.v-card {
    backdrop-filter: blur(5px);
    background-color: rgba(255, 255, 255, 0.9);
}

.v-card-title {
    color: #646dcf;
}
</style>
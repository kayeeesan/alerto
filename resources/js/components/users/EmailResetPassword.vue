<script setup>
import { ref } from "vue";
import { useRouter } from 'vue-router';
import useResetPasswordRequest from "../../composables/resetPasswordRequest"; // ✅ correct import path
import Swal from "sweetalert2";

const email = ref('');
const router = useRouter();

// ✅ only call composable once and destructure directly
const { isLoading, sendResetLink } = useResetPasswordRequest();

const handleSend = async () => {
    const success = await sendResetLink(email.value);
    if (success) {
        // alert("Reset link sent! Please check your email.");
        Swal.fire({
            icon: "success",
            title: "Success",
            text: "Reset link sent! Please check your email.",
        });
        router.push('/');
    }
};
</script>

<template>
    <v-app>
        <v-layout class="bg-indigo-darken-1">
            <v-container fluid class="fill-height d-flex align-center justify-center">
                <v-card class="pa-8" elevation="8" width="600px" rounded="lg">
                    <v-card-title class="text-h5 text-center font-weight-bold">Email Verification</v-card-title>
                    <v-card-text>
                        <v-form @submit.prevent="handleSend">
                            <v-row>
                                <v-col>
                                    <v-text-field
                                        v-model="email"
                                        variant="outlined"
                                        label="Email*"
                                        type="email"
                                        required
                                    />
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                    <v-card-actions class="justify-end">
                        <v-btn
                            color="red-darken-4"
                            variant="flat"
                            :to="'/'"
                        >
                            Cancel
                        </v-btn>
                        <v-btn
                            color="blue-darken-4"
                            variant="flat"
                            @click="handleSend"
                            :loading="isLoading"
                        >
                            Send
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-container>
        </v-layout>
    </v-app>
</template>

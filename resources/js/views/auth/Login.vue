<script setup>
import { ref, reactive } from 'vue';
import useAuth from '../../composables/auth.js';
import { useRouter } from 'vue-router';

const { error, is_loading, login } = useAuth();

const form = reactive({
    username: '',
    password: ''
});

const visible = ref(false);

const handleSubmit = async () => {
    await login({ ...form });
}
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
                        <v-card-title class="text-h4 text-md-h4 font-weight-bold text-primary">
                            Welcome Back
                        </v-card-title>
                        <v-card-subtitle class="text-caption">Sign in to your account</v-card-subtitle>
                    </div>

                    <v-card-text>
                        <div v-if="error" class="alert alert-danger mb-4" role="alert">
                            {{ error.message }}
                        </div>

                        <v-form>
                            <!-- Login Information Section -->
                            <v-card variant="outlined" class="mb-6 pa-4" color="blue-darken-4">
                                <v-card-title class="text-subtitle-1 font-weight-bold">Login Information</v-card-title>
                                
                                <v-row>
                                    <v-col cols="12">
                                        <div class="d-flex">
                                            <v-icon class="mt-3 mr-3" style="color: #6E92C1;">mdi-email</v-icon>
                                            <v-text-field
                                                v-model="form.username"
                                                placeholder="Enter your username"
                                                variant="outlined"
                                                density="comfortable"
                                                bg-color="white"
                                                class="dark-input"
                                                :rules="[
                                                    (v) => !!v || 'Username is required'
                                                ]"
                                            ></v-text-field>
                                        </div>
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <v-col cols="12">
                                        <div class="d-flex">
                                            <v-icon class="mt-3 mr-3" style="color: #6E92C1;">mdi-lock</v-icon>
                                            <v-text-field
                                                v-model="form.password"
                                                :type="visible ? 'text' : 'password'"
                                                placeholder="Enter your password"
                                                variant="outlined"
                                                density="comfortable"
                                                bg-color="white"
                                                class="dark-input"
                                                :rules="[
                                                    (v) => !!v || 'Password is required'
                                                ]"
                                                @keyup.enter="handleSubmit()"
                                            >
                                                <template v-slot:append-inner>
                                                    <v-icon
                                                        :icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                                                        @click="visible = !visible"
                                                        style="cursor: pointer;"
                                                    ></v-icon>
                                                </template>
                                            </v-text-field>
                                        </div>
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <v-col class="text-right">
                                        <router-link to="/email-reset-password" class="text-sm text-blue-darken-2 text-decoration-none">
                                            Forgot Password?
                                        </router-link>
                                    </v-col>
                                </v-row>
                            </v-card>
                        </v-form>
                    </v-card-text>

                    <!-- Action Buttons -->
                    <v-card-actions class="justify-end pt-4 pr-4">
                        <v-row>
                            <v-col class="d-flex justify-end">
                                <v-btn 
                                    color="red-darken-2" 
                                    variant="flat" 
                                    :to="'/'"
                                    size="large"
                                    class="mr-2"
                                >
                                    <v-icon>mdi-home</v-icon>
                                </v-btn>
                          
                                <v-btn 
                                    color="blue-darken-4" 
                                    variant="flat" 
                                    :loading="is_loading" 
                                    @click="handleSubmit"
                                    size="large"
                                >
                                    <v-icon>mdi-login</v-icon>
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

.alert-danger {
    background-color: #ffebee;
    color: #c62828;
    padding: 12px;
    border-radius: 4px;
    border-left: 4px solid #c62828;
}
</style>
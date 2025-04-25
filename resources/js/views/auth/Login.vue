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
      <!-- Circular background elements -->
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
      
      <v-main style="background-color: transparent; position: relative; z-index: 1;" class="mt-7">
        <div>
          <v-card
            class="mx-auto pa-12 pb-8 mt-15"
            elevation="8"
            max-width="448"
            rounded="lg"
          >
          <div class="text-center mb-6">
                  <v-img
                    class="mx-auto mb-4"
                    max-width="100"
                    src="https://rdrrmc9-alerto.com/assets/images/logo3.png"
                  ></v-img>
                  <v-card-title class="text-h4 font-weight-bold text-primary">Welcome Back</v-card-title>
                  <v-card-subtitle class="text-caption">Sign in to your account</v-card-subtitle>
                </div>

            <div v-if="error" class="alert alert-danger" role="alert">
                {{  error.message }}
            </div>

            <div class="text-subtitle-1 text-medium-emphasis">
                Username
            </div>
      
            <v-text-field
                v-model="form.username"
                placeholder="Enter your username"
                prepend-inner-icon="mdi-email-outline"
                variant="outlined"
            ></v-text-field>
      
            <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
              Password
            </div>
      
            <v-text-field
                v-model="form.password"
                :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                :type="visible ? 'text' : 'password'"
                placeholder="Enter your password"
                prepend-inner-icon="mdi-lock-outline"
                variant="outlined"
                @click:append-inner="visible = !visible"
                @keyup.enter="handleSubmit()"
            ></v-text-field>
            <v-row>
              <v-col>
                <router-link to="/email-reset-password" class="text-sm text-blue-500 hover:underline">
                Forgot Password?
              </router-link>
              </v-col>
            </v-row>
            <v-row>
              <v-col>
                <v-btn
                  class="mb-15"
                  color="red darken-2"
                  size="large"
                  variant="flat"
                  block
                  :to="'/'"
                >
                 Cancel
                </v-btn>
              </v-col>
              <v-col>
                <v-btn
                  class="mb-15"
                  color="primary"
                  size="large"
                  variant="flat"
                  block
                  @click="handleSubmit()"
                  :loading="is_loading"
                >
                Login
                </v-btn>
              </v-col>
            </v-row>
        
          </v-card>
        </div>
      </v-main>
    </v-layout>
  </v-app>
</template>

<style scoped>
/* Optional animation for the circles */
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
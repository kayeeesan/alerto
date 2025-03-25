<script setup>
import { ref, reactive } from 'vue';
import useAuth from '../../composables/auth.js';

const { error, is_loading, setPassword } = useAuth();

const form = reactive({
    password: "",
    password_confirmation: "",
});

const visible = ref(false);
const handleSubmit = async () => {
    await setPassword({ ...form });
};
</script>
<template>
  <v-app>
    <v-layout class="bg-indigo-darken-1">
      <v-main style="background-color: transparent;" class="mt-7">
        <div>
          <v-card
            class="mx-auto pa-12 pb-8 mt-15"
            elevation="8"
            max-width="448"
            rounded="lg"
          >

            <v-img
                class="mx-auto mb-10"
                max-width="120"
                src="https://rdrrmc9-alerto.com/assets/images/logo3.png"
            ></v-img>

            <h3 class="text-center mb-15">Set your password</h3>

            <div v-if="error" class="alert alert-danger" role="alert">
                {{  error }}
            </div>

            <div class="text-subtitle-1 text-medium-emphasis">
                New Password
            </div>
      
            <v-text-field
                v-model="form.password"
                :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                :type="visible ? 'text' : 'password'"
                placeholder="Enter your new password"
                prepend-inner-icon="mdi-lock-outline"
                variant="outlined"
                @click:append-inner="visible = !visible"
            ></v-text-field>
      
            <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
              Confirm Password
            </div>
      
            <v-text-field
                v-model="form.password_confirmation"
                :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                :type="visible ? 'text' : 'password'"
                placeholder="Re-enter your new password"
                prepend-inner-icon="mdi-lock-outline"
                variant="outlined"
                @click:append-inner="visible = !visible"
                @keyup.enter="handleSubmit()"
            ></v-text-field>

      
            <v-btn
              class="mb-15"
              color="primary"
              size="large"
              variant="tonal"
              block
              @click="handleSubmit()"
              :loading="is_loading"
            >
              Submit
            </v-btn>
          </v-card>
        </div>
      </v-main>
    </v-layout>
  </v-app>

   
  </template>
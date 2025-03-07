<script setup>
import { ref, reactive } from 'vue';
import useAuth from '../../composables/auth.js';

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
    <div>
      <v-card
        class="mx-auto pa-12 pb-8 mt-15"
        elevation="8"
        max-width="448"
        rounded="lg"
      >
        <v-img
            class="mx-auto mb-10"
            max-width="300"
            src="./images/BIS.png"
        ></v-img>

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

  
        <v-btn
          class="mb-15"
          color="primary"
          size="large"
          variant="tonal"
          block
          @click="handleSubmit()"
          :loading="is_loading"
        >
          Log In
        </v-btn>
      </v-card>
    </div>
  </template>
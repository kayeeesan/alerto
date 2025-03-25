<script setup>
import { ref } from "vue";
import Login from "../../views/auth/loginII.vue";

const isDialogActive = ref(false);
</script>

<template>
  <v-app-bar style="background: #003092;">
    <v-app-bar-nav-icon @click="$emit('toggle-sidebar')"></v-app-bar-nav-icon>

    <v-responsive width="260">
      <v-text-field
        density="compact"
        label="Search"
        rounded="lg"
        variant="solo-filled"
        flat
        hide-details
        single-line
      ></v-text-field>
    </v-responsive>
    
    <v-divider></v-divider>
    
    <div class="mr-1">
      <v-btn
        to="/login"
        text="Log in"
        class="text-none mr-2"
        color="white"
        rounded="4"
        variant="outlined"
      ></v-btn>
      <!-- <v-dialog v-model="isDialogActive" max-width="900">
        <template v-slot:activator="{ props }">
          <v-btn
            v-bind="props"
            text="Log in"
            class="text-none mr-2"
            color="white"
            rounded="4"
            variant="outlined"
          ></v-btn>
        </template>

        <Login @closeDialog="isDialogActive = false" />
      </v-dialog> -->
    </div>
    
    <v-btn
      to="/registration"
      text="Registration"
      class="text-none mr-5"
      color="white"
      rounded="4"
      variant="flat"
    >
    </v-btn>
  </v-app-bar>
</template>

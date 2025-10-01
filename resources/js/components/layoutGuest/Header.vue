<script setup>
import { ref } from "vue";
import Login from "../../views/auth/loginII.vue";
import RegistrationDialog from "../../views/clientRegistration/RegistrationDialog.vue";

const isDialogActive = ref(false);
const isRegistrationDialogActive = ref(false);
</script>

<template>
  <v-app-bar style="background: linear-gradient(90deg, #003092 0%, #001a6e 100%);" class="app-bar" elevation="2">
    <v-app-bar-nav-icon 
      @click="$emit('toggle-sidebar')" 
      color="white"
      class="nav-icon"
      variant="text"
    ></v-app-bar-nav-icon>
    
    <v-spacer></v-spacer>
    
    <div class="auth-buttons">
      <!-- <v-btn
        to="/login"
        class="text-none login-btn"
        color="white"
        rounded="lg"
        variant="outlined"
        size="large"
      >
        <span class="btn-text">Log in</span>
      </v-btn> -->

       <v-dialog v-model="isDialogActive" max-width="900">
        <template v-slot:activator="{ props }">
          <v-btn
            v-bind="props"
            text="Log in"
            class="text-none login-btn"
            color="white"
            rounded="lg"
            variant="outlined"
            size="large"
          ></v-btn>
        </template>

        <Login @closeDialog="isDialogActive = false" />
      </v-dialog>

      <v-dialog v-model="isRegistrationDialogActive" max-width="900">
        <template v-slot:activator="{ props }">
          <v-btn
            v-bind="props"
            class="text-none register-btn mr-5"
            color="white"
            rounded="lg"
            variant="flat"
            size="large"
          >
            <span class="btn-text">Registration</span>
          </v-btn>
        </template>

        <RegistrationDialog @close="isRegistrationDialogActive = false" @input="isRegistrationDialogActive = false" />
      </v-dialog>

      
    </div>
  </v-app-bar>
</template>

<style scoped>
.app-bar {
  background: linear-gradient(90deg, #003092 0%, #001a6e 100%);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  padding: 0 16px;
  height: 64px !important;
}

.nav-icon {
  border-radius: 10px;
  transition: all 0.3s ease;
  margin-right: 8px;
}

.nav-icon:hover {
  background: rgba(255, 255, 255, 0.1);
  transform: scale(1.1);
}

.auth-buttons {
  display: flex;
  gap: 12px;
  align-items: center;
}

.login-btn {
  border: 1px solid rgba(255, 255, 255, 0.3);
  padding: 0 20px;
  height: 36px;
  text-transform: none;
  font-weight: 500;
  letter-spacing: 0.5px;
  transition: all 0.3s ease;
  background: rgba(255, 255, 255, 0.05);
}

.login-btn:hover {
  background: rgba(255, 255, 255, 0.1);
  border-color: rgba(255, 255, 255, 0.5);
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.register-btn {
  border: 1px solid rgba(255, 255, 255, 0.3);
  padding: 0 20px;
  height: 36px;
  text-transform: none;
  font-weight: 500;
  letter-spacing: 0.5px;
  transition: all 0.3s ease;
  background: linear-gradient(90deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.15) 100%);
}

.register-btn:hover {
  background: linear-gradient(90deg, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0.2) 100%);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.btn-text {
  font-size: 0.875rem;
  font-weight: 500;
}

/* For mobile responsiveness */
@media (max-width: 600px) {
  .auth-buttons {
    gap: 8px;
  }
  
  .login-btn,
  .register-btn {
    padding: 0 16px;
    height: 32px;
  }
  
  .btn-text {
    font-size: 0.8125rem;
  }
}


</style>
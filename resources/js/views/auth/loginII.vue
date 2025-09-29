<script setup>
import { ref, reactive } from "vue";
import useAuth from "../../composables/auth";
import ResetPassword from "../../components/users/ResetPasswordDialog.vue";
import RegistrationDialog from "../clientRegistration/RegistrationDialog.vue";

const { error, is_loading, login } = useAuth();

const form = reactive({
  username: "",
  password: ""
});

const visible = ref(false);
const resetDialog = ref(false); 
const registrationDialog = ref(false);

const handleSubmit = async () => {
  await login({ ...form });
};
</script>

<template>
  <div class="login-container">
    <div v-if="!resetDialog" class="login-card">
      <div class="avatar">
        <img src="https://rdrrmc9-alerto.com/assets/images/logo3.png" alt="Alerto Logo" />
      </div>

      <h2 class="title">Welcome Back</h2>
      <p class="subtitle">Please login to continue</p>

      <form @submit.prevent="handleSubmit" class="form">
        <div class="input-box">
          <i class="mdi mdi-email-outline input-icon"></i>
          <input
            v-model="form.username"
            type="text"
            placeholder="Email"
            required
          />
        </div>

        <div class="input-box">
          <i class="mdi mdi-lock-outline input-icon"></i>
          <input
            v-model="form.password"
            :type="visible ? 'text' : 'password'"
            placeholder="Password"
            required
          />
          <span class="toggle-password" @click="visible = !visible">
            <i :class="visible ? 'mdi mdi-eye-off' : 'mdi mdi-eye'"></i>
          </span>
        </div>

        <div class="options">
          <label><input type="checkbox" /> Remember me</label>
           <a href="javascript:void(0)" @click="resetDialog = true">Forgot Password?</a>
        </div>

        <button type="submit" class="btn" :disabled="is_loading">
          {{ is_loading ? "Logging in..." : "LOGIN" }}
        </button>
      </form>

      <div class="register-link">
        <p>Don't have an account yet? <a href="javascript:void(0)" @click="registrationDialog = true">Sign up now</a></p>
      </div>
    </div>

    <!-- Reset Password Dialog -->
    <v-dialog v-model="resetDialog" max-width="600px" persistent>
      <ResetPassword @close="resetDialog = false" />
    </v-dialog>
    <v-dialog v-model="registrationDialog" max-width="1000px" persistent>
        <RegistrationDialog @close="registrationDialog = false" />
    </v-dialog>
  </div>
</template>

<style scoped>
.login-container {
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

.login-card {
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
  margin: 15px 0;
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

.input-icon {
  position: absolute;
  top: 12px;
  left: 14px;
  font-size: 18px;
  color: #888;
}

.toggle-password {
  position: absolute;
  right: 15px;
  top: 10px;
  cursor: pointer;
  font-size: 18px;
  color: #888;
}

.options {
  display: flex;
  justify-content: space-between;
  font-size: 12px;
  margin: 10px 0 20px;
  color: #555;
}
.options a {
  text-decoration: none;
  color: #011a6e;
}

.btn {
  width: 100%;
  padding: 14px;
  background: linear-gradient(135deg, #011a6e, #2a5da8);
  color: #fff;
  border: none;
  border-radius: 30px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s ease;
}
.btn:hover {
  background: linear-gradient(135deg, #2a5da8, #5487ca);
  color: #fff;
}

.register-link {
  margin-top: 25px;
  font-size: 13px;
}
.register-link a {
  color: #011a6e;
  text-decoration: none;
  font-weight: 600;
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
</style>

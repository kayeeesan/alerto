<script setup>
import { ref, watch, toRefs } from "vue";
import useUsers from "../../composables/users.js";
import Swal from "sweetalert2";

// Props and emit
const props = defineProps({
  modelValue: Boolean,
  user: Object, // ✅ Expecting user object from parent
});
const emit = defineEmits(["update:modelValue"]);

const { user } = toRefs(props); // ✅ Easier access to user prop
const { errors, is_loading, is_success, manualResetPassword } = useUsers();

// Modal control
const show_form_modal = ref(props.modelValue);
watch(() => props.modelValue, (val) => (show_form_modal.value = val));
watch(show_form_modal, (val) => emit("update:modelValue", val));

// Password fields
const visible = ref(false);
const password = ref("");
const confirmPassword = ref("");

// Close modal
const close = () => {
  show_form_modal.value = false;
  password.value = "";
  confirmPassword.value = "";
};

// Submit handler
const ManualResetPassword = async () => {
  if (password.value !== confirmPassword.value) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "Passwords do not match",
    });
    return;
  }

  if (user.value?.id && password.value) {
      await manualResetPassword(user.value.id, {
      password: password.value,
      password_confirmation: confirmPassword.value,
    });


    if (is_success.value) {
      Swal.fire({
        icon: "success",
        title: "Password Reset",
        text: "Password reset successfully!",
      });
      close();
    }
  }
};
</script>

<template>
  <v-dialog v-model="show_form_modal" max-width="500px">
    <v-card>
      <v-card-title>
        <span class="text-h5">New Password</span>
      </v-card-title>

      <v-card-text>
        <v-container>
          <v-row class="d-flex flex-column">
            <div class="text-subtitle-1 text-medium-emphasis">New Password</div>
            <v-text-field
              v-model="password"
              :type="visible ? 'text' : 'password'"
              :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
              prepend-inner-icon="mdi-lock-outline"
              placeholder="Enter new password"
              variant="outlined"
              @click:append-inner="visible = !visible"
            />
          </v-row>

          <v-row class="d-flex flex-column">
            <div class="text-subtitle-1 text-medium-emphasis">Confirm Password</div>
            <v-text-field
              v-model="confirmPassword"
              :type="visible ? 'text' : 'password'"
              :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
              prepend-inner-icon="mdi-lock-outline"
              placeholder="Confirm new password"
              variant="outlined"
              @click:append-inner="visible = !visible"
            />
          </v-row>
        </v-container>
      </v-card-text>

      <v-card-actions class="justify-end mb-4 mr-5">
        <v-btn variant="flat" color="blue-grey-lighten-2" @click="close">Cancel</v-btn>
        <v-btn
          variant="flat"
          color="primary"
          @click="ManualResetPassword"
          :loading="is_loading"
          :disabled="!password || !confirmPassword"
        >
          Submit
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

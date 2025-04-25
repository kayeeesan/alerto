<script setup>
import { ref, watch, computed, reactive } from "vue";
import resetPassword from "../users/ManualResetPassword.vue";

const props = defineProps({
  modelValue: Boolean,
  user: Object,
  staff: {
    type: Object,
    default: null
  }
});

const initialState = {
  id: null,
  username: null,
  first_name: null,
  last_name: null,
  mobile_number: null,
  region: {},
  province: {},
  municipality: {},
  river: {}
};

const form = reactive({ ...initialState });

watch(
  () => props.staff,
  (value) => {
    if (value) {
      Object.assign(form, value);
    }
  }
);

const emit = defineEmits(["update:modelValue"]);
const dialogVisible = ref(props.modelValue);

watch(() => props.modelValue, (newVal) => {
  dialogVisible.value = newVal;
});

watch(dialogVisible, (newVal) => {
  emit("update:modelValue", newVal);
});

const close = () => {
  dialogVisible.value = false;
  emit("update:modelValue", false);
};

const show_form_modal = ref(false);
const ShowModalForm = () => {
  show_form_modal.value = true;
};
</script>

<template>
  <v-dialog v-model="dialogVisible" max-width="500">
    <v-card class="user-info-card">
      <!-- Header with gradient background -->
      <v-card-title class="card-header">
        <span class="header-title">User Information</span>
        <v-btn
          icon
          @click="close"
          class="close-btn"
          size="small"
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>

      <v-card-text class="card-content">
        <!-- User Profile Section -->
        <div class="profile-section">
          <v-avatar size="80" class="user-avatar">
            <img 
              src="../../../img/icon/user_icon.svg" 
              alt="User Avatar" 
              class="avatar-image"
            />
          </v-avatar>
          
          <div class="profile-info">
            <h3 class="user-name">{{ user.first_name }} {{ user.last_name }}</h3>
            <div class="username">@{{ user.username }}</div>
          </div>
        </div>

        <v-divider class="divider"></v-divider>

        <!-- User Details -->
        <div class="details-section">
          <!-- Full Name -->
          <div class="detail-item" v-if="user.full_name">
            <div class="detail-label">
              <v-icon size="20" class="detail-icon">mdi-account</v-icon>
              Full Name
            </div>
            <div class="detail-value">{{ user.full_name }}</div>
          </div>

          <!-- Roles -->
          <div class="detail-item">
            <div class="detail-label">
              <v-icon size="20" class="detail-icon">mdi-account-tie</v-icon>
              Roles
            </div>
            <div class="detail-value">{{ user.roles.map(role => role.name).join(', ') }}</div>
          </div>

          <!-- Mobile Number -->
          <div class="detail-item" v-if="form.mobile_number">
            <div class="detail-label">
              <v-icon size="20" class="detail-icon">mdi-phone</v-icon>
              Mobile Number
            </div>
            <div class="detail-value">{{ form.mobile_number }}</div>
          </div>

          <!-- Reset Password Button -->
          <div class="action-buttons">
            <v-btn
              variant="flat"
              color="primary"
              @click="ShowModalForm"
              class="reset-btn"
            >
              <v-icon left>mdi-lock-reset</v-icon>
              Reset Password
            </v-btn>
            <resetPassword v-model="show_form_modal" :user="user"/>
          </div>
        </div>
      </v-card-text>

      <!-- Footer Actions -->
      <v-card-actions class="card-actions">
        <!-- <v-btn
          variant="flat"
          @click="close"
          class="close-button"
          color="primary"
        >
          Close
        </v-btn> -->
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<style scoped>
.user-info-card {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.card-header {
  background: linear-gradient(135deg, #003366 0%, #0066cc 100%);
  color: white;
  padding: 16px 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-title {
  font-size: 1.25rem;
  font-weight: 600;
}

.close-btn {
  color: black;
}

.card-content {
  padding: 24px;
}

.profile-section {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 16px;
}

.user-avatar {
  border: 3px solid #e0f2fe;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.avatar-image {
  width: 100%;
  height: auto;
}

.profile-info {
  flex: 1;
}

.user-name {
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 4px;
  color: #1e293b;
}

.username {
  font-size: 0.875rem;
  color: #64748b;
}

.divider {
  margin: 16px 0;
  border-color: rgba(0, 0, 0, 0.08);
}

.details-section {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.detail-item {
  background-color: #f8fafc;
  border-radius: 8px;
  padding: 12px;
}

.detail-label {
  font-size: 0.75rem;
  color: #64748b;
  margin-bottom: 4px;
  display: flex;
  align-items: center;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 500;
}

.detail-icon {
  margin-right: 8px;
  color: #003366;
}

.detail-value {
  font-size: 0.9375rem;
  font-weight: 500;
  color: #1e293b;
  padding-left: 28px;
}

.action-buttons {
  margin-top: 16px;
}

.reset-btn {
  font-weight: 500;
  letter-spacing: 0.5px;
}

.card-actions {
  padding: 16px 24px;
  background-color: #f8fafc;
  border-top: 1px solid #e2e8f0;
  justify-content: flex-end;
}

.close-button {
  font-weight: 500;
  min-width: 100px;
}
</style>
<script setup>
import { ref, watch } from "vue";

const props = defineProps({
  modelValue: Boolean,
  user: Object,
});

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
};

const openProfile = () => {
  // Add your profile action logic here
};

const logout = () => {
  // Add your logout logic here
};
</script>

<template>
  <v-dialog v-model="dialogVisible" max-width="380">
    <v-card class="rounded-xl pa-4" style="background-color: #f5f7fa;">
      
      <!-- Profile Info -->
      <div class="d-flex align-center mb-4">
        <v-avatar size="56" class="mr-3" color="primary">
          <v-icon color="white" size="36">mdi-account</v-icon>
        </v-avatar>
        <div>
          <div class="text-subtitle-1 font-weight-medium">{{ user.full_name }}</div>
          <div class="text-caption text-medium-emphasis">{{ user.username }}</div>
        </div>
      </div>

      <v-divider></v-divider>

      <!-- Actions -->
      <div class="d-flex flex-column mt-3 gap-2">
        <v-btn
          variant="text"
          prepend-icon="mdi-account-cog"
          class="justify-start"
          @click="openProfile"
        >
          Manage Account
        </v-btn>

        <v-btn
          variant="text"
          prepend-icon="mdi-logout"
          class="justify-start"
          @click="logout"
        >
          Sign Out
        </v-btn>
      </div>

      <!-- Optional Close Button at Bottom -->
      <v-card-actions class="justify-end mt-4">
        <v-btn variant="flat" color="primary" class="text-white" @click="close">
          Close
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

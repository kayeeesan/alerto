<script setup>
import { watch, ref } from "vue";

const props = defineProps({
  modelValue: Boolean,
  user: Object,
});

const emit = defineEmits(["update:modelValue"]);

const dialogVisible = ref(props.modelValue);

// Keep local dialogVisible in sync with modelValue
watch(() => props.modelValue, (newVal) => {
  dialogVisible.value = newVal;
});

// Emit to parent when closed
watch(dialogVisible, (newVal) => {
  emit("update:modelValue", newVal);
});

const close = () => {
  dialogVisible.value = false;
};
</script>
<template>
    <v-dialog v-model="dialogVisible" max-width="500">
      <v-card>
        <v-card-title class="text-h6 font-weight-bold">User Info</v-card-title>
        <v-card-text>
          <v-list>
            <v-list-item>
              <v-list-item-title>Full Name</v-list-item-title>
              <v-list-item-subtitle>{{ user.full_name }}</v-list-item-subtitle>
            </v-list-item>
            <v-list-item>
              <v-list-item-title>Username</v-list-item-title>
              <v-list-item-subtitle>{{ user.username }}</v-list-item-subtitle>
            </v-list-item>
            <v-list-item v-if="user.status">
              <v-list-item-title>Status</v-list-item-title>
              <v-list-item-subtitle>{{ user.status }}</v-list-item-subtitle>
            </v-list-item>
            <v-list-item v-if="user.roles && user.roles.length">
              <v-list-item-title>Role</v-list-item-title>
              <v-list-item-subtitle>
                {{ user.roles.map(role => role.name).join(', ') }}
              </v-list-item-subtitle>
            </v-list-item>
          </v-list>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn text color="primary" @click="close">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </template>
  
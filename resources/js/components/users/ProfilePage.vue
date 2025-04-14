<script setup>
import { ref, watch } from "vue";
import resetPassword from "../users/ManualResetPassword.vue";

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

const show_form_modal = ref(false);

const ShowModalForm = () => {
  show_form_modal.value = true;
}
</script>

<template>
  <v-dialog v-model="dialogVisible" max-width="500">
    <v-card class="rounded-xl overflow-hidden">

      <!-- Header -->
      <v-card-title class="text-white text-h6 font-weight-bold" style="background-color: #003366">
        User Info
      </v-card-title>


      <v-card-text class="pa-6 ">
        <!-- Avatar and Name -->
        <div class="d-flex align-center">
          <v-avatar size="70" class="me-4">
            <img src="../../../img/icon/user_icon.svg" alt="User Avatar" style="width: 100%;" />
          </v-avatar>
          
          <div>
            <div class="text-subtitle-1 font-weight-bold">{{ user.full_name }}</div>
            <div class="text-body-2 text-grey-darken-1">{{ user.username }}</div>
          </div>
        </div>

        
      <v-divider></v-divider>

        <!-- Info Fields -->
        <v-row dense>
          <v-col cols="12" v-if="user.full_name">
            <div class="text-caption text-grey">Fullname</div>
            <v-sheet class="pa-2 rounded bg-grey-lighten-4">
              <v-icon size="18" class="me-2" color="grey-darken-1">mdi-account</v-icon>
              {{ user.full_name }}</v-sheet>
          </v-col>

          <v-col cols="12" v-if="user.roles?.length">
            <div class="text-caption text-grey">Role</div>
            <v-sheet class="pa-2 rounded bg-grey-lighten-4">
              <v-icon size="18" class="me-2" color="grey-darken-1">mdi-account-tie</v-icon>
              {{ user.roles.map(role => role.name).join(', ') }}
            </v-sheet>
          </v-col>

          <v-col cols="12">
            <div class="text-caption text-grey">Contact No.</div>
            <v-sheet class="pa-2 rounded bg-grey-lighten-4 d-flex align-center">
              <v-icon size="18" class="me-2" color="grey-darken-1">mdi-phone</v-icon>
              
            </v-sheet>
          </v-col>



          <v-col cols="12" >
            <div class="text-caption text-grey">LGU FB Page</div>
            <v-sheet class="pa-2 rounded bg-grey-lighten-4">
              <v-icon size="18" class="me-2" color="grey-darken-1">mdi-facebook</v-icon></v-sheet>
          </v-col>

          <v-col cols="12" >
            <div class="text-caption text-grey">Registered</div>
            <v-sheet class="pa-2 rounded bg-grey-lighten-4">
              <v-icon size="18" class="me-2" color="grey-darken-1">mdi-account-check</v-icon> </v-sheet>
          </v-col>

          <v-col cols="12" >
            <div class="text-caption text-grey">Validated</div>
            <v-sheet class="pa-2 rounded bg-grey-lighten-4" >
              <v-icon size="18" class="me-2" color="grey-darken-1">mdi-check-circle</v-icon></v-sheet>
          </v-col>

          <v-col cols="12">
            <v-btn :to="``" variant="flat" color="teal-lighten-1" class="mt-2" @click="ShowModalForm">
              Reset Password
            </v-btn>
            <resetPassword v-model="show_form_modal" :user="user"/>

          </v-col>

        </v-row>
      </v-card-text>

      
      <v-divider></v-divider>

      <!-- Actions -->
      <v-card-actions class="justify-end px-4 pb-4">
        <v-btn color="primary" variant="flat" @click="close" class="text-white">Close</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

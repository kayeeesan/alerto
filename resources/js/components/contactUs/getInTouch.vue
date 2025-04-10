<script setup>
import { ref, reactive, watch, onMounted } from "vue";
import useContactMessages from "../../composables/contactMessage";

const { errors, is_loading, is_success, storeContactMessage} = useContactMessages();

const emit = defineEmits(["input", "reloadContactMessages"]);
const props = defineProps({
    contact_message: Object
});

const initialState = {
  id: null,
  email: "",
  name: "",
  contact_number: "",
  message: ""
};

const form = reactive({ ...initialState });

watch(
  () => props.contact_message,
  (value) => {
    if (value) {
      Object.assign(form, { ...value});
    }
  },
  { deep: true}
);

const resetForm = () => {
  Object.assign(form, { ...initialState});
};

const save = async () => {
  await storeContactMessage({ ...form });

  if (is_success.value) {
    emit("reloadContactMessages");
    emit("input", false);
    resetForm();
  }
};

</script>
<template>
    <v-col cols="12" class="p-10 ml-5">
      <v-sheet
      class="pa-8 rounded-lg shadow-lg elevation-3"
      style="position: relative; background: #F8FAF0; border: 1px solid #E0E0E0;"
    >
        <!-- Top Bar -->
        <span
        style="background: var(--primary-color); position: absolute; left: 0; right: 0; top: 0; border-top-left-radius: 11px; border-top-right-radius: 11px; height: 8px;"
      ></span>
       
        
        <v-row class="mt-1 ml-5">
          <v-col cols="12" sm="6" >
            <v-row class="d-flex flex-column text-center mb-2">
                <v-img
                    style="height: 120px;"
                    src="https://rdrrmc9-alerto.com/assets/images/logo3.png"
                ></v-img>
                <p class="text-lg font-bold text-gray-800" style="margin-bottom: 0 !important;">ALeRTO</p>
                <p class="text-sm text-gray-600" style="margin-bottom: 0 !important;">Ateneo Center for Environment and Sustainability</p>
                <p class="text-s text-gray-600">Ateneo de Zamboanga University</p>
        </v-row>
        <v-divider :thickness="2" class="border-opacity-75 mt-1 mb-4"></v-divider>
            <v-row class="my-4">
              <v-col cols="12" class="d-flex align-center">
                <v-icon class="mr-4" style="font-size: 28px; color: #3b5998;">mdi-facebook</v-icon>
                <span class="text-lg font-bold">facebook.com/alerto</span>
              </v-col>
              <v-col cols="12" class="d-flex align-center ">
                <v-icon class="mr-4" style="font-size: 28px; color: #d32f2f;">mdi-email</v-icon>
                <span class="text-lg font-bold">alerto@ateneo.edu</span>
              </v-col>
              <v-col cols="12" class="d-flex align-center ">
                <v-icon class="mr-4" style="font-size: 28px; color: #388e3c;">mdi-phone</v-icon>
                <span class="text-lg font-bold">+63 912 345 6789</span>
              </v-col>
              <v-col cols="12" class="d-flex align-center ">
                <v-icon class="mr-4" style="font-size: 28px; color: #1976d2;">mdi-map-marker</v-icon>
                <span class="text-lg font-bold">Ateneo de Zamboanga University, Zamboanga City</span>
              </v-col>
            </v-row>

          </v-col>

          <v-col class="shadow-md bg-gray-100 border border-gray-300 rounded-lg" style="background: lightgrey; margin: 15px; margin-top: 1px; ">
            <v-row>
                <v-col cols="6">
                  <v-text-field
                    v-model="form.email"
                    hint="Email"
                    label="Email"
                    variant="solo"
                    :error="!!errors.email"
                    :error-messages="errors.email"
                  ></v-text-field>

                </v-col>
                <v-col cols="6">
                    <v-text-field
                    v-model="form.name"
                    label="Name"
                    variant="solo"
                    :error="!!errors.name"
                    :error-messages="errors.name"
                    ></v-text-field>
                </v-col>
            </v-row>
            <v-row>
                    <v-col cols="12">
                    <v-text-field
                    v-model="form.contact_number"
                    label="Phone No."
                    variant="solo"
                    :error="!!errors.contact_number"
                    :error-messages="errors.contact_number"
                    ></v-text-field>
                </v-col>
            </v-row>
            
            <v-row>
                <v-col>
                    <v-textarea
                    v-model="form.message"
                    label="Message"
                    maxlength="120"
                    counter
                    single-line
                    variant="solo"
                    :error="!!errors.message"
                    :error-messages="errors.message"
                    ></v-textarea>
                </v-col>
            </v-row>

            <v-row class="d-flex justify-end pb-6 pr-4">
                <!-- <v-btn
                    color="blue-darken-4"
                    text="SUBMIT"
                    variant="flat"
                    class="text-white"
                    append-icon="mdi-send-outline"
                    @click="save"
                    ></v-btn> -->
                    <v-btn
                    color="blue-darken-4"
                    text="SUBMIT"
                    variant="flat"
                    class="text-white"
                    append-icon="mdi-send-outline"
                    @click="save"
                    :loading="is_loading"
                    :disabled="is_loading"
                  ></v-btn>

            </v-row>

          </v-col>
        </v-row>
      </v-sheet>
    </v-col>
  </template>
  
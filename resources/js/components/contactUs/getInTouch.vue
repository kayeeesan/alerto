<script setup>
import { ref, reactive, watch, onMounted } from "vue";
import useContactMessages from "../../composables/contactMessage";

import alertoLogo from "../../../img/logo/alerto-logo.png";

const { errors, is_loading, is_success, storeContactMessage } = useContactMessages();

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
      Object.assign(form, { ...value });
    }
  },
  { deep: true }
);

const resetForm = () => {
  Object.assign(form, { ...initialState });
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
  <v-col class="threshold-container ml-5">
    <v-sheet class="threshold-sheet" rounded="lg">
      <div class="header-container bg-blue-grey-lighten-5">
        <div class="alert-indicator"></div>
        <h1 class="section-title">CONTACT US</h1>
      </div>
      
      <v-divider class="divider"></v-divider>
      
      <div class="contact-content">
        <v-row>
          <!-- Left Info Column -->
          <v-col cols="12" md="6" class="info-column">
            <div class="logo-section">
              <v-img
                :src="alertoLogo"
                class="logo-img"
                max-width="180px"
                contain
              ></v-img>
              <h2 class="logo-title">ALeRTO</h2>
              <p class="logo-subtitle">Ateneo Center for Environment and Sustainability</p>
              <p class="logo-institution">Ateneo de Zamboanga University</p>
            </div>

            <v-divider class="content-divider"></v-divider>

            <div class="contact-details">
              <div class="contact-item">
                <v-icon color="#3b5998" class="mr-2">mdi-facebook</v-icon>
                <span>facebook.com/alerto</span>
              </div>
              <div class="contact-item">
                <v-icon color="#d32f2f" class="mr-2">mdi-email</v-icon>
                <span>alerto@ateneo.edu</span>
              </div>
              <div class="contact-item">
                <v-icon color="#388e3c" class="mr-2">mdi-phone</v-icon>
                <span>+63 912 345 6789</span>
              </div>
              <div class="contact-item">
                <v-icon color="#1976d2" class="mr-2">mdi-map-marker</v-icon>
                <span>Ateneo de Zamboanga University, Zamboanga City</span>
              </div>
            </div>
          </v-col>

          <!-- Right Form Column -->
          <v-col cols="12" md="6" class="form-column">
            <v-card class="form-card bg-blue-grey-lighten-5">
              <v-row>
                <v-col cols="12" sm="6" class="mb-4">
                  <v-text-field
                    v-model="form.email"
                    label="Email"
                    variant="outlined"
                    :error="!!errors.email"
                    :error-messages="errors.email"
                    density="comfortable"
                    class="bg-white"
                    style="height: 48px; border-radius: 11px;"
                  />
                </v-col>
                <v-col cols="12" sm="6">
                  <v-text-field
                    v-model="form.name"
                    label="Name"
                    variant="outlined"
                    :error="!!errors.name"
                    :error-messages="errors.name"
                    density="comfortable"
                    class="bg-white"
                    style="height: 48px; border-radius: 11px;"
                  />
                </v-col>
              </v-row>
              <v-row>
                <v-col>
                  <div class="mb-4">
                      <v-text-field
                      v-model="form.contact_number"
                      label="Phone Number"
                      variant="outlined"
                      :error="!!errors.contact_number"
                      :error-messages="errors.contact_number"
                      density="comfortable"
                      class="bg-white"
                      style="height: 48px; border-radius: 11px;"
                    />
                  </div>
                </v-col>
              </v-row>
              
            <v-row>
              <v-col>
                  <div>
                    <v-textarea
                    v-model="form.message"
                    label="Message"
                    variant="outlined"
                    counter
                    maxlength="120"
                    :error="!!errors.message"
                    :error-messages="errors.message"
                    density="comfortable"
                    rows="3"
                    class="bg-white"
                    style="height: 95px; border-radius: 11px;"
                  />
                </div>
              </v-col>
            </v-row>
              

              <div class="form-actions">
                <v-btn
                  color="primary"
                  variant="flat"
                  size="large"
                  append-icon="mdi-send-outline"
                  @click="save"
                  :loading="is_loading"
                  :disabled="is_loading"
                >
                  SUBMIT
                </v-btn>
              </div>
            </v-card>
          </v-col>
        </v-row>
      </div>
    </v-sheet>
  </v-col>
</template>

<style scoped>
.threshold-container {
  padding: 0 !important;
}

.threshold-sheet {
  padding: 0;
  border: 1px solid #E0E0E0;
  background: #FFFFFF;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
}

.header-container {
  padding: 16px 24px 8px;
  position: relative;
  /* background: #F5F5F5; */
  border-bottom: 1px solid #E0E0E0;
}

.alert-indicator {
  position: absolute;
  left: 0;
  top: 0;
  height: 100%;
  width: 4px;
  background: var(--primary-color);
}

.section-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 4px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.divider {
  margin: 0;
  border-color: rgba(0, 0, 0, 0.1) !important;
}

.contact-content {
  padding: 24px;
}

.info-column {
  padding: 16px;
  display: flex;
  flex-direction: column;
}

.logo-section {
  text-align: center;
  margin-bottom: 24px;
}

.logo-img {
  margin: 0 auto 16px;
}

.logo-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 4px;
}

.logo-subtitle {
  font-size: 1rem;
  color: #666;
  margin-bottom: 4px;
}

.logo-institution {
  font-size: 0.875rem;
  color: #888;
}

.content-divider {
  margin: 16px 0;
  border-color: rgba(0, 0, 0, 0.1) !important;
}

.contact-details {
  margin-top: 16px;
}

.contact-item {
  display: flex;
  align-items: center;
  margin: 12px 0;
  font-size: 0.9375rem;
  color: #555;
}

.form-column {
  padding: 16px;
}

.form-card {
  padding: 24px;
  border: 1px solid #E0E0E0;
  box-shadow: none;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 16px;
}

@media (max-width: 959px) {
  .info-column {
    order: 2;
    margin-top: 24px;
  }
  
  .form-column {
    order: 1;
  }
}
</style>
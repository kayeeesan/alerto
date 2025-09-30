<script setup>
import { ref, reactive, watch, onMounted, computed } from "vue";
import { useRouter } from 'vue-router';
import useStaffs from "../../composables/staff";
import useRegions from "../../composables/region";
import useProvinces from "../../composables/province";
import useMunicipalities from "../../composables/municipality";
import useRivers from "../../composables/river";
import useRoles from "../../composables/roles";
import { get } from "@vueuse/core";

const props = defineProps({
    staff: Object
});

const emit = defineEmits(["reloadStaffs", "input", "close"]);
const router = useRouter();

const { errors, is_loading, is_success, storeWalkinStaff, updateStaff } = useStaffs();
const { roles, getRoles } = useRoles();
const { regions, getMultiselectRegions } = useRegions();
const { provinces, getMultiselectProvinces } = useProvinces();
const { municipalities, getMultiselectMunicipalities } = useMunicipalities();
const { rivers, getRivers } = useRivers();

const initialState = {
    id: null,
    username: "",
    first_name: "",
    last_name: "",
    mobile_number: "",
    role: null,
    region: null,
    province: null,
    municipality: null,
    river: null,
    fb_lgu: null,
    password: "",
    password_confirmation: ""
};
const form = reactive({ ...initialState });

watch(
    () => props.staff,
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
    form.role = roles.value.find(role => role.slug === 'project-staff');
    
    if (props.staff?.id) {
        await updateStaff({ ...form });
    } else {
        await storeWalkinStaff({ ...form });
    }

    if (is_success.value) {
        emit("reloadStaffs");
        emit("input", false);
        emit("close");
        resetForm();
        router.push('/');
    }
};

watch(
    () => form.region,
    () => {
        form.province = null; 
    }
);
watch(
    () => form.province,
    () => {
        form.municipality = null;
    }
);
watch(
    () => form.municipality,
    () => {
        form.river = null;
    }
)

onMounted(() => {
    getRoles();
    getMultiselectProvinces();
    getMultiselectMunicipalities();
    getRivers();
    getMultiselectRegions();
});

const filteredProvinces = computed(() => {
    if (!form.region || !form.region.id) return [];
    return provinces.value.filter(p => p.region.id === form.region.id);
});

const filteredMunicipalities = computed(() => {
    if (!form.province || !form.province.id) return [];
    return municipalities.value.filter(m => m.province.id === form.province.id);
});

const filteredRivers = computed(() => {
    if (!form.municipality || !form.municipality.id) return [];
    return rivers.value.filter(r => r.municipality.id === form.municipality.id);
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);
const close = () => {
    emit("input", false);
    emit("close");
};
</script>

<template>
  <div class="registration-container">
    <div class="registration-card hidden-scroll bg-blue-grey-lighten-5">
      <div class="avatar">
        <img src="https://rdrrmc9-alerto.com/assets/images/logo3.png" alt="Alerto Logo" />
      </div>
      <v-card-title class="text-h5 text-md-h5 text-lg-h5 font-weight-bold text-primary">
            Member<span class="d-sm-none"><br></span> Registration
        </v-card-title>
        <v-card-subtitle class="text-caption">Please fill out all <span class="d-sm-none"><br></span> required fields</v-card-subtitle>
      <v-card-text>
        <v-form>
           <v-card variant="outlined" class="mb-6 pa-4 bg-white" color="blue-darken-4">
                <v-card-title class="text-subtitle-1 font-weight-bold">Personal Information</v-card-title>
                    <v-row>
                        <v-col cols="12" sm="6">
                            <div class="d-flex">
                                <v-icon class="mt-3 mr-3" style="color: #6E92C1;">mdi-account</v-icon>
                                    <v-text-field 
                                                v-model="form.first_name"
                                                label="First Name*" 
                                                variant="outlined"
                                                density="comfortable"
                                                :error-messages="errors.first_name || []"
                                                :rules="[
                                                    (v) => !!v || 'This field is required',
                                                    (v) => /^[a-zA-Z\s]+$/.test(v) || 'Only letters are allowed',
                                                ]"
                                                bg-color="white"
                                                class="dark-input"
                                                ></v-text-field>
                            </div>
                                            
                        </v-col>
                        <v-col cols="12" sm="6">
                            <div class="d-flex">
                                <v-icon class="mt-3 mr-3" style="color: #6E92C1;"> mdi-account</v-icon>
                                            <v-text-field 
                                            v-model="form.last_name" 
                                            label="Last Name*" 
                                            variant="outlined"
                                            density="comfortable"
                                            :error-messages="errors.last_name || []"
                                            bg-color="white"
                                            class="dark-input"
                                ></v-text-field>
                            </div>
                                            
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col cols="12" sm="6">
                            <div class="d-flex">
                                    <v-icon class="mt-3 mr-3" style="color: #6E92C1;">mdi-email</v-icon>
                                     <v-text-field
                                                v-model="form.username"
                                                :rules="[
                                                    (v) => !!v || 'This field is required',
                                                    (v) => /.+@.+\..+/.test(v) || 'Invalid email address',
                                                ]"
                                                label="Email*"
                                                variant="outlined"
                                                density="comfortable"
                                                :error-messages="errors.username || []"
                                                bg-color="white"
                                                class="dark-input"
                                            ></v-text-field>
                            </div>
                                           
                        </v-col>
                        <v-col cols="12" sm="6">
                            <div class="d-flex">
                               <v-icon class="mt-3 mr-3" style="color: #6E92C1;">mdi-phone</v-icon>
                                    <v-text-field 
                                                v-model="form.mobile_number" 
                                                label="Mobile Number*" 
                                                variant="outlined"
                                                density="comfortable"
                                                :error-messages="errors.mobile_number || []"
                                                :rules="[
                                                    (v) => !!v || 'This field is required',
                                                    (v) => /^09\d{9}$/.test(v) || 'Invalid phone number format',
                                                ]"
                                                bg-color="white"
                                                class="dark-input"
                                    ></v-text-field>
                            </div>
                                            
                        </v-col>
                    </v-row>
            </v-card>

            <v-card variant="outlined" class="mb-6 pa-4 bg-white" color="blue-darken-4">
                <v-card-title class="text-subtitle-1 font-weight-bold">Location Information</v-card-title>
                    <v-row>
                        <v-col>
                            <v-input prepend-icon="mdi-map" v-model="form.region">
                                        <vue-multiselect 
                                        v-model="form.region" 
                                        :options="regions" 
                                        placeholder="Select Region"
                                        label="name" 
                                        track-by="name"/>
                                    </v-input>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col>
                                <v-input prepend-icon="mdi-map" v-model="form.province">
                                            <vue-multiselect
                                            v-model="form.province"
                                            :options="filteredProvinces"
                                            :disabled="!form.region"
                                            placeholder="Select Province"
                                            label="name"
                                            track-by="name"/>      
                                        </v-input>
                                    </v-col>
                    </v-row>
                    <v-row>
                        <v-col>
                            <v-input prepend-icon="mdi-city" v-model="form.municipality" >
                                    <vue-multiselect 
                                            v-model="form.municipality" 
                                            :options="filteredMunicipalities"
                                            :disabled="!form.province"
                                            placeholder="Select Municipality" 
                                            label="name" 
                                            track-by="name"/>
                                        </v-input>

                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col>
                            <v-input  prepend-icon="mdi-waves" v-model="form.river">
                                            <vue-multiselect 
                                            v-model="form.river" 
                                            :options="filteredRivers" 
                                            :disabled="!form.municipality"
                                            placeholder="Select River" 
                                            label="name"
                                            track-by="name"/>
                            </v-input>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12">
                                <v-text-field 
                                        v-model="form.fb_lgu" 
                                        prepend-icon="mdi-account"
                                        label="Facebook LGU*" 
                                        variant="outlined"
                                        :error-messages="errors.fb_lgu || []">
                                        </v-text-field>
                        </v-col>
                    </v-row>
            </v-card>

            <v-card variant="outlined" class="mb-6 pa-4 bg-white" color="blue-darken-4">
                    <v-card-title class="text-subtitle-1 font-weight-bold">Set Password</v-card-title>
                        <v-row>
                            <v-col cols="12" sm="6">
                                    <div class="d-flex">
                                        <v-icon class="mt-3 mr-3" style="color: #6E92C1;">mdi-lock</v-icon>
                                        <v-text-field
                                                v-model="form.password"
                                                label="Password*"
                                                variant="outlined"
                                                density="comfortable"
                                                :type="showPassword ? 'text' : 'password'"  
                                                :error-messages="errors.password || []"
                                                :rules="[
                                                    (v) => !!v || 'This field is required',
                                                    (v) => v.length >= 6 || 'Must be at least 6 characters'
                                                ]"
                                                bg-color="white"
                                                class="dark-input"
                                            >
                                     <template v-slot:append-inner>
                                                <v-icon
                                                    :icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                                                    @click="showPassword = !showPassword"
                                                    style="cursor: pointer;"
                                                ></v-icon>
                                    </template>
                                </v-text-field>
                             </div>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <div class="d-flex">
                                <v-icon class="mt-3 mr-3" style="color: #6E92C1;">mdi-lock</v-icon>
                                <v-text-field
                                                v-model="form.password_confirmation"
                                                label="Confirm Password*"
                                                variant="outlined"
                                                density="comfortable"
                                                :type="showConfirmPassword ? 'text' : 'password'" 
                                                :error-messages="errors.password_confirmation || []"
                                                :rules="[
                                                    (v) => !!v || 'This field is required',
                                                    (v) => v === form.password || 'Passwords do not match'
                                                ]"
                                                bg-color="white"
                                                class="dark-input"
                                            >
                                    <template v-slot:append-inner>
                                        <v-icon
                                                    :icon="showConfirmPassword ? 'mdi-eye-off' : 'mdi-eye'"
                                                    @click="showConfirmPassword = !showConfirmPassword"
                                                    style="cursor: pointer;"
                                                ></v-icon>
                                    </template>
                            </v-text-field>
                             </div>
                        </v-col>
                    </v-row>

            </v-card>

        </v-form>
      </v-card-text>

      <v-card-actions class="button-group">
        <button type="button" class="btn btn-secondary" @click="close">
            <i class="mdi mdi-close"></i> Close
        </button>
        <button type="button" class="btn btn-warning" @click="resetForm">
            <i class="mdi mdi-refresh"></i> Reset
        </button>
        <button type="button" class="btn btn-primary" :disabled="is_loading" @click="save">
            <span v-if="is_loading">
            <i class="mdi mdi-loading mdi-spin"></i> Saving...
            </span>
            <span v-else>
            <i class="mdi mdi-content-save"></i> Save
            </span>
        </button>
    </v-card-actions>

    </div>

  </div>
</template>

<style scoped>
.registration-container {
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

.registration-card {
  width: 800px;
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
  aspect-ratio: 1 / 1; /* ensure perfect square so 50% radius is a true circle */
  margin: -90px auto 20px;
  border-radius: 50%;
  background: #fff;
  box-shadow: 0 4px 15px rgba(0,0,0,0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  flex: 0 0 auto; /* prevent flexbox from stretching */
  align-self: center; /* guard against cross-axis stretching */
}
.avatar img {
  width: 100%;
  height: 100%;
  object-fit: contain; /* preserve aspect ratio without cropping */
  object-position: center;
  display: block; /* remove inline-gap artifacts */
}

/* Make only the card content scroll while keeping the logo/title steady */
.registration-card {
  display: flex;
  flex-direction: column;
  max-height: 90vh; /* constrain dialog height to viewport */
  overflow: visible; /* allow avatar to overflow and remain visible */
}

.registration-card .v-card-text {
  flex: 1 1 auto; /* take remaining space between header and footer */
  overflow-y: auto; /* enable vertical scroll for the cards area */
  min-height: 0;   /* important for flex children to allow shrinking */
  padding-right: 6px; /* space for scrollbar */
  scrollbar-width: thin; /* Firefox */
  scrollbar-color: rgba(0, 0, 0, 0.25) transparent; /* Firefox */
}

/* Optional: nicer scrollbar for WebKit browsers */
.registration-card .v-card-text::-webkit-scrollbar {
  width: 8px;
}
.registration-card .v-card-text::-webkit-scrollbar-thumb {
  background-color: rgba(0, 0, 0, 0.25);
  border-radius: 8px;
}

.button-group {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  margin-top: 20px;
}

.btn {
  flex: 1;
  max-width: 140px;
  padding: 12px;
  border: none;
  border-radius: 30px;
  font-size: 14px;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  color: #fff; 
  border: none; 
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Warning (Reset) */
.btn-warning {
  background: linear-gradient(135deg, #ff9800, #ffb74d);
  color: #fff; /* always white */
}
.btn-warning:hover:not(:disabled) {
  background: linear-gradient(135deg, #ffb74d, #ffc947);
  color: #0c0c0c; /* keep white text */
}

/* Secondary (Close) */
.btn-secondary {
  background: linear-gradient(135deg, #e57373, #ef9a9a);
  color: #fff; /* always white */
}
.btn-secondary:hover:not(:disabled) {
  background: linear-gradient(135deg, #ef9a9a, #ffcdd2);
  color: #0c0c0c; /* keep white text */
}

/* Primary (Save) */
.btn-primary {
  background: linear-gradient(135deg, #011a6e, #2a5da8);
  color: #fff; /* always white */
}
.btn-primary:hover:not(:disabled) {
  background: linear-gradient(135deg, #2a5da8, #5487ca);
  color: #0c0c0c; /* keep white text */
}

</style>

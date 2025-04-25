<script setup>
import { ref, reactive, watch, onMounted, computed } from "vue";
import { useRouter } from 'vue-router';
import useStaffs from "../../composables/staff";
import useRegions from "../../composables/region";
import useProvinces from "../../composables/province";
import useMunicipalities from "../../composables/municipality";
import useRivers from "../../composables/river";
import useRoles from "../../composables/roles";

const props = defineProps({
    staff: Object
});

const emit = defineEmits(["reloadStaffs", "input"]);
const router = useRouter();

const { errors, is_loading, is_success, storeWalkinStaff, updateStaff } = useStaffs();
const { roles, getRoles } = useRoles();
const { regions, getRegions } = useRegions();
const { provinces, getProvinces } = useProvinces();
const { municipalities, getMunicipalities } = useMunicipalities();
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
    fb_lgu: null
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
    getRegions();
    getProvinces();
    getMunicipalities();
    getRivers();
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
</script>

<template>
    <v-app>
        <v-layout class="bg-indigo-darken-1" style="position: relative; overflow: hidden;">
            <!-- Background Elements -->
            <div class="bg-circle" style="
                position: absolute;
                top: -100px;
                right: -100px;
                width: 400px;
                height: 400px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.1);
            "></div>
            <div class="bg-circle" style="
                position: absolute;
                bottom: -150px;
                left: -150px;
                width: 500px;
                height: 500px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.08);
            "></div>
            
            <v-container fluid class="fill-height d-flex align-center justify-center">
                <v-card class="pa-8" elevation="8" width="850px" rounded="lg" style="position: relative; z-index: 1;">
                    <!-- Header Section -->
                    <div class="text-center mb-6">
                        <v-img
                            class="mx-auto mb-4"
                            max-width="100"
                            src="https://rdrrmc9-alerto.com/assets/images/logo3.png"
                        ></v-img>
                        <v-card-title class="text-h4 font-weight-bold text-primary">Member Registration</v-card-title>
                        <v-card-subtitle class="text-caption">Please fill out all required fields</v-card-subtitle>
                    </div>

                    <v-card-text>
                        <v-form>
                            <!-- Personal Information Section -->
                            <v-card variant="outlined" class="mb-6 pa-4" color="blue-darken-4">
                                <v-card-title class="text-subtitle-1 font-weight-bold">Personal Information</v-card-title>
                                <v-row>
                                    <v-col cols="12" sm="6">
                                        <v-text-field 
                                        v-model="form.first_name"
                                        prepend-inner-icon="mdi-account-outline"
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
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-text-field 
                                            v-model="form.last_name" 
                                            prepend-inner-icon="mdi-account-outline"
                                            label="Last Name*" 
                                            variant="outlined"
                                            density="comfortable"
                                            :error-messages="errors.last_name || []"
                                            bg-color="white"
                                            class="dark-input"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>

                                <v-row>
                                    <v-col cols="12" sm="6">
                                        <v-text-field
                                            v-model="form.username"
                                            prepend-inner-icon="mdi-email-outline"
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
                                    </v-col>
                                    <v-col cols="12" sm="6">
                                        <v-text-field 
                                            v-model="form.mobile_number" 
                                            prepend-inner-icon="mdi-phone-outline"
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
                                    </v-col>
                                </v-row>
                            </v-card>

                            <!-- Location Information Section -->
                            <v-card variant="outlined" class="mb-6 pa-4" color="blue-darken-4">
                                <v-card-title class="text-subtitle-1 font-weight-bold">Location Information</v-card-title>
                                <v-row>
                                    <v-col>
                                        <v-select
                                            v-model="form.region"
                                            :items="regions"
                                            item-title="name"
                                            item-value="id"
                                            prepend-inner-icon="mdi-map-outline"
                                            label="Region*"
                                            variant="outlined"
                                            density="comfortable"
                                            clearable
                                            bg-color="white"
                                            class="dark-input"
                                        ></v-select>
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col>
                                        <v-select
                                            v-model="form.province"
                                            :items="filteredProvinces"
                                            item-title="name"
                                            item-value="id"
                                            prepend-inner-icon="mdi-map-marker-outline"
                                            label="Province*"
                                            variant="outlined"
                                            density="comfortable"
                                            :disabled="!form.region"
                                            clearable
                                            bg-color="white"
                                            class="dark-input"
                                        ></v-select>
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col>
                                        <v-select
                                            v-model="form.municipality"
                                            :items="filteredMunicipalities"
                                            item-title="name"
                                            item-value="id"
                                            prepend-inner-icon="mdi-city"
                                            label="Municipality*"
                                            variant="outlined"
                                            density="comfortable"
                                            :disabled="!form.province"
                                            clearable
                                            bg-color="white"
                                            class="dark-input"
                                        ></v-select>
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col>
                                        <v-select
                                            v-model="form.river"
                                            :items="filteredRivers"
                                            item-title="name"
                                            item-value="id"
                                            prepend-inner-icon="mdi-waves"
                                            label="River*"
                                            variant="outlined"
                                            density="comfortable"
                                            :disabled="!form.municipality"
                                            clearable
                                            bg-color="white"
                                            class="dark-input"
                                        ></v-select>
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field 
                                            v-model="form.fb_lgu" 
                                            prepend-inner-icon="mdi-facebook"
                                            label="Facebook LGU*" 
                                            variant="outlined"
                                            density="comfortable"
                                            :error-messages="errors.fb_lgu || []"
                                            bg-color="white"
                                            class="dark-input"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                            </v-card>
                        </v-form>
                    </v-card-text>

                    <!-- Action Buttons -->
                    <v-card-actions class="justify-center pt-4">
                        <v-btn 
                            color="red-darken-2" 
                            variant="flat" 
                            :to="'/'"
                            size="large"
                            class="mr-4"
                            height="48"
                            min-width="120"
                        >
                            <v-icon start class="mr-1">mdi-close</v-icon>
                            <span>Cancel</span>
                        </v-btn>
                        
                        <v-btn 
                            color="orange-darken-2" 
                            variant="flat" 
                            @click="resetForm"
                            size="large"
                            class="mr-4"
                            height="48"
                            min-width="120"
                        >
                            <v-icon start class="mr-1">mdi-refresh</v-icon>
                            <span>Reset</span>
                        </v-btn>
                        
                        <v-btn 
                            color="blue-darken-4" 
                            variant="flat" 
                            :loading="is_loading" 
                            @click="save"
                            size="large"
                            height="48"
                            min-width="120"
                        >
                            <v-icon start class="mr-1">mdi-content-save</v-icon>
                            <span>Submit</span>
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-container>
        </v-layout>
    </v-app>
</template>

<style scoped>
.bg-circle {
    animation: float 15s infinite ease-in-out;
    filter: blur(1px);
}

.bg-circle:nth-child(1) {
    animation-delay: 0s;
}
.bg-circle:nth-child(2) {
    animation-delay: 3s;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }
    50% {
        transform: translateY(-20px) rotate(5deg);
    }
}

.v-card {
    backdrop-filter: blur(5px);
    background-color: rgba(255, 255, 255, 0.9);
}

.v-card-title {
    color: #242faa;
}
</style>
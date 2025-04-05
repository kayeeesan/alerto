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
    river: null
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
    if (props.staff?.id) {
        await updateStaff({ ...form });
    } else {
        await storeWalkinStaff({ ...form });
    }

    if (is_success.value) {
        emit("reloadStaffs");
        emit("input", false);
        resetForm();
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
    return rivers.value.filter(r => r.municipality.id === form.municipality.id );
 });

</script>

<template>
    <v-app>
        <v-layout class="bg-indigo-darken-1">
            <v-container fluid class="fill-height d-flex align-center justify-center">
                <v-card class="pa-8" elevation="8" width="600px" rounded="lg">
                    <v-card-title class="text-h5 text-center font-weight-bold"> Member Registration </v-card-title>
                    <v-card-text>
                        <v-form>
                            <!-- Name Fields -->
                            <v-row>
                                <v-col>
                                    <v-text-field v-model="form.first_name" label="First Name*" variant="outlined"
                                        :error-messages="errors.first_name || []">
                                    </v-text-field>
                                </v-col>
                                <v-col>
                                    <v-text-field v-model="form.last_name" label="Last Name*" variant="outlined"
                                        :error-messages="errors.last_name || []">
                                    </v-text-field>
                                </v-col>
                            </v-row>

                            <!-- Username & Mobile -->
                            <v-row>
                                <v-col>
                                    <v-text-field v-model="form.username" label="Username*" variant="outlined"
                                        :error-messages="errors.username || []">
                                    </v-text-field>
                                </v-col>
                                <v-col>
                                    <v-text-field v-model="form.mobile_number" label="Mobile Number*" variant="outlined"
                                        :error-messages="errors.mobile_number || []">
                                    </v-text-field>
                                </v-col>
                            </v-row>

                            <!-- Role -->
                            <v-row>
                                <v-col>
                                    <vue-multiselect v-model="form.role" :options="roles" placeholder="Select Role" label="name"
                                        track-by="name">
                                    </vue-multiselect>
                                </v-col>
                            </v-row>

                            <!-- Location Fields -->
                            <v-row>
                                <v-col>
                                    <vue-multiselect v-model="form.region" :options="regions" placeholder="Select Region"
                                        label="name" track-by="name">
                                    </vue-multiselect>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col>
                                    <vue-multiselect
                                        v-model="form.province"
                                        :options="filteredProvinces"
                                        :disabled="!form.region"
                                        placeholder="Select Province"
                                        label="name"
                                        track-by="name">
                                        </vue-multiselect>

                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col>
                                    <vue-multiselect 
                                    v-model="form.municipality" 
                                    :options="filteredMunicipalities"
                                    :disabled="!form.province"
                                    placeholder="Select Municipality" 
                                    label="name" 
                                    track-by="name">
                                    </vue-multiselect>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col>
                                    <vue-multiselect 
                                    v-model="form.river" 
                                    :options="filteredRivers" 
                                    :disabled="!form.municipality"
                                    placeholder="Select River" 
                                    label="name"
                                    track-by="name">
                                    </vue-multiselect>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>

                    <v-card-actions class="justify-end">
                        <v-btn 
                            color="red-darken-2" 
                            variant="flat" 
                            :to="'/'" 
                            class="mr-2">
                            <v-icon left>mdi-close</v-icon> Cancel
                        </v-btn>
                        <v-btn 
                            color="orange-darken-2" 
                            variant="flat" 
                            @click="resetForm" 
                            class="mr-2">
                            <v-icon left>mdi-refresh</v-icon> Reset
                        </v-btn>
                        <v-btn 
                            color="blue-darken-4" 
                            variant="flat" 
                            :loading="is_loading" 
                            @click="save">
                            <v-icon left>mdi-content-save</v-icon> Save
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-container>
        </v-layout>
    </v-app>
   
</template>

<script setup>
import { ref, reactive, watch, onMounted, computed } from "vue";
import useStaffs from "../../../composables/staff";
import useRegions from "../../../composables/region";
import useProvinces from "../../../composables/province";
import useMunicipalities from "../../../composables/municipality";
import useRivers from "../../../composables/river";
import useRoles from "../../../composables/roles";

const { errors, is_loading, is_success, storeStaff, updateStaff } = useStaffs();
const { roles, getRoles } = useRoles();
const { regions, getRegions } = useRegions();
const { provinces, getProvinces } = useProvinces();
const { municipalities, getMunicipalities } = useMunicipalities();
const { rivers, getRivers } = useRivers();

const emit = defineEmits(["input", "reloadStaffs"]);
const props = defineProps({
    staff: {
        type: Object,
        default: null
    },
    action_type: {
        type: String,
        default: null,
    },
    value: {
        type: Boolean,
        default: false,
    }
});

const initialState = {
    id: null,
    username: null,
    first_name: null,
    last_name: null,
    middle_name: null,
    mobile_number: null,
    role: {},
    region: {},
    province: {},
    municipality: {},
    river: {},
    fb_lgu: null
}
const form = reactive({ ...initialState });

watch(
    () => props.staff,
    (value)  => {
        form.id = value.id;
            form.username = value.username;
            form.first_name = value.first_name;
            form.last_name = value.last_name;
            form.middle_name = value.middle_name;
            form.mobile_number = value.mobile_number;
            form.role = value.role;
            form.region = value.region;
            form.province = value.province;
            form.municipality = value.municipality;
            form.river = value.river;
            form.fb_lgu = value.fb_lgu;
    }
);



const show_form_modal = ref(false);
watch(
    () => props.value,
    (value)  => {
        show_form_modal.value = value;
    }
);

watch(
    () => form.role,
    (value) => {
        
    }
)

const close = () => {
    Object.assign(form, initialState);
    emit("input", false);
    errors.value = {};
}

const save = async () => {

    form.role = roles.value.find(role => role.slug === 'project-staff');

    if(props.staff && props.staff.id) {
        await updateStaff({ ...form });
    } else {
        await storeStaff({ ...form });
    }

    if (is_success.value == true){
        emit("reloadStaffs");
        emit("input", false);
    }
}

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
    <v-dialog v-model="show_form_modal" max-width="500px" scrollable persistent>
        <v-card>
            <v-card-title>
                <span class="text-h5" v-if="action_type == 'Update'">{{ action_type }} Member</span>
                <span class="text-h5" v-else>New Member</span>
            </v-card-title>
    
            <v-card-text>
                <v-container fluid>
                    <v-row>
                        <v-col cols="12" md="5" sm="5">
                            <v-text-field
                            v-model="form.first_name"
                            label="First Name*"
                            variant="outlined"
                            :error-messages="
                                errors['first_name'] ? errors['first_name'] : []
                            "
                        ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="5" sm="5">
                            <v-text-field
                            v-model="form.last_name"
                            label="Last Name*"
                            variant="outlined"
                            :error-messages="
                                errors['last_name'] ? errors['last_name'] : []
                            "
                        ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="2" sm="2">
                            <v-text-field
                            v-model="form.middle_name"
                            label="M.I*"
                            variant="outlined"
                            :error-messages="
                                errors['last_name'] ? errors['last_name'] : []
                            "
                        ></v-text-field>
                        </v-col>
                       
                    </v-row>
                    <v-row>
                        <v-col cols="12" md="6" sm="6">
                            <v-text-field
                            v-model="form.username"
                            label="Email*"
                            variant="outlined"
                            :error-messages="
                                errors['username'] ? errors['username'] : []
                            "
                        ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6" sm="6">
                            <v-text-field
                                v-model="form.mobile_number"
                                label="Mobile Number*"
                                variant="outlined"
                                :error-messages="
                                    errors['mobile_number'] ? errors['mobile_number'] : []
                                "
                            ></v-text-field>
                        </v-col>
                       
                    </v-row>

                    <!-- <v-row>
                        <v-col>
                            <vue-multiselect
                                v-model="form.role"
                                :options="roles"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="true"
                                :preserve-search="true"
                                placeholder="Select Role/s"
                                label="name"
                                track-by="name"
                                select-label=""
                                deselect-label=""
                                class="mb-3"
                            >
                            </vue-multiselect>

                        </v-col>
                    </v-row> -->
                    <v-row>
                        <v-col>
                            <vue-multiselect
                                v-model="form.region"
                                :options="regions"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="true"
                                :preserve-search="true"
                                placeholder="Select Region"
                                label="name"
                                track-by="name"
                                select-label=""
                                deselect-label=""
                                class="mb-3"
                            >
                            </vue-multiselect>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col>
                            <vue-multiselect
                                v-model="form.province"
                                :options="filteredProvinces"
                                :disabled="!form.region"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="true"
                                :preserve-search="true"
                                placeholder="Select province"
                                label="name"
                                track-by="name"
                                select-label=""
                                deselect-label=""
                                class="mb-3"
                            >
                            </vue-multiselect>

                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col>
                            <vue-multiselect
                                v-model="form.municipality" 
                                :options="filteredMunicipalities"
                                :disabled="!form.province"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="true"
                                :preserve-search="true"
                                placeholder="Select municipality"
                                label="name"
                                track-by="name"
                                select-label=""
                                deselect-label=""
                                class="mb-3"
                            >
                            </vue-multiselect>

                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col>
                            <vue-multiselect
                                v-model="form.river"
                                :options="filteredRivers"                              
                                :disabled="!form.municipality"
                                :multiple="false"
                                :close-on-select="true"
                                :clear-on-select="true"
                                :preserve-search="true"
                                placeholder="Select River"
                                label="name"
                                track-by="name"
                                select-label=""
                                deselect-label=""
                                class="mb-3"
                            >
                            </vue-multiselect>

                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col>
                            <v-text-field
                                v-model="form.fb_lgu"
                                label="Facebook LGU Link*"
                                variant="outlined"
                                :error-messages="
                                    errors['fb_lgu'] ? errors['fb_lgu'] : []
                                "
                            ></v-text-field>

                        </v-col>
                    </v-row>
                </v-container>
            </v-card-text>
    
            <v-card-actions class="mb-4 mr-5">
                <v-spacer></v-spacer>
                <v-btn color="blue-grey-lighten-2" @click="close()" variant="flat">
                    Cancel
                </v-btn>
                <v-btn color="primary" @click="save()" variant="flat" :loading="is_loading">
                    Save
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

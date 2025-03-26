<script setup>
import { ref, reactive, watch, onMounted } from "vue";
import useRoles from "../../composables/roles.js";
import useRegions from "../../composables/region.js";
import useProvinces from "../../composables/province.js";
import useMunicipalities from "../../composables/municipality.js";
import useRivers from "../../composables/river.js";
import useStaffs from "../../composables/staff.js";
import { defineProps, defineEmits } from "vue";

// ✅ Define props explicitly
const props = defineProps({
    staff: Object
});

// ✅ Define emits explicitly
const emit = defineEmits(["reloadStaffs", "input"]);

const { errors, is_loading, is_success, storeStaff, updateStaff } = useStaffs();
const { roles, getRoles } = useRoles();
const { regions, getRegions } = useRegions();
const { provinces, getProvinces } = useProvinces();
const { municipalities, getMunicipalities } = useMunicipalities();
const { rivers, getRivers } = useRivers();

const initialState = {
    id: null,
    username: null,
    first_name: null,
    last_name: null,
    mobile_number: null,
    role: {},
    region: {},
    province: {},
    municipality: {},
    river: {}
};
const form = reactive({ ...initialState });

// ✅ Fix: Use `props.staff` safely
watch(
    () => props.staff,
    (value) => {
        if (value) {
            form.id = value.id;
            form.username = value.username;
            form.first_name = value.first_name;
            form.last_name = value.last_name;
            form.mobile_number = value.mobile_number;
            form.role = value.role;
            form.region = value.region;
            form.province = value.province;
            form.municipality = value.municipality;
            form.river = value.river;
        }
    },
    { deep: true }
);

const save = async () => {
    if (props.staff && props.staff.id) {
        await updateStaff({ ...form });
    } else {
        await storeStaff({ ...form });
    }

    if (is_success.value == true) {
        emit("reloadStaffs");
        emit("input", false);
    }
};

onMounted(() => {
    getRoles();
    getRegions();
    getProvinces();
    getMunicipalities();
    getRivers();
});
</script>

<template>
    <div>
        <v-card>
            <v-card-title class="mt-2">
                <span class="text-h5">Register</span>
            </v-card-title>
    
            <v-card-text>
                <v-container>
                    <v-row>
                        <v-text-field
                            v-model="form.first_name"
                            label="First Name*"
                            variant="outlined"
                            :error-messages="
                                errors['first_name'] ? errors['first_name'] : []
                            "
                        ></v-text-field>
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.last_name"
                            label="Last Name*"
                            variant="outlined"
                            @keydown="generateUsername"
                            :error-messages="
                                errors['last_name'] ? errors['last_name'] : []
                            "
                        ></v-text-field>
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.username"
                            label="Username*"
                            variant="outlined"
                            :error-messages="
                                errors['username'] ? errors['username'] : []
                            "
                        ></v-text-field>
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.mobile_number"
                            label="Mobile Number*"
                            variant="outlined"
                            :error-messages="
                                errors['mobile_number'] ? errors['mobile_number'] : []
                            "
                        ></v-text-field>
                    </v-row>
                    <v-row>
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
                        >
                        </vue-multiselect>
                    </v-row>
                    <v-row>
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
                        >
                        </vue-multiselect>
                    </v-row>
                    <v-row>
                        <vue-multiselect
                            v-model="form.province"
                            :options="provinces"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :preserve-search="true"
                            placeholder="Select province"
                            label="name"
                            track-by="name"
                            select-label=""
                            deselect-label=""
                        >
                        </vue-multiselect>
                    </v-row>
                    <v-row>
                        <vue-multiselect
                            v-model="form.municipality"
                            :options="municipalities"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :preserve-search="true"
                            placeholder="Select municipality"
                            label="name"
                            track-by="name"
                            select-label=""
                            deselect-label=""
                        >
                        </vue-multiselect>
                    </v-row>
                    <v-row>
                        <vue-multiselect
                            v-model="form.river"
                            :options="rivers"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :preserve-search="true"
                            placeholder="Select River"
                            label="name"
                            track-by="name"
                            select-label=""
                            deselect-label=""
                        >
                        </vue-multiselect>
                    </v-row>
                </v-container>
            </v-card-text>
    
            <v-card-actions class="mb-4 mr-5">
                <v-spacer></v-spacer>
                <v-btn color="blue-grey-lighten-2" @click="close()" variant="tonal">
                    Cancel
                </v-btn>
                <v-btn color="primary" @click="save()" variant="tonal" :loading="is_loading">
                    Save
                </v-btn>
            </v-card-actions>
        </v-card>
    </div>
</template>

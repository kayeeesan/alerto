<script setup>
import { ref, reactive, watch, onMounted } from "vue";
import useSensorsUnderAlerto from "../../../composables/sensorsUnderAlerto";
import useRivers from "../../../composables/river";
import useMunicipalities from "../../../composables/municipality";

const { errors, is_loading, is_success, storeSensorUnderAlerto, updateSensorUnderAlerto } = useSensorsUnderAlerto();
const {rivers, getRivers} = useRivers();
const {municipalities, getMunicipalities} = useMunicipalities();

const emit = defineEmits(["input", "reloadSensorsUnderAlerto"]);
const props = defineProps({
    sensor_under_alerto: {
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
    name: null,
    river: {},
    municipality: {},
    long: null,
    lat: null,
    status: null,
};
const form = reactive({ ...initialState });

watch(
    () => props.sensor_under_alerto,
    (value) => {
        if(value){
            form.id = value.id;
            form.name = value.name;
            form.river = value.river;
            form.municipality = value.municipality;
            form.long = value.long;
            form.lat = value.lat;
            form.status = value.status;
         }
    }
);

const show_form_modal = ref(false);

watch(
    () => props.value,
    (value) => {
        show_form_modal.value = value;
    }
);

const close = () => {
    // Object.assign(form, initialState);
    emit("input", false);
    errors.value = {};
};

const save = async () => {
    if (props.sensor_under_alerto && props.sensor_under_alerto.id) {
        await updateSensorUnderAlerto({ ...form });
    } else {
        await storeSensorUnderAlerto({ ...form });
    }

    if (is_success.value == true) {
        emit("reloadSensorsUnderAlerto");
        emit("input", false);
    }
};

onMounted(() => {
    getRivers();
    getMunicipalities();
});
</script>

<template>
    <v-dialog v-model="show_form_modal" max-width="500px" scrollable>
        <v-card>
            <v-card-title>
                <!-- <span class="text-h5">{{ props.sensor_under_alerto ? 'Edit Sensor' : 'New Sensor' }}</span> -->
                <span class="text-h5" v-if="action_type == 'Update'">{{ action_type }} Sensor</span> 
                <span class="text-h5" v-else>New Sensor</span>
            </v-card-title>
    
            <v-card-text>
                <v-container>
                    <v-row>
                        <v-text-field
                            v-model="form.name"
                            label="Sensor name"
                            variant="outlined"
                            :error-messages="errors['name'] ? errors['name'] : []"
                            @keyup.enter="save()"
                        ></v-text-field>
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
                            track-by="id"
                            select-label=""
                            deselect-label=""
                            class="mb-3"
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
                            placeholder="Select Municipality"
                            label="name"
                            track-by="id"
                            select-label=""
                            deselect-label=""
                            class="mb-3"
                        >
                        </vue-multiselect>
                    </v-row>
                   
                    <v-row>
                        <v-text-field
                            v-model="form.long"
                            label="long"
                            variant="outlined"
                            @keyup.enter="save()"
                        ></v-text-field>
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.lat"
                            label="lat"
                            variant="outlined"
                            @keyup.enter="save()"
                        ></v-text-field>
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.status"
                            label="status"
                            variant="outlined"
                            @keyup.enter="save()"
                        ></v-text-field>
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
    </v-dialog>
</template>

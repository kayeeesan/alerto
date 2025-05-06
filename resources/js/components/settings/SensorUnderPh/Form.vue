<script setup>
import { ref, reactive, watch, onMounted, computed } from "vue";
import useSensorsUnderPh from "../../../composables/sensorsUnderPh";
import useRivers from "../../../composables/river";
import useMunicipalities from "../../../composables/municipality";

const { errors, is_loading, is_success, storeSensorUnderPh, updateSensorUnderPh } = useSensorsUnderPh();
const {rivers, getRivers} = useRivers();

const {municipalities, getMunicipalities} = useMunicipalities();
const emit = defineEmits(["input", "reloadSensorsUnderPh"]);
const props = defineProps({
    sensor_under_ph: {
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
    sensor_type: null
};
const form = reactive({ ...initialState });

watch(
    () => props.sensor_under_ph,
    (value) => {
        if(value){
            form.id = value.id;
            form.name = value.name;
            form.river = value.river;
            form.municipality = value.municipality;
            form.long = value.long;
            form.lat = value.lat;
            form.sensor_type = value.sensor_type;
         }
    }
);

const sensorType = [
    'ARG',
    'WLMS',
    'TANDEM'
];


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
    if (props.sensor_under_ph && props.sensor_under_ph.id) {
        await updateSensorUnderPh({ ...form });
    } else {
        await storeSensorUnderPh({ ...form });
    }

    if (is_success.value == true) {
        emit("reloadSensorsUnderPh");
        emit("input", false);
    }
};

onMounted(() => {
    getRivers();
    getMunicipalities();
});

const filteredRivers = computed(() => {
    if (!form.municipality || !form.municipality.id) return [];
    return rivers.value.filter(r => r.municipality.id === form.municipality.id );
 });
</script>

<template>
    <v-dialog v-model="show_form_modal" max-width="500px" scrollable>
        <v-card>
            <v-card-title>
                <!-- <span class="text-h5">{{ props.sensor_under_ph ? 'Edit Sensor' : 'New Sensor' }}</span> -->
               <span class="text-h5" v-if="action_type == 'Update'">{{ action_type }} Sensor</span> 
                <span class="text-h5" v-else>New Sensor</span>
            </v-card-title>
    
            <v-card-text>
                <v-container fluid>
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
                        <vue-multiselect
                            v-model="form.sensor_type"
                            :options="sensorType"
                            placeholder="Sensor Type"
                        ></vue-multiselect>
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

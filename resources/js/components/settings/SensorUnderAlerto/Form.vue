<script setup>
import { ref, reactive, watch, onMounted, computed } from "vue";
import useSensorsUnderAlerto from "../../../composables/sensorsUnderAlerto";
import useRivers from "../../../composables/river";
import useMunicipalities from "../../../composables/municipality";
import axios from "axios";

const { errors, is_loading, is_success, storeSensorUnderAlerto, updateSensorUnderAlerto } = useSensorsUnderAlerto();
const { rivers, getRivers } = useRivers();
const { municipalities, getMunicipalities } = useMunicipalities();

const devices = ref([]);
const selectedDevice = ref(null);

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
    sensor_type: null,
    device_rain_amount: null,
    device_water_level: null,
    device_id: null, 
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
            form.sensor_type = value.sensor_type;
            form.device_rain_amount = value.device_rain_amount;
            form.device_water_level = value.device_water_level;
            form.device_id = value.device_id;
            selectedDevice.value = devices.value.find(d => d.device_id === value.device_id);
        }
    }
);

const sensorType = ['ARG', 'WLMS', 'TANDEM'];
const show_form_modal = ref(false);

watch(
    () => props.value,
    (value) => {
        show_form_modal.value = value;
    }
);

const close = () => {
    emit("input", false);
    errors.value = {};
    Object.assign(form, initialState);
    selectedDevice.value = null;
};

const save = async () => {
    if (props.sensor_under_alerto?.id) {
        await updateSensorUnderAlerto({ ...form });
    } else {
        await storeSensorUnderAlerto({ ...form });
    }

    if (is_success.value) {
        emit("reloadSensorsUnderAlerto");
        close();
    }
};

const filteredRivers = computed(() => {
    if (!form.municipality?.id) return [];
    return rivers.value.filter(r => r.municipality.id === form.municipality.id);
});

const fetchDevices = async () => {
    try {
        const response = await axios.get('/api/fetch-devices');
        devices.value = response.data;
    } catch (error) {
        console.error("Failed to fetch devices:", error);
        devices.value = [];
    }
};

const onDeviceSelected = (device) => {
    if (device) {
        form.name = device.name;
        form.device_id = device.device_id;
        form.device_rain_amount = device.device_rain_amount;
        form.long = device.long;
        form.lat = device.lat;
        selectedDevice.value = device;
    }
};

onMounted(async () => {
    await Promise.all([
        getRivers(),
        getMunicipalities(),
        fetchDevices()
    ]);
});
</script>

<template>
    <v-dialog v-model="show_form_modal" max-width="500px" scrollable>
        <v-card>
            <v-card-title>
                <span class="text-h5">{{ action_type || 'New' }} Sensor</span>
            </v-card-title>
    
            <v-card-text>
                <v-container fluid>
                    <v-row>
                        <vue-multiselect
                            v-model="selectedDevice"
                            :options="devices"
                            label="name"
                            placeholder="Select Device"
                            @select="onDeviceSelected"
                            track-by="device_id"
                            class="mb-3"
                        />
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.name"
                            label="Name"
                            variant="outlined"
                            readonly
                        />
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.device_id"
                            label="Device ID"
                            variant="outlined"
                            readonly
                        />
                    </v-row>
                    <v-row>
                        <vue-multiselect
                            v-model="form.municipality"
                            :options="municipalities"
                            :multiple="false"
                            placeholder="Select Municipality"
                            label="name"
                            track-by="id"
                            class="mb-3"
                        />
                    </v-row>
                    <v-row>
                        <vue-multiselect
                            v-model="form.river"
                            :options="filteredRivers"                              
                            :disabled="!form.municipality"
                            :multiple="false"
                            placeholder="Select River"
                            label="name"
                            track-by="id"
                            class="mb-3"
                        />
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.long"
                            label="Longitude"
                            variant="outlined"
                            readonly
                        />
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.lat"
                            label="Latitude"
                            variant="outlined"
                            readonly
                        />
                    </v-row>
                    <v-row>
                        <vue-multiselect
                            v-model="form.sensor_type"
                            :options="sensorType"
                            placeholder="Sensor Type"
                            class="mb-3"
                        />
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.device_rain_amount"
                            label="Rain Amount"
                            variant="outlined"
                        />
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.device_water_level"
                            label="Water Level"
                            variant="outlined"
                        />
                    </v-row>
                </v-container>
            </v-card-text>
    
            <v-card-actions class="mb-4 mr-5">
                <v-spacer></v-spacer>
                <v-btn color="blue-grey-lighten-2" @click="close" variant="flat">
                    Cancel
                </v-btn>
                <v-btn color="primary" @click="save" variant="flat" :loading="is_loading">
                    Save
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
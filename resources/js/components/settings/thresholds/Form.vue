<script setup>
import { ref, reactive, watch, onMounted, computed } from "vue";
import useThresholds from "../../../composables/threshold";
import useSensorsUnderAlerto from "../../../composables/sensorsUnderAlerto";
import useSensorsUnderPh from "../../../composables/sensorsUnderPh";

const { errors, is_loading, is_success, storeThreshold, updateThreshold } = useThresholds();
const { sensors_under_alerto, getSensorsUnderAlerto } = useSensorsUnderAlerto();
const { sensors_under_ph, getSensorsUnderPh } = useSensorsUnderPh();

const emit = defineEmits(["input", "reloadThresholds"]);
const props = defineProps({
    threshold: { type: Object, default: null },
    action_type: { type: String, default: null },
    value: { type: Boolean, default: false }
});

const initialState = {
    id: null,
    sensor: null,
    sensor_id: null,
    baseline: null,
    sixty_percent: null,
    eighty_percent: null,
    one_hundred_percent: null,
    xs_date: null,
    water_level: null
};

const form = reactive({ ...initialState });

// Watch for changes to the threshold prop and update form data accordingly
watch(() => props.threshold, (value) => {
    if (value) {
        form.id = value.id;
        form.sensor = value.sensor;
        form.sensor_id = value.sensor?.id;
        form.baseline = value.baseline;
        form.sixty_percent = value.sixty_percent;
        form.eighty_percent = value.eighty_percent;
        form.one_hundred_percent = value.one_hundred_percent;
        form.xs_date = value.xs_date;
        form.water_level = value.water_level;
    }
});

const show_form_modal = ref(false);
watch(() => props.value, (value) => {
    show_form_modal.value = value;
});

const sensor_group = ref("alerto");

const available_sensors = computed(() => {
    return sensor_group.value === "alerto"
        ? sensors_under_alerto.value.map(sensor => ({
            ...sensor,
            type: "App\\Models\\SensorUnderAlerto"  // Add the type explicitly
        }))
        : sensors_under_ph.value.map(sensor => ({
            ...sensor,
            type: "App\\Models\\SensorUnderPh"  // Add the type explicitly
        }));
});


// Close the form modal
const close = () => {
    emit("input", false);
    errors.value = {};
};

// Save the threshold data
const save = async () => {
    console.log("Selected sensor:", form.sensor); // Check if sensor has 'id' and 'type'
    
    const payload = {
        ...form,
        sensorable_id: form.sensor?.id,
        sensorable_type: sensor_group.value === "alerto" 
            ? "App\\Models\\SensorUnderAlerto" 
            : "App\\Models\\SensorUnderPh"
    };

    try {
        if (form.id) {
            await updateThreshold(payload);
        } else {
            await storeThreshold(payload);
        }

        if (is_success.value === true) {
            emit("reloadThresholds");
            emit("input", false);
        }
    } catch (error) {
        errors.value = error.response?.data?.errors || {};
    }
};



// Fetch sensor data on mounted
onMounted(() => {
    getSensorsUnderAlerto();
    getSensorsUnderPh();
});
</script>

<template>
    <v-dialog v-model="props.value" max-width="500px" scrollable persistent>
        <v-card>
            <v-card-title>
                <span class="text-h5" v-if="action_type === 'Update'">{{ action_type }} Threshold</span>
                <span class="text-h5" v-else>New Threshold</span>
            </v-card-title>

            <v-card-text>
                <v-container fluid>
                    <v-row>
                        <v-radio-group v-model="sensor_group" label="Sensor Group">
                            <v-radio label="Sensors under Alerto" value="alerto"></v-radio>
                            <v-radio label="Sensors under PH" value="ph"></v-radio>
                        </v-radio-group>
                    </v-row>

                    <v-row>
                        <!-- Sensor selection based on selected sensor group -->
                        <vue-multiselect
                            v-model="form.sensor"
                            :options="available_sensors"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :preserve-search="true"
                            placeholder="Select Sensor"
                            label="name"
                            track-by="id"
                            select-label=""
                            deselect-label=""
                        />
                    </v-row>

                    <v-row>
                        <v-text-field v-model="form.baseline" label="Baseline" variant="outlined" class="mt-4" @keyup.enter="save" />
                    </v-row>
                    <v-row>
                        <v-text-field v-model="form.sixty_percent" label="60%" variant="outlined" bg-color="teal-lighten-4" @keyup.enter="save" />
                    </v-row>
                    <v-row>
                        <v-text-field v-model="form.eighty_percent" label="80%" variant="outlined" bg-color="orange-lighten-3" @keyup.enter="save" />
                    </v-row>
                    <v-row>
                        <v-text-field v-model="form.one_hundred_percent" label="100%" variant="outlined" bg-color="deep-orange-lighten-4" @keyup.enter="save" />
                    </v-row>
                    <v-row>
                        <v-text-field v-model="form.xs_date" label="XS Date" type="date" variant="outlined" @keyup.enter="save" />
                    </v-row>
                    <v-row>
                        <v-text-field v-model="form.water_level" label="Water Level" variant="outlined" @keyup.enter="save" />
                    </v-row>
                </v-container>
            </v-card-text>

            <v-card-actions class="mb-4 mr-5">
                <v-spacer></v-spacer>
                <v-btn color="blue-grey-lighten-2" @click="close" variant="tonal">Cancel</v-btn>
                <v-btn color="primary" @click="save" variant="tonal" :loading="is_loading">Save</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

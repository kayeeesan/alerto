<script setup>
import { ref, reactive, watch, onMounted, computed, nextTick  } from "vue";
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
    xs_date: null
};

const form = reactive({ ...initialState });

watch(() => props.threshold, async (value) => {
    if (value) {
        await nextTick(); // Wait for components to render
        
        form.id = value.id;
        form.sensor = value.sensor;
        form.sensor_id = value.sensor?.id;
        form.baseline = value.baseline;
        form.sixty_percent = value.sixty_percent;
        form.eighty_percent = value.eighty_percent;
        form.one_hundred_percent = value.one_hundred_percent;
        form.xs_date = value.xs_date;

        // Set sensor group based on type
        if (value.sensor?.type) {
            if (value.sensor.type.includes('SensorUnderAlerto')) {
                sensor_group.value = ['alerto'];
            } else if (value.sensor.type.includes('SensorUnderPh')) {
                sensor_group.value = ['ph'];
            }
        }
    }
}, { immediate: true, deep: true });

const sensor_group = ref([]);

watch(sensor_group, (val) => {
    if (val.length > 1) {
        // Keep only the last selected item
        sensor_group.value = [val[val.length - 1]];
    }
});


const show_form_modal = ref(false);

watch(() => props.value, (value) => {
    show_form_modal.value = value;

});

const available_sensors = computed(() => {
    const result = [];
    
    // Add current sensor first if it exists
    if (props.threshold?.sensor) {
        result.push({
            ...props.threshold.sensor,
            type: props.threshold.sensor.type
        });
    }

    // Add sensors from selected groups
    if (sensor_group.value.includes("alerto")) {
        sensors_under_alerto.value.forEach(sensor => {
            if (!result.some(s => s.id === sensor.id)) {
                result.push({
                    ...sensor,
                    type: "App\\Models\\SensorUnderAlerto"
                });
            }
        });
    }

    if (sensor_group.value.includes("ph")) {
        sensors_under_ph.value.forEach(sensor => {
            if (!result.some(s => s.id === sensor.id)) {
                result.push({
                    ...sensor,
                    type: "App\\Models\\SensorUnderPh"
                });
            }
        });
    }

    return result;
});

const close = () => {
    emit("input", false);
    errors.value = {};
};

const save = async () => {
    // Ensure we have the type from either current selection or existing threshold
    const sensorable_type = form.sensor?.type || props.threshold?.sensor?.type;
    
    if (!sensorable_type) {
        errors.value = { sensorable_type: ["The sensorable type field is required."] };
        return;
    }

    const payload = {
        ...form,
        sensorable_id: form.sensor?.id || form.sensor_id,
        sensorable_type: sensorable_type
    };

    try {
        if (form.id) {
            await updateThreshold(payload);
        } else {
            await storeThreshold(payload);
        }

        if (is_success.value) {
            emit("reloadThresholds");
            close();
        }
    } catch (error) {
        errors.value = error.response?.data?.errors || {};
    }
};

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
                <v-container fluid class="pt-0">
                    <div class="d-flex">
                        <v-checkbox
                            v-model="sensor_group"
                            label="Sensors under Alerto"
                            value="alerto"
                        ></v-checkbox>
                        <v-checkbox
                            v-model="sensor_group"
                            label="Sensors under PH"
                            value="ph"
                        ></v-checkbox>
                    </div>
                   
                    <v-row>
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
                        <v-text-field v-model="form.sixty_percent" label="60%" variant="outlined" bg-color="yellow-lighten-3" @keyup.enter="save" />
                    </v-row>
                    <v-row>
                        <v-text-field v-model="form.eighty_percent" label="80%" variant="outlined" bg-color="orange-lighten-2" @keyup.enter="save" />
                    </v-row>
                    <v-row>
                        <v-text-field v-model="form.one_hundred_percent" label="100%" variant="outlined" bg-color="deep-orange-lighten-3" @keyup.enter="save" />
                    </v-row>
                    <v-row>
                        <v-text-field v-model="form.xs_date" label="XS Date" type="date" variant="outlined" @keyup.enter="save" />
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

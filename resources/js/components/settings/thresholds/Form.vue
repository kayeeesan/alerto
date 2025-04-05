<script setup>
import { ref, reactive, watch, onMounted } from "vue";
import useThresholds from "../../../composables/threshold";
import useSensorsUnderAlerto from "../../../composables/sensorsUnderAlerto";

const { errors, is_loading, is_success, storeThreshold, updateThreshold } = useThresholds();
const {sensors_under_alerto, getSensorsUnderAlerto} = useSensorsUnderAlerto();


const emit = defineEmits(["input", "reloadThresholds"]);
const props = defineProps({
    threshold: {
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
    sensor: {},
    baseline: null,
    sixty_percent: null,
    eighty_percent: null,
    one_hundred_percent: null,
    xs_date: null,
    water_level: null
   
}
const form = reactive({ ...initialState });

watch(
    () => props.threshold,
    (value)  => {
        if(value){
            form.id = value.id;
            form.sensor = value.sensor;
            form.baseline = value.baseline;
            form.sixty_percent = value.sixty_percent;
            form.eighty_percent = value.eighty_percent;
            form.one_hundred_percent = value.one_hundred_percent;
            form.xs_date = value.xs_date;
            form.water_level = value.water_level;
        }
    }
);

const show_form_modal = ref(false);

watch(
    () => props.value,
    (value)  => {
        show_form_modal.value = value;
    }
);

const close = () => {
    // Object.assign(form, initialState);
    emit("input", false);
    errors.value = {};
}

const save = async () => {
    if(props.threshold && props.threshold.id) {
        await updateThreshold({ ...form });
    } else {
        await storeThreshold({ ...form });
    }

    if (is_success.value == true){
        emit("reloadThresholds");
        emit("input", false);
    }
}


onMounted(() => {
    getSensorsUnderAlerto();
});
</script>
<template>
    <v-dialog v-model="props.value" max-width="500px" scrollable persistent>
        <v-card>
            <v-card-title>
                <!-- <span class="text-h5">New Threshold</span> -->
                <span class="text-h5" v-if="action_type == 'Update'">{{ action_type }} Threshold</span>
                <span class="text-h5" v-else>New Threshold</span>
            </v-card-title>
    
            <v-card-text>
                <v-container fluid>
                    <v-row>
                    </v-row>
                    <v-row>
                        <vue-multiselect
                            v-model="form.sensor"
                            :options="sensors_under_alerto"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :preserve-search="true"
                            placeholder="Select Sensor"
                            label="name"
                            track-by="id"
                            select-label=""
                            deselect-label=""
                        >
                        </vue-multiselect>
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.baseline"
                            label="baseline"
                            variant="outlined"
                            @keyup.enter="save()"
                            class="mt-4"
                        ></v-text-field>
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.sixty_percent"
                            label="60%"
                            variant="outlined"
                            bg-color="teal-lighten-4"
                            @keyup.enter="save()"
                        ></v-text-field>
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.eighty_percent"
                            label="80%"
                            variant="outlined"
                            bg-color="orange-lighten-3"
                            @keyup.enter="save()"
                        ></v-text-field>
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.one_hundred_percent"
                            label="100%"
                            variant="outlined"
                            bg-color="deep-orange-lighten-4
"
                            @keyup.enter="save()"
                        ></v-text-field>
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.xs_date"
                            label="XS Date"
                            variant="outlined"
                            type="date"
                            @keyup.enter="save()"
                        ></v-text-field>
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.water_level"
                            label="Water Level"
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

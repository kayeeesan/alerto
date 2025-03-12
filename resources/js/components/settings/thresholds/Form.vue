<script setup>
import { ref, reactive, watch, onMounted } from "vue";
import useThresholds from "../../../composables/threshold";
import useRivers from "../../../composables/river";
import useSensorsUnderAlerto from "../../../composables/sensorsUnderAlerto";
import useMunicipalities from "../../../composables/municipality";

const { errors, is_loading, is_success, storeThreshold, updateThreshold } = useThresholds();
const {rivers, getRivers} = useRivers();
const {sensors_under_alerto, getSensorsUnderAlerto} = useSensorsUnderAlerto();
const {municipalities, getMunicipalities} = useMunicipalities();


const emit = defineEmits(["input", "reloadThresholds"]);
const props = defineProps({
    threshold: {
        type: Object,
        default: null
    },
    value: {
        type: Boolean,
        default: false,
    }
});

const initialState = {
    river: null,
    sensor: null,
    municipality: null,
    xs_date: null,
   
}
const form = reactive({ ...initialState });

watch(
    () => props.threshold,
    (value)  => {
        form.river = value.river;
        form.sensor = value.sensor;
        form.municipality = value.municipality;
        form.xs_date = value.xs_date;
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
    Object.assign(form, initialState);
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
    getRivers();
    getSensorsUnderAlerto();
    getMunicipalities();
});
</script>
<template>
    <v-dialog v-model="props.value" max-width="500px" scrollable persistent>
        <v-card>
            <v-card-title>
                <span class="text-h5">New Threshold</span>
            </v-card-title>
    
            <v-card-text>
                <v-container>
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
                        >
                        </vue-multiselect>
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
                        <vue-multiselect
                            v-model="form.municipality"
                            :options="municipalities"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :preserve-search="true"
                            placeholder="Select River"
                            label="name"
                            track-by="id"
                            select-label=""
                            deselect-label=""
                        >
                        </vue-multiselect>
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

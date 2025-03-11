<script setup>
import { ref, reactive, watch } from "vue";
import useSensorsUnderPh from "../../../composables/sensorsUnderPh";

const { errors, is_loading, is_success, storeSensorUnderPh, updateSensorUnderPh } = useSensorsUnderPh();

const emit = defineEmits(["input", "reloadSensorsUnderPh"]);
const props = defineProps({
    sensor_under_ph: {
        type: Object,
        default: null
    },
    value: {
        type: Boolean,
        default: false,
    }
});

const initialState = {
    id: null,
    name: null,
    baseline: null,
    sixty_percent: null,
    eighty_percent: null,
    one_hundred_percent: null,
};
const form = reactive({ ...initialState });

watch(
    () => props.sensor_under_ph,
    (value) => {
        form.id = value.id;
        form.name = value.name;
        form.baseline = value.baseline;
        form.sixty_percent = value.sixty_percent;
        form.eighty_percent = value.eighty_percent;
        form.one_hundred_percent = value.one_hundred_percent;
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
    Object.assign(form, initialState);
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
</script>

<template>
    <v-dialog v-model="show_form_modal" max-width="500px" scrollable>
        <v-card>
            <v-card-title>
                <span class="text-h5">{{ props.sensor_under_ph ? 'Edit Sensor' : 'New Sensor' }}</span>
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
                        <v-text-field
                            v-model="form.baseline"
                            label="Baseline"
                            variant="outlined"
                            @keyup.enter="save()"
                        ></v-text-field>
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.sixty_percent"
                            label="60%"
                            variant="outlined"
                            @keyup.enter="save()"
                        ></v-text-field>
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.eighty_percent"
                            label="80%"
                            variant="outlined"
                            @keyup.enter="save()"
                        ></v-text-field>
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.one_hundred_percent"
                            label="100%"
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

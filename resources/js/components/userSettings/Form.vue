<script setup>
import { ref, reactive, watch, onMounted } from "vue";
import useAlerts from "../../composables/alerts";
import useResponses from "../../composables/response";

const { errors, is_loading, is_success, updateAlert } = useAlerts();
const { responses, getResponses } = useResponses();

const emit = defineEmits(["input", "reloadAlerts"]);
const props = defineProps({
    alert: {
        type: Object,
        default: null
    },
    value: {
        type: Boolean,
        default: false
    }
});

const initialState = {
    id: null,
    response: null,
    action: null
}

const form = reactive({ ...initialState });

watch(
    () => props.alert,
    (value) => {
        if (value) {
            form.id = value.id;
            form.response = value.response || null; 
            form.details = value.details;
        }
    }
);

const show_form_modal = ref(false);

const close = () => {
    // Reset form
    Object.assign(form, initialState);
    emit("input", false);
    errors.value = {};
}

const save = async () => {
    if (form.id) { // Ensure the alert has an id before trying to update
        await updateAlert({ ...form });

        // If the update is successful
        if (is_success.value) {
            emit("reloadAlerts"); // Emit to reload alerts
            emit("input", false); // Close the modal
        }
    }
}

onMounted(() => {
    getResponses(); // Fetch responses when the component is mounted
});
</script>

<template>
    <v-dialog v-model="props.value" max-width="500px" scrollable persistent>
        <v-card>
            <v-card-title>
                <span class="text-h5">Alert Details</span>
            </v-card-title>

            <v-card-text>
                <v-row>
                    <v-text-field
                            v-model="form.details"
                            variant="outlined"
                            readonly
                        ></v-text-field>
                </v-row>
                <v-row>
                    <vue-multiselect
                        v-model="form.response"
                        :options="responses"
                        :multiple="false"
                        :close-on-select="true"
                        :clear-on-select="true"
                        :preserve-search="true"
                        placeholder="Select response"
                        label="action"
                        track-by="id"
                        select-label=""
                        deselect-label=""
                    ></vue-multiselect>
                </v-row>
            </v-card-text>

            <v-card-actions class="mb-4 mr-5">
                <v-spacer></v-spacer>
                <v-btn color="blue-grey-lighten-2" @click="close" variant="tonal">
                    Cancel
                </v-btn>
                <v-btn color="primary" variant="tonal" @click="save" :loading="is_loading">
                    Save
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

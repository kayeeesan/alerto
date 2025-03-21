<script setup>
import { ref, reactive, watch } from "vue";
import useResponses from "../../../composables/response";

const { errors, is_loading, is_success, storeResponse, updateResponse } = useResponses();

const emit = defineEmits(["input", "reloadResponses"]);
const props = defineProps({
    response: {
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
    color: null,
    action: null,
    code: null
}
const form = reactive({ ...initialState });

watch(
    () => props.response,
    (value)  => {
        form.id = value.id;
        form.color = value.color;
        form.action = value.action;
        form.code = value.code;
    }
);

const colors = [
    'red',
    'orange',
    'yellow'
];

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
    if(props.response && props.response.id) {
        await updateResponse({ ...form });
    } else {
        await storeResponse({ ...form });
    }

    if (is_success.value == true){
        emit("reloadResponses");
        emit("input", false);
    }
}
</script>
<template>
    <v-dialog v-model="show_form_modal" max-width="500px" scrollable persistent>
        <v-card>
            <v-card-title>
                <span class="text-h5">New Response</span>
            </v-card-title>
    
            <v-card-text>
                <v-container>
                    <v-row>
                        <vue-multiselect
                            v-model="form.color"
                            :options="colors"
                            class="mb-4"
                            placeholder="Select Color"
                        ></vue-multiselect>
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.action"
                            label="ACTION NEEDED"
                            variant="outlined"
                            @keyup.enter="save()"
                        ></v-text-field>
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.code"
                            label="CODE"
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

<script setup>
import { ref, reactive, watch, onMounted } from "vue";
import useAlerts from "../../composables/alerts";
import useResponses from "../../composables/response";

const {errors, is_loading, is_success, updateAlert } = useAlerts();
const {responses, getResponses} = useResponses();

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
    response: {},
    user: {},
}

const form = reactive({ ...initialState });

watch(
    () => props.alert,
    (value) => {
        if(value){
            form.id = value.id;
            form.response = value.response || {}; 
        }
    }
);

const show_form_modal = ref(false);

const close = () => {
    //Object.assign(form, initialState);
    emit("input", false);
    errors.value = {};
}


onMounted(() => {
    getResponses();
})

</script>
<template>
      <v-dialog v-model="props.value" max-width="500px" scrollable persistent>
        <v-card>
            <v-card-title>
                <span class="text-h5" >Respond</span>
            </v-card-title>
    
            <v-card-text>
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
                <v-btn color="blue-grey-lighten-2" @click="close()" variant="tonal">
                    Cancel
                </v-btn>
                <v-btn color="primary" variant="tonal" :loading="is_loading">
                    Save
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
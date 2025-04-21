<script setup>
import { ref, reactive, watch } from "vue";
import useRegions from "../../../composables/region";

const { errors, is_loading, is_success, storeRegion, updateRegion } = useRegions();

const emit = defineEmits(["input", "reloadRegions"]);
const props = defineProps({
    region: {
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
}
const form = reactive({ ...initialState });

watch(
    () => props.region,
    (value)  => {
        form.id = value.id;
        form.name = value.name;
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
    if(props.region && props.region.id) {
        await updateRegion({ ...form });
    } else {
        await storeRegion({ ...form });
    }

    if (is_success.value == true){
        emit("reloadRegions");
        emit("input", false);
    }
}

watch(
    () => form.name,
    (value) => {
        if (value) {
            form.name = value.toUpperCase();
        }
    }
);

</script>
<template>
    <v-dialog v-model="show_form_modal" max-width="500px" scrollable persistent>
        <v-card>
            <v-card-title>
                <span v-if="action_type == 'Update'">{{ action_type }} Region</span>
                <span v-else class="text-h5">New Region</span>
            </v-card-title>
    
            <v-card-text>
                <v-container>
                    <v-row>
                        <v-text-field
                            v-model="form.name"
                            label="Region name"
                            variant="outlined"
                            :error-messages="errors['name'] ? errors['name'] : []"
                            @keyup.enter="save()"
                            @input="form.name = form.name.toUpperCase()"
                        />

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

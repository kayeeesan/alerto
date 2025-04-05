<script setup>
import { ref, reactive, watch, onMounted } from "vue";
import useRivers from "../../../composables/river";
import useMunicialities from "../../../composables/municipality";

const { errors, is_loading, is_success, storeRiver, updateRiver } = useRivers();
const {municipalities, getMunicipalities} = useMunicialities();

const emit = defineEmits(["input", "reloadRivers"]);
const props = defineProps({
    river: {
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
    river_code: null,
    municipality: {},
}
const form = reactive({ ...initialState });

watch(
    () => props.river,
    (value)  => {
        form.id = value.id;
        form.name = value.name;
        form.river_code = value.river_code;
        form.municipality = value.municipality;
    }
);

watch(
    () => form.name,
    (name) => {
        if (name) {
            form.river_code = name
                .toLowerCase()
                .replace(/ /g, '_')  // Replace spaces with hyphens
                .replace(/[^\w-]+/g, '');  // Remove special characters
        } else {
            form.river_code = null;
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
    Object.assign(form, initialState);
    emit("input", false);
    errors.value = {};
}

const save = async () => {
    if(props.river && props.river.id) {
        await updateRiver({ ...form });
    } else {
        await storeRiver({ ...form });
    }

    if (is_success.value == true){
        emit("reloadRivers");
        emit("input", false);
    }
}

onMounted(() => {
    getMunicipalities();
})
</script>
<template>
    <v-dialog v-model="show_form_modal" max-width="500px" scrollable persistent>
        <v-card>
            <v-card-title>
                <span class="text-h5" v-if="action_type == 'Update'">{{ action_type }} River</span>
                <span class="text-h5" v-else>New River</span>
            </v-card-title>
    
            <v-card-text>
                <v-container fluid>
                    <v-row>
                        <vue-multiselect
                            v-model="form.municipality"
                            :options="municipalities"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :preserve-search="true"
                            placeholder="Select Municipality/City"
                            label="name"
                            track-by="id"
                            select-label=""
                            deselect-label=""
                            class="mb-4"
                        >
                        </vue-multiselect>
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.name"
                            label="River name"
                            variant="outlined"
                            :error-messages="
                                errors['name'] ? errors['name'] : []
                            "
                            @keyup.enter="save()"
                        ></v-text-field>
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.river_code"
                            label="River code"
                            variant="outlined"
                            :error-messages="errors['river_code'] ? errors['river_code'] : []"
                            @keyup.enter="save()"
                            readonly
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

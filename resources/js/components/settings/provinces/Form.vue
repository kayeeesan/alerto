<script setup>
import { ref, reactive, watch, onMounted } from "vue";
import useProvinces from "../../../composables/province";
import useRegions from "../../../composables/region";

const { errors, is_loading, is_success, storeProvince, updateProvince } = useProvinces();
const {regions, getMultiselectRegions} = useRegions();

const emit = defineEmits(["input", "reloadProvinces"]);
const props = defineProps({
    province: {
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
    region: {}
}
const form = reactive({ ...initialState });

watch(
    () => props.province,
    (value)  => {
        form.id = value.id;
        form.name = value.name;
        form.region = value.region;
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
    if(props.province && props.province.id) {
        await updateProvince({ ...form });
    } else {
        await storeProvince({ ...form });
    }

    if (is_success.value == true){
        emit("reloadProvinces");
        emit("input", false);
    }
}

onMounted(() => {
    getMultiselectRegions();
});
</script>
<template>
    <v-dialog v-model="show_form_modal" max-width="500px" scrollable persistent>
        <v-card>
            <v-card-title>
                <span class="text-h5" v-if="action_type == 'Update'">{{ action_type }} Province</span>
                <span class="text-h5" v-else>New Province</span>
            </v-card-title>
    
            <v-card-text>
                <v-container fluid>
                    <v-row>
                        <vue-multiselect
                            v-model="form.region"
                            :options="regions"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :preserve-search="true"
                            placeholder="Select Region"
                            label="name"
                            track-by="id"
                            select-label=""
                            deselect-label=""
                            class="mb-3"
                        >
                        </vue-multiselect>
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.name"
                            label="Province name"
                            variant="outlined"
                            :error-messages="
                                errors['name'] ? errors['name'] : []
                            "
                            @keyup.enter="save()"
                        ></v-text-field>
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

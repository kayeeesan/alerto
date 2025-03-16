<script setup>
import { ref, reactive, watch, onMounted } from "vue";
import useMunicipalities from "../../../composables/municipality";
import useProvinces from "../../../composables/province";

const { errors, is_loading, is_success, storeMunicipality, updateMunicipality } = useMunicipalities();
const {provinces, getProvinces} = useProvinces();

const emit = defineEmits(["input", "reloadMunicipalities"]);
const props = defineProps({
    municipality: {
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
    province: {},
}
const form = reactive({ ...initialState });

watch(
    () => props.municipality,
    (value)  => {
        if(value){
            form.id = value.id;
            form.name = value.name;
            form.province = value.province;
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
    //Object.assign(form, initialState);
    emit("input", false);
    errors.value = {};
}

const save = async () => {
    if(props.municipality && props.municipality.id) {
        await updateMunicipality({ ...form });
    } else {
        await storeMunicipality({ ...form });
    }

    if (is_success.value == true){
        emit("reloadMunicipalities");
        emit("input", false);
    }
}

onMounted(() => {
    getProvinces();
});
</script>
<template>
    <v-dialog v-model="props.value" max-width="500px" scrollable persistent>
        <v-card>
            <v-card-title>
             
                <span class="text-h5" v-if="action_type == 'Update'">{{ action_type }} Municipality</span>
                <span class="text-h5" v-else>New Municipality</span>
            </v-card-title>
    
            <v-card-text>
                <v-container>
                    <v-row>
                        <v-text-field
                            v-model="form.name"
                            label="Municipality name"
                            variant="outlined"
                            :error-messages="
                                errors['name'] ? errors['name'] : []
                            "
                            @keyup.enter="save()"
                        ></v-text-field>
                    </v-row>
                    <v-row>
                        <vue-multiselect
                            v-model="form.province"
                            :options="provinces"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :preserve-search="true"
                            placeholder="Select Province"
                            label="name"
                            track-by="id"
                            select-label=""
                            deselect-label=""
                        >
                        </vue-multiselect>
                       
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

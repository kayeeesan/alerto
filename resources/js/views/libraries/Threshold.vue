<script setup>
import { ref, onMounted } from "vue";
import ThresholdForm from "../../components/settings/thresholds/Form.vue";
import useThresholds from "../../composables//threshold";

const { thresholds, pagination, query, is_loading, getThresholds, destoryThreshold } = useThresholds();

const threshold = ref({});
const action_type = ref('');
const show_form_modal = ref(false);

const headers = [
    { title: "Sensor", key: "sensor.name" },
    { title: "Baseline", key: "baseline" },
    { title: "60%", key: "sixty_percent" },
    { title: "80%", key: "eighty_percent" },
    { title: "100%", key: "one_hundred_percent" },
    { title: "XS date", key: "xs_date" },
    { title: "Actions", key: "actions", sortable: false },
];

const showModalForm = (val) => {
    show_form_modal.value = val;
};

onMounted(() => {
    getThresholds();
});

const editItem = (value, action) => {
    threshold.value = value;
    action_type.value = action;
    show_form_modal.value = value;
};

const deleteItem = async (value) => {
    await destoryThreshold(value.id);
};

const reloadThresholds = async () => {
    await getThresholds();
    threshold.value = {};
};


</script>
<template>
    <v-row class="p-2">
        <h5 class="fw-bold p-3">List of Thresholds </h5>
        <v-spacer></v-spacer>
        <v-btn color="primary" @click="showModalForm(true)" class="m-3">
            New Threshold
        </v-btn>
    </v-row>
    <v-card>
        <div class="overflow-hidden overflow-x-auto min-w-full align-middle">
            <v-card-title>
                <v-text-field
                    v-model="query.search"
                    append-icon="mdi-magnify"
                    label="Search"
                    single-line
                    hide-details
                ></v-text-field>
            </v-card-title>
            <v-data-table 
                :headers="headers" 
                :items="thresholds"
                :search="query.search"
                class="elevation-1 p-2"
                :loading="is_loading"
                loading-text="Loading... Please wait"
            >
                <template v-slot:item.actions="{ item }">
                    <v-btn
                        class="me-2"
                        color="success"
                        @click="editItem(item, 'Update')"
                        variant="tonal"
                        size="small"
                    >
                        <v-icon size="small"> mdi-pencil </v-icon> Edit
                    </v-btn>
                    <v-btn
                        color="error"
                        @click="deleteItem(item)"
                        variant="tonal"
                        size="small"
                    >
                        <v-icon> mdi-delete </v-icon> delete
                    </v-btn>
                </template>
                <template v-slot:bottom>
                    <div class="m-2">
                        <span style="color: gray" v-if="pagination">
                            Showing {{ pagination.from }} to
                            {{ pagination.to }} out of
                            <b>{{ pagination.total }} records</b>
                        </span>
                        <div class="text-center">
                            <v-pagination
                                v-model="query.page"
                                :length="pagination.last_page"
                                circle
                                @click="getThresholds"
                            >
                            </v-pagination>
                        </div>
                    </div>
                </template>
            </v-data-table>
        </div>
    </v-card>

    <ThresholdForm
    :value="show_form_modal"
    :threshold="threshold"
    :action_type="action_type"
    @input="showModalForm"
    @reloadThresholds="reloadThresholds"
    />

</template>

<script setup>
import { ref, onMounted } from "vue";
import SensorsUnderAlertoForm from "../../components/settings/SensorUnderAlerto/Form.vue";
import useSensorsUnderAlerto from "../../composables/sensorsUnderAlerto";

const { sensors_under_alerto, pagination, query, is_loading, getSensorsUnderAlerto, destorySensorUnderAlerto } = useSensorsUnderAlerto();

const sensor_under_alerto = ref({});
const show_form_modal = ref(false);

const headers = [
    { title: "Name", align: "start", sortable: false, key: "name" },
    { title: "river", key: "river" },
    { title: "municipality", key: "municipality" },
    { title: "long", key: "long" },
    { title: "lat", key: "lat" },
    { title: "status", key: "status" },
    { title: "", key: "actions" },
];

const showModalForm = (val) => {
    show_form_modal.value = val;
};

onMounted(() => {
    getSensorsUnderAlerto();
});

const editItem = (value) => {
    sensor_under_alerto.value = value;
    showModalForm(true);
};

const deleteItem = async (value) => {
    await destorySensorUnderAlerto(value.id);
};

const reloadSensorsUnderAlerto = async () => {
    await getSensorsUnderAlerto();
    sensor_under_alerto.value = {};
};
</script>

<template>
    <v-row class="p-2">
        <h5 class="fw-bold p-3">List of Sensors</h5>
        <v-spacer></v-spacer>
        <v-btn color="primary" @click="showModalForm(true)" class="m-3">
            New Sensor
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
                :items="sensors_under_alerto"
                :search="query.search"
                class="elevation-1 p-2"
                :loading="is_loading"
                loading-text="Loading... Please wait"
            >
                <template v-slot:item.actions="{ item }">
                    <v-btn
                        class="me-2"
                        color="success"
                        @click="editItem(item)"
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
                                @click="getSensorsUnderAlerto"
                            >
                            </v-pagination>
                        </div>
                    </div>
                </template>
            </v-data-table>
        </div>
    </v-card>

    <!-- Sensor Form Modal -->
    <SensorsUnderAlertoForm
        :value="show_form_modal"
        :sensor_under_alerto="sensor_under_alerto"
        @input="showModalForm"
        @reloadSensorsUnderAlerto="reloadSensorsUnderAlerto"
    />
</template>

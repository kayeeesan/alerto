<script setup>
import { ref, onMounted, computed } from "vue";
import SensorsUnderAlertoForm from "../../components/settings/SensorUnderAlerto/Form.vue";
import useSensorsUnderAlerto from "../../composables/sensorsUnderAlerto";
import Store from "@/store";

const { sensors_under_alerto, pagination, query, is_loading, getSensorsUnderAlerto, destorySensorUnderAlerto } = useSensorsUnderAlerto();

const sensor_under_alerto = ref({});
const action_type = ref('');
const show_form_modal = ref(false);

const user = Store.state.auth.user;
const isAdmin = computed(() => user?.roles?.some(role => role.slug === 'administrator'));
const userRiverId = computed(() => user?.river?.id);

const headers = [
    { title: "Sensor", key: "name", width: "15%" },
    { title: "Sensor ID", key: "id", width: "10%" },
    { title: "River", key: "river.name", width: "12%" },
    { title: "Municipality", key: "municipality.name", width: "12%" },
    { title: "Longitude", key: "long", width: "8%" },
    { title: "Latitude", key: "lat", width: "8%" },
    { title: "Type", key: "sensor_type", width: "8%" },
    { title: "Status", key: "status", width: "8%" },
    { title: "Actions", key: "actions", sortable: false, align: "end", width: "7%" },
];

const showModalForm = (val) => {
    show_form_modal.value = val;
    sensor_under_alerto.value = {};
};

onMounted(() => {
    getSensorsUnderAlerto();
});

const editItem = (value, action) => {
    sensor_under_alerto.value = value;
    action_type.value = action;
    show_form_modal.value = true;
};

const deleteItem = async (value) => {
    await destorySensorUnderAlerto(value.id); 
};

const reloadSensorsUnderAlerto = async () => {
    await getSensorsUnderAlerto();
    sensor_under_alerto.value = {};
};

const filterSensors = computed(() => {
    if (isAdmin.value) {
        return sensors_under_alerto.value;
    }

    else if (!isAdmin.value && userRiverId.value) {
        return sensors_under_alerto.value.filter(sensor => sensor.river_id === userRiverId.value);
    }
})
</script>

<template>
    <v-container fluid class="pa-6">
        <v-row class="mb-4" align="center">
            <v-col cols="12" md="6">
                <h2 class="text-h5 font-weight-bold">Sensors Under Alert</h2>
                <v-breadcrumbs :items="[{ title: 'Settings', disabled: true }, { title: 'Sensors' }]" class="pa-0"></v-breadcrumbs>
            </v-col>
            <v-col cols="12" md="6" class="text-md-right">
                <v-btn 
                    color="primary" 
                    @click="showModalForm(true)" 
                    prepend-icon="mdi-plus"
                    class="text-capitalize"
                >
                    Add New Sensor
                </v-btn>
            </v-col>
        </v-row>

        <v-card elevation="1" rounded="lg">
            <v-card-title class="d-flex align-center ">
                <v-text-field
                    v-model="query.search"
                    append-inner-icon="mdi-magnify"
                    label="Search sensors..."
                    single-line
                    hide-details
                    density="comfortable"
                    variant="outlined"
                    class="mr-4 "
                    style="max-width: 400px;"
                ></v-text-field>
                <v-spacer></v-spacer>
                <v-btn
                    variant="text"
                    icon="mdi-refresh"
                    @click="reloadSensorsUnderAlerto"
                    title="Refresh"
                ></v-btn>
            </v-card-title>

            <v-divider></v-divider>

            <div class="horizontal-scroll">
                <v-data-table 
                    :headers="headers" 
                    :items="filterSensors"
                    :search="query.search"
                    :loading="is_loading"
                    loading-text="Loading sensor data..."
                    class="elevation-0"
                    :items-per-page="pagination.per_page"
                    :page="query.page"
                >
                    <template v-slot:item.name="{ item }">
                        <span class="font-weight-medium">{{ item.name }}</span>
                    </template>

                    <template v-slot:item.river.name="{ item }">
                        <span>{{ item.river?.name || '-' }}</span>
                    </template>

                    <template v-slot:item.municipality.name="{ item }">
                        <span>{{ item.municipality?.name || '-' }}</span>
                    </template>

                    <template v-slot:item.municipality.province.name="{ item }">
                        <span>{{ item.municipality?.province?.name || '-' }}</span>
                    </template>

                    <template v-slot:item.actions="{ item }">
                        <div class="d-flex justify-end">
                            <v-btn
                                variant="text"
                                color="primary"
                                icon="mdi-pencil"
                                size="small"
                                @click="editItem(item, 'Update')"
                                class="mr-1"
                                title="Edit"
                            ></v-btn>
                            <v-btn
                                variant="text"
                                color="error"
                                icon="mdi-delete"
                                size="small"
                                @click="deleteItem(item)"
                                title="Delete"
                            ></v-btn>
                        </div>
                    </template>

                    <template v-slot:bottom>
                        <div class="d-flex flex-column flex-md-row justify-space-between align-center pa-4">
                            <div class="text-caption text-medium-emphasis mb-2 mb-md-0">
                                Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} entries
                            </div>
                            <v-pagination
                                v-model="query.page"
                                :length="pagination.last_page"
                                :total-visible="5"
                                density="comfortable"
                                @update:model-value="getSensorsUnderAlerto"
                            ></v-pagination>
                        </div>
                    </template>
                </v-data-table>
            </div>
        </v-card>

        <SensorsUnderAlertoForm
            :value="show_form_modal"
            :sensor_under_alerto="sensor_under_alerto"
            :action_type="action_type"
            @input="showModalForm"
            @reloadSensorsUnderAlerto="reloadSensorsUnderAlerto"
        />
    </v-container>
</template>

<style scoped>
.v-card {
    border-radius: 8px;
}
.v-data-table {
    border-radius: 8px;
}
.horizontal-scroll {
    overflow-x: auto;
    max-width: 100%;
}
</style>
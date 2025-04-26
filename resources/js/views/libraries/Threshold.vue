<script setup>
import { ref, onMounted } from "vue";
import ThresholdForm from "../../components/settings/thresholds/Form.vue";
import useThresholds from "../../composables/threshold";

const { thresholds, pagination, query, is_loading, getThresholds, destoryThreshold } = useThresholds();

const threshold = ref({});
const action_type = ref('');
const show_form_modal = ref(false);

const headers = [
    { title: "River", key: "sensor.river.name", width: "12%" },
    { title: "Sensor Name", key: "sensor.name", width: "12%" },
    { title: "Sensor ID", key: "sensor.id", width: "8%" },
    { title: "Baseline", key: "baseline", width: "8%" },
    { title: "60%", key: "sixty_percent", width: "7%" },
    { title: "80%", key: "eighty_percent", width: "7%" },
    { title: "100%", key: "one_hundred_percent", width: "7%" },
    { title: "Municipality", key: "sensor.municipality.name", width: "12%" },
    { title: "XS Date", key: "xs_date", width: "10%" },
    { title: "Water Level", key: "water_level", width: "8%" },
    { title: "Actions", key: "actions", sortable: false, align: "end", width: "9%" },
];

const showModalForm = (val) => {
    show_form_modal.value = val;
    threshold.value = {};
};

onMounted(() => {
    getThresholds();
});

const editItem = (value, action) => {
    threshold.value = value;
    action_type.value = action;
    show_form_modal.value = true;
};

const deleteItem = async (value) => {
    await destoryThreshold(value.id); // Swal handled in composable
};

const reloadThresholds = async () => {
    await getThresholds();
    threshold.value = {};
};
</script>

<template>
    <v-container fluid class="pa-6">
        <v-row class="mb-4" align="center">
            <v-col cols="12" md="6">
                <h2 class="text-h5 font-weight-bold">Thresholds Management</h2>
                <v-breadcrumbs :items="[{ title: 'Settings', disabled: true }, { title: 'Thresholds' }]" class="pa-0"></v-breadcrumbs>
            </v-col>
            <v-col cols="12" md="6" class="text-md-right">
                <v-btn 
                    color="primary" 
                    @click="showModalForm(true)" 
                    prepend-icon="mdi-plus"
                    class="text-capitalize"
                >
                    Add New Threshold
                </v-btn>
            </v-col>
        </v-row>

        <v-card elevation="1" rounded="lg">
            <v-card-title class="d-flex align-center ">
                <v-text-field
                    v-model="query.search"
                    append-inner-icon="mdi-magnify"
                    label="Search thresholds..."
                    single-line
                    hide-details
                    density="comfortable"
                    variant="outlined"
                    class="mr-4"
                    style="max-width: 400px;"
                ></v-text-field>
                <v-spacer></v-spacer>
                <v-btn
                    variant="text"
                    icon="mdi-refresh"
                    @click="reloadThresholds"
                    title="Refresh"
                ></v-btn>
            </v-card-title>

            <v-divider></v-divider>

            <div class="horizontal-scroll">
                <v-data-table 
                    :headers="headers" 
                    :items="thresholds"
                    :search="query.search"
                    :loading="is_loading"
                    loading-text="Loading threshold data..."
                    class="elevation-0"
                    :items-per-page="pagination.per_page"
                    :page="query.page"
                    @update:page="getThresholds"
                >
                    <template v-slot:item.sensor.river.name="{ item }">
                        <span>{{ item.sensor?.river?.name || '-' }}</span>
                    </template>

                    <template v-slot:item.sensor.name="{ item }">
                        <span>{{ item.sensor?.name || '-' }}</span>
                    </template>

                    <template v-slot:item.sensor.id="{ item }">
                        <span>{{ item.sensor?.id || '-' }}</span>
                    </template>

                    <template v-slot:item.sensor.municipality.name="{ item }">
                        <span>{{ item.sensor?.municipality?.name || '-' }}</span>
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
                                @update:model-value="getThresholds"
                            ></v-pagination>
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
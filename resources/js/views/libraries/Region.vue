<script setup>
import { ref, onMounted } from "vue";
import RegionForm from "../../components/settings/regions/Form.vue";
import useRegions from "../../composables/region";

const { regions, pagination, query, is_loading, getRegions, destoryRegion } = useRegions();

const region = ref({});
const action_type = ref('');
const show_form_modal = ref(false);

const headers = [
    { title: "Name", key: "name", width: "80%" },
    { title: "Actions", key: "actions", sortable: false, align: "end", width: "20%" },
];

const showModalForm = (val) => {
    show_form_modal.value = val;
    region.value = {};
};

onMounted(() => {
    getRegions();
});

const editItem = (value, action) => {
    region.value = value;
    action_type.value = action;
    show_form_modal.value = true;
};

const deleteItem = async (value) => {
    await destoryRegion(value.id); // Swal confirmation will be handled in your composable
};

const reloadRegions = async () => {
    await getRegions();
    region.value = {};
};
</script>

<template>
    <v-container fluid class="pa-6">
        <v-row class="mb-4" align="center">
            <v-col cols="12" md="6">
                <h2 class="text-h5 font-weight-bold">Regions Management</h2>
                <v-breadcrumbs :items="[{ title: 'Settings', disabled: true }, { title: 'Regions' }]" class="pa-0"></v-breadcrumbs>
            </v-col>
            <v-col cols="12" md="6" class="text-md-right">
                <v-btn 
                    color="primary" 
                    @click="showModalForm(true)" 
                    prepend-icon="mdi-plus"
                    class="text-capitalize"
                >
                    Add New Region
                </v-btn>
            </v-col>
        </v-row>

        <v-card elevation="1" rounded="lg">
            <v-card-title class="d-flex align-center ">
                <v-text-field
                    v-model="query.search"
                    append-inner-icon="mdi-magnify"
                    label="Search regions..."
                    single-line
                    hide-details
                    density="comfortable"
                    variant="outlined"
                    class="mr-4 "
                ></v-text-field>
                <v-spacer></v-spacer>
                <v-btn
                    variant="text"
                    icon="mdi-refresh"
                    @click="reloadRegions"
                    title="Refresh"
                ></v-btn>
            </v-card-title>

            <v-divider></v-divider>

            <v-data-table 
                :headers="headers" 
                :items="regions"
                :search="query.search"
                :loading="is_loading"
                loading-text="Loading regions..."
                class="elevation-0"
                :items-per-page="pagination.per_page"
                :page="query.page"
                @update:page="getRegions"
            >
                <template v-slot:item.name="{ item }">
                    <span class="font-weight-medium">{{ item.name }}</span>
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
                            @update:model-value="getRegions"
                        ></v-pagination>
                    </div>
                </template>
            </v-data-table>
        </v-card>

        <region-form
            :value="show_form_modal"
            :region="region"
            :action_type="action_type"
            @input="showModalForm"
            @reloadRegions="reloadRegions"
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
</style>
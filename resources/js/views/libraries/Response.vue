<script setup>
import { ref, onMounted } from "vue";
import ResponseForm from "../../components/settings/responses/Form.vue";
import useResponses from "../../composables/response";

const { responses, pagination, query, is_loading, getResponses, destoryResponse } = useResponses();

const response = ref({});
const action_type = ref('');
const show_form_modal = ref(false);

const headers = [
    { title: "Color Warning", key: "color", width: "20%" },
    { title: "LGUs Actions", key: "action", width: "35%" },
    { title: "Codes", key: "code", width: "25%" },
    { title: "Actions", key: "actions", sortable: false, align: "end", width: "20%" },
];

const showModalForm = (val) => {
    show_form_modal.value = val;
    response.value = {};
};

onMounted(() => {
    getResponses();
});

const editItem = (value, action) => {
    response.value = value;
    action_type.value = action;
    show_form_modal.value = true;
};

const deleteItem = async (value) => {
    await destoryResponse(value.id); // Swal handled in composable
};

const reloadResponses = async () => {
    await getResponses();
    response.value = {};
};
</script>

<template>
    <v-container fluid class="pa-6">
        <v-row class="mb-4" align="center">
            <v-col cols="12" md="6">
                <h2 class="text-h5 font-weight-bold">Response Management</h2>
                <v-breadcrumbs :items="[{ title: 'Settings', disabled: true }, { title: 'Responses' }]" class="pa-0"></v-breadcrumbs>
            </v-col>
            <v-col cols="12" md="6" class="text-md-right">
                <v-btn 
                    color="primary" 
                    @click="showModalForm(true)" 
                    prepend-icon="mdi-plus"
                    class="text-capitalize"
                >
                    Add New Response
                </v-btn>
            </v-col>
        </v-row>

        <v-card elevation="1" rounded="lg">
            <v-card-title class="d-flex align-center">
                <v-text-field
                    v-model="query.search"
                    append-inner-icon="mdi-magnify"
                    label="Search responses..."
                    single-line
                    hide-details
                    density="comfortable"
                    variant="outlined"
                    class="mr-4"
                ></v-text-field>
                <v-spacer></v-spacer>
                <v-btn
                    variant="text"
                    icon="mdi-refresh"
                    @click="reloadResponses"
                    title="Refresh"
                ></v-btn>
            </v-card-title>

            <v-divider></v-divider>

            <v-data-table 
                :headers="headers" 
                :items="responses"
                :search="query.search"
                :loading="is_loading"
                loading-text="Loading response data..."
                class="elevation-0"
                :items-per-page="pagination.per_page"
                :page="query.page"
                @update:page="getResponses"
            >
                <template v-slot:item.color="{ item }">
                    <!-- <v-chip 
                        :color="item.color" 
                        dark
                        class="text-capitalize"
                    >
                        {{ item.color }}
                    </v-chip> -->
                    <v-btn
                class="ma-2"
                :color="item.color"
                icon="mdi-alert-circle-outline"
            ></v-btn>
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
                            @update:model-value="getResponses"
                        ></v-pagination>
                    </div>
                </template>
            </v-data-table>
        </v-card>

        <response-form
            :value="show_form_modal"
            :response="response"
            :action_type="action_type"
            @input="showModalForm"
            @reloadResponses="reloadResponses"
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
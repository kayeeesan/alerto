<script setup>
import { ref, onMounted, watch, computed} from "vue";
import useAlerts from "../../composables/alerts";
import AlertForm from "../../components/userSettings/Form.vue"
import store from "@/store";

const { respondedAlerts, pagination, query, is_loading, destroyAlert, updateAlert, getAlerts, respondedPagination, respondedPage,  } = useAlerts();

const respondedAlert = ref([]);
const show_form_modal = ref(false);

const headers = [
    { title: "Alert Level", key: "color", width: "10%" },
    { title: "Details", key: "details", width: "20%" },
    { title: "Location", key: "threshold.sensorable.municipality.name", width: "15%" },
    { title: "Action Needed", key: "response.action", width: "20%" },
    { title: "River", key: "threshold.sensorable.river.name", width: "15%" },
    { title: "Responder", key: "user.username", width: "10%" },
    { title: "Status", key: "status", width: "10%" },
    { title: "Actions", key: "actions", sortable: false, align: "end", width: "10%" }
];

const getColor = (alert) => {
    if (alert.details.includes("critical")) return 'red';
    if (alert.details.includes("alert")) return 'orange';
    if (alert.details.includes("monitor")) return 'yellow';
    return 'grey';
};

const statusColor = (status) => {
  return {
    responded: 'error',
    responded: 'success', 
    responded: 'warning'
  }[status] || 'grey';
};

const reloadAlerts = async () => {
    await getAlerts( {query:query.search });
    respondedAlert.value = {};
};


const deleteItem = async (value) => {
    await destroyAlert(value.id);
    reloadAlerts();
};

const showModalForm = (val) => {
  show_form_modal.value = val;
  alert.value = {};
};

const editItem = (value) => {
    respondedAlert.value = value;
    show_form_modal.value = true;
}

const save = async (formData) => {
    try {
        await updateAlert(formData);
        show_form_modal.value = false;
        reloadAlerts();
    } catch (error) {
    console.error("Failed to update alert:", error);
  }
}

onMounted(async() => {
    await getAlerts();
});
</script>

<template>
    <v-container fluid class="pa-6">
        <v-row class="mb-4" align="center">
            <v-col cols="12" md="6">
                <h2 class="text-h5 font-weight-bold">Respondeds </h2>
                <v-breadcrumbs :items="[{ title: 'Settings', disabled: true }, { title: 'respondeds' }]" class="pa-0"></v-breadcrumbs>
            </v-col>
        </v-row>

        <v-card elevation="1" rounded="lg">
            <v-card-title class="d-flex align-center ">
                <v-text-field
                    v-model="query.search"
                    append-inner-icon="mdi-magnify"
                    label="Search respondeds..."
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
                    @click="reloadAlerts"
                    title="Refresh"
                ></v-btn>
            </v-card-title>

            <v-data-table
            :headers="headers"
            :items="respondedAlerts"
            :search="query.search"
            :loading="is_loading"
            loading-text="Loading alert data..."
            class="elevation-0"
            :items-per-page="respondedPagination.per_page"
            :page="respondedPage"
          
            >

            <template v-slot:item.color="{ item }">
                <v-btn
                class="ma-2"
                :color="getColor(item)"
                icon="mdi-alert-circle-outline"
                ></v-btn>
            </template>

            <template #item.status="{ item }">
                <v-chip 
                :color="statusColor(item.status)" 
                dark>{{ item.status }}
            </v-chip>
            </template>

               <template v-slot:item.actions="{ item }">
                <div class="d-flex justify-end">
                    <v-btn
                    variant="text"
                    color="primary"
                    icon="mdi-pencil"
                    size="small"
                    @click="editItem(item)"
                    class="mr-1"
                    :title="tab === 'responded' ? 'Respond' : 'Update'"
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
                Showing {{ respondedPagination.from }} to {{ respondedPagination.to }} of {{ respondedPagination.total }} entries
                </div>
                <v-pagination
                    v-model="respondedPage"
                    :length="respondedPagination.last_page"
                    :total-visible="5"
                    density="comfortable"
                    @update:model-value="getAlerts"
                    />

            </div>
            </template>
        </v-data-table>
        </v-card>

        <AlertForm
        :value="show_form_modal"
        :alert="respondedAlert"
        @input="showModalForm"
        @reloadAlerts="reloadAlerts"
        @save="save" 
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
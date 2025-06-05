<script setup>
import { ref, onMounted, watch, computed} from "vue";
import useAlerts from "../../composables/alerts";
import store from "@/store";

const { expiredAlerts, respondedAlerts, pagination, query, is_loading, destroyAlert, updateAlert, getAlerts, expiredPagination, expiredPage,  } = useAlerts();

const user = store.state.auth.user;
const isAdmin = computed(() => user?.roles?.some(role => role.slug === 'administrator'));
const userRiverId = computed(() => user?.river?.id);

const headers = [
    { title: "Alert Level", key: "color", width: "10%" },
    { title: "Details", key: "details", width: "20%" },
    { title: "Location", key: "threshold.sensorable.municipality.name", width: "15%" },
    { title: "River", key: "threshold.sensorable.river.name", width: "15%" },
    { title: "Status", key: "status", width: "10%" },
    { title: "Assigned", key: "assigned_user_names", width: "10%" },
    { title: "Time/date", key: "created_at", width: "10%" },
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
    pending: 'warning',
    responded: 'success', 
    expired: 'error'
  }[status] || 'grey';
};

const formatDateTime = (isoString) => {
    if (!isoString) return '';

    const date = new Date(isoString);
    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    }).format(date);
};

const reloadAlerts = async () => {
    await getAlerts( {query:query.search });
};

const deleteItem = async (value) => {
    await destroyAlert(value.id);
    reloadAlerts();
};


const filteredAlerts = computed(() => {
    if (isAdmin.value){
        return expiredAlerts.value;
    }

    return expiredAlerts.value.filter(alert =>
        alert?.threshold?.sensorable?.river?.name === user?.river?.name);
});


onMounted(async() => {
    await getAlerts();
});
</script>

<template>
    <v-container fluid class="pa-6">
        <v-row class="mb-4" align="center">
            <v-col cols="12" md="6">
                <h2 class="text-h5 font-weight-bold">Expired </h2>
                <v-breadcrumbs :items="[{ title: 'Settings', disabled: true }, { title: 'Expired' }]" class="pa-0"></v-breadcrumbs>
            </v-col>
        </v-row>

        <v-card elevation="1" rounded="lg">
            <v-card-title class="d-flex align-center ">
                <v-text-field
                    v-model="query.search"
                    append-inner-icon="mdi-magnify"
                    label="Search Expired..."
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
            :items="filteredAlerts"
            :search="query.search"
            :loading="is_loading"
            loading-text="Loading alert data..."
            class="elevation-0"
            :items-per-page="expiredPagination.per_page"
            :page="expiredPage"
          
            >
            <template #item.created_at="{ item }">
                {{ formatDateTime(item.created_at) }}
            </template>

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

            <template #item.actions="{ item }">
                 <v-btn
                    variant="text"
                    color="error"
                    icon="mdi-delete"
                    size="small"
                    @click="deleteItem(item)"
                    title="Delete"
                    ></v-btn>
            </template>

            <template v-slot:bottom>
            <div class="d-flex flex-column flex-md-row justify-space-between align-center pa-4">
                <div class="text-caption text-medium-emphasis mb-2 mb-md-0">
                Showing {{ expiredPagination.from }} to {{ expiredPagination.to }} of {{ expiredPagination.total }} entries
                </div>
                <v-pagination
                    v-model="expiredPage"
                    :length="expiredPagination.last_page"
                    :total-visible="5"
                    density="comfortable"
                    @update:model-value="getAlerts"
                    />

            </div>
            </template>
        </v-data-table>

        </v-card>
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
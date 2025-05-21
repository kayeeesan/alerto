<script setup>
import { ref, onMounted, watch, computed } from "vue";
import useAlerts from "../../composables/alerts";
import AlertForm from "../../components/userSettings/Form.vue";
import store from "@/store";

const { pendingAlerts, respondedAlerts, expiredAlerts, pagination, query, is_loading, destroyAlert, updateAlert, getAlerts } = useAlerts();


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
    pending: 'error',
    responded: 'success', 
    expired: 'warning'
  }[status] || 'grey';
};

onMounted(async() => {
    console.log('Fetching alerts...');
    await getAlerts();
    console.log('Pending Alerts:', pendingAlerts.value);
    console.log('Responded Alerts:', respondedAlerts.value);
    console.log('Expired Alerts:', expiredAlerts.value);
});
</script>

<template>
    <v-container fluid class="pa-6">
        <v-row class="mb-4" align="center">
            <v-col cols="12" md="6">
                <h2 class="text-h5 font-weight-bold">Pendings </h2>
                <v-breadcrumbs :items="[{ title: 'Settings', disabled: true }, { title: 'Pendings' }]" class="pa-0"></v-breadcrumbs>
            </v-col>
        </v-row>

        <v-card elevation="1" rounded="lg">
            <v-card-title class="d-flex align-center ">
                <v-text-field
                    v-model="query.search"
                    append-inner-icon="mdi-magnify"
                    label="Search pendings..."
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
                    @click=""
                    title="Refresh"
                ></v-btn>
            </v-card-title>

            <v-data-table
            :headers="headers"
            :items="pendingAlerts"
            :search="query.search"
            :loading="is_loading"
            loading-text="Loading alert data..."
            class="elevation-0"
            :items-per-page="pagination.per_page || 10"
            :page="pagination.current_page"
            @update:page="(page) => {
                query.page = page;
                getAlerts();
            }"
            >
            <template #item.color="{ item }">
                <v-chip :color="getColor(item)" dark>{{ item.details }}</v-chip>
            </template>

            <template #item.status="{ item }">
                <v-chip 
                :color="statusColor(item.status)" 
                dark>{{ item.status }}
            </v-chip>
            </template>

            <template #item.actions="{ item }">
                <v-btn 
                icon 
                @click="" 
                title="Delete">
                <v-icon color="red">mdi-delete
                </v-icon>
                </v-btn>
                <v-btn 
                icon 
                @click="" 
                title="Edit">
                <v-icon color="blue">mdi-pencil</v-icon>
                </v-btn>
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
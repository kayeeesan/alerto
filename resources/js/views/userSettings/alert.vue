<script setup>
import { ref, onMounted, watch, computed } from "vue";
import useAlerts from "../../composables/alerts";
import AlertForm from "../../components/userSettings/Form.vue";

const { alerts, pagination, query, is_loading, destroyAlert, updateAlert, getAlerts } = useAlerts();

const alert = ref({});
const show_form_modal = ref(false);
const tab = ref("pending");

const alert_tabs = ref([
  { id: 1, name: "Pending", value: "pending", count: 0 },
  { id: 2, name: "Responded", value: "responded", count: 0 },
  { id: 3, name: "Expired", value: "expired", count: 0 }
]);

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

const filteredHeaders = computed(() => {
  return tab.value === "pending" 
    ? headers.filter(header => !['response.action', 'user.username'].includes(header.key))
    : headers;
});

const filteredAlerts = computed(() => {
  const filtered = alerts.value.filter(a => a.status === tab.value);
  return query.search 
    ? filtered.filter(a => 
        Object.values(a).some(val => 
          val?.toString().toLowerCase().includes(query.search.toLowerCase())
        )
      )
    : filtered;
});

watch([() => tab.value, () => query.search], async () => {
  await getAlerts({ status: tab.value, query: query.search });
});

const reloadAlerts = async () => {
  await getAlerts({ status: tab.value, query: query.search });
  alert.value = {};
};

const showModalForm = (val) => {
  show_form_modal.value = val;
  alert.value = {};
};

const editItem = (value) => {
  alert.value = value;
  show_form_modal.value = true;
};

const deleteItem = async (value) => {
  await destroyAlert(value.id);
  reloadAlerts();
};

const save = async (formData) => {
  try {
    await updateAlert(formData);
    show_form_modal.value = false;
    reloadAlerts();
  } catch (error) {
    console.error("Failed to update alert:", error);
  }
};

const statusColor = (status) => {
  return {
    pending: 'error',
    responded: 'success', 
    expired: 'warning'
  }[status] || 'grey';
};

watch(
  () => alerts.value,
  (newAlerts, oldAlerts) => {
    alert_tabs.value.forEach(tab => {
      tab.count = newAlerts.filter(a => a.status === tab.value).length;
    });
    
    if (oldAlerts && newAlerts.some(a => 
      a.status === "expired" && 
      !oldAlerts.some(o => o.id === a.id && o.status === "expired")
    )) {
      tab.value = "expired";
    }
  },
  { deep: true, immediate: true }
);

onMounted(() => {
  getAlerts({ status: tab.value });
  setInterval(() => getAlerts({ status: tab.value }), 5000);
});
</script>

<template>
  <v-container fluid class="pa-6">
    <v-row class="mb-4" align="center">
      <v-col cols="12">
        <h2 class="text-h5 font-weight-bold">Alerts Management</h2>
        <v-breadcrumbs :items="[{ title: 'Monitoring', disabled: true }, { title: 'Alerts' }]" class="pa-0"></v-breadcrumbs>
      </v-col>
    </v-row>

    <v-card elevation="1" rounded="lg">
      <v-tabs v-model="tab" grow>
        <v-tab v-for="tabItem in alert_tabs" :key="tabItem.id" :value="tabItem.value">
          <v-badge v-if="tabItem.count > 0" :content="tabItem.count" color="red" inline>
            {{ tabItem.name }}
          </v-badge>
          <span v-else>{{ tabItem.name }}</span>
        </v-tab>
      </v-tabs>

      <v-divider></v-divider>

      <v-card-title class="d-flex align-center">
        <v-text-field
          v-model="query.search"
          append-inner-icon="mdi-magnify"
          label="Search alerts..."
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
          @click="reloadAlerts"
          title="Refresh"
        ></v-btn>
      </v-card-title>

      <v-data-table 
        :headers="filteredHeaders"
        :items="filteredAlerts"
        :search="query.search"
        :loading="is_loading"
        loading-text="Loading alert data..."
        class="elevation-0"
        :items-per-page="pagination.per_page"
        :page="query.page"
        @update:page="getAlerts"
      >
          <template v-slot:item.color="{ item }">
            <v-btn
              class="ma-2"
              :color="getColor(item)"
              icon="mdi-alert-circle-outline"
            ></v-btn>
          </template>

        <template v-slot:item.status="{ item }">
          <v-chip :color="statusColor(item.status)" variant="flat" class="text-capitalize">
            {{ item.status }}
          </v-chip>
        </template>

        <template v-slot:item.actions="{ item }">
          <div class="d-flex justify-end">
            <v-btn
              v-if="tab === 'pending' || tab === 'responded'"
              variant="text"
              color="primary"
              icon="mdi-pencil"
              size="small"
              @click="editItem(item)"
              class="mr-1"
              :title="tab === 'pending' ? 'Respond' : 'Update'"
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
              @update:model-value="getAlerts"
            ></v-pagination>
          </div>
        </template>
      </v-data-table>
    </v-card>

    <AlertForm
      :value="show_form_modal"
      :alert="alert"
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
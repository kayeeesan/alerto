<script setup>
import { ref, onMounted, watch, computed } from "vue";
import useAlerts from "../../composables/alerts"; // Importing useAlerts composable
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
  { title: "Color", key: "color" },
  { title: "Details", key: "details" },
  { title: "Sensor Location", key: "threshold.sensor.municipality.name" },
  { title: "Action Needed", key: "response.action" },
  { title: "River", key: "threshold.sensor.river.name" },
  { title: "Responder", key: "user.username" },
  { title: "Status", key: "status" },
  { title: "Actions", key: "actions", sortable: false }
];

const getColor = (alert) => {
  if (alert.details.includes("critical")) {
    return 'red'; // Critical water level
  } else if (alert.details.includes("alert")) {
    return 'orange'; // Alert water level
  } else if (alert.details.includes("monitor")) {
    return 'yellow'; // Monitor water level
  }
  return 'gray'; // Default color if no match
};

// Calculate filtered headers based on active tab
const filteredHeaders = computed(() => {
  if (tab.value === "pending") {
    return headers.filter(header => header.key !== 'response.action' && header.key !== 'user.username');
  }
  return headers;
});

// Filtered alerts based on search and active tab
const filteredAlerts = computed(() => {
  let filtered = [];

  // Filter based on the current tab
  if (tab.value === "pending") {
    filtered = alerts.value.filter(alert => alert.status === "pending");
  } else if (tab.value === "responded") {
    filtered = alerts.value.filter(alert => alert.status === "responded");
  } else if (tab.value === "expired") {
    filtered = alerts.value.filter(alert => alert.status === "expired");
  }

  // Apply search filter only if the search query is not empty
  if (query.search) {
    filtered = filtered.filter(alert => {
      return Object.values(alert).some(val => 
        val.toString().toLowerCase().includes(query.search.toLowerCase())
      );
    });
  }

  return filtered;
});

// Watcher to reload alerts when the tab is changed or search query changes
watch([() => tab.value, () => query.search], async () => {
  // Fetch alerts when the tab or search query changes
  await getAlerts({ status: tab.value, query: query.search });
});

// Fetch the initial alerts when mounted
onMounted(() => {
  getAlerts({ status: tab.value });
});

// Reload alerts function
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
  show_form_modal.value = value;
};

const deleteItem = async (value) => {
  await destroyAlert(value.id);
  reloadAlerts();  // Re-fetch alerts after delete
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

// Update alert tabs counts when alerts change
watch(
  () => alerts.value,
  (newAlerts) => {
    alert_tabs.value.forEach((tabItem) => {
      tabItem.count = newAlerts.filter(alert => alert.status === tabItem.value).length;
    });
  },
  { immediate: true }
);
</script>

<template>
  <v-row class="p-2">
    <h5 class="fw-bold p-3">List of Alerts</h5>
  </v-row>

  <!-- Tabs for Pending, Responded, and Expired alerts -->
  <v-tabs v-model="tab" vertical>
    <v-tab
      v-for="tabItem in alert_tabs"
      :key="tabItem.id"
      :value="tabItem.value"
    >
      {{ tabItem.name }} ({{ tabItem.count }})
    </v-tab>
  </v-tabs>

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
        :headers="filteredHeaders"
        :items="filteredAlerts"
        :search="query.search"
        class="elevation-1 p-2"
        :loading="is_loading"
        loading-text="Loading... Please wait"
      >
        <template v-slot:item.color="{ item }">
          <v-btn
            class="ma-2"
            :color="getColor(item)"
            icon="mdi-alert-circle-outline"
          ></v-btn>
        </template>

        <template v-slot:item.actions="{ item }">
          <v-btn
            class="me-2"
            color="success"
            @click="editItem(item, 'Update')"
            variant="tonal"
            size="small"
          >
            <v-icon size="small">mdi-pencil</v-icon> Respond
          </v-btn>
          <v-btn
            color="error"
            @click="deleteItem(item)"
            variant="tonal"
            size="small"
          >
            <v-icon>mdi-delete</v-icon> Delete
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
                circle
                @click="getAlerts"
              >
              </v-pagination>
            </div>
          </div>
        </template>
      </v-data-table>
    </div>
  </v-card>

  <!-- Alert form modal -->
  <AlertForm
    :value="show_form_modal"
    :alert="alert"
    @input="showModalForm"
    @reloadAlerts="reloadAlerts"
    @save="save" 
  />
</template>

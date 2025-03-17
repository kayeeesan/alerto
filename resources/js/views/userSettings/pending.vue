<script setup>
import { ref, onMounted } from "vue";
import useAlerts from "../../composables/alerts";

const { alerts, pagination, query, is_loading, getAlerts, destoryAlert} = useAlerts();

const alert = ref({});
const show_form_modal = ref(false);

const headers = [
    { title: "color", key: ""},
    { title: "details", key: ""},
    { title: "sensor location", key: ""},
    { title: "action needed", key: ""},
    { title: "river", key: ""},// color, alert details, river, date_updated, responder, response in responded
    { title: "status", key: "status"}
];

const showModalForm = (val) => {
    show_form_modal.value = val;
    alert.value = {};

};

const reloadAlerts = async () => {
    await getAlerts();
    alert.value = {};
};

onMounted(() => {
    getAlerts();
})
</script>
<template>
   <v-row class="p-2">
        <h5 class="fw-bold p-3">List of pending Alerts {{ alerts }}</h5>
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
                :items="alerts"
                :search="query.search"
                class="elevation-1 p-2"
                :loading="is_loading"
                loading-text="Loading... Please wait"
            >
                <template v-slot:item.actions="{ item }">
                    <v-btn
                        class="me-2"
                        color="success"
                        @click="editItem(item, 'Update')"
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
</template>
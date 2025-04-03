<script setup>
import {ref, onMounted} from "vue";
import useContactMessages from "../../composables/contactMessage";
import MessageForm from "../../components/messages/Form.vue";

const { contact_messages, pagination, query, is_loading, getContactMessages} = useContactMessages();

const contact_message = ref({});
const show_form_modal = ref(false);

const headers = [
    { title: "Email", key: "email"},
    { title: "Contact No.", key: "contact_number"},
    { title: "Name", key: "name"},
    { title: "Actions", key: "actions", sortable: false },
];

onMounted(() => {
    getContactMessages();
});
</script>
<template>
    <v-row class="p-2">
        <h5 class="fw-bold p-3">List of Messages </h5>
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
                :items="contact_messages"
                :search="query.search"
                class="elevation-1 p-2"
                :loading="is_loading"
                loading-text="Loading... Please wait"
            >
            <template v-slot:item.actions="{ item }">
                    <v-btn
                        class="me-2"
                        color="blue"
                        @click="editItem(item, 'Update')"
                        variant="tonal"
                        size="small"
                    >
                        <v-icon size="small"> mdi-eye</v-icon> View
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
                                :length="pagination.last_page"
                                circle
                                @click="getContactMessages"
                            >
                            </v-pagination>
                        </div>
                    </div>
                </template>
        </v-data-table>
        </div>
    </v-card>
</template>
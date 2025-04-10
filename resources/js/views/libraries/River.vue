<script setup>
import { ref, onMounted } from "vue";
import RiverForm from "../../components/settings/rivers/Form.vue";
import useRivers from "../../composables/river";

const { rivers, pagination, query, is_loading, getRivers, destoryRiver } = useRivers();

const river = ref({});
const action_type = ref('');
const show_form_modal = ref(false);

const headers = [
    { title: "Name", key: "name" },
    { title: "River Code", key: "river_code" },
    { title: "Municipality", key: "municipality.name" },
    { title: "Actions", key: "actions", sortable: false },
];

const showModalForm = (val) => {
    show_form_modal.value = val;
    river.value = {};
};

onMounted(() => {
    getRivers();
});

const editItem = (value, action) => {
    river.value = value;
    action_type.value = action;
    show_form_modal.value = value;
};

const deleteItem = async (value) => {
    await destoryRiver(value.id);
};

const reloadRivers = async () => {
    await getRivers();
    river.value = {};
};
</script>
<template>
    <v-row class="p-2 ml-8">
        <h5 class="fw-bold p-3">List of Rivers</h5>
        <v-spacer></v-spacer>
        <v-btn color="primary" @click="showModalForm(true)" class="m-3">
            New River
        </v-btn>
    </v-row>
    <v-card class="ml-8">
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
                :items="rivers"
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
                                :length="pagination.last_page"
                                circle
                                @click="getRivers"
                            >
                            </v-pagination>
                        </div>
                    </div>
                </template>
            </v-data-table>
        </div>
    </v-card>

    <river-form
        :value="show_form_modal"
        :river="river"
        :action_type="action_type"
        @input="showModalForm"
        @reloadRivers="reloadRivers"
    />
</template>

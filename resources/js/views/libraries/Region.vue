<script setup>
import { ref, onMounted } from "vue";
import RegionForm from "../../components/settings/regions/Form.vue";
import useRegions from "../../composables/region";

const { regions, pagination, query, is_loading, getRegions, destoryRegion } = useRegions();

const region = ref({});
const show_form_modal = ref(false);

const headers = [
    { title: "Name", key: "name" },
    { title: "Actions", key: "actions", sortable: false },
];

const showModalForm = (val) => {
    show_form_modal.value = val;
};

onMounted(() => {
    getRegions();
});

const editItem = (value) => {
    region.value = value;
    showModalForm(true);
};

const deleteItem = async (value) => {
    await destoryRegion(value.id);
};

const reloadRegions = async () => {
    await getRegions();
    region.value = {};
};
</script>
<template>
    <v-row class="p-2">
        <h5 class="fw-bold p-3">List of Regions</h5>
        <v-spacer></v-spacer>
        <v-btn color="primary" @click="showModalForm(true)" class="m-3">
            New Region
        </v-btn>
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
                :items="regions"
                :search="query.search"
                class="elevation-1 p-2"
                :loading="is_loading"
                loading-text="Loading... Please wait"
            >
                <template v-slot:item.actions="{ item }">
                    <v-btn
                        class="me-2"
                        color="success"
                        @click="editItem(item)"
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
                                @click="getRegions"
                            >
                            </v-pagination>
                        </div>
                    </div>
                </template>
            </v-data-table>
        </div>
    </v-card>

    <region-form
        :value="show_form_modal"
        :region="region"
        @input="showModalForm"
        @reloadRegions="reloadRegions"
    />
</template>

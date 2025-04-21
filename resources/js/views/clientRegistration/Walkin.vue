<script setup>
import { ref, onMounted, computed } from "vue";
import StaffForm from "../../components/settings/staffs/Form.vue";
import useStaffs from "../../composables/staff";
import StaffModal from "../../components/settings/staffs/Staff.vue";

const { staffs, pagination, query, is_loading, getStaffs, destoryStaff } = useStaffs();

const staff = ref({});
const action_type = ref('');
const show_form_modal = ref(false);
const show_staff_modal = ref(false);

const headers = [
    { title: "Name", key: "username" },
    { title: "contact No.", key: "mobile_number" },
    { title: "Municipality", key: "municipality.name" },
    { title: "River", key: "river.name" },
    { title: "Status", key: "status"},
    { title: "Actions", key: "actions", sortable: false },
];

const showModalForm = (val) => {
    show_form_modal.value = val;
    staff.value = {};
};

const showModalStaff = (val, data = null) => {
    show_staff_modal.value = val;
    if (data) {
        staff.value = data;
    }
}
onMounted(() => {
    getStaffs();
});

const editItem = (value, action) => {
    staff.value = value;
    action_type.value = action;
    show_form_modal.value = value;
};

const deleteItem = async (value) => {
    await destoryStaff(value.id);
};

const reloadStaffs = async () => {
    await getStaffs();
    staff.value = {};
};



const statusColor = (status) => {
    switch (status) {
        case "approved":
            return 'green';
        case "pending":
            return 'grey';
        case "disabled":
            return 'red';
    }
}
</script>
<template>
    <v-row class="p-2 ml-8">
        <h5 class="fw-bold p-3">List of Members </h5>
        <v-spacer></v-spacer>
        <v-btn color="primary" @click="showModalForm(true)" class="m-3">
            New Member
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
                :items="staffs"
                :search="query.search"
                class="elevation-1 p-2"
                :loading="is_loading"
                loading-text="Loading... Please wait"
            >
                <template v-slot:item.status="{ item}">
                        <v-chip :color="statusColor(item.status)" variant="flat" class="pb-1">
                            {{ item.status === 'approved' ? 'Active' : item.status }}
                        </v-chip>
                </template>
                <template v-slot:item.actions="{ item }">
                <v-menu open-on-hover>
                    <template v-slot:activator="{ props}">
                        <v-btn color="#BDBDBD" v-bind="props" size="small">
                            Action
                        </v-btn>
                    </template>
                    <v-list max-width="200px" class="p-2">
                        <div width="100%">
                            <v-btn
                                width="100%"
                                class="me-2 mb-2"
                                color="primary"
                                @click="() => showModalStaff(true, item)"
                                variant="flat"
                                size="small"
                            >
                                <v-icon size="small"> mdi-eye </v-icon> View
                            </v-btn>
                            <v-btn
                                width="100%"
                                class="me-2 mb-2"
                                color="success"
                                @click="editItem(item, 'Update')"
                                variant="flat"
                                size="small"
                            >
                                <v-icon size="small"> mdi-pencil </v-icon> Edit
                            </v-btn>
                            <v-btn
                                width="100%"
                                color="error"
                                @click="deleteItem(item)"
                                variant="flat"
                                size="small"
                            >
                                <v-icon> mdi-delete </v-icon> delete
                            </v-btn>
                        </div>
                    </v-list>
                </v-menu>
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
                                @click="getStaffs"
                            >
                            </v-pagination>
                        </div>
                    </div>
                </template>
            </v-data-table>
        </div>
    </v-card>

    <staff-form
        :value="show_form_modal"
        :staff="staff"
        :action_type="action_type"
        @input="showModalForm"
        @reloadStaffs="reloadStaffs"
    />
    <staff-modal
        :value="show_staff_modal"
        :staff="staff"
        @input="showModalStaff"
        @reloadStaffs="reloadStaffs"
    />
</template>

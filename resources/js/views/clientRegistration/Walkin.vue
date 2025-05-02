<script setup>
import { ref, onMounted } from "vue";
import StaffForm from "../../components/settings/staffs/Form.vue";
import StaffModal from "../../components/settings/staffs/Staff.vue";
import useStaffs from "../../composables/staff";

const { staffs, pagination, query, is_loading, getStaffs, destoryStaff } = useStaffs();

const staff = ref({});
const action_type = ref('');
const show_form_modal = ref(false);
const show_staff_modal = ref(false);

const headers = [
    { title: "Name", key: "full_name", width: "20%" },
    { title: "Email", key: "username", width: "20%" },
    { title: "Contact No.", key: "mobile_number", width: "15%" },
    { title: "Municipality", key: "municipality.name", width: "15%" },
    { title: "River", key: "river.name", width: "15%" },
    { title: "Status", key: "status", width: "10%" }, 
    { title: "Actions", key: "actions", sortable: false, align: "end", width: "25%" },
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
};

onMounted(() => {
    getStaffs();
});

const editItem = (value, action) => {
    staff.value = value;
    action_type.value = action;
    show_form_modal.value = true;
};

const deleteItem = async (value) => {
    await destoryStaff(value.id); // Swal handled in composable
};

const reloadStaffs = async () => {
    await getStaffs();
    staff.value = {};
};

const statusColor = (status) => {
    switch (status) {
        case "approved":
            return 'success';
        case "pending":
            return 'warning';
        case "disabled":
            return 'error';
        default:
            return 'grey';
    }
};
</script>

<template>
    <v-container fluid class="pa-6">
        <v-row class="mb-4" align="center">
            <v-col cols="12" md="6">
                <h2 class="text-h5 font-weight-bold">Staff Members</h2>
                <v-breadcrumbs :items="[{ title: 'Settings', disabled: true }, { title: 'Staff' }]" class="pa-0"></v-breadcrumbs>
            </v-col>
            <v-col cols="12" md="6" class="text-md-right">
                <v-btn 
                    color="primary" 
                    @click="showModalForm(true)" 
                    prepend-icon="mdi-plus"
                    class="text-capitalize"
                >
                    Add New Member
                </v-btn>
            </v-col>
        </v-row>

        <v-card elevation="1" rounded="lg">
            <v-card-title class="d-flex align-center">
                <v-text-field
                    v-model="query.search"
                    append-inner-icon="mdi-magnify"
                    label="Search members..."
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
                    @click="reloadStaffs"
                    title="Refresh"
                ></v-btn>
            </v-card-title>

            <v-divider></v-divider>

            <v-data-table 
                :headers="headers" 
                :items="staffs"
                :search="query.search"
                :loading="is_loading"
                loading-text="Loading staff data..."
                class="elevation-0"
                :items-per-page="pagination.per_page"
                :page="query.page"
                @update:page="getStaffs"
            >
                <template v-slot:item.status="{ item }">
                    <v-chip 
                        :color="statusColor(item.status)" 
                        variant="flat"
                        class="text-capitalize"
                    >
                        {{ item.status === 'approved' ? 'Active' : item.status }}
                    </v-chip>
                </template>

                <template v-slot:item.actions="{ item }">
                    <div class="d-flex justify-end">
                        <v-btn
                            variant="text"
                            color="info"
                            icon="mdi-eye"
                            size="small"
                            @click="() => showModalStaff(true, item)"
                            class="mr-1"
                            title="View"
                        ></v-btn>
                        <v-btn
                            variant="text"
                            color="primary"
                            icon="mdi-pencil"
                            size="small"
                            @click="editItem(item, 'Update')"
                            class="mr-1"
                            title="Edit"
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
                            @update:model-value="getStaffs"
                        ></v-pagination>
                    </div>
                </template>
            </v-data-table>
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
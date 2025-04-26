<script setup>
import { ref, onMounted } from "vue";
import UserForm from "../../components/users/Form.vue";
import UserLog from "../../components/users/Logs.vue";
import useUsers from "../../composables/users.js";
import useLogs from "../../composables/usersLog.js";

const { users, pagination, querySearch, is_loading, getUsers, destoryUser } = useUsers();
const { getUserLogs, user_logs } = useLogs();

const user = ref({});
const show_form_modal = ref(false);
const show_log_modal = ref(false);

const headers = [
    { title: "Name", key: "full_name", width: "20%" },
    { title: "Username", key: "username", width: "15%" },
    { title: "Role", key: "user_roles", width: "25%" },
    { title: "Status", key: "status", width: "15%" },
    { title: "Actions", key: "actions", sortable: false, align: "end", width: "25%" },
];

const showModalForm = (val) => {
    show_form_modal.value = val;
};

const showModalLog = (val) => {
    show_log_modal.value = val;
}

onMounted(() => {
    getUsers();
    getUserLogs();
});

const addItem = () => {
    user.value = {};
    showModalForm(true);
}

const editItem = (value) => {
    user.value = value;
    showModalForm(true);
};

const deleteItem = async (value) => {
    await destoryUser(value.id); // Swal handled in composable
};

const showItemLog = (value) => {
    user.value = value; 
    showModalLog(true);
};

const reloadUsers = async () => {
    await getUsers();
    user.value = {};
};

const statusColor = (status) => {
    switch (status) {
        case "pending":
            return 'warning';
        case "approved":
            return 'success';
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
                <h2 class="text-h5 font-weight-bold">User Management</h2>
                <v-breadcrumbs :items="[{ title: 'Settings', disabled: true }, { title: 'Users' }]" class="pa-0"></v-breadcrumbs>
            </v-col>
            <v-col cols="12" md="6" class="text-md-right">
                <v-btn 
                    color="primary" 
                    @click="addItem()" 
                    prepend-icon="mdi-plus"
                    class="text-capitalize"
                >
                    New User
                </v-btn>
            </v-col>
        </v-row>

        <v-card elevation="1" rounded="lg">
            <v-card-title class="d-flex align-center">
                <v-text-field
                    v-model="querySearch.search"
                    append-inner-icon="mdi-magnify"
                    label="Search users..."
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
                    @click="reloadUsers"
                    title="Refresh"
                ></v-btn>
            </v-card-title>

            <v-divider></v-divider>

            <v-data-table 
                :headers="headers" 
                :items="users"
                :search="querySearch.search"
                :loading="is_loading"
                loading-text="Loading user data..."
                class="elevation-0"
                :items-per-page="pagination.per_page"
                :page="querySearch.page"
                @update:page="getUsers"
            >
                <template v-slot:item.status="{ item }">
                    <v-chip 
                        :color="statusColor(item.status)" 
                        variant="flat"
                        class="text-capitalize"
                    >
                        {{ item.status }}
                    </v-chip>
                </template>

                <template v-slot:item.user_roles="{ item }">
                    <span v-for="(role, i) in item.roles" :key="role.id">
                        {{ role.name }}{{ item.roles[i+1] ? ", " : "" }}
                    </span>
                </template>

                <template v-slot:item.actions="{ item }">
                    <div class="d-flex justify-end">
                        <v-btn
                            variant="text"
                            color="primary"
                            icon="mdi-pencil"
                            size="small"
                            @click="editItem(item)"
                            class="mr-1"
                            title="Edit"
                        ></v-btn>
                        <v-btn
                            variant="text"
                            color="error"
                            icon="mdi-delete"
                            size="small"
                            @click="deleteItem(item)"
                            class="mr-1"
                            title="Delete"
                        ></v-btn>
                        <v-btn
                            variant="text"
                            color="info"
                            icon="mdi-post"
                            size="small"
                            @click="showItemLog(item)"
                            title="View Logs"
                        ></v-btn>
                    </div>
                </template>

                <template v-slot:bottom>
                    <div class="d-flex flex-column flex-md-row justify-space-between align-center pa-4">
                        <div class="text-caption text-medium-emphasis mb-2 mb-md-0">
                            Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} entries
                        </div>
                        <v-pagination
                            v-model="querySearch.page"
                            :length="pagination.last_page"
                            :total-visible="5"
                            density="comfortable"
                            @update:model-value="getUsers"
                        ></v-pagination>
                    </div>
                </template>
            </v-data-table>
        </v-card>

        <user-form
            :value="show_form_modal"
            :user="user"
            @input="showModalForm"
            @reloadUsers="reloadUsers"
        />

        <user-log 
            v-model:value="show_log_modal" 
            :user="user" 
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
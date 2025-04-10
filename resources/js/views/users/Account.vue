<script setup>
import { ref, onMounted } from "vue";
import UserForm from "../../components/users/Form.vue";
import UserLog from "../../components/users/Logs.vue";
import useUsers from "../../composables/users.js";
import useLogs from "../../composables/usersLog.js";

const { users, pagination, querySearch, is_loading, getUsers, destoryUser } = useUsers();
const {getUserLogs, user_logs} = useLogs();

const user = ref({});
const show_form_modal = ref(false);
const show_log_modal = ref(false);

const headers = [
    { title: "Name", key: "full_name" },
    { title: "Username", key: "username" },
    { title: "Role", key: "user_roles" },
    { title: "Status", key: "status" },
    { title: "Actions", key: "actions", sortable: false },
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
    await destoryUser(value.id);
};

const showItemLog = (value) => {
    user.value = value; 
    showModalLog(true);
};


const reloadUsers = async () => {
    await getUsers();
    user.value = {};
};
</script>
<template>
    <v-row class="p-2 ml-8">
        <h5 class="fw-bold p-3">List of Users </h5>
        <v-spacer></v-spacer>
        <v-btn color="primary" @click="addItem()" class="m-3">
            New User
        </v-btn>
    </v-row>
    <v-card class="ml-8">
        <div class="overflow-hidden overflow-x-auto min-w-full align-middle">
            <v-card-title>
                <v-text-field
                    v-model="querySearch.search"
                    append-icon="mdi-magnify"
                    label="Search"
                    single-line
                    hide-details
                ></v-text-field>
            </v-card-title>
            <v-data-table 
                :headers="headers" 
                :items="users"
                :search="querySearch.search"
                class="elevation-1 p-2"
                :loading="is_loading"
                loading-text="Loading... Please wait"
            >
                <template v-slot:item.user_roles="{ item }">
                    <span v-for="(role, i) in item.roles">
                        {{  role.name }}{{ item.roles[i+1] ? ", " : "" }}
                    </span>
                </template>
                <template v-slot:item.actions="{ item }">
                    <v-btn
                        class="me-2"
                        color="success"
                        @click="editItem(item)"
                        variant="tonal"
                        size="small"
                    >
                        <v-icon size="small"> mdi-pencil </v-icon> Update
                    </v-btn>
                    <v-btn
                        color="error"
                        @click="deleteItem(item)"
                        variant="tonal"
                        size="small"
                    >
                        <v-icon> mdi-delete </v-icon> delete
                    </v-btn>
                    <v-btn
                        class="me-2 ml-2"
                        color="blue"
                        variant="tonal"
                        size="small"
                        @click="showItemLog(item)"
                    >
                        <v-icon> mdi-post </v-icon> logs
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
                                v-model="querySearch.page"
                                :length="pagination.last_page"
                                circle
                                @click="getUsers"
                            >
                            </v-pagination>
                        </div>
                    </div>
                </template>
            </v-data-table> 
        </div>
    </v-card>

    <user-form
        :value="show_form_modal"
        :user="user"
        @input="showModalForm"
        @reloadUsers="reloadUsers"
    />
    <user-log v-model:value="show_log_modal" :user="user" />


</template>

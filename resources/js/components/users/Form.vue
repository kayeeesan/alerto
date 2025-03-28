<script setup>
import { ref, reactive, watch, onMounted } from "vue";
import useUsers from "../../composables/users.js";
import useRoles from "../../composables/roles.js";

const { errors, is_loading, is_success, storeUser, updateUser, resetPassword } = useUsers();
const { roles, getRoles } = useRoles();

const emit = defineEmits(["input", "reloadUsers"]);
const props = defineProps({
    user: {
        type: Object,
        default: null
    },
    value: {
        type: Boolean,
        default: false,
    }
});

onMounted(() => {
    getRoles();
});

const initialState = {
    id: null,
    username: null,
    first_name: null,
    last_name: null,
    middle_name: null,
    selected_roles: [],
    user_roles: [],
    status: null,
}
const form = reactive({ ...initialState });

const status = 
[
    'pending',
    'approved',
    'disabled'
];

watch(
    () => props.user,
    (value)  => {
        form.id = value.id;
        form.username = value.username;
        form.first_name = value.first_name;
        form.last_name = value.last_name;
        form.middle_name = value.middle_name;
        form.selected_roles = value.roles;
        form.status = value.status;
    }
);

const show_form_modal = ref(false);
watch(
    () => props.value,
    (value)  => {
        show_form_modal.value = value;
    }
);

const generateUsername = () => {
    if(!form.id){
        var fname = form.first_name ? form.first_name.charAt(0).toUpperCase() : ""; 
        var mname = form.middle_name ? form.middle_name.charAt(0).toUpperCase() : ""; 
    
        form.username = fname + mname;
    }
}

watch(
    () => form.last_name,
    (value)  => {
        if(!form.id){
            if (value) {
                form.username += value.toUpperCase();
            }
        }
    }
);

watch(
    () => form.selected_roles,
    (value)  => {
        if(value){
            form.user_roles = value.map(x => x.id);
        }
    }
);

const close = () => {
    Object.assign(form, initialState);
    emit("input", false);
    errors.value = {};
}

const save = async () => {
    if(props.user && props.user.id) {
        await updateUser({ ...form });
    } else {
        await storeUser({ ...form });
    }

    if (is_success.value == true){
        emit("reloadUsers");
        emit("input", false);
    }
}

const ResetPassword = async () => {
    if (form.id) {
        await resetPassword(form.id);
    }

    if (is_success.value){
        emit("reloadUsers");
        emit("input", false);
    }
}
</script>
<template>
    <v-dialog v-model="show_form_modal" max-width="500px" scrollable persistent>
        <v-card>
            <v-card-title class="mt-2">
                <span class="text-h5">New User</span>
            </v-card-title>
    
            <v-card-text>
                <v-container>
                    <v-row>
                        <v-text-field
                            v-model="form.first_name"
                            label="First Name*"
                            variant="outlined"
                            :error-messages="
                                errors['first_name'] ? errors['first_name'] : []
                            "
                        ></v-text-field>
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.middle_name"
                            label="Middle Name"
                            variant="outlined"
                            :error-messages="
                                errors['middle_name'] ? errors['middle_name'] : []
                            "
                        ></v-text-field>
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.last_name"
                            label="Last Name*"
                            variant="outlined"
                            @keydown="generateUsername"
                            :error-messages="
                                errors['last_name'] ? errors['last_name'] : []
                            "
                        ></v-text-field>
                    </v-row>
                    <v-row>
                        <v-text-field
                            v-model="form.username"
                            label="Username*"
                            variant="outlined"
                            :error-messages="
                                errors['username'] ? errors['username'] : []
                            "
                        ></v-text-field>
                    </v-row>
                    <v-row>
                        <vue-multiselect
                            v-model="form.selected_roles"
                            :options="roles"
                            :multiple="true"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :preserve-search="true"
                            placeholder="Select Role/s"
                            label="name"
                            track-by="name"
                            select-label=""
                            deselect-label=""
                        >
                        </vue-multiselect>
                        <span v-if="errors['user_roles']" class="error-msg">{{ errors['user_roles'][0] }}</span>
                    </v-row>
                    <v-row>
                        <vue-multiselect
                            v-model="form.status"
                            :options="status"
                            :multiple="false"
                            :close-on-select="true"
                            :clear-on-select="true"
                            :preserve-search="true"
                            placeholder="Status"
                            select-label=""
                            deselect-label=""
                        >
                        </vue-multiselect>
                    </v-row>
                    <v-row>
                        
                <v-btn color="primary" @click="ResetPassword()" variant="tonal" class="mt-2">
                    RESET PASSWORD
                </v-btn>
                    </v-row>
                </v-container>
            </v-card-text>
    
            <v-card-actions class="mb-4 mr-5">
                <v-spacer></v-spacer>
                <v-btn color="blue-grey-lighten-2" @click="close()" variant="tonal">
                    Cancel
                </v-btn>
                <v-btn color="primary" @click="save()" variant="tonal" :loading="is_loading">
                    Save
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
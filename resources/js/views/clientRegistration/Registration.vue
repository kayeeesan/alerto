<script setup>
import { ref, reactive, watch, onMounted } from "vue";
import useUsers from "../../composables/users.js";

const { errors, is_loading, is_success, storeUser, updateUser } = useUsers();

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

const initialState = {
    id: null,
    username: null,
    first_name: null,
    last_name: null,
    middle_name: null,
};

const form = reactive({ ...initialState });

watch(
    () => props.user,
    (value) => {
        form.id = value?.id;
        form.username = value?.username;
        form.first_name = value?.first_name;
        form.last_name = value?.last_name;
        form.middle_name = value?.middle_name;
    }
);

const show_form_modal = ref(false);
watch(
    () => props.value,
    (value) => {
        show_form_modal.value = value;
    }
);


// watch(
//     () => form.last_name,
//     (value) => {
//         if (!form.id && value) {
//             form.username += value.toUpperCase();
//         }
//     }
// );

const close = () => {
    Object.assign(form, initialState);
    emit("input", false);
    errors.value = {};
};

const save = async () => {
    if (props.user && props.user.id) {
        await updateUser({ ...form });
    } else {
        await storeUser({ ...form });
    }

    if (is_success.value) {
        emit("reloadUsers");
        emit("input", false);
    }
};
</script>

<template>
    <v-app>
        <v-layout class="bg-indigo-darken-1">
            <v-main style="background-color: transparent; " class="mt-7">
               <v-container class="overflow-y-auto border-none pt-0">
                    <v-card class="mx-auto mt-5" max-width="1000" style="margin-top: 0 !important;">
                        <div class="d-flex justify-content-center">
                            <v-card-title class="text-h6 font-weight-bold justify-space-between mt-5">
                            <v-avatar color="indigo-darken-1" size="24" class="ml-2 mb-1 mr-1"></v-avatar>
                            <span>Member Registration</span>
                            </v-card-title>
                            
                        </div>
                        <div class="bg-light text-center text-red pa-5 mb-2">Note: all fields with * are required! </div>

                        <div class="mt-4 mr-10 ml-10">
                            <v-form>
                                <v-row >
                                    <v-col cols="12" md="6" sm="6">
                                        <v-text-field
                                            v-model="form.first_name"
                                            prepend-icon="mdi-account"
                                            :rules="[
                                            (v) => !!v || 'This field is required',
                                            (v) => /^[a-zA-Z\s]+$/.test(v) || 'Only letters are allowed',
                                            ]"
                                            label="Firstname*"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6" sm="6">
                                        <v-text-field
                                        v-model="form.last_name"
                                            prepend-icon="mdi-account"
                                            :rules="[
                                            (v) => !!v || 'This field is required',
                                            (v) => /^[a-zA-Z\s]+$/.test(v) || 'Only letters are allowed',
                                            ]"
                                            label="Lastname*"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                                <v-row >
                                    <v-col cols="12" md="6" sm="6">
                                        <v-text-field
                                            v-model="form.middle_name"
                                            prepend-icon="mdi-email"
                                            :rules="[
                                            (v) => !!v || 'This field is required',
                                            (v) => /^[a-zA-Z\s]+$/.test(v) || 'Only letters are allowed',
                                            ]"
                                            label="Middle Name*"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6" sm="6">
                                        <v-text-field
                                            v-model="form.username"
                                            prepend-icon="mdi-phone"
                                            label="Username*"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col cols="12">
                                <v-row>
                                </v-row>
                                    </v-col>
                                </v-row>
                                <!-- <v-row >
                                    <v-col cols="12">
                                        <v-text-field
                                            prepend-icon="mdi-account"
                                            :rules="[
                                            (v) => !!v || 'This field is required',
                                            (v) => /^[a-zA-Z\s]+$/.test(v) || 'Only letters are allowed',
                                            ]"
                                            label="Position*"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                                <v-row >
                                    <v-col cols="12">
                                        <v-text-field
                                            prepend-icon="mdi-home-city-outline"
                                            :rules="[
                                            (v) => !!v || 'This field is required',
                                            (v) => /^[a-zA-Z\s]+$/.test(v) || 'Only letters are allowed',
                                            ]"
                                            label="Government Agency*"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                                <v-row >
                                    <v-col cols="12" md="6" sm="6">
                                        <v-text-field
                                            prepend-icon="mdi-map-marker"
                                            :rules="[
                                            (v) => !!v || 'This field is required',
                                            (v) => /^[a-zA-Z\s]+$/.test(v) || 'Only letters are allowed',
                                            ]"
                                            label="Region*"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6" sm="6">
                                        <v-text-field
                                            prepend-icon="mdi-map-marker"
                                            :rules="[
                                            (v) => !!v || 'This field is required',
                                            (v) => /^[a-zA-Z\s]+$/.test(v) || 'Only letters are allowed',
                                            ]"
                                            label="Province*"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                                <v-row >
                                    <v-col cols="12" md="6" sm="6">
                                        <v-text-field
                                            prepend-icon="mdi-city"
                                            :rules="[
                                            (v) => !!v || 'This field is required',
                                            (v) => /^[a-zA-Z\s]+$/.test(v) || 'Only letters are allowed',
                                            ]"
                                            label="City/Municipality*"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6" sm="6">
                                        <v-text-field
                                            prepend-icon="mdi-waves"
                                            :rules="[
                                            (v) => !!v || 'This field is required',
                                            (v) => /^[a-zA-Z\s]+$/.test(v) || 'Only letters are allowed',
                                            ]"
                                            label="Riverbasin assigned*"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                                <v-row >
                                    <v-col cols="12">
                                        <v-text-field
                                            prepend-icon="mdi-facebook"
                                            :rules="[
                                            (v) => !!v || 'This field is required',
                                            (v) => /^[a-zA-Z\s]+$/.test(v) || 'Only letters are allowed',
                                            ]"
                                            label="LGU Facebook Page (If any)"
                                        ></v-text-field>
                                    </v-col>
                                </v-row> -->

                                <!-- <div class="text-center mt-5 mb-0 pb-10">
                                    <div>
                                        <v-btn to="/" variant="outlined" color="primary">Back</v-btn>
                                        <v-btn   type="submit" class="ml-5">Submit</v-btn>
                                    </div>
                                </div> -->
                                <v-card-actions class="mb-4 mr-5">
                                    <v-spacer></v-spacer>
                                    <v-btn to="/" color="blue-grey-lighten-2" @click="close()" variant="tonal">
                                        Cancel
                                    </v-btn>
                                    <v-btn color="primary" @click="save()" variant="tonal" :loading="is_loading">
                                        Save
                                    </v-btn>
                                </v-card-actions>
                            </v-form>
                              
                        </div>
                    </v-card>
               </v-container>
            </v-main>
        </v-layout>
    </v-app>
</template>
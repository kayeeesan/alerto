<script setup>
import { ref, reactive, watch, onMounted, computed } from "vue";

const emit = defineEmits(["input", "reloadStaffs"]);
const props = defineProps({
    staff: {
        type: Object,
        default: null
    },
    value: {
        type: Boolean,
        default: false
    }
});

const initialState = {
    id: null,
    username: null,
    first_name: null,
    last_name: null,
    mobile_number: null,
    role: {},
    region: {},
    province: {},
    municipality: {},
    river: {},
    fb_lgu: null
}
const form = reactive({ ...initialState });

watch(
    () => props.staff,
    (value)  => {
        form.id = value.id;
            form.username = value.username;
            form.first_name = value.first_name;
            form.last_name = value.last_name;
            form.mobile_number = value.mobile_number;
            form.role = value.role;
            form.region = value.region;
            form.province = value.province;
            form.municipality = value.municipality;
            form.river = value.river;
            form.fb_lgu = value.fb_lgu;
    }
);

const show_staff_modal = ref(false);
watch(
    () => props.value,
    (value) => {
        show_staff_modal.value = value;
    }
);

const close = () => {
    Object.assign(form, initialState);
    emit("input", false);
    errors.value = {};
}
</script>
<template>
    <v-dialog v-model="show_staff_modal" max-width="500px" persistent>
        <v-card>
            <v-card-title>
                <span >{{form.username}}</span>
            </v-card-title>
            <v-card-actions class="mb-4 mr-5">
                <v-spacer></v-spacer>
                <v-btn color="blue-grey-lighten-2" @click="close()" variant="flat">
                    Close
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
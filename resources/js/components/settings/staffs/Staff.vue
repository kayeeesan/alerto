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
    fb_lgu: null,
    status: null
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
            form.status = value.status;
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
    emit("input", false);
    errors.value = {};
}

const statusUpdate = computed(() => {
    return form.status === 'approved' ? 'Active' : form.status;
    color
});

const statusColor = computed(() => {
    switch (form.status){
        case "approved":
            return 'green';
        case "pending":
            return 'grey';
        case "disabled":
            return 'red';
    }
});
</script>
<template>
    <v-dialog v-model="show_staff_modal" max-width="500px" persistent>
        <v-card>
            <v-card-title>
                <span >Member info</span>
            </v-card-title>

            <v-card-text class="pa-6">
                <v-row>
                    <v-col cols="12" md="2" sm="2">
                        <v-avatar size="70" class="me-4">
                        <img src="../../../../img/icon/user_icon.svg" alt="User Avatar" style="width: 100%;" />
                    </v-avatar>
                    </v-col>
                    <v-col cols="12" md="9" sm="9">
                        <div>
                        <div class="text-subtitle-1 font-weight-bold">{{ form.first_name }} {{ form.    last_name }}</div>
                        <div class="text-body-2 text-grey-darken-1">{{ form.username }}</div>
                        <v-chip class="mt-1" :color="statusColor">{{ statusUpdate }}</v-chip>
                    </div>
                    </v-col>
                </v-row>

                <v-divider></v-divider>

                <v-row dense>
                    <v-col cols="12" v-if="form.first_name">
                        <div class="text-caption text-grey">Fullname</div>
                        <v-sheet class="pa-2 rounded bg-grey-lighten-4">
                            <v-icon size="18" class="me-2" color="grey-darken-1">mdi-account</v-icon>
                            {{ form.first_name }} {{ form.last_name }}
                        </v-sheet>
                    </v-col>
                    <v-col cols="12" v-if="form.mobile_number">
                        <div class="text-caption text-grey">Mobile number</div>
                        <v-sheet class="pa-2 rounded bg-grey-lighten-4">
                            <v-icon size="18" class="me-2" color="grey-darken-1">mdi-phone</v-icon>
                            {{ form.mobile_number }}
                        </v-sheet>
                    </v-col>
                    <v-col cols="12" v-if="form.municipality">
                        <div class="text-caption text-grey">Municipality</div>
                        <v-sheet class="pa-2 rounded bg-grey-lighten-4">
                            <v-icon size="18" class="me-2" color="grey-darken-1">mdi-map</v-icon>
                            {{ form.municipality.name }}
                        </v-sheet>
                    </v-col>
                    <v-col cols="12" v-if="form.river">
                        <div class="text-caption text-grey">Riverbasin</div>
                        <v-sheet class="pa-2 rounded bg-grey-lighten-4">
                            <v-icon size="18" class="me-2" color="grey-darken-1">mdi-waves</v-icon>
                            {{ form.river.name }}
                        </v-sheet>
                    </v-col>
                </v-row>

            </v-card-text>

            <v-card-actions class="mb-4 mr-5">
                <v-spacer></v-spacer>
                <v-btn color="blue-grey-lighten-2" @click="close()" variant="flat">
                    Close
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
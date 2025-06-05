<script setup>
import { ref, reactive, watch, computed } from "vue";

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
    // role: {},
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
        if (value) {
            Object.assign(form, value);
        }
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
}

const statusUpdate = computed(() => {
    return form.status === 'approved' ? 'Active' : form.status;
});

const statusColor = computed(() => {
    switch (form.status){
        case "approved":
            return 'success';
        case "pending":
            return 'warning';
        case "disabled":
            return 'error';
        default:
            return 'grey';
    }
});
</script>

<template>
    <v-dialog v-model="show_staff_modal" max-width="600px" persistent>
        <v-card class="member-modal">
            <v-card-title class="modal-header">
                <span class="headline">Member Information</span>
                <v-btn
                    icon
                    @click="close"
                    class="close-btn"
                >
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-card-title>

            <v-card-text class="modal-content">
                <!-- Profile Header -->
                <div class="profile-header">
                    <v-avatar size="80" class="profile-avatar">
                        <img src="../../../../img/icon/user_iconI.svg" alt="User Avatar" style="width: 100%;" />
                    </v-avatar>
                    <div class="profile-info">
                        <h2 class="profile-name">{{ form.first_name }} {{ form.last_name }}</h2>
                        <div class="profile-username">@{{ form.username }}</div>
                        <v-chip 
                            class="status-badge"
                            :color="statusColor"
                            size="small"
                            label
                        >
                            {{ statusUpdate }}
                        </v-chip>
                    </div>
                </div>

                <v-divider class="divider"></v-divider>

                <!-- Member Details -->
                <div>
                    <div class="detail-item mb-4" v-if="form.first_name">
                        <div class="detail-label">
                            <v-icon size="20" class="detail-icon">mdi-account</v-icon>
                            Full Name
                        </div>
                        <div class="detail-value">{{ form.first_name }} {{ form.last_name }}</div>
                    </div>

                    <div class="detail-item mb-4" v-if="form.mobile_number">
                        <div class="detail-label">
                            <v-icon size="20" class="detail-icon">mdi-phone</v-icon>
                            Mobile Number
                        </div>
                        <div class="detail-value">{{ form.mobile_number }}</div>
                    </div>

                    <!-- <div class="detail-item mb-4" v-if="form.role?.name">
                        <div class="detail-label">
                            <v-icon size="20" class="detail-icon">mdi-account-cog</v-icon>
                            Role
                        </div>
                        <div class="detail-value">{{ form.role.name }}</div>
                    </div> -->

                    <div class="detail-item mb-4" v-if="form.municipality?.name">
                        <div class="detail-label">
                            <v-icon size="20" class="detail-icon">mdi-home-city</v-icon>
                            Municipality
                        </div>
                        <div class="detail-value">{{ form.municipality.name }}</div>
                    </div>

                    <div class="detail-item mb-4" v-if="form.river?.name">
                        <div class="detail-label">
                            <v-icon size="20" class="detail-icon">mdi-waves</v-icon>
                            River Basin
                        </div>
                        <div class="detail-value">{{ form.river.name }}</div>
                    </div>

                    <div class="detail-item" v-if="form.fb_lgu">
                        <div class="detail-label">
                            <v-icon size="20" class="detail-icon">mdi-facebook</v-icon>
                            Facebook LGU
                        </div>
                        <div class="detail-value">{{ form.fb_lgu }}</div>
                    </div>
                </div>
            </v-card-text>

            <v-card-actions class="modal-actions">
                <v-spacer></v-spacer>
                <!-- <v-btn
                    color="primary"
                    variant="flat"
                    @click="close"
                    class="action-btn"
                >
                    Close
                </v-btn> -->
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<style scoped>
.member-modal {
    border-radius: 12px !important;
    overflow: hidden;
}

.modal-header {
    background-color: var(--primary-color);
    color: white;
    padding: 16px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header .headline {
    font-size: 1.25rem;
    font-weight: 600;
}

.close-btn {
    color: black;
}

.modal-content {
    padding: 24px;
}

.profile-header {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
}


.profile-info {
    flex: 1;
}

.profile-name {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 4px;
    color: #263238;
}

.profile-username {
    font-size: 0.9rem;
    color: #607D8B;
    margin-bottom: 8px;
}

.status-badge {
    font-weight: 600;
    letter-spacing: 0.5px;
}

.divider {
    margin: 5px 0;
    border-color: rgba(0, 0, 0, 0.08);
}


.detail-item {
    background-color: #f8fafc;
    border-radius: 8px;
    padding: 12px;
    transition: all 0.2s ease;
}

.detail-item:hover {
    background-color: #ECEFF1;
    transform: translateY(-2px);
}

.detail-label {
    font-size: 0.75rem;
    color: #607D8B;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-icon {
    margin-right: 8px;
    color: #003366;
}

.detail-value {
    font-size: 0.95rem;
    font-weight: 500;
    color: #37474F;
}

.modal-actions {
    padding: 16px 24px;
    background-color: #FAFAFA;
    border-top: 1px solid #E0E0E0;
}

.action-btn {
    min-width: 120px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}
</style>
<script setup>
import { computed, defineEmits, defineProps, watch } from "vue";
import useLogs from "../../composables/usersLog";

const { user_logs, is_loading, getUserLogs } = useLogs();
const emit = defineEmits(["update:value"]);
const props = defineProps({
    value: Boolean, // Bound with v-model:value
    user: Object,
});

// Proxy the `value` prop to make it reactive
const showDialog = computed({
    get: () => props.value,
    set: (newValue) => {
        emit("update:value", newValue);
    },
});

// Fetch logs when user changes
watch(() => props.user, async (newUser) => {
    if (newUser && newUser.id) {
        await getUserLogs(newUser.id);
    }
}, { immediate: true });


// Function to get action details (icon, color, background)
const getActionDetails = (action) => {
    const actions = {
        delete: { icon: "mdi-delete", color: "red", bg: "#FFEBEE" }, // Light red
        update: { icon: "mdi-pencil", color: "green", bg: "#E8F5E9" }, // Light green
        create: { icon: "mdi-plus-circle", color: "blue", bg: "#E3F2FD" }, // Light blue
    };
    return actions[action] || { icon: "mdi-alert-circle", color: "gray", bg: "#F5F5F5" }; // Default gray
};

const closeModal = () => {
    emit("update:value", false);
};
</script>

<template>
    <v-dialog v-model="showDialog" max-width="600px">
        <v-card>
            <v-card-title>
                <span class="text-h5">User Logs</span>
            </v-card-title>
            <v-card-text>
                <p v-if="user">Logs for <strong>{{ user.full_name }}</strong></p>
                <v-divider class="mb-2"></v-divider>

                <!-- Loading Spinner -->
                <v-progress-circular v-if="is_loading" indeterminate></v-progress-circular>

                <!-- Logs List -->
                <v-list v-else-if="user_logs.length" dense>
                    <template v-for="(log, index) in user_logs" :key="log.id">
                        <v-list-item
                            :style="{
                                backgroundColor: getActionDetails(log.action).bg,
                                borderRadius: '8px',
                                padding: '10px',
                                marginBottom: '6px',
                            }"
                        >
                            <!-- <v-icon 
                                :color="getActionDetails(log.action).color"
                                class="mr-2"
                            >
                                {{ getActionDetails(log.action).icon }}
                            </v-icon> -->
                            <v-list-item-content>
                                <v-list-item-title>
                                    <strong>{{ log.action.toUpperCase() }}</strong> - {{ log.entity_type }}
                                </v-list-item-title>
                                <v-list-item-subtitle>
                                    {{ log.created_at }}
                                </v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>

                        <!-- Divider between logs -->
                        <v-divider v-if="index !== user_logs.length - 1"></v-divider>
                    </template>
                </v-list>

                <!-- No Logs Available -->
                <p v-else class="text-grey text-center">No logs available.</p>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error" @click="closeModal">Close</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import { ref } from 'vue';
import { RouterView } from "vue-router";
import thresholdDetails from './thresholdDetails.vue';
import totalSensors from './totalSensors.vue';
import sensorsStatus from './sensorsStatus.vue';

const tab = ref(null);
const items = ref(['ARG', 'WLMS', 'TANDEM']);
const texts = ref(['lorem', 'lorem', 'lorem']);
</script>

<template>
    <v-container fluid class="px-4 py-8">
        <v-row>
            <!-- Left Column -->
            <v-col cols="12" lg="8" >
                <thresholdDetails />
               
            </v-col>

            <!-- Right Column -->
            <v-col cols="12" lg="4">
                 <totalSensors />
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="12">
                <sensorsStatus />
            </v-col>
        </v-row>
    </v-container>
</template>

<style scoped>
/* Remove fixed margins and use padding/margin from Vuetify */
</style>

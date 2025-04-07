<script setup>
import { ref, reactive, watch, onMounted, computed } from "vue";
import { useRouter } from 'vue-router';

</script>

<template>
    <v-app>
        <v-layout class="bg-indigo-darken-1">
            <v-container fluid class="fill-height d-flex align-center justify-center">
                <v-card class="pa-8" elevation="8" width="600px" rounded="lg">
                    <v-card-title class="text-h5 text-center font-weight-bold"> Email Verification </v-card-title>
                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col>
                                    <v-text-field variant="outlined" label="Email*"></v-text-field>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                    <v-card-actions class="justify-end">
                        <v-btn 
                            color="red-darken-4" 
                            variant="flat" 
                            :to="'/'"
                            :loading="is_loading" >Cancel
                        </v-btn>
                        <v-btn 
                            color="blue-darken-4" 
                            variant="flat" 
                            :loading="is_loading" >
                           Send
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-container>
        </v-layout>
    </v-app>
   
</template>

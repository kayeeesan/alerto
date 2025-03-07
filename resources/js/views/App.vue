<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import Sidebar from "../components/layouts/Sidebar.vue";
import useAuth from "../composables/auth.js";
import store from "@/store";

const { logout } = useAuth();

const user = store.state.auth.user;

const drawer = ref(false);

const cleanUpExpiredItems = () => {
    const expiry = localStorage.getItem("expiry");

    if (!expiry) {
        return null;
    }

    const now = new Date();

    if (now.getTime() > expiry) {
        logout();
        return null;
    }
};

onMounted(() => {
    setInterval(cleanUpExpiredItems(), 28800000); //restart every 8hours
});

onUnmounted(() => {
    clearInterval(interval);
});
</script>
<template>
    <v-app>
        <sidebar :drawer="drawer"/>

        <v-app-bar app>
            <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
            <v-toolbar-title>QMS</v-toolbar-title>
            <v-spacer></v-spacer>
            
            <v-badge
                color="blue-grey-lighten-5"
                :content="user.full_name"
                inline
            ></v-badge>
            <v-btn icon="mdi-logout" variant="text" @click="logout()"></v-btn>
        </v-app-bar>

        <v-main>
            <v-container>
                <router-view />
            </v-container>
        </v-main>
    </v-app>
</template>
<style>
#app {
    font-family: Avenir, Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
</style>

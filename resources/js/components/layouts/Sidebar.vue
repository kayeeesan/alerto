<script setup>
import { ref, watch } from "vue";

const props = defineProps({
    drawer: {
        type: Boolean,
        default: null
    },
});

const items = [
    { title: 'Home', icon: 'mdi-home', route: "/" },
];

const libraries = [
    { title: 'Role', route: "/roles" },
    { title: 'Accounts', route: "/users" },
];

const rail = ref(false);

watch(
    () => props.drawer,
    (value)  => {
        rail.value = value;
        console.log(value);
        
    }
);
</script>
<template>
    <v-navigation-drawer v-model="rail" app>
        <v-list>
            <template v-for="item in items">
                <v-list-item
                    :prepend-icon="item.icon"
                    :title="item.title"
                    :to="item.route"
                ></v-list-item>
            </template>

            <v-list-group value="Actions">
                <template v-slot:activator="{ props }">
                    <v-list-item
                        prepend-icon="mdi-format-list-bulleted-type"
                        v-bind="props"
                        title="Libraries"
                    ></v-list-item>
                </template>

                <v-list-item
                    v-for="library in libraries"
                    :title="library.title"
                    :to="library.route"
                ></v-list-item>
            </v-list-group>
        </v-list>
      </v-navigation-drawer>
</template>
<style>
.v-navigation-drawer--rail.v-navigation-drawer--expand-on-hover:not(.v-navigation-drawer--is-hovering) .v-list-item .v-avatar, .v-navigation-drawer--rail:not(.v-navigation-drawer--expand-on-hover) .v-list-item .v-avatar{
    width: 38px;
    height: 38px;
}
.nav-header {
    margin-top: 10px
}
.nav-header.v-list-item--nav .v-list-item-title {
    font-size: 15px;
    font-weight: bold;
}
</style>
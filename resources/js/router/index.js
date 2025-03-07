import { createRouter, createWebHashHistory } from "vue-router";
import store from '@/store'

import Login from '../views/auth/Login.vue';
import PageNotFound from '../components/layouts/PageNotFound.vue';
import App from '../views/App.vue';
import Home from '../components/layouts/Home.vue';
import Accounts from '../views/users/Account.vue';
import Roles from '../views/libraries/Role.vue';
import UpdatePassword from "../components/users/UpdatePassword.vue";
import Registration from "../views/clientRegistration/Registration.vue";


import Welcome from '../views/welcomeGuest/Welcome.vue';
import Dashboard from '../views/welcomeGuest/dashboard/dashboard.vue';
import RiverStatus from '../components/dashboard/river/form.vue';
import Weather from '../components/dashboard/weather/form.vue';
import AdzuWeather from '../components/dashboard/adzuWeatherStation/form.vue';
import Earthquake from '../components/dashboard/earthQuake/form.vue';
import HistoryDataExtraction from '../components/dashboard/historyDataExtraction/form.vue';
import VisualizationMap from '../components/dashboard/visualizationMap/form.vue';

const routes = [
    {
        path: '/:pathMatch(.*)*',
        name: 'page-not-found',
        component: PageNotFound
    },
    {
        path: '/',
        component: Welcome,
        children: [
            {
                path: '',
                name: 'welcome',
                component: Dashboard, // Set default child route
            },
            {
                path: 'river-status',
                name: 'River Status',
                component: RiverStatus
            },
            {
                path: 'weather-updates',
                name: 'Weather',
                component: Weather
            },
            {
                path: 'adzu-weather',
                name: 'Adzu Weather',
                component: AdzuWeather
            },
            {
                path: 'earthquake',
                name: 'Earthquak',
                component: Earthquake
            },
            {
                path: 'visualization-map',
                name: 'Visualization Map',
                component: VisualizationMap
            },
            {
                path: 'history-data-extraction',
                name: 'History Data Extraction',
                component: HistoryDataExtraction
            }
        ],
        meta: {
            middleware: "guest"
        }
    },
    {
        path: '/registration',
        name: 'registration',
        component: Registration,
        meta: {
            middleware: "guest"
        }
    },
    
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: {
            middleware: "guest"
        }
    },
    {
        path: '/update-password',
        name: 'update-password',
        component: UpdatePassword
    },
    {
        path: '/home',
        name: 'app',
        component: App,
        children: [
            {
                path: '/home',
                name: 'home',
                component: Home
            },
            {
                path: '/users',
                name: 'users',
                component: Accounts
            },
            {
                path: '/roles',
                name: 'roles',
                component: Roles
            },
        ]
    },
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    if (to.meta.middleware == "guest") {
        if (store.state.auth.authenticated) {
            if(store.state.auth.password_reset){
                next({ name: "update-password" })
            }else{
                next({ name: "home" })
            }
        }
        next()
    } else {
        if (store.state.auth.authenticated) {
            next()
        } else {
            next({ name: "login" })
        }
    }
})

export default router;
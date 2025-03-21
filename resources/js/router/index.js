// src/router/index.js

import { createRouter, createWebHashHistory } from "vue-router";
import store from '@/store';

// Auth and Layouts
import Login from '../views/auth/Login.vue';
import Registration from "../views/clientRegistration/Registration.vue";
import UpdatePassword from "../components/users/UpdatePassword.vue";
import PageNotFound from '../components/layouts/PageNotFound.vue';
import App from '../views/App.vue';
import Home from '../components/layouts/Home.vue';

// User Management
import Accounts from '../views/users/Account.vue';
import Roles from '../views/libraries/Role.vue';

// Public Pages
import Welcome from '../views/welcomeGuest/Welcome.vue';
import Dashboard from '../views/welcomeGuest/dashboard/dashboard.vue';
import AboutUs from "../views/aboutUs/aboutUs.vue";
import ContactUs from "../views/contactUs/contactUs.vue";

// Dashboard Components
import RiverStatus from '../components/dashboard/river/form.vue';
import Weather from '../components/dashboard/weather/form.vue';
import AdzuWeather from '../components/dashboard/adzuWeatherStation/form.vue';
import Earthquake from '../components/dashboard/earthQuake/form.vue';
import HistoryDataExtraction from '../components/dashboard/historyDataExtraction/form.vue';
import VisualizationMap from '../components/dashboard/visualizationMap/form.vue';

// Settings and Libraries
import Responses from "../views/libraries/Response.vue"
import SensorsUnderAlerto from "../views/libraries/SensorUnderAlerto.vue";
import SensorsUnderPh from "../views/libraries/SensorUnderPh.vue";
import Provinces from "../views/libraries/Province.vue";
import Rivers from "../views/libraries/River.vue";
import Municipalities from "../views/libraries/Municipality.vue";
import Thresholds from "../views/libraries/Threshold.vue";

//User Settings
import Alerts from "../views/userSettings/alert.vue";

const routes = [
    // Catch-all for 404
    {
        path: '/:pathMatch(.*)*',
        name: 'page-not-found',
        component: PageNotFound
    },
    // Public Routes
    {
        path: '/',
        component: Welcome,
        children: [
            {
                path: '',
                name: 'welcome',
                component: Dashboard
            },
            {
                path: 'river-status',
                name: 'public-river-status',
                component: RiverStatus
            },
            {
                path: 'weather-updates',
                name: 'public-weather',
                component: Weather
            },
            {
                path: 'adzu-weather',
                name: 'public-adzu-weather',
                component: AdzuWeather
            },
            {
                path: 'earthquake',
                name: 'public-earthquake',
                component: Earthquake
            },
            {
                path: 'visualization-map',
                name: 'public-visualization-map',
                component: VisualizationMap
            },
            {
                path: 'history-data-extraction',
                name: 'public-history-data-extraction',
                component: HistoryDataExtraction
            },
            {
                path: 'contact-us',
                name: 'public-contact-us',
                component: ContactUs
            },
            {
                path: 'about-us',
                name: 'public-about-us',
                component: AboutUs
            }
        ],
        meta: {
            middleware: "guest"
        }
    },
    // Auth Routes
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
    // Protected Routes
    {
        path: '/home',
        name: 'app',
        component: App,
        children: [
            {
                path: '',
                name: 'home',
                component: Home
            },
            {
                path: 'users',
                name: 'users',
                component: Accounts
            },
            {
                path: 'roles',
                name: 'roles',
                component: Roles
            },
            {
                path: 'sensors-under-alerto',
                name: 'sensors-under-alerto',
                component: SensorsUnderAlerto
            },
            {
                path: 'sensors-under-ph',
                name: 'sensors-under-ph',
                component: SensorsUnderPh
            },
            {
                path: 'provinces',
                name: 'provinces',
                component: Provinces
            },
            {
                path: 'rivers',
                name: 'rivers',
                component: Rivers
            },
            {
                path: 'river-status',
                name: 'private-river-status',
                component: RiverStatus
            },
            {
                path: 'weather-updates',
                name: 'private-weather-updates',
                component: Weather
            },
            {
                path: 'adzu-weather',
                name: 'private-adzu-weather',
                component: AdzuWeather
            },
            {
                path: 'earthquake',
                name: 'private-earthquake',
                component: Earthquake
            },
            {
                path: 'visualization-map',
                name: 'private-visualization-map',
                component: VisualizationMap
            },
            {
                path: 'history-data-extraction',
                name: 'private-history-data-extraction',
                component: HistoryDataExtraction
            },
            {
                path: 'contact-us',
                name: 'private-contact-us',
                component: ContactUs
            },
            {
                path: 'about-us',
                name: 'private-about-us',
                component: AboutUs
            },
            {
                path: 'municipalities',
                name: 'private-municipalities',
                component: Municipalities
            },
            {
                path: 'thresholds',
                name: 'private-thresholds',
                component: Thresholds
            },
            {
                path: 'responses',
                name: 'private-responses',
                component: Responses
            },
            {
                path: 'alerts',
                name: 'private-alerts',
                component: Alerts
            },
        ]
    }
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
});

// Navigation Guards
router.beforeEach((to, from, next) => {
    if (to.meta.middleware === "guest") {
        if (store.state.auth.authenticated) {
            if (store.state.auth.password_reset) {
                next({ name: "update-password" });
            } else {
                next({ name: "home" });
            }
        } else {
            next();
        }
    } else {
        if (store.state.auth.authenticated) {
            next();
        } else {
            next({ name: "login" });
        }
    }
});

export default router;

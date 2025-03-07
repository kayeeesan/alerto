import './bootstrap';
import { createApp } from 'vue/dist/vue.esm-bundler.js';
import router from './router/index.js';
import vuetify from './plugins/vuetify.js';
import store from './store/index.js';
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";
import "../css/app.css";
import VueMultiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.css';

import axios from "axios";
axios.defaults.headers['Authorization'] = `Bearer ${localStorage.getItem('token')}`;
axios.defaults.headers.common['Access-Control-Allow-Origin'] = '*';

const app = createApp({});
app.use(router)
app.use(vuetify)
app.use(store)
app.component('VueMultiselect', VueMultiselect);
app.mount('#app')
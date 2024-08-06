import { createApp } from 'vue'
import router from './router';
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'

import AlertMessage from "./composables/AlertMessage";

import App from './App.vue';
const app = createApp(App);
app.use(router);
app.use(ElementPlus);
app.provide('alert', AlertMessage);
app.mount('#gym_test_app');
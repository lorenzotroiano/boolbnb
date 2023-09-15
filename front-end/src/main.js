import { createApp } from 'vue'
import { createPinia } from 'pinia'
import './axios'
import './style.css'
import App from './App.vue'
import { router } from './router.js';

const pinia = createPinia();
const app = createApp(App);
app.use(pinia)
app.use(router)


import 'bootstrap/dist/css/bootstrap.min.css'

app.mount('#app')

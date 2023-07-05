/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';
import { createApp } from 'vue';
import { createPinia, storeToRefs } from 'pinia';
import { createRouter, createWebHistory } from 'vue-router';
import App from './components/App.vue';
import Blank from './components/Right/Blank.vue';
import Right from './components/Right/Right.vue';
import { useUserStore } from './stores/user.js';

const app = createApp(App);


const routes = [
    {
        name: 'blank',
        path: '/',
        component: Blank
    },
    {
        name: 'conversation',
        path: '/conversation/:id',
        component: Right
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
})

app.use(createPinia());
app.use(router);
app.mount('#app');

const { username } = storeToRefs(useUserStore()); 
username.value = document.querySelector("#app").dataset.username
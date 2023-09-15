import { createRouter, createWebHistory } from 'vue-router';

// import NotFound from './pages/NotFound.vue';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            name: 'home',
            component: () => import('./components/ApartmentHome.vue')
        },

        {
            path: '/show/:id',
            name: 'apartment-show',
            component: () => import('./components/ApartmentShow.vue'),
            props: true
        },

        {
            path: '/login',
            name: 'login',
            component: () => import('./components/Login.vue'),
            props: true
        },

        {
            path: '/register',
            name: 'register',
            component: () => import('./components/Register.vue'),
            props: true
        },
        {
            path: '/dashboard',
            name: 'dashboard',
            component: () => import('./components/Dashboard.vue'),
            props: true
        }

    ]
});

export { router };

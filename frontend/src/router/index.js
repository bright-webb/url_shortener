import { createRouter, createWebHistory } from 'vue-router';

import Index from '../views/Index.vue';
import Redirect from '../components/Redirect.vue';

const routes = [
    {
        path: '/',
        name: 'Index',
        component: Index
    },
    {
        path: '/:code',
        component: Redirect
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
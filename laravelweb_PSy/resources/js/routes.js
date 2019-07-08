import Vue from 'vue';
import VueRouter from 'vue-router';

import Unauthenticated from '@/js/components/Unauthenticated'
import Authenticated from '@/js/components/Authenticated'
import Login from '@/js/components/Login'
import Floor from '@/js/components/Floor'
import Room from '@/js/components/Room'
import User from '@/js/components/User'
import Building from '@/js/components/Building'


import Home from '@/js/components/Home';
import About from '@/js/components/About';

Vue.use(VueRouter);

const routes = [
    {
        path:'/login', component: Unauthenticated,
        children: [
            { path: '/login', component: Login },
        ]
    },

    {
        path:'/', component: Authenticated,
        children: [
            { path: '/', component: Home },
            { path: '/floor', component: Floor },
            { path: '/room', component: Room },
            { path: '/user', component: User },
            { path: '/building', component: Building },
           
        ],
        meta: { requiresAuth: false }
    },


]

const router = new VueRouter({
    routes,
    hashbang: false,
    mode: 'history',
})

router.beforeEach(async (to, from, next) => {
   

    next()
})

export default router;
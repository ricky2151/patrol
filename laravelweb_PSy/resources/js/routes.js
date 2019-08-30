import Vue from 'vue';
import VueRouter from 'vue-router';

import Unauthenticated from '@/js/components/Unauthenticated'
import Authenticated from '@/js/components/Authenticated'
import Login from '@/js/components/Login'
import Floor from '@/js/components/Floor'
import Room from '@/js/components/Room'
import User from '@/js/components/User'
import Building from '@/js/components/Building'
import Time from '@/js/components/Time'
import StatusNode from '@/js/components/StatusNode'


import Home from '@/js/components/Home';
import About from '@/js/components/About';
import Logout from '@/js/components/Logout';

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
            { path: '/time', component: Time },
            { path: '/statusnode', component: StatusNode },
            { path: '/logout', component: Logout,},
           
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
   
    // check if the route requires authentication and user is not logged in
    if (to.matched.some(route => route.meta.requiresAuth)) {
        try {
            if(!localStorage.getItem('token')) {
                next({ path: '/login', replace: true})
                return
            }
        } catch (err) {
            return
        }
    }

    // if logged in redirect to dashboard
    if(to.path === '/login') {
        if(localStorage.getItem('token')) {
            next({ path: '/', replace: true})
            return
        }
    }

    next()
})

export default router;
import Vue from 'vue';
import VueRouter from 'vue-router';

import Unauthenticated from './components/Unauthenticated'
import Authenticated from './components/Authenticated'
import Login from './components/Login'
import Floor from './components/Floor'
import Room from './components/Room'
import User from './components/User'
import Building from './components/Building'
import Time from './components/Time'
import StatusNode from './components/StatusNode'
import Report from './components/Report'


import Home from './components/Home';
import About from './components/About';
import Logout from './components/Logout';

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
            { path: '/report', component: Report },
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
            if(!localStorage.getItem('token') || localStorage.getItem('token') === 'undefined') {
                next({ path: '/login', replace: true})
                return
            }
        } catch (err) {
            return
        }
    }

    // if logged in redirect to dashboard
    if(to.path === '/login') {
        if(localStorage.getItem('token') && localStorage.getItem('token') !== 'undefined') {
            next({ path: '/', replace: true})
            return
        }
    }

    next()
})

export default router;
import Vue from 'vue';
import VueRouter from 'vue-router';

import Unauthenticated from './components/Unauthenticated'
import Authenticated from './components/Authenticated'
import Login from './components/Login'
import Floor from './components/Floor'
import Room from './components/Room'
import User from './components/User'
import Building from './components/Building'
import Gateway from './components/Gateway'
import Time from './components/Time'
import StatusNode from './components/StatusNode'
import Report from './components/Report'
import ShiftToday from './components/ShiftToday'
import Iot from './components/Iot'


import Home from './components/Home';
import About from './components/About';
import Logout from './components/Logout';

Vue.use(VueRouter);

const routes = [
    {
        path:'/login', component: Unauthenticated,name: 'login',
        children: [
            { path: '/login', component: Login, },
        ]
    },

    {
        path:'/', component: Authenticated,
        children: [
            { path: '/', component: Home },
            { path: '/iot', component: Iot },
            { path: '/floor', component: Floor },
            { path: '/room', component: Room },
            { path: '/user', component: User },
            { path: '/building', component: Building },
            { path: '/gateway', component: Gateway },
            { path: '/time', component: Time },
            { path: '/statusnode', component: StatusNode },
            { path: '/report', component: Report },
            { path: '/ShiftToday', component: ShiftToday },
            { path: '/logout', component: Logout,},
           
        ],
        meta: { requiresAuth: true }
    },


]

const router = new VueRouter({
    routes,
    hashbang: false,
    mode: 'history',
})

router.beforeEach(async (to, from, next) => {
    console.log('masuk navigation guard');
    console.log('cek to : ' + to.path);
    console.log('cek from : ' + from.path);
    // check if the route requires authentication and user is not logged in
    if (to.matched.some(route => route.meta.requiresAuth)) {
        try {
            if(!localStorage.getItem('token') || localStorage.getItem('token') === 'undefined' || localStorage.getItem('token') === 'null') {
                next({ path: '/login', replace: true})
                return
            }
        } catch (err) {
            return
        }
    }

    // if logged in redirect to dashboard
    if(to.path === '/login') {
        if(localStorage.getItem('token') && localStorage.getItem('token') !== 'undefined' && localStorage.getItem('token') !== 'null') {
            next({ path: '/', replace: true})
            return
        }
    }

    next()
})

export default router;
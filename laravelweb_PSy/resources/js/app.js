/**
 * First, we will load all of this project's Javascript utilities and other
 * dependencies. Then, we will be ready to develop a robust and powerful
 * application frontend using useful Laravel and JavaScript libraries.
 */

 window.Vue = require('vue');
window.axios = require('axios');



import Vuetify from 'vuetify';
import Routes from './routes.js';

import Vuefs from 'vue-fullscreen';

import VueSwal from 'vue-swal'
import 'vuetify/dist/vuetify.min.css'
import colors from 'vuetify/es5/util/colors'
import PluginValidation from './plugin/PluginValidation'
import PatrolApp from './views/PatrolApp'

//import 'vuetify/dist/vuetify.min.css'


import VueGoogleCharts from 'vue-google-charts'

require('./css/custom/basic.css');

Vue.use(PluginValidation);
Vue.use(VueGoogleCharts)
Vue.use(Vuefs);
Vue.use(VueSwal);



Vue.use(Vuetify, {
    iconfont: 'md',
    // override colors
    theme: {
    	menu:colors.blue.darken4,
    }
});

const app = new Vue({
	el: '#app',
	router: Routes,
	components: {
        PatrolApp,
    },
});



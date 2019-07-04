/**
 * First, we will load all of this project's Javascript utilities and other
 * dependencies. Then, we will be ready to develop a robust and powerful
 * application frontend using useful Laravel and JavaScript libraries.
 */

import './bootstrap';
import Vue from 'vue';
import Vuetify from 'vuetify';
import Vuefs from 'vue-fullscreen';

//Route information for Vue Router
import Routes from '@/js/routes.js';

//component file
//import App from '@/js/views/App';

import PatrolApp from '@/js/views/PatrolApp'
import VueSwal from 'vue-swal'
import PluginValidation from '@/js/plugin/PluginValidation'
import BasicCss from '@/js/css/custom/basic.css'

Vue.use(Vuetify);
Vue.use(Vuefs);
Vue.use(VueSwal);
Vue.use(PluginValidation);
Vue.use(BasicCss);

const app = new Vue({
	el: '#app',
	router: Routes,
	components: {
        PatrolApp,
    },
});

export default app;

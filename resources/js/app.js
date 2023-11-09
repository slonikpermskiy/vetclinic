/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

// https://materialdesignicons.com/
import '@mdi/font/css/materialdesignicons.css'

// https://fonts.google.com/icons
import 'material-design-icons-iconfont/dist/material-design-icons.css';

import Vuetify from './plugins/vuetify'

import ProductsservicesguideComponent from './components/ProductsservicesguideComponent.vue';

import SalaryComponent from './components/SalaryComponent.vue';

import VueRouter from 'vue-router'


Vue.component('productsservicesguide-component', ProductsservicesguideComponent);

Vue.component('salary-component', SalaryComponent);

Vue.use(VueRouter);


// Define some routes
const routes = [
	{ path: '/test', redirect: '/test' },
]

// Create the router instance and pass the `routes` option
const router = new VueRouter({
	mode: 'history',
    routes
})

export default router;


window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';


//let token = document.head.querySelector('meta[name="csrf-token"]');

let token = document.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}


const app = new Vue({
	vuetify: Vuetify,
	router
}).$mount('#app'); 


window.$ = window.jQuery = require('jquery');
window.Vue = require('vue');

// Global Components
Vue.component('nav-bar', require('./Components/NavBar.vue'));

const app = new Vue({}).$mount('#appRoot');//


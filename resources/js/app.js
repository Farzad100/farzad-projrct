import Vue from 'vue';
import router from './src/router/index';
import store from './src/store/index';


/**
 * -----------------------
 * Globals
 */
require('./src/global/GlobalComponents');
require('./src/global/VueFilters');


/**
 * -----------------------
 * Styles
 */
import './src/assets/scss/style.scss';

/**
 * -----------------------
 * Mixins
 */
import ghestify from '@/global/Ghestify';
import mainInstance from '@/global/MainInstance';

window.vm = new Vue({
  mixins: [
    mainInstance,
    ghestify
  ],
  store,
  router
}).$mount('#app');

// Vue.config.devtools = false;
// Vue.config.debug = false;
// Vue.config.silent = true;

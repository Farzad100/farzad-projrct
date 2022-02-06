import Vue from 'vue';
import Vuex from 'vuex';
Vue.use(Vuex);

import createPersistedState from 'vuex-persistedstate';

// Modules
import dashboard from './modules/dashboard/index';
import auth from './modules/auth/index';

export default new Vuex.Store({
  modules:{
    dashboard,
    auth,
  },
  plugins: [createPersistedState({
    key: 'presistedData',
    paths: ['dashboard.role']
  })]
});

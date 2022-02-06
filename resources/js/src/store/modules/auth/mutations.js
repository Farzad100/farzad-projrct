import Cookies from 'js-cookie';

export default {

  SET_TOKEN(state, payload) {
    state.access_token = payload;
    Cookies.set('access_token', payload, { expires: 60 });
  },

  SET_ROLES(state, payload) {
    state.roles = payload;
    Cookies.set('roles', payload, { expires: 60 });
  },

  REMOVE_TOKEN(state) {
    state.access_token = null;
    Cookies.remove('access_token');
  }

};

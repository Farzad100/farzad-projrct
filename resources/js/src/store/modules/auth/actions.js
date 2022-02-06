import Auth from '@/api/auth';

export default {

  login: async ({ commit }, payload) => {
    const response = await Auth.login(payload);

    if (response) {
      if (response.data.status) {
        commit('SET_TOKEN', response.data.result.token);
        commit('SET_ROLES', response.data.result.roles);
        commit('dashboard/CHANGE_ROLE', response.data.result.roles[0], { root: true });
      }
      return response;
    }
  },

  staticLogin: async ({commit}, payload) => {
    commit('SET_TOKEN', payload.token);
    commit('SET_ROLES', payload.roles);
    commit('dashboard/CHANGE_ROLE', payload.roles[0], { root: true });
  },

  logout: async ({ commit }) => await commit('REMOVE_TOKEN')

};
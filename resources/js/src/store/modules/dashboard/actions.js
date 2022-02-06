export default {

  changeRole({commit}, payload) {
    commit('CHANGE_ROLE', payload);
  },

  inboxUpdate({commit}, payload) {
    commit('CHANGE_INBOX', payload);
  }
};
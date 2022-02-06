// Vuex
import { mapState, mapActions } from 'vuex';

export default {
  name: 'ChangeRole',

  computed: {
    ...mapState('dashboard', ['role']),
    ...mapState('auth', ['roles'])
  },

  watch: {
    roles() {
      this.$forceUpdate();
    }
  },

  methods: {
    ...mapActions('dashboard', ['changeRole'])
  }
};

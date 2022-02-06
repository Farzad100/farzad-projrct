import StatusColors from '@/global/StatusColors';
import { mapState } from 'vuex';

export default {

  name: 'OrderCard',

  props: {
    data: {
      type: Object,
    },

    role: {
      type: String,
    },

    isAdmin: {
      type: Boolean
    },

    skLoading: {
      type: Boolean
    }
  },

  mixins: [StatusColors],

  computed: {
    ...mapState('dashboard', ['role'])
  }

};

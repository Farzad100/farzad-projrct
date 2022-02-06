import { mapState } from 'vuex';

export default {
  name: 'ContractDownload',

  props: {
    cDlOnly: {
      type: Boolean
    },

    data: {
      type: Object
    }
  },

  computed: {
    ...mapState('dashboard', ['role'])
  }
};

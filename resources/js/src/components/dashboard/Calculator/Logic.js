import ghestify from '@/global/Ghestify';

export default {
  name: 'Calculator',

  mixins: [ghestify],

  data() {
    return {
      modal: false,
      loading: true,
      prePaymentEditing: false
    };
  },

  created() {
    this.getGlobalsData()
      .then(() => {
        this.loading = false;
      });
  },

  methods: {
    openModal() {
      this.modal = true;
    }
  }
};
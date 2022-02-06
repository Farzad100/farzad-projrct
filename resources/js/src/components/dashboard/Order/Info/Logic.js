import StatusColors from '@/global/StatusColors';
import { mapState } from 'vuex';

/**
 * API Services
 */
import Orders from '@/api/orders';

export default {
  name: 'OrderInfo',

  mixins: [StatusColors],

  props: {
    data: {
      type: Object,
      required: true
    }
  },

  computed: {
    ...mapState('dashboard', ['role']),

    /**
     * Show cancel button
     * condition
     */
    showCancel() {
      switch (this.data.status) {
      case 'draft':
      case 'submitted':
      case 'docs_uploaded':
      case 'upload_secondary':
      case 'check_secondary':
      case 'wait_for_cheques':
        return true;
      default:
        return false;
      }
    }
  },

  data() {
    return {
      order: {
        type:
          this.data.shop_id || this.data.organ_id
            ? {
              is_shop: !!this.data.shop_id,
              is_organ: !!this.data.organ_id,
              name: this.data.shop_id
                ? this.data.shop.name
                : this.data.organ.name || this.data.organ.fame
            }
            : null
      },

      /**
       * Cancel modal
       */
      cancelModal: false,
      cancelReason: ''
    };
  },

  methods: {
    /**
     * Cancel the order by user
     */
    cancel() {
      this.$refs.submit.loading('start');

      Orders.cancel(this.$route.params.id, {
        reason: this.cancelReason
      })
        .then(r => {
          this.$refs.submit.loading('end');
          this.cancelModal = false;
          if (r.data.status) {
            this.$parent.loadData();
          } else {
            this.$alerts.show({
              msg: r.data.error.name,
              type: 'danger',
              style: 'float'
            });
          }
        })
        .catch(e => {
          this.$refs.submit.loading('end');
          this.$alerts.errHandle(e);
        });
    }
  }
};

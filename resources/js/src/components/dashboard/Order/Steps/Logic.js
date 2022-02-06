import { mapState } from 'vuex';

import Order from '@/api/orders';

export default {
  name: 'OrderSteps',

  props: {
    orderData: {
      type: Object,
      required: true
    },
    type: {
      type: String
    }
  },

  data() {
    return {
      nid: '',
      birth: ''
    };
  },

  components: {
    chequesInfo: () => import('./ChequesInfo/Index.vue'),
    ContractDownload: () => import('./ContractDownload/Index.vue'),
    CardStatus: () => import('./CardStatus/Index.vue'),
    Invoice: () => import('./Invoice/Index.vue'),
    InvoiceInquiry: () => import('./InvoiceInquiry/Index.vue'),
    ExtraCharge: () => import('./ExtraCharge/Index.vue')
  },

  computed: {
    ...mapState('dashboard', ['role'])
  },

  methods: {
    stepDetector(step, isPending) {
      if (typeof step === 'string') {
        if (step === this.orderData.status)
          return isPending ? '_pending' : '_active';
      } else if (typeof step === 'object') {
        for (const i of step) {
          if (i === this.orderData.status)
            return isPending ? '_pending' : '_active';
        }
      }
    },

    loadData() {
      this.$parent.loadData();
    },

    actionsHandle(payload) {
      this.$parent.actionsHandle(payload);
    },

    regEssentials() {
      this.$refs.regEssentials.loading('start');

      Order.regEssentials(
        {
          nid: this.nid,
          birth: this.birth
        },
        this.orderData.oid
      )
        .then(r => {
          if (r.data.status) {
            this.$alerts.show({
              msg: 'اطلاعات ثبت شد',
              type: 'success',
              style: 'float'
            });

            this.$parent.loadData();
          } else {
            this.$alerts.show({
              msg: r.data.error.message,
              type: 'danger',
              style: 'float'
            });

            this.loading = false;
          }
        })
        .catch(e => {
          this.$alerts.errHandle(e);
        })
        .finally(() => {
          this.$refs.regEssentials.loading('end');
          this.loadData();
        });
    }
  }
};

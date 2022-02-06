import Orders from '@/api/orders';

export default {
  name: 'InvoiceInquiry',

  props: {
    data: {
      type: Object,
      required: true
    }
  },

  data() {
    return {
      discountCode: ''
    };
  },

  methods: {
    /**
     * Payment button
     */
    pay() {
      this.$refs.pay.loading('start');
      Orders.payInquiryInvoice(this.$route.params.id, {
        code: this.discountCode
      })
        .then(r => {
          window.location.replace(r.data.result.url);
        })
        .catch(e => {
          this.$alerts.errHandle(e);
        })
        .finally(() => {
          this.$refs.pay.loading('end');
        });
    },

    checkCode() {
      this.$refs.discount.loading('start');

      Orders.check
        .invoiceInquiryDiscountCode({
          code: this.discountCode
        })
        .then(r => {
          if (r.data.status) {
            console.log('GOOD');
          } else {
            this.$alerts.show({
              msg: r.data.error.message,
              type: 'danger',
              style: 'float'
            });
          }
        })
        .finally(() => {
          this.$refs.discount.loading('end');
        });
    }
  }
};

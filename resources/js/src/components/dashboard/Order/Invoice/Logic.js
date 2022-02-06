import Orders from '@/api/orders';

export default {
  name: 'Invoice',

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
      Orders.pay(this.$route.params.id, {
        code: this.discountCode
      })
        .then(r => {
          window.location.replace(r.data.result.url);
        })
        .catch(e => {
          this.$alerts.errHandle(e);
        });
    },

    checkCode() {
      this.$refs.discount.loading('start');

      Orders.check.discountCode({
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
          
          this.$refs.discount.loading('end');
        });
    }
  }
};
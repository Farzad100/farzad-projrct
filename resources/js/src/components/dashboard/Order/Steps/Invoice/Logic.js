import { api, wrapper } from '@/global/services';

export default {
  name: 'Invoice',

  data() {
    return {
      orderDiscount: ''
    };
  },

  methods: {
    async checkDiscount() {
      this.$refs.discount.loading('start');

      const { data } = await wrapper(
        api.Orders.check.discountCode(this.$route.params.id, {
          code: this.orderDiscount
        }),
        'مشکلی در بررسی کد تخفیف پیش آمده است'
      );

      if (data) {
        this.$refs.discount.loading('end');
        const { status, result } = data;

        if (status) {
          this.discounted = result.new_amount;
        } else {
          this.$alerts.show({
            msg: data.error.message,
            type: 'danger',
            style: 'float'
          });
        }
      }
    },

    async pay() {
      this.$refs.pay.loading('start');

      const { data } = await wrapper(
        api.Orders.pay(this.$route.params.id, {
          code: this.orderDiscount
        }),
        'مشکلی در پرداخت پیش آمده است'
      );

      if (data) {
        this.$refs.pay.loading('end');

        const { status } = data;

        if (status) {
          window.location.replace(data.result.url);
        }
      }

    },
  }
};

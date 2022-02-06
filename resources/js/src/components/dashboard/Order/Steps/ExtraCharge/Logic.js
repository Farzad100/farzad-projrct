import { api, wrapper } from '@/global/services';

export default {
  name: 'ExtraCharge',

  data() {
    return {
      extraAmount: 150000
    };
  },

  methods: {
    async payExtra() {
      this.$refs.payExtra.loading('start');

      const { data } = await wrapper(
        api.Orders.payExtra(this.$route.params.id, {
          amount: this.extraAmount
        }),
        'مشکلی در درخواست شارژ اضافی پیش آمده است'
      );

      if (data) {
        this.$refs.payExtra.loading('end');
        const { status } = data;

        if (status) {
          window.location.replace(data.result.url);
        }
      }
    },
  }
};
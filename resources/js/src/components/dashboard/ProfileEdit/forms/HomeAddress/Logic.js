import { api, wrapper } from '@/global/services';
import stateList from '@/data/States';

export default {
  name: 'HomeAddress',

  data() {
    return {
      address: {
        home: {
          address: '',
          city: '',
          editable: true,
          phone: '',
          postal_code: '',
          state: '',
        },
      },

      stateList
    };
  },

  created() {
    this.loadData();
  },

  methods: {
    async loadData() {
      const { data } = await wrapper(
        api.Account.get.addresses(this.$route.params.id),
        'مشکلی در دریافت اطلاعات آدرس منزل پیش آمد'
      );

      if (data) {
        const { result } = data;
        const address = result.addresses;

        const home = address.filter(obj => obj.type === 'home');

        if (home[0]) this.address.home = home[0];
        if (result.is_filled) this.$parent.allowToBack.push('1');
      }
    },

    async saveAddress() {
      this.$refs.submit.loading('start');

      const home = {
        ...this.address.home,
        type: 'home'
      };

      const { data} = await wrapper(
        api.Account.save.addresses(home),
        'آدرس منزل ذخیره نشد - دوباره امتحان کنید'
      );

      this.$refs.submit.loading('end');

      if (data) {
        const { status } = data;

        if (status) {
          this.$alerts.show({
            msg: 'آدرس‌ها با موفقیت ویرایش شدند',
            type: 'success',
            style: 'float'
          });
        } else {
          this.$alerts.show({
            msg: data.error.message || 'مشکلی در ذخیره کردن این آدرس پیش آمده است',
            type: 'danger',
            style: 'float'
          });
        }
          
        this.$parent.getFormStatus();
      }
    },
  }
};

import { api, wrapper } from '@/global/services';
import stateList from '@/data/States';

export default {
  name: 'HomeAddress',

  data() {
    return {
      address: {
        work: {
          address: '',
          city: '',
          editable: true,
          phone: '',
          postal_code: '',
          state: '',
        },
      },

      stateList,
    };
  },

  created() {

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

        const work = address.filter(obj => obj.type === 'work');

        if (work[0]) this.address.work = work[0];
      }
    },

    async saveAddress() {
      const work = {
        ...this.address.work,
        type: 'work'
      };

      const { data} = await wrapper(
        api.Account.save.addresses(work),
        'آدرس محل کار ذخیره نشد - دوباره امتحان کنید'
      );

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

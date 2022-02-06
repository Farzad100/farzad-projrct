import { api, wrapper } from '@/global/services';

export default {
  name: 'UserInfo',

  data() {
    return {
      userInfo: {},
    };
  },

  created() {
    this.loadData();
  },

  methods: {
    async loadData() {
      const { data } = await wrapper(
        api.Account.get.info(this.$route.params.id)
      );

      if (data) {
        const { result } = data;
        this.userInfo = result;

        if (result.is_filled) {
          this.$parent.allowToBack.push('1');
        }
      }
    },

    async saveInfo() {
      this.$refs.submit.loading('start');

      const { data } = await wrapper(
        api.Account.save.info({
          nid: this.userInfo.nid,
          email: this.userInfo.email,
          birth: this.userInfo.birth
        })
      );

      this.$refs.submit.loading('end');

      if (data) {
        const { status } = data;

        if (status) {
          this.$alerts.show({
            msg: 'اطلاعات با موفقیت ویرایش شد',
            type: 'success',
            style: 'float'
          });
        } else {
          this.$alerts.show({
            msg: 'مشکلی در ذخیره اطلاعات پیش آمده است',
            type: 'danger',
            style: 'float'
          });
        }
        
        this.$parent.getFormStatus();
        this.infoSaveLoading = false;
      }
    },
  }
};

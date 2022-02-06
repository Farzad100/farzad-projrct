import { api, wrapper } from '@/global/services';

export default {
  name: 'Extra',

  data() {
    return {
      formModel: {},
      formData: [],
    };
  },

  created() {
    this.loadData();
  },

  methods: {
    async loadData() {
      const { data } = await wrapper(
        api.custom.get('/profile/meta/edit'),
        'مشکلی در دریافت فرم اطلاعات تکمیلی بوجود آمد'
      );

      if (data) {
        const { result } = data;
        this.formData = result;
      }
    },

    async saveData() {
      this.$refs.metaSave.loading('start');
      
      const { data } = await wrapper(
        api.custom.post('/profile/meta/edit', this.formModel),
        'مشکلی در ثبت اطلاعات پیش آمده'
      );

      this.$refs.metaSave.loading('end');

      if (data) {
        this.$parent.getFormStatus();
        const { status } = data;

        if (status) {
          this.$alerts.show({
            msg: 'اطلاعات با موفقیت ذخیره شد',
            type: 'success',
            style: 'float'
          });
        } else {
          const { message } = data.error;
          this.$alerts.show({
            msg: message,
            type: 'danger',
            style: 'float'
          });
        }
      }
    },

    onSubmitMeta () {
      this.$refs.fb.$refs.observer.validate().then(success => {
        if (!success) {
          this.$alerts.show({
            msg: 'همه مواردی که دایره قرمز دارند را پر کنید',
            type: 'danger',
            style: 'float'
          });

          return;
        }

        this.saveData();
      });
    },
  }
};

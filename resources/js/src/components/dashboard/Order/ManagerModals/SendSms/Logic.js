import { api, wrapper } from '@/global/services';

export default {
  name: 'SendSMS',

  data() {
    return {
      smsModal: false,
      smsText: '',
    };
  },

  methods: {
    async sendMessage() {
      this.$refs.sms.loading('start');

      const { data } = await wrapper(
        api.Admin.orders.sendMessage(
          this.$route.params.id,
          { message: this.smsText }
        ),
        'مشکلی در ارسال پیامک پیش آمد'
      );

      this.$refs.sms.loading('end');

      if (data) {
        this.$alerts.show({
          msg: 'پیامک با موفقیت ارسال شد',
          type: 'success',
          style: 'float'
        });

        this.smsModal = false;
      }
    },

    openModal() {
      this.smsModal = true;
    }
  }
};

import FormValidator from '@/global/FormValidator';
import Auth from '@/api/auth';

export default {
  name: 'GetMobileNumber',

  mixins: [FormValidator],

  props: {
    text: {
      type: String
    },

    who: {
      type: String
    },

    type: {
      type: String,
      required: true
    }
  },

  data() {
    return {
      mobileNumber: '',
    };
  },

  methods: {
    handleSubmit() {
      this.$refs.submit.loading('start');
      const customEndpoint = this.type === 'shop_order' ? 'sendOtpShop' : 'sendOtp';
      
      Auth[customEndpoint]({ username: this.mobileNumber, type: this.type, route: this.$route.name })
        .then(response => {
          const { status } = response.data;

          if (status) {
            this.$emit('formSubmit', {
              mobile: this.mobileNumber,
              trackId: response.data.result.track_id
            });
          } else {
            this.$alerts.show({
              msg: response.data.error.message,
              type: 'danger',
              style: 'float'
            });
          }

          this.$refs.submit.loading('end');
        })
        .catch(error=> { 
          this.$refs.submit.loading('end');
          this.$alerts.errHandle(error);
        });
    }
  }
};

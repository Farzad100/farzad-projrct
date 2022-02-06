import FormValidator from '@/global/FormValidator';
import Auth from '@/api/auth';

export default {
  name: 'UserRegisterBySeller',

  mixins: [FormValidator],

  props: {
    otpData: {
      type: Object,
      required: true
    }, 
    externalRouting: {
      type: Boolean
    }
  },

  data() {
    return {
      password: '',
      nid: '',
      birth: '',
      isPassShow: false,
      loading: false,
    };
  },

  methods: {
    handleSubmit() {
      this.loading = true;
      Auth.register({
        username: this.otpData.mobile,
        tracker: this.otpData.tracker,
        nid: this.nid, 
        birth: this.birth, 
        mode: 'shop',
      })
        .then(response => { 
          this.$emit('formSubmit', {
            mobile: this.otpData.mobile,
            user: response.data.result.user,
            user_exists: response.data.result.user_exists
          });
        })
        .catch(error=> { 
          this.loading = false;
          this.$alerts.errHandle(error);
        });

    },
    passTrick(e) {
      e.target.removeAttribute('readonly');
    },
    togglePassShow() {
      const input = this.$refs.password;
      let attr = input.getAttribute('type');

      if (attr === 'password') {
        input.setAttribute('type', 'text');
        this.isPassShow = true;
      } else {
        input.setAttribute('type', 'password');
        this.isPassShow = false;
      }
    }
  },
};

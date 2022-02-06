import FormValidator from '@/global/FormValidator';
import Auth from '@/api/auth';
import Cookies from 'js-cookie';

export default {
  name: 'UserRegister',

  mixins: [FormValidator],

  props: {
    otpData: {
      type: Object,
      required: true
    },
    noUtm: {
      type: Boolean
    },
    externalRouting: {
      type: Boolean
    },
    who: {
      type: Boolean
    }
  },

  data() {
    return {
      isPassShow: false,
      loading: false,
      registerForm: {
        password: '',
        utm: Cookies.get('utm') ? JSON.parse(Cookies.get('utm')) : {},
        rfrr: Cookies.get('rfrr') || '',
        nid: '',
        birth: ''

      }
    };
  },

  methods: {
    handleSubmit() {
      this.loading = true;
      
      Auth.register({
        username: this.otpData.mobile,
        tracker: this.otpData.tracker,
        ...this.registerForm
      })
        .then(r => {
          if (r.data.status) {
            if (!this.externalRouting) {
              this.$router.push({
                name: 'login'
              }).then(r => r);
            }
  
            this.$emit('formSubmit', {
              mobile: this.otpData.mobile,
              user: r.data.result.user,
              user_exists: r.data.result.user_exists
            });
          } else {
            this.$alerts.show({
              msg: r.data.error.message,
              type: 'danger',
              style: 'float'
            });

            this.loading = false;
          }
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

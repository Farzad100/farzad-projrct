import FormValidator from '@/global/FormValidator';
import Auth from '@/api/auth';
import { mapActions } from 'vuex';

export default {
  name: 'UserRegister',

  mixins: [FormValidator],

  props: {
    otpData: {
      type: Object,
      required: true
    }
  },

  data() {
    return {
      password: '',
      isPassShow: false,
      loading: false
    };
  },

  methods: {
    ...mapActions('auth', ['staticLogin']),

    handleSubmit() {
      this.loading = true;
      Auth.reset({
        username: this.otpData.mobile,
        tracker: this.otpData.tracker,
        password: this.password
      })
        .then(r => {
          const { user } = r.data.result;
          this.staticLogin(user)
            .then(() => {
              this.$router.push({
                name: 'dash-user',
                query: { passChanged: true }
              }).then(r => r);
            });
        })
        .catch(error => { 
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

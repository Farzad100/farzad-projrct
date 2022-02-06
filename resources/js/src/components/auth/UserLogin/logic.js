import FormValidator from '@/global/FormValidator';
import { mapActions } from 'vuex';
import Cookies from 'js-cookie';

export default {
  name: 'UserLogin',

  mixins: [FormValidator],

  data() {
    return {
      mobileNumber: '',
      password: '',
      isPassShow: false,
      loading: false
    };
  },

  methods: {
    ...mapActions('auth', ['login']),

    handleSubmit() {
      this.loading = true;

      this.login({
        username: this.mobileNumber,
        password: this.password
      })
        .then(response => {
          if (!response.data.status) {
            this.$alerts.show({
              msg: 'شماره موبایل یا رمز عبور نادرست است',
              type: 'danger',
              style: 'float'
            });
            this.loading = false;
          } else {
            const redirect_path = Cookies.get('rd_route_path');

            if (redirect_path) {
              this.$router.push({
                path: redirect_path
              }).then(r => r);
            } 
            else {
              this.$router.push({
                name: 'dash-user'
              }).then(r => r);
            }
          }
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

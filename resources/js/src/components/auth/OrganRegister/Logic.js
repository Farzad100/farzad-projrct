import FormValidator from '@/global/FormValidator';
import stateList from '@/data/States';
import Organ from '@/api/organ';
import Account from '@/api/account';
import Cookies from 'js-cookie';
import { mapState, mapActions } from 'vuex';

export default {
  name: 'OrganRegisterForm',

  mixins: [FormValidator],

  data() {
    return {
      helpShow: false,
      
      form: {
        name: '',
        fame: '',
        employees: '',
        about: '',
        age: '',
        website: '',
        email: '',
        phone: '',
        phone_direct: '',
        state: '',
        city: '',
        postal_code: '',
        address: ''
      },
      loading: false,
      submitDone: false,
      stateList
    };
  },

  computed: {
    ...mapState('dashboard', ['role']),
    ...mapState('auth', ['roles'])
  },

  created() {
    this.loadData();
  },

  methods: {
    ...mapActions('dashboard', ['changeRole']),

    register() {
      this.loading = true;

      Organ.register(this.form)
        .then(r => {
          if (r.data.status) {
            this.submitDone = true;

            Account.get.info().then(r => {
              Cookies.set('roles', r.data.result.roles, { expires: 60 });
              this.changeRole('organ');

              setTimeout(() => {
                this.$router.push({
                  name: 'dash-organ'
                });
              }, 3000);
            });
          } else {
            this.$alerts.show({
              msg: r.data.error.message,
              type: 'danger',
              style: 'float'
            });
          }

          this.loading = false;
        })
        .catch(e => {
          this.$alerts.errHandle(e);
          this.loading = false;
        });
    },

  }
};

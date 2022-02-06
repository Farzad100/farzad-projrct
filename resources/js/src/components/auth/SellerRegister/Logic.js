import FormValidator from '@/global/FormValidator';
import stateList from '@/data/States';
import Seller from '@/api/seller';
import Cookies from 'js-cookie';
import Account from '@/api/account';

import { mapState, mapActions } from 'vuex';

export default {
  name: 'SellerRegisterForm',

  mixins: [FormValidator],

  data() {
    return {
      helpShow: false,

      form: {
        ownershipType: 'real',
        name: '',
        category: 'digital',
        type: 'offline',
        website: '',
        email: '',
        phone: '',
        phone_direct: '',
        state: '',
        city: '',
        address: '',
        about: '',
        postal_code: '',

        company_name: '',
        company_rn: '',
        company_nic: '',
        company_ec: '',
        company_postal_code: '',
        company_state: '',
        company_city: '',
        company_address: '',
        company_phone: '',
        ceo_fname: '',
        ceo_lname: '',
        ceo_nid: ''
      },
      stateList,
      loading: false,
      submitDone: false
    };
  },

  computed: {
    ...mapState('dashboard', ['role']),
    ...mapState('auth', ['roles'])
  },

  created() {},

  methods: {
    ...mapActions('dashboard', ['changeRole']),

    register() {
      this.loading = true;

      Seller.register(this.form)
        .then(r => {
          if (r.data.status) {
            this.submitDone = true;

            Account.get.info().then(r => {
              Cookies.set('roles', r.data.result.roles, { expires: 60 });
              this.changeRole('shop');

              setTimeout(() => {
                this.$router.push({
                  name: 'dashboard',
                  params: { role: this.type === 'offline' ? 'shop' : 'eshop' }
                });
              }, 2000);
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

    extractHostname(url) {
      let hostname;

      hostname = url.replace('www.', '');
      hostname = hostname.replace('https://', '');
      hostname = hostname.replace('http://', '');
      if (hostname.indexOf('/') > -1) hostname = hostname.split('/')[0];
      if (hostname.indexOf('.') > -1) {
        let l = hostname.split('.').length;
        if (l > 2)
          hostname = hostname.split('.')[1] + '.' + hostname.split('.')[2];
      }
      return hostname;
    }

    // loadData() {
    //   Account.get.info()
    //     .then(r => {
    //       this.form.fname = r.data.result.fname;
    //       this.form.lname = r.data.result.lname;
    //       this.form.nid = r.data.result.nid;
    //     })
    //     .catch(e => {
    //       this.$alerts.errHandle(e);
    //       this.loading = false;
    //     });
    // }
  }
};

import Cookies from 'js-cookie';
import { mapState } from 'vuex';
import { HasContain } from '@/global/Functions';

/**
 * API Services
 */
import Orders from '@/api/orders';
import Custom from '@/api/custom';

/**
 * Global functions
 */
import StatusColors from '@/global/StatusColors';
import FormValidator from '@/global/FormValidator';

import orderSteps from '@/components/dashboard/Order/Steps/Index';

export default {
  name: 'OrderSingle',

  components: {
    orderSteps,
    orderStatus: () => import('@/components/dashboard/Order/Status/Index'),
    orderInfo: () => import('@/components/dashboard/Order/Info/Index'),
    docsTypeNote: () => import('@/components/dashboard/Docs/TypeNote/Index'),
  },

  mixins: [FormValidator, StatusColors],

  data() {
    return {
      order: {},
      docs: [],
      ghests: [],
      loading: true,
      interval: null,

      /**
       * CTA action
       */
      submitCtaLoading: false,
    };
  },

  computed: {
    ...mapState('dashboard', ['role']),
  },

  created() {
    this.loadData();
  },

  mounted() {
    this.interval = setInterval(() => {
      this.loadData();
    }, 60000);
  },

  beforeDestroy () {
    clearInterval(this.interval);
  },

  methods: {
    actionsHandle(payload) {
      const { type, modal_name, endpoint, method, confirm_message } = payload;

      if (type === 'confirm') {
        if (confirm(confirm_message ? confirm_message : 'آیا مطمئن هستید؟')) {
          Custom[method](endpoint)
            .then(r => {
              if (r.data.status) {
                this.loadData();
              }
              this.onChangeBtn = payload.e;
            })
            .catch(e => {
              this.$alerts.errHandle(e);
              this.onChangeBtn = payload.e;
            });
        } else {
          this.onChangeBtn = payload.e;
        }
      }

      if (type === 'straight') {
        Custom[method](endpoint)
          .then(r => {
            if (r.data.status) {
              window.location = r.data.result.url;
            }
          })
          .catch(e => {
            this.$alerts.errHandle(e);
          });
      }

      if (type === 'modal') {
        this[modal_name] = true;
        this.currentModalEndpoint = endpoint;
        this.currentModalMethod = method;

        if (modal_name === 'editModal') {
          this.editModalLoading = true;

          Custom.get(endpoint)
            .then(r => {
              const { result } = r.data;
              this.editModalData = result;
              this.editModalLoading = false;
            })
            .catch(e => {
              this.$alerts.errHandle(e);
              this.editModalLoading = false;
            });
        }
      }
    },

    loadData() {
      Orders.get
        .single(this.$route.params.id)
        .then(r => {
          this.loading = false;
          this.order = r.data.result;

          const d = r.data.result;


          if (!Cookies.get('first')) {
            Cookies.set('first', 'pashmak');
            window.location.reload();
          }
          
          if (HasContain(d.status, ['cycle_cheque', 'cycle_epay', 'completed'])) {
            Orders.get
              .ghests(this.$route.params.id)
              .then(r => {
                this.ghests = r.data.result;
              })
              .catch(e => {
                this.$alerts.errHandle(e);
              });
          }
        })
        .catch(() => {
          this.loading = false;
          this.$alerts.show({
            msg:
              'مشکلی در بارگذاری اطلاعات سفارش پیش آمده است! دوباره تلاش کنید یا با پشتیبانی تماس بگیرید',
            type: 'danger',
            style: 'static'
          });
        });
    },

    pay() {
      this.payLoading = true;

      Orders.pay(this.$route.params.id, {
        code: this.orderDiscount
      })
        .then(r => {
          window.location.replace(r.data.result.url);
        })
        .catch(e => {
          this.discountLoading = false;
          this.payLoading = false;
          this.$alerts.errHandle(e);
        });
    },
  }
};

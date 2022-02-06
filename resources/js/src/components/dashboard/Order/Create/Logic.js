// Components
import TabButtonGroup from '@/components/overall/TabButtonGroup/Index';
import AmountRanger from '@/components/dashboard/AmountRanger/Index';

// Api
import Orders from '@/api/orders';
import Seller from '@/api/seller';

// State
import { mapState } from 'vuex';

export default {
  name: 'CreateOrder',

  components: {
    TabButtonGroup,
    AmountRanger,
    SendOtp: () => import('@/components/auth/SendOtp/Index.vue'),
    CheckOtp: () => import('@/components/auth/CheckOtp/Index.vue'),
    UserRegisterBySeller: () => import('@/components/auth/UserRegisterBySeller/Index'),
    Calculator: () => import('@/components/dashboard/Calculator/Index')
  },

  data() {
    return {
      organCode: '',
      timeout: null,
      checkDone: false,
      step: 1,
      otpData: '',
      checkingOrgCode: false,
      checkingOrgOpt: {},
      refund: { months: 0, cheques: 0 },
      order: {
        amount: 1500000,
        gain: null,
        status: null,
        rpa: null,
        custom_card_msg: null,
        important_msg: null,
        rpao: null,
        isOrgan: false,
        type: 'individual',
        manual: false,
        prepayment: '',
        product: ''
      },

      loading: false,
      loadingPage: false, 

      maxAllowedAmount: null,
      maxAllowedGhest: null,

      maxes: {
        first: '',
        second: '',
        third: ''
      },

      allowToCreate: null,
      allowCreateMsg: '',
      allowCreateMsgStatus: '',

      tracker: '',
      mobile: '',

      shopStatus: '',

      create_control: {}
    };
  },

  computed: {
    ...mapState('dashboard', ['role']),

    allowCreate() {
      if (this.refund.months === 0 || this.refund.cheques == 0) {
        return true;
      } else {
        return false;
      }
    }
  },

  watch: {
    role(val) {
      if (val === 'user') {
        this.checkLimit();
        this.step = 4;
      }

      if (val === 'shop') {
        this.step = 1;
        this.allowToCreate = true;
      }

      if (val === 'organ') {
        this.allowToCreate = false;
        this.allowCreateMsg = 'شما به عنوان یک ارگان امکان ثبت سفارش را ندارید';
      }
    },

    step(val) {
      if (val === 4 && this.role === 'shop') {
        this.checkLimit(this.mobile);
      }
    },

    'order.isOrgan'() {
      if (this.order.isOrgan) {
        setTimeout(() => {
          this.$refs.organCde.focus();
        }, 200);
      } else if (!this.order.isOrgan && this.checkingOrgOpt.type === 'success') {
        this.checkingOrgOpt = {};
        this.organCode = '';
        this.checkLimit();
        this.refund = { months: 6, cheques: 6 };
        setTimeout(() => {
          if(this.order.amount > parseInt(this.maxAllowedAmount)) this.order.amount = 1500000;
        }, 500);
        
      }
    },

    'order.amount'() {
      if (this.ghestify(this.order, this.refund).ghest >= this.maxAllowedGhest) {
        this.$refs.refundSelector.$children.forEach(i => {
          i.$el.querySelector('input').checked = false;
          this.refund = { months: 0, cheques: 0 };
        });
      }
    }
  },

  methods: {
    ghestify(order, refund) {
      order.amount = parseInt(order.amount);
      order.gain = parseFloat(order.gain);
      refund.cheques = parseInt(refund.cheques);
      refund.months = parseInt(refund.months);

      let up, down, divide, k, n, y, res, prepayment, ghest;

      if (order.manual) {
        prepayment = parseInt(order.prepayment);
      } else {
        prepayment = Math.ceil((order.rpa * order.amount) / 1000) * 1000;
      }

      k = order.gain + 1;
      n = refund.months;
      y = order.amount - prepayment;
      if (refund.months === 2 * refund.cheques) {
        up = y * k ** n * (k ** 2 - 1);
      } else up = y * k ** n * (k - 1);

      down = k ** n - 1;
      down === 0 ? (divide = 0) : (divide = up / down);
      res = Math.ceil(divide / 5000) * 5000;
      res > 1000 ? (ghest = res) : (ghest = 0);

      order.ghest = ghest;
      order.payback = order.ghest * refund.cheques;
      order.prepayment = prepayment;
      order.total = order.payback + order.prepayment;

      return order;
    },

    checkOrgCode() {
      this.checkingOrgCode = true;

      Orders.check
        .code(this.organCode)
        .then(r => {
          this.checkingOrgCode = false;

          if (r.data.status) {
            this.checkingOrgOpt = {
              type: 'success',
              msg: 'کد سازمانی معتبر است'
            };
            this.checkDone = true;

            this.checkLimit(this.organCode);
          } else {
            this.checkingOrgOpt = {
              type: 'error',
              msg: r.data.error.message
            };
          }
        })
        .catch(() => {
          this.checkingOrgCode = false;
        });
    },

    createOrder() {
      this.loading = true;
      let creator;
      this.role === 'shop' ? (creator = 'shop') : (creator = '');
      Orders.add(
        {
          amount: this.order.amount,
          prepayment: this.order.prepayment,
          product: this.order.product,
          months: this.refund.months,
          cheques: this.refund.cheques,
          organ_code: this.organCode,
          checkOrgan: this.order.type != 'individual',
          tracker: this.tracker,
          username: this.mobile
        },
        creator
      )
        .then(r => {
          this.loading = false;

          if (r.data.status) {
            this.$router.push({
              name: 'dash-order-single',
              params: {
                oid: r.data.result.oid,
                role: this.role
              }
            });
          } else {
            this.$alerts.show({
              msg: r.data.error.message,
              type: 'danger',
              style: 'float'
            });
          }
        })
        .catch(e => {
          this.loading = false;
          this.$alerts.errHandle(e);
        });
    },

    checkLimit(mobile = null) {
      this.loadingPage = true;
      let mode;

      if (mobile && mobile.length >= 10) {
        mode = 'shopy';
      } else if (mobile && mobile.length < 10 && mobile.length > 3) {
        mode = 'organy';
        //use organCode in mobile field.
      } 

      if (mode === 'shopy') {
        Orders.check
          .shopLimit(mobile)
          .then(r => {
            this.loadingPage = false;
            this.allowToCreate = r.data.status;

            this.create_control = r.data.result.create_control;
            
            if (r.data.status) {
              let d = r.data.result; 

              this.maxAllowedAmount = d.max_allowed_amount;
              this.maxAllowedGhest = d.max_allowed_ghest;

              this.maxes.first = d.max_amount_first;
              this.maxes.second = d.max_amount_second;
              this.maxes.third = d.max_amount_third;

              this.order.rpa = d.rpa;
              this.order.gain = d.gain;
              this.order.custom_card_msg = d.custom_card_msg;
              this.order.important_msg = d.important_msg;
            } else {
              this.allowCreateMsg = r.data.error.message;
              this.allowCreateMsgStatus = r.data.error.name;
            }
          })
          .catch(e => {
            this.loadingPage = false;
            this.$alerts.errHandle(e);
          });
      } else if (mode === 'organy') {
        Orders.check
          .organLimit(mobile)
          .then(r => {
            this.loadingPage = false;
            this.allowToCreate = r.data.status;

            this.create_control = r.data.result.create_control;

            if (r.data.status) {
              let d = r.data.result;

              this.maxAllowedAmount = d.max_allowed_amount;
              this.maxAllowedGhest = d.max_allowed_ghest;

              this.maxes.first = d.max_amount_first_organ_level_2;
              this.maxes.second = d.max_amount_second_organ_level_2;
              this.maxes.third = d.max_amount_second_organ_level_2;

              this.order.rpa = d.rpa;
              this.order.gain = d.gain;
              this.order.custom_card_msg = d.custom_card_msg;
              this.order.important_msg = d.important_msg;
            } else {
              this.allowCreateMsg = r.data.error.message;
            }
          })
          .catch(e => {
            this.loadingPage = false;
            this.$alerts.errHandle(e);
          });
      } else {
        Orders.check
          .limit()
          .then(r => {
            this.loadingPage = false;
            this.allowToCreate = r.data.status;

            if (r.data.status) {
              let d = r.data.result; 

              this.maxAllowedAmount = d.max_allowed_amount;
              this.maxAllowedGhest = d.max_allowed_ghest;

              this.maxes.first = d.max_amount_first;
              this.maxes.second = d.max_amount_second;
              this.maxes.third = d.max_amount_third;

              this.order.rpa = d.rpa; 
              this.order.gain = d.gain;
              this.order.custom_card_msg = d.custom_card_msg;
              this.order.important_msg = d.important_msg;

              this.create_control = r.data.result.create_control;
            } else {
              this.allowCreateMsg = r.data.error.message;
              this.allowCreateMsgStatus = r.data.error.name;
            }
          })
          .catch(e => {
            this.loadingPage = false;
            this.$alerts.errHandle(e);
          });
      }
    },

    keyUpHandle(event) {
      const val = event.target.value;
      clearTimeout(this.timeout);
      this.timeout = setTimeout(() => {
        if (val.length >= 3 && val.length <= 8) {
          this.checkOrgCode();
        }
      }, 500);
    },

    nextStep(payload) {
      if (payload) {
        this.otpData = payload;

        if (payload.tracker) {
          this.tracker = payload.tracker;
        }

        if (payload.mobile) {
          this.mobile = payload.mobile;
        }
      }

      if (payload.user_exists) {
        this.step = 4;
      } else {
        this.step++;
      }
    },

    showFirstStep() {
      this.step = 1;
    },

    loadData() {
      if (this.role === 'shop') {
        Seller.get
          .info('status')
          .then(r => {
            this.shopStatus = r.data;
          })
          .catch(e => {
            this.$alerts.errHandle(e);
          });
      }
    },

    haveOrganCode() {
      
    }
  },

  created() {
    this.$root.getGlobalsData().then(r => {
      const { gain, rpa } = r.data.result;

      this.order.gain = gain;
      this.order.rpa = rpa;
    });

    if (this.role === 'user') {
      this.checkLimit();
      this.step = 4;
    }

    if (this.role === 'shop') {
      this.loadData();
      this.step = 1;
      this.allowToCreate = true;
    }

    if (this.role === 'organ') {
      this.allowToCreate = false;
      this.allowCreateMsg = 'شما به عنوان یک ارگان امکان ثبت سفارش را ندارید';
    }
  }
};

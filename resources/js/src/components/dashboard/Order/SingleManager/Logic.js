import { mapState } from 'vuex';
import { HasContain } from '@/global/Functions';
import m from 'moment-jalaali';
m.loadPersian({ usePersianDigits: false });

/**
 * API Services
 */
import Admin from '@/api/admin';
import Custom from '@/api/custom';
import Orders from '@/api/orders';
import Payment from '@/api/payment';
import { api, wrapper } from '@/global/services';

/**
 * Global Functions
 */
import StatusColors from '@/global/StatusColors';
import stateList from '@/data/States';

import orderSteps from '@/components/dashboard/Order/Steps/Index';

export default {
  name: 'Order',

  components: {
    orderSteps,
    orderStatus: () => import('@/components/dashboard/Order/Status/Index'),
    orderInfo: () => import('@/components/dashboard/Order/Info/Index'),
    UserInfo: () => import('@/components/dashboard/UserInfo/Index'),
    Invoice: () => import('@/components/dashboard/Order/Invoice/Index'),
    chequesInfo: () =>
      import('@/components/dashboard/Order/Steps/ChequesInfo/Index.vue'),
    ContractDownload: () =>
      import('@/components/dashboard/Order/Steps/ContractDownload/Index.vue'),
    SmallNote: () => import('@/components/dashboard/SmallNote/Index.vue'),

    InquiryModal: () => import('@/components/dashboard/Modals/Inquiry/Index'),
    smsModal: () =>
      import('@/components/dashboard/Order/ManagerModals/SendSms/Index'),
    OrderChargeModal: () =>
      import('@/components/dashboard/Order/ManagerModals/OrderCharge/Index'),
    UserInfoModal: () =>
      import('@/components/dashboard/Order/ManagerModals/UserInfo/Index'),
    AllDatesModal: () =>
      import('@/components/dashboard/Order/ManagerModals/AllDates/Index'),
    EditOrderModal: () =>
      import('@/components/dashboard/Order/ManagerModals/EditOrder/Index')
  },

  mixins: [StatusColors],

  data() {
    return {
      loading: true,
      interval: null,

      invoice: {},

      gfy: {
        prepayment: '',
        ghest: '',
        total: ''
      },

      order: {},
      docs: [],
      ghests: [],
      notes: [],

      changeStatusLoading: false,

      /**
       * Reject
       * -------------------------------------
       */
      rejectModal: false,
      rejectReason: '',

      submitCtaLoading: false,

      /**
       * Transactions
       */
      transactionsModal: false,
      payLoading: false,
      
      cardAddress: 'home',
      cardPassword: '',

      stateList
    };
  },

  watch: {
    role() {
      this.loadData();
    }
  },

  computed: {
    ...mapState('dashboard', ['role']),

    year() {
      const now = Number(m(new Date()).format('jYYYY'));
      const end = now + 1;
      const y = [];
      for (let i = 1395; i <= end; i++) {
        y.push(i);
      }
      return y;
    },

    birthYears() {
      const y = [];
      for (let i = 1382; i >= 1300; i--) {
        y.push(i);
      }
      return y;
    },

    /**
     * Show cancel button
     * condition
     */
    showCancelForShop() {
      switch (this.order.status) {
      case 'draft':
      case 'submitted':
      case 'docs_uploaded':
      case 'upload_secondary':
      case 'check_secondary':
      case 'wait_for_cheques':
        return true;
      default:
        return false;
      }
    }
  },

  created() {
    this.loadData();
    this.loadNotes();
  },

  mounted() {
    if (this.role === 'admin') {
      this.interval = setInterval(() => {
        this.loadData();
      }, 60000);
    }
  },

  beforeDestroy() {
    clearInterval(this.interval);
  },

  methods: {
    openInquiryModal() {
      const payload = {
        endpoint: `/admin/users/${this.order.user_id}/inquiry`
      };

      this.$refs.inquiryModal.openModal(payload);
    },

    paymentName(type) {
      switch (type) {
      case 'prepayment':
        return 'پیش‌ پرداخت';
      case 'ghest':
        return 'قسط';
      case 'extra':
        return 'شارژ اضافی';
      case 'inquiry':
        return 'هزینه استعلام';
      case 'validation':
        return 'هزینه اعتبارسنجی';
      default:
        return false;
      }
    },

    async docIsReadableChange(payload) {
      const { data } = await wrapper(
        api.custom.post(`${this.docIsReadableUrl}${payload}`)
      );

      if (data.status) {
        this.$alerts.show({
          msg: 'اطلاعات با موفقیت ثبت شد',
          type: 'success',
          style: 'float'
        });
        this.loadDocs();
      } else {
        this.$alerts.show({
          msg: '😰 اوه، خورد به دیوار',
          type: 'danger',
          style: 'float'
        });
      }
    },

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

    transactionReCheck(id) {
      Payment.check
        .transaction(this.$route.params.id, id)
        .then(r => {
          if (r.status) {
            this.loadData();
            this.$alerts.show({
              msg: 'بررسی مجدد انجام شد',
              type: 'success',
              style: 'float'
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
          this.$alerts.errHandle(e);
        });
    },

    ghestify(order, refund) {
      order.amount = parseInt(order.amount);
      order.gain = parseFloat(order.gain);
      refund.cheques = parseInt(refund.cheques);
      refund.months = parseInt(refund.months);

      let up, down, divide, k, n, y, res, prepayment, ghest;

      prepayment = parseInt(order.prepayment);

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

    async loadData() {
      const { data } = await wrapper(
        api.Admin.orders.single(this.role, this.$route.params.id)
      );

      this.loading = false;

      if (data) {
        const { result } = data;

        this.order = result;
        this.firstGhestAt = result.first_ghest;

        this.invoice = {
          description: 'توضیحات...',
          prepayment: result.prepayment,
          validation: 10000,
          card_issued: 50000,
          total: 2310000
        };

        if (
          HasContain(result.status, ['cycle_cheque', 'cycle_epay', 'completed'])
        ) {
          Orders.get
            .ghests(this.$route.params.id)
            .then(r => {
              this.ghests = r.data.result;
            })
            .catch(e => {
              this.$alerts.errHandle(e);
            });
        }
      }

      return data;
    },

    reject() {
      this.$refs.reject.loading('start');

      if (confirm('آیا از رد این سفارش مطمئن هستید؟')) {
        Admin.orders
          .reject(
            this.$route.params.id,
            {
              reason: this.rejectReason
            },
            this.role
          )
          .then(r => {
            this.$refs.reject.loading('end');
            this.rejectModal = false;

            if (r.data.status) {
              this.$alerts.show({
                msg: 'سفارش لغو شد',
                type: 'success',
                style: 'float'
              });

              this.loadData();
            } else {
              this.$alerts.show({
                msg: r.data.error.name,
                type: 'danger',
                style: 'float'
              });
            }
          })
          .catch(e => {
            this.$refs.reject.loading('end');
            this.$alerts.errHandle(e);
          });
      }
    },

    submitReq(endpoint) {
      this.submitCtaLoading = true;
      Orders.submitDocs(this.$route.params.id, endpoint)
        .then(() => {
          this.submitCtaLoading = false;
          this.loadData();
        })
        .catch(e => {
          this.submitCtaLoading = false;
          this.$alerts.errHandle(e);
          this.loadData();
        });
    },

    deleteNote(id) {
      Admin.notes
        .delete(id)
        .then(r => {
          if (r.data.status) {
            this.notesModal = false;
            this.loadNotes();

            this.$alerts.show({
              msg: 'یادداشت با موفقیت حذف شد',
              type: 'success',
              style: 'float'
            });
          } else {
            this.$alerts.show({
              msg: 'مشکلی در حذف یادداشت بوجود آمد',
              type: 'danger',
              style: 'float'
            });
          }
        })
        .catch(e => {
          this.$alerts.errHandle(e);
          this.notesDeleteLoading = false;
        });
    },

    loadNotes() {
      if (this.role === 'admin') {
        Admin.notes.all(this.$route.params.id, 'orders').then(r => {
          const { result } = r.data;
          this.notes = result;
        });
      }
    },

    handleOrderCta({ method, url, type, modal_name, endpoint, loader }) {
      if (type === 'modal') {
        this[modal_name] = true;
        this.$refs[modal_name].openModal();
        this.currentModalEndpoint = endpoint;
        this.currentModalMethod = method;
      } else {
        let canDo = confirm('آیا از انجام این عملیات مطمئن هستید؟');

        if (canDo) {
          this.$refs[loader].loading('start');

          Custom[method](url)
            .then(r => {
              this.$refs[loader].loading('end');

              if (r.data.status) {
                this.loadData();

                this.$alerts.show({
                  msg: 'وضعیت سفارش با موفقیت تغییر کرد',
                  type: 'success',
                  style: 'float'
                });
              } else {
                this.$alerts.show({
                  msg:
                    r.data.error.message |
                    'خطای نامشخص • لطفا به پشتیبانی اطلاع دهید',
                  type: 'danger',
                  style: 'float'
                });
              }
            })
            .catch(e => {
              this.$alerts.errHandle(e);
              this.$refs[loader].loading('end');
            });
        }
      }
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
          this.payLoading = true;
          this.$alerts.errHandle(e);
        });
    },

    inArray(member, array) {
      return HasContain(member, array);
    }
  }
};

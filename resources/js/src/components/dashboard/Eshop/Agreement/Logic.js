import StatusColors from '@/global/StatusColors';

/**
 * API Services
 */
import Seller from '@/api/seller';
import Custom from '@/api/custom';

export default {
  name: 'EshopAgreement',

  mixins: [StatusColors],

  props: {
    info: {},
  },

  data() {
    return { 
      onChangeBtn: null,
      currentModalEndpoint: '',
      currentModalMethod: ''
    };
  },

  created() {
    this.loadData();
  },

  methods: {
    loadData() {
      Seller.get
        .info()
        .then(r => {
          this.info = r.data.result;
        })
        .catch(e => {
          this.$alerts.errHandle(e);
        });
    },

    acceptAgreement() {
      this.loadingAgreement = true;

      Seller.agree()
        .then(() => {
          this.loadData();
          this.loadingAgreement = false;
        })
        .catch(e => {
          this.$alerts.errHandle(e);
          this.loadingAgreement = false;
        });
    },

    rejectOrder(endpoint, method) {
      Custom[method](endpoint, {
        reason: this.rejectReason
      })
        .then(r => {
          if (r.data.status) {
            this.loadOrderPending();
            this.$alerts.show({
              msg: 'سفارش با موفقیت رد شد',
              type: 'success',
              style: 'float'
            });
          }

          this.rejectModal = false;
        })
        .catch(e => {
          this.$alerts.errHandle(e);
          this.rejectModal = false;
        });
    },

    actionsHandle(payload) {
      if (payload.type === 'confirm') {
        if (
          confirm(
            payload.confirm_message
              ? payload.confirm_message
              : 'آیا مطمئن هستید؟'
          )
        ) {
          Custom[payload.method](payload.endpoint)
            .then(r => {
              if (r.data.status) {
                this.loadOrderPending();
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

      if (payload.type === 'straight') {
        Custom[payload.method](payload.endpoint)
          .then(r => {
            if (r.data.status) {
              window.location = r.data.result.url;
            }
          })
          .catch(e => {
            this.$alerts.errHandle(e);
          });
      }

      if (payload.type === 'modal') {
        this[payload.modal_name] = true;
        this.currentModalEndpoint = payload.endpoint;
        this.currentModalMethod = payload.method;
      }
    },

    verifyDomain() {
      this.$refs.verifyDomain.loading('start');
      Seller.verifyDomain()
        .then(() => {
          this.loadData();
        })
        .catch(e => {
          this.$alerts.errHandle(e);
        })
        .finally(() => {
          this.$refs.verifyDomain.loading('end');
        });
    },

    verifyEmail() {
      this.$refs.verifyEmail.loading('start');
      Seller.verifyEmail({ otp: this.emailCode })
        .then(() => {
          this.loadData();
        })
        .catch(e => {
          this.$alerts.errHandle(e);
        })
        .finally(() => {
          this.$refs.verifyEmail.loading('end');
        });
    },

    sendEmailAgain() {
      this.$refs.sendEmailAgain.loading('start');
      Seller.sendEmailAgain()
        .then(() => {
          this.loadData();
        })
        .catch(e => {
          this.$alerts.errHandle(e);
        })
        .finally(() => {
          this.$refs.sendEmailAgain.loading('end');
        });
    }
  }
}; 
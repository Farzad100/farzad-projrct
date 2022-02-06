import StatusColors from '@/global/StatusColors';

/**
 * API Services
 */
import Organ from '@/api/organ';
import Custom from '@/api/custom';

export default {
  name: 'HomeOrgan',

  mixins: [StatusColors],

  data() {
    return {
      info: {},

      tableListLoading: false,
      pendingOrders: [],

      onChangeBtn: null,
      currentModalEndpoint: '',
      currentModalMethod: ''
    };
  },

  created() {
    this.loadData();
  },

  methods: {
    loadOrderPending() {
      this.tableListLoading = true;

      Organ.get
        .orderPending()
        .then(r => {
          this.pendingOrders = r.data.result;
          this.tableListLoading = false;
        })
        .catch(e => {
          this.tableListLoading = false;
          this.$alerts.errHandle(e);
        });
    },

    loadData() {
      Organ.get
        .info()
        .then(r => {
          this.info = r.data.result;
        })
        .catch(e => {
          this.$alerts.errHandle(e);
        });

      this.loadOrderPending();
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
    }
  }
};
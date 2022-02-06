import StatusColors from '@/global/StatusColors';

/**
 * API Services
 */
import Orders from '@/api/orders';
import Custom from '@/api/custom';

export default {
  name: 'HomeUser',

  mixins: [StatusColors],

  components: {
    activeCard: () => import('@/components/dashboard/ActiveCard/Index'),
    helpBox: () => import('@/components/dashboard/HelpBox/Index'),
  },

  data() {
    return {
      activeCard: null,

      ghests: [],
      ghestsPagination: false,
      ghestsLoading: false,

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
      this.ghestsLoading = true;

      Orders.get
        .latest()
        .then(r => {
          this.activeCard = r.data.result;
        })
        .catch(e => {
          this.$alerts.errHandle(e);
        });

      Orders.get
        .recentGhests()
        .then(r => {
          this.ghests = r.data.result;
          this.ghestsLoading = false;
        })
        .catch(e => {
          this.$alerts.errHandle(e);
          this.ghestsLoading = false;
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
    }
  }
};
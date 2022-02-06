import Custom from '@/api/custom';

export default {
  data() {
    return {
      modal: {
        name: '',
        onChangeBtn: null,
        loadingData: false,
        submitLoading: false,
        endpoint: '',
        method: ''
      }
    };
  },

  methods: {
    actionsHandle(payload) {
      const { type, modal_name, endpoint, method, confirm_message } = payload;

      if (type === 'confirm') {
        this.confirmType(endpoint, method, confirm_message, payload.e);
      }

      if (type === 'straight') {
        this.straightType(endpoint, method);
      }

      if (type === 'modal') {
        this.modalType(modal_name, endpoint, method);
      }
    },

    /**
     * Only handle action with
     * confirmation
     */
    confirmType(endpoint, method, confirm_message, e) {
      if (confirm(confirm_message ? confirm_message : 'آیا مطمئن هستید؟')) {
        Custom[method](endpoint)
          .then(res => {
            if (res.data.status) {
              this.loadData();
            }
            this.modal.onChangeBtn = e;
          })
          .catch(err => {
            this.$alerts.errHandle(err);
            this.modal.onChangeBtn = e;
          });
      } else {
        this.modal.onChangeBtn = e;
      }
    },

    /**
     * Only handle actions that
     * want to go to another URL
     */
    straightType(endpoint, method) {
      Custom[method](endpoint)
        .then(res => {
          console.log(res.data);
          /* if (res.data.status && method === 'get') {
            window.open(res.data.result.url, '_blank');
          } */
        })
        .catch(err => {
          this.$alerts.errHandle(err);
        });
    },

    /**
     * Only handle modals, opening,
     * fetching modal data and...
     */
    modalType(modal_name, endpoint, method) {
      this.modal.loadingData = true;
      this.modal.endpoint = endpoint;
      this.modal.method = method;
      this.modal.name = modal_name;

      // Open modal
      this.modal = {
        ...this.modal,
        [modal_name]: true
      };
    }
  }
};

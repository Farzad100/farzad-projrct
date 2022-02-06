export default {
  name: 'ReportWrapper',

  components: {
    UserReports: () => import('./reports/users/Index'),
    OrderReports: () => import('./reports/orders/Index.vue'),
    SalesReports: () => import('./reports/sales/Index'),
    InstallmentReports: () => import('./reports/installment/Index'),
  },

  data() {
    return {
      selectedReport: 'users'
    };
  },

  methods: {
    changeReport(payload) {
      this.selectedReport = payload;
    }
  }
};
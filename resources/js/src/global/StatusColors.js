export default {
  methods: {
    /**
     * Set colors for order status
     * @param {*} status
     */
    handleStatus(status) {
      switch (status) {
      case 'draft':
      case 'submitted':
      case 'pended_by_organ':
      case 'upload_secondary':
      case 'wait_for_cheques':
      case 'prepayment':
      case 'uploading':
      case 'agreement':
        return 'badge-warning';

      case 'docs_uploaded':
      case 'scoring':
      case 'scored':
      case 'check_secondary':
      case 'pending':
      case 'final':
        return 'badge-info';

      case 'rejected':
      case 'blocked':
        return 'badge-danger';

      case 'cancelled':
      case 'inactive':
        return 'badge-secondary';

      case 'active':
      case 'cycle_epay':
      case 'cycle_cheque':
        return 'badge-success';

      case 'prepaid':
        return 'border border-success text-success';

      case 'completed':
        return 'border border-success text-success';

      default:
        return 'badge-light';
      }
    }
  }
};

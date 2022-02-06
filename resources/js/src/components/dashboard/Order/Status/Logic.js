export default {
  name: 'OrderStatus',

  props: {
    shop_id: '',
    ghestacard: '',
    prepayment: '',
    status: {
      type: String
    },
    reason: {
      type: String
    }
  }
};
export default {
  name: 'ActiveCard',

  props: {
    data: {
      type: Object|null,
      required: true
    }
  },

  components: {
    orderCard: () => import('@/components/dashboard/Order/Card/Index')
  }
};

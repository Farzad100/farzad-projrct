import StatusColors from '@/global/StatusColors';
import { mapState } from 'vuex';

export default {
  name: 'OrderInfo',

  mixins: [StatusColors],

  props: {
    data: {
      type: Object,
      required: true
    },

    type: {
      type: String,
    }
  },

  computed: {
    ...mapState('dashboard', ['role']),
  },

  methods: {
    employeeCount(payload) {
      switch (payload) {
      case 20:
        return 'کمتر از 20 نفر';
      case 100:
        return 'بین 20 تا 100 نفر';
      case 500:
        return 'بین 100 تا 500 نفر';
      case 2500:
        return 'بین 500 تا 2500 نفر';
      case 10000:
        return 'بیشتر از 2500 نفر';
      }
    },

    age(payload) {
      switch (payload) {
      case 3:
        return 'زیر ۳ سال';
      case 10:
        return '۳ تا ۱۰ سال';
      case 100:
        return 'بیشتر از ۱۰ سال';
      default:
        return false;
      }
    },
  }
};
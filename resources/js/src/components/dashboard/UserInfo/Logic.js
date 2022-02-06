import { mapState } from 'vuex';

export default {
  name: 'UserInfo',

  props: {
    data: {
      type: Object,
      required: true
    },

    type: {
      type: String,
    }
  },

  data() {
    return {
      info: this.data[`${this.type}`],
    };
  },

  computed: {
    ...mapState('dashboard', ['role']),

    agentPosition() {
      switch (this.data.agent_position) {
      case 'cao':
        return 'رئیس هیئت مدیره';
      case 'ceo':
        return 'مدیرعامل';
      case 'cfo':
        return 'مدیر/معاون امور مالی';
      case 'chro':
        return 'مدیر/معاون منابع انسانی';
      case 'hr':
        return 'کارشناس منابع انسانی/امور مالی';
      default:
        break;
      }
    }
  },
};
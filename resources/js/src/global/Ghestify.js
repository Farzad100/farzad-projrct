import Orders from '@/api/orders';

export default {
  data() {
    return {
      refund: { months: 6, cheques: 6 },

      canCalShow: false,

      order: {
        amount: 1500000,
        gain: null,
        status: null,
        rpa: null,
        custom_card_msg: null,
        rpao: null,
        isOrgan: false,
        type: 'individual',
        manual: false,
        prepayment: '',
        product: ''
      },

      maxes: {
        first: '',
        second: '',
        third: '',
        gFirst: '',
        gSecond: '',
        gThird: ''
      },

      payback_models: [],

      limits: {},

      isHintShow: true
    };
  },

  watch: {
    'order.type'() {
      if (
        this.order.type === 'individual' &&
        (this.refund.months > 10 || this.refund.cheques > 6)
      ) {
        this.refund = { months: 6, cheques: 6 };
      } else if (this.order.type === 'organ1' && this.refund.months > 10) {
        this.refund = { months: 10, cheques: 10 };
      }
      switch (this.order.type) {
      case 'individual':
      default:
        this.maxes.first = parseInt(this.limits.first);
        this.maxes.second = parseInt(this.limits.second);
        this.maxes.third = parseInt(this.limits.third);

        this.maxes.gFirst = parseInt(this.limits.gFirst);
        this.maxes.gSecond = parseInt(this.limits.gSecond);
        this.maxes.gThird = parseInt(this.limits.gThird);

        this.payback_models = this.payback_months;
        break;
      case 'organ1':
        this.maxes.first = parseInt(this.limits.first_organ_level_1);
        this.maxes.second = parseInt(this.limits.second_organ_level_1);

        this.maxes.gFirst = parseInt(this.limits.gFirst_organ);
        this.maxes.gSecond = parseInt(this.limits.gFirst_organ) + 1000000;
        this.maxes.gThird = parseInt(this.limits.gFirst_organ) + 2000000;

        this.payback_models = this.payback_months_organ_level_1;
        this.order.rpa = this.order.rpa_organ_level_1;
        break;
      case 'organ2':
        this.maxes.first = parseInt(this.limits.first_organ_level_2);
        this.maxes.second = parseInt(this.limits.second_organ_level_2);

        this.maxes.gFirst = parseInt(this.limits.gFirst_organ);
        this.maxes.gSecond = parseInt(this.limits.gFirst_organ) + 1000000;
        this.maxes.gThird = parseInt(this.limits.gFirst_organ) + 2000000;

        this.payback_models = this.payback_months_organ_level_2;
        this.order.rpa = this.order.rpa_organ_level_2;
        break;
      }
    },
    'order.amount'() {
      this.isHintShow = false;
    }
  },

  methods: {
    ghestify(order, refund) {
      order.amount = parseInt(order.amount);
      order.gain = parseFloat(order.gain);
      refund.cheques = parseInt(refund.cheques);
      refund.months = parseInt(refund.months);

      let up, down, divide, k, n, y, res, prepayment, ghest;

      if (order.manual) prepayment = parseInt(order.prepayment);
      else prepayment = Math.ceil((order.rpa * order.amount) / 1000) * 1000;

      k = order.gain + 1;
      n = refund.months;
      y = order.amount - prepayment;
      if (refund.months === 2 * refund.cheques) {
        up = y * k ** n * (k ** 2 - 1);
      } else up = y * k ** n * (k - 1);

      down = k ** n - 1;
      down === 0 ? (divide = 0) : (divide = up / down);
      res = Math.ceil(divide / 5000) * 5000;
      res > 1000 ? (ghest = res) : (ghest = 0);

      order.ghest = ghest;
      order.payback = order.ghest * refund.cheques;
      order.prepayment = prepayment;
      order.total = order.payback + order.prepayment;

      return order;
    },

    async getGlobalsData() {
      return await new Promise((resolve, reject) => {
        Orders.check
          .global()
          .then(r => {
            const {
              gain,
              rpa,
              rpa_organ_level_1,
              rpa_organ_level_2,
              max_amount_first,
              max_amount_second,
              max_amount_third,
              max_ghest_first,
              max_ghest_second,
              max_ghest_third,
              max_amount_first_organ_level_1,
              max_amount_second_organ_level_1,
              max_amount_first_organ_level_2,
              max_amount_second_organ_level_2,
              payback_months,
              payback_months_organ_level_1,
              payback_months_organ_level_2
            } = r.data.result;

            this.order.gain = gain;
            this.order.rpa = rpa;
            this.order.rpa_organ_level_1 = rpa_organ_level_1;
            this.order.rpa_organ_level_2 = rpa_organ_level_2;

            this.maxes.first = max_amount_first;
            this.maxes.second = max_amount_second;
            this.maxes.third = max_amount_third;

            this.maxes.gFirst = max_ghest_first;
            this.maxes.gSecond = max_ghest_second;
            this.maxes.gThird = max_ghest_third;

            this.limits.first = max_amount_first;
            this.limits.second = max_amount_second;
            this.limits.third = max_amount_third;

            this.limits.gFirst_organ = 5000000;
            this.limits.gFirst = max_ghest_first;
            this.limits.gSecond = max_ghest_second;
            this.limits.gThird = max_ghest_third;

            this.limits.first_organ_level_1 = max_amount_first_organ_level_1;
            this.limits.second_organ_level_1 = max_amount_second_organ_level_1;

            this.limits.first_organ_level_2 = max_amount_first_organ_level_2;
            this.limits.second_organ_level_2 = max_amount_second_organ_level_2;

            this.payback_models = payback_months;
            this.payback_months = payback_months;
            this.payback_months_organ_level_1 = payback_months_organ_level_1;
            this.payback_months_organ_level_2 = payback_months_organ_level_2;

            this.canCalShow = true;

            resolve(r);
          })
          .catch(e => {
            reject(e);
          });
      });
    },

    changeRefund(val) {
      this.refund = val;
    }
  }
};

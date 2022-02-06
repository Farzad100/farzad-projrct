/**
 * API Services
 */
import Orders from '@/api/orders';

export default {
  name: 'CardStatus',

  props: {
    data: {
      type: Object,
      required: true
    }
  },

  data() {
    return {
      checkingCardNumber: false,
      cardRecived: false,
      cardNumber: '',
      checkingCardOpt: {},
    };
  },

  methods: {
    /**
     * Check card number
     */
    checkCardNumber() {
      this.checkingCardNumber = true;

      Orders.check
        .cardRecived({
          card_number: this.cardNumber
        })
        .then(r => {
          this.checkingCardNumber = false;

          if (r.data.status) {
            this.checkingCardOpt = {
              type: 'success',
              msg: 'شماره کارت صحیح است'
            };

            this.$parent.$parent.loadInfo();

          } else {
            this.checkingCardOpt = {
              type: 'error',
              msg: r.data.error.message
            };
          }
        })
        .catch(e => {
          this.checkingCardNumber = false;
          this.$alerts.errHandle(e);
        });
    },

    keyUpHandle(event) {
      const val = event.target.value;
      clearTimeout(this.timeout);
      this.timeout = setTimeout(() => {
        if (val.length >= 3) {
          this.checkCardNumber();
        }
      }, 500);
    },
  }

};
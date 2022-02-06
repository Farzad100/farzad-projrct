import { api, wrapper } from '@/global/services';

export default {
  name: 'OrderCharge',

  data() {
    return {
      orderChargeModal: false,
      form: {
        series: '',
        mode: 'charge'
      }
    };
  },

  methods: {
    async chargeGhestaCard() {
      // Confirmation
      let isOk = confirm('آیا از شارژ این قسطا کارت مطمئن هستید؟');
      
      if (isOk) {
        this.$refs.chargeGC.loading('start');

        const { data } = await wrapper(
          api.custom.post(this.$parent.order.cta.url, this.form)
        );
        
        this.$refs.chargeGC.loading('end');
        
        if (data) {
          this.$parent.loadData();
          this.orderChargeModal = false;
        }
      }
    },

    openModal() {
      this.orderChargeModal = true;
    }
  }
};

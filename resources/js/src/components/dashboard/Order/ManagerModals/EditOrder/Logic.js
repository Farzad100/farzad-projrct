import { api, wrapper } from '@/global/services';

export default {
  name: 'EditOrder',

  data() {
    return {
      editModal: false,
      loading: true,

      editFormModels: {
        product: '',
        amount: '',
        prepayment: '',
        months: '',
        cheques: '',
        gain: '',
        payback_type: '',
        first_ghest_at: '',
        charge_type: '',
        series: '',
        series_card: '',
        isOrgan: false,
        manual: true
      },
    };
  },

  methods: {
    async editOrder() {
      this.$refs.edit.loading('start');

      const { data } = await wrapper(
        api.Admin.orders.edit(this.$route.params.id, this.editFormModels)
      );

      this.$refs.edit.loading('end');

      if (data) {
        const { status } = data;

        if (status) {
          this.editModal = false;
          this.$parent.loadData();

          this.$alerts.show({
            msg: 'سفارش با موفقیت ویرایش شد',
            type: 'success',
            style: 'float'
          });
        }
      }
    },

    openModal() {
      this.editModal = true;

      this.$parent.loadData() 
        .then(res => {
          this.loading = false;
          
          const { result } = res;

          this.editFormModels = {
            product: result.product,
            amount: result.amount,
            prepayment: result.prepayment,
            months: result.months,
            cheques: result.cheques,
            gain: result.gain,
            first_ghest_at: result.first_ghest,
            payback_type: result.payback_type,
            charge_type: result.charge_type,
            series: result.series,
            series_card: result.series_card,
            isOrgan: result.organ_id ? true : false
          };
        });
    }
  }
};

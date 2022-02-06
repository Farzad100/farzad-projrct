import Banks from '@/data/Banks';
import { api, wrapper } from '@/global/services';

export default {
  name: 'Banks',

  props: {
    inUploader: {
      type: Boolean
    }
  },

  data() {
    return {
      banks: [],
      selectedBankIndex: '', 

      shebaBankErr: false,
      isAdding: false
    };
  },

  watch: {
    banks: { 
      deep: true,

      handler(val) {
        if (val.length !== 0) {
          const iban = val[val.length-1].iban;

          if (iban && iban.length >= 5) {
            this.shebaBankErr = true;
            const bankId = `${iban[2]}${iban[3]}${iban[4]}`;
            this.bankId = bankId;
  
            Banks.forEach(obj => {
              if (obj.id === bankId) {
                this.banks[this.banks.length-1].logo = obj.name;
                this.banks[this.banks.length-1].name = obj.fa;
                this.shebaBankErr = false;
              } 
            });
          }
        }
      }
    },
  },

  created() {
    this.getBankList();
  },

  methods: {
    async getBankList() {
      const { data } = await wrapper(
        api.bankList.get(this.$route.params.id),
        'مشکلی در دریافت لیست حساب‌های بانکی پیش آمد!'
      );

      if (data) {
        const { result } = data;
        this.banks = result;

        if (result.length === 0) {
          this.addNewBank();
        }
      }
    },
    
    addNewBank() {
      this.isAdding = true;

      this.banks.push({
        logo: '',
        name: '',
        iban: '',
        branch_name: '',
        branch_code: '',
      });

      this.selectedBankIndex = this.banks.length-1;
    },

    async saveSelectedBank() {
      this.$refs.saveBank.loading('start');

      const bank = {
        iban: this.banks[this.selectedBankIndex].iban,
        branch_name: this.banks[this.selectedBankIndex].branch_name,
        branch_code: this.banks[this.selectedBankIndex].branch_code,
      };

      const { data } = await wrapper(
        api.bankList.post(this.$route.params.id, bank),
        'مشکلی در ذخیره اطلاعات بانکی پیش آمد!'
      );

      if (data) {
        const { status } = data;
        
        if (status) {
          this.$alerts.show({
            msg: 'اطلاعات بانکی ذخیره شد',
            type: 'success',
          });
        }
      } else {
        this.$refs.saveBank.loading('end');
      }
    }
  }
};
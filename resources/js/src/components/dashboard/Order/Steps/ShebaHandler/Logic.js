import Banks from '@/data/Banks';
import { mapState } from 'vuex';

export default {
  name: 'ShebaHandler',

  props: {
    chequeData: {
      type: Object,
      default: function() {
        return {};
      }
    },
    value: {
      type: Object
    }
  },

  components: {
    QrReader: () => import('@/components/overall/QrReader/Index')
  },

  data() {
    return {
      wfcForm: {
        iban: '',
        branch_name: '',
        branch_code: ''
      },

      cheques_numbers_sm: {
        default: ''
      },

      shabaBank: { name: '', fa: '' },
      chequesPreview: null,
      Banks,

      shebaBankErr: false,
      bankId: '',

      qrcode: false
    };
  },

  watch: {
    /**
     * Find bank logo and name
     * based on shaba code
     */
    'wfcForm.iban'(val) {
      if (val && val.length >= 5) {
        this.shebaBankErr = true;
        const bankId = `${val[2]}${val[3]}${val[4]}`;
        this.bankId = bankId;

        Banks.forEach(obj => {
          if (obj.id === bankId) {
            this.shabaBank.name = obj.name;
            this.shabaBank.fa = obj.fa;
            this.shebaBankErr = false;
          }
        });

        this.$emit('sheba-error', this.shebaBankErr);
      }
    },

    value(val) {
      this.wfcForm = val;
    },

    wfcForm: {
      handler(val) {
        this.$emit('input', val);
        if (
          this.wfcForm &&
          this.wfcForm.iban &&
          this.wfcForm.branch_code &&
          this.wfcForm.branch_name
        )
          this.$emit(
            'field-error',
            !(
              this.wfcForm.iban.length > 23 &&
              this.wfcForm.branch_code.length > 0 &&
              this.wfcForm.branch_name.length > 1
            )
          );
      },
      deep: true
    }
  },

  computed: {
    ...mapState('dashboard', ['role'])
  },

  mounted() {
    this.wfcForm = this.chequeData;
  }
};

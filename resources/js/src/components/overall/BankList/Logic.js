import Banks from '@/data/Banks';

export default {
  name: 'BankListSelect',

  props: {
    size: {
      type: String
    },
  },

  data() {
    return {
      selected: {},
      isOpen: false,
      searchKey: '',
      bankList: Banks,
      Banks
    };
  },

  watch: {
    searchKey(val) {
      if (val) {
        this.bankList = [];
        for (let bank of this.Banks) {
          if (bank.fa.includes(val)) {
            this.bankList.push(bank);
          }
        }
      } else {
        this.bankList = this.Banks;
      }
    }
  },

  methods: {
    select(payload) {
      this.selected = payload;
      this.isOpen = false;
      this.$emit('input', payload);
    },
  }
};
export default {
  name: 'ActionDate',

  data() {
    return {
      actionDate: 'today',
      actionType: ''
    };
  },

  watch: {
    actionDate(val) {
      this.$emit('input', val);
    }
  },

  methods: {
    handleType() {
      this.actionType = this.actionDate;
    }
  }
};
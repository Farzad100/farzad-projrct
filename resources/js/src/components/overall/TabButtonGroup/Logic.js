export default {
  name: 'TabButtonGroup',
  watch: {
    selectedInput(val) {
      this.$emit('input', val);
    }
  },
  data() {
    return {
      selectedInput: false
    };
  }
};

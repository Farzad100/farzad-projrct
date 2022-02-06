export default {
  name: 'SelectableInputGroup',
  props: {
    type: {
      type: String,
      required: true
    },
    name: {
      type: String,
      required: true
    }
  },
  mounted() {
    this.$slots.default.forEach(slot => {
      if (slot.tag) {
        if (slot.componentInstance.checked) {
          this.$emit('input', slot.componentInstance.value);
        }
        slot.elm.children[0].addEventListener('change', () => {
          this.$emit('input', slot.componentInstance.value);
        });
      }
    });
  }
};

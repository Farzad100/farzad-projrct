export default {
  name: 'SelectableInput',
  props: {
    id: {
      type: String,
      required: true
    },
    checked: {
      type: Boolean
    },
    value: {
      required: true
    }
  }
};

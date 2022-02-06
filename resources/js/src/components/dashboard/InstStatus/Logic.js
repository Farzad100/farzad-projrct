export default {
  name: 'InstStatus',

  props: {
    status: {
      type: Object,
      required: true
    }
  },

  computed: {
    renderedStatus() {
      let i, array = [];

      for (i = 1; i <= this.status.number; i++) {
        if (i <= this.status.passed) {
          array.push({ id: i, passed: true });
        } else {
          array.push({ id: i, passed: false });
        }
      }

      return array;
    }
  }
};
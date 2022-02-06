export default {
  name: 'Modal',

  props: {
    title: {
      type: String,
    },

    size: {
      type: String
    }
  },

  methods: {
    close() {
      this.$emit('close');
    },

    handleClick(event) {
      if ((' ' + event.target.className + ' ').replace(/[\n\t]/g, ' ').indexOf(' show ') > -1) {
        this.close();
      }
    }
  }
};
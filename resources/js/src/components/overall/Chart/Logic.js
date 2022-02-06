export default {
  name: 'Chart',

  props: {
    type: {
      type: String,
    },
    options: {
      type: Object,
      required: true
    },
    series: {
      type: Array,
      required: true
    },
    height: {
      type: String
    }
  },

  methods: {
    updateSeries(payload) {
      this.$refs.apex.updateSeries(payload);
    },
    
    updateOptions(payload) {
      this.$refs.apex.updateOptions(payload);
    }
  }
};
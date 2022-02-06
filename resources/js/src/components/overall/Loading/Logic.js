export default {
  name: 'Loading',

  props: {
    /**
     * Specified style and type
     * of loading
     */
    type: {
      type: String,
      required: true
    },

    /**
     * How many loading needs
     */
    iterateCount: {
      type: Number
    }
  }
};

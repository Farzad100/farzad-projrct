export default {
  name: 'TheButton',

  props: {
    /**
     * Type of button
     */
    type: {
      type: String,
      default: 'button'
    },

    /**
     * Disability
     */
    disabled: {
      type: Boolean,
      default: false
    },

    /**
     * Large size
     */
    lg: {
      type: Boolean,
      default: false
    },

    /**
     * Small size
     */
    sm: {
      type: Boolean,
      default: false
    },

    /**
     * Very Small size
     */
    xs: {
      type: Boolean,
      default: false
    },

    /**
     * Text in the button
     */
    text: {
      type: String,
      default: ''
    },

    /**
     * Type of badge
     */
    badge: {
      type: String,
    },

    badgeColor: {
      type: String,
    },

    /**
     * Icon of the button
     */
    iconRight: {
      type: String
    },

    /**
     * Icon of the button
     */
    iconRightSpace: {
      type: String
    },

    /**
     * Icon of the button
     */
    iconLeftSpace: {
      type: String
    },

    /**
     * Icon of the button
     */
    iconLeft: {
      type: String
    },

    /**
     * Text centerize
     */
    center: {
      type: Boolean
    },

    /**
     * Full width
     */
    block: {
      type: Boolean
    },

    /**
     * Colors like primary, danger, etc.
     */
    color: {
      type: String
    }
  },

  data() {
    return {
      spinner: false
    };
  },

  methods: {
    loading(payload) {
      switch (payload) {
      case 'start':
        this.spinner = true;
        break;
      case 'end':
        this.spinner = false;
        break;
      }
    }
  }
};

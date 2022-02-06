export default {
  name: 'AllDates',

  data() {
    return {
      allDatesInfoModal: false,
    };
  },

  computed: {
    allDates() {
      const result = this.$parent.order.all_dates.filter(date => {
        return date.date;
      });

      return result;
    }
  },

  methods: {
    openModal() {
      this.allDatesInfoModal = true;
    }
  }
};

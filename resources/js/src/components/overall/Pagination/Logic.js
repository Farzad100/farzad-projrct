export default {
  name: 'Pagination',

  props: {
    data: {
      type: Object,
      required: true
    }
  },
  
  watch: {
    data(val) {
      this.pagination = val;
    }
  },

  data() {
    return {
      pagination: this.data,
    };
  },

  computed: {
    pageNumbers() {
      let result = [];
      
      for (
        let i = this.pagination.current_page - 2;
        i <= this.pagination.current_page + 2 && i <= this.pagination.last_page;
        i++
      ) {
        if (i > 0) {
          result = [
            ...result,
            i
          ];
        }
      }
      
      return result;
    }
  },

  methods: {
    changePage(payload) {
      this.pagination.current_page = payload;
      this.$emit('onPageChange', payload);
    }
  }
};
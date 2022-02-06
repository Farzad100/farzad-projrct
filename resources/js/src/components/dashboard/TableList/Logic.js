import Pagination from '@/components/overall/pagination';
import { mapState } from 'vuex';

export default {
  name: 'OrderListFilter',

  props: {
    listName: {
      type: String,
    },

    minimal: {
      type: Boolean,
    },

    tableList: {
      type: Array,
      required: true
    },

    route: {
      type: Object
    },

    pagination: {
      type: Object
    },

    loading: {
      type: Boolean
    },

    addButton: {
      type: String
    }
  },

  data() {
    return {
      triggeredBtn: null,
      selectedHead: '',
      headIndex : null,
    };
  },

  watch: {
    '$parent.onChangeBtn'(btn) {
      btn.target.classList.remove('btn-loading');
      btn.target.removeAttribute('disabled');
    }
  },

  components: {
    Pagination,
    instStatus: () => import('../InstStatus/Index')
  },

  computed: {
    ...mapState('dashboard', ['role']),

    renderedData() {
      
      const result = {
        headList: [],
      };

      /**
       * Get table <thead> data
       */
      for (const i of Object.entries(this.tableList[0].td)) {
        // eslint-disable-next-line no-debugger
        // debugger; UNCX
        result.headList.push({
          title: i[1].title,
          nosort: i[1].nosort,
          sort_key:  i[0],
          sort: this.$route.query.sort ? this.$route.query.sort.split('-')[1] : '',
        });
      }

      return result;
    }
  },

  methods: {

    selectedHeadtitle(index) {
      this.selectedHead = index;

    },
    handleClick(payload, e) {
      if (payload.type === 'route') {
        this.$router.push({
          name: payload.route,
          params: payload.params
        });
      } else {
        if (payload.type === 'straight' || payload.type === 'confirm') {
          this.triggeredBtn = e;
          this.triggeredBtn.target.classList.add('btn-loading');
          this.triggeredBtn.target.setAttribute('disabled', true);
        }
  
        this.$emit('buttonClick', { ...payload, e });
      }
    },

    handlePageChange(payload) {
      this.$emit('onPageChange', payload);
    },

    theadClicked(index) {
      // eslint-disable-next-line no-debugger
      // debugger; UNCX
      var currentSort = this.renderedData.headList[index].sort; 
      
      if (!currentSort) {
        this.renderedData.headList[index] = {
          ...this.renderedData.headList[index],
          sort: 'asc'
        };
      } else if (currentSort === 'asc') {
        this.renderedData.headList[index].sort = 'desc';
      } else if (currentSort === 'desc') {
        this.renderedData.headList[index].sort = '';
      }
      
      this.$emit('onSort', this.renderedData.headList[index]);
    },

    add() {
      this.$emit('addTrigger');
    }
  },

};
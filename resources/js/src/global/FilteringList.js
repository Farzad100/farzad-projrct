import m from 'moment-jalaali';
m.loadPersian({ usePersianDigits: false });

/**
 * *************************************** *
 *       TODO: refactor or rewrite         *
 *     the whole code! because it's a      *
 *            compelete SHIT               *
 * *************************************** *
 */

export default {
  data() {
    return {
      td: [],
      loading: false,
      pagination: {},
      filterShow: false,

      fromDate: {
        d: '',
        m: '',
        y: '',
      },

      toDate: {
        d: '',
        m: '',
        y: '',
      },

      filter: {
        fast_status: '',
        oid: '',
        status: '',
        mobile: '',
        fname: '',
        lname: '',
        nid: '',
        min: '',
        max: '',
        from_date: '',
        to_date: '',
        page: 1,
        name: '',
        type: '',
        category: '',
        date_type: '',
        payback_type: '',
        utm_content: '',
        utm_campaign: '',
        utm_medium: '',
        utm_source: '',
        card_number: '',
        sort: '',
        trait: '',
        organ: '',
        shop: '',
        ref_id: '',
        amount_min: '',
        amount_max: '',
        com_min: '',
        com_max: '',
        date_sort: ''
      },

      fastFilter: 'all',
      filterCount: {},
    };
  },

  computed: {
    /**
     * Return a list of years from
     * 1395 until now
     */
    year() {
      const now = Number(m(new Date()).format('jYYYY'));
      const end = now + 1;
      const y = [];
      for (let i = 1395; i <= end; i++) {
        y.push(i);
      }
      return y;
    }
  },
  
  watch: {
    /**
     * Fill fast_status with fastFilter
     * value and execute fetching function
     */
    fastFilter(val) {
      this.removeFast();
      this.filter.status = val;
      this.addFiltersToRoute();
    },
  },

  created() {
    this.getFilters();
  },

  methods: {
    /**
     * Open and close filter collapse
     */
    toggleFilter() {
      this.filterShow = !this.filterShow;
    },

    removeSpanFilter(filterKey) {
      this.filter[filterKey] = '';
      this.addFiltersToRoute();
    },

    /**
     * Get route queries and put
     * their value into filter object
     */
    getFilters() {
      Object.keys(this.$route.query).map(i => {
        this.filter = {
          ...this.filter,
          [i]: this.$route.query[i],
        };

        if (i === 'status') {
          this.fastFilter = this.$route.query[i];
        }
      });
    },

    /**
     * Get filter object data and
     * turn them into route queries
     */
    addFiltersToRoute() {
      let obj = {};

      const pageInRoute = this.$route.query.page;
      const pageInFilters = this.filter.page;

      Object.keys(this.filter).map((i) => {
        if (this.filter[i] && i !== 'page' && i !== 'fast_status') { 
          obj = {
            ...obj,
            [i]: this.filter[i]
          };
        }
      });

      this.$router.push({
        query: {
          ...obj,
          page: (pageInFilters == pageInRoute) ? 1 : this.filter.page
        }
      }).catch(() => {});

      this.loadData();
    },

    /**
     * Reset all filters and remove
     * queries from route
     */
    removeFiltersFromRoute() {
      this.removeFast();
      this.loadData();
      this.fastFilter = 'all';
    },

    removeFast() {
      let obj = {};

      this.filter.page = 1;

      Object.keys(this.filter).map((i) => {
        if (i !== 'page') {
          this.filter[i] = '';
        }
        if (i === 'status') {
          this.filter[i] = 'all';
        }
      });

      Object.keys(this.filter).map((i) => {
        if (this.filter[i]) {
          obj = {
            ...obj,
            [i]: this.filter[i],
          };
        }
      });

      this.$router.push({
        query: obj,
      }).catch(() => {});
    },

    /**
     * Get a page number from tableList
     * component and set it in this.filter.page
     */
    changePage(payload) {
      this.filter.page = payload;
      this.addFiltersToRoute();
    },

    /**
     * Under Construction!
     */
    sort(payload) {
      if (payload.sort) {
        this.filter.sort = `${payload.sort_key}-${payload.sort}`;
      } else {
        this.filter.sort = '';
      }

      this.addFiltersToRoute();
    },
  }
};
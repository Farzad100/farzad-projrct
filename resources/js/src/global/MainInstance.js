import Cookies from 'js-cookie';
import { mapActions, mapState } from 'vuex';
import { api, wrapper } from '@/global/services';

export default {
  data() {
    return {
      nav: {
        isOpen: false,
        isScrolled: false,
        lastRole: this.$store.state.dashboard.role
      },

      isAuth: !!Cookies.get('access_token')
    };
  },

  mounted() {
    this.utmSet();
    this.rfrrSet();
    // this.metaSet();

    window.addEventListener('scroll', () => {
      if (window.scrollY > 50) {
        this.nav.isScrolled = true;
      } else {
        this.nav.isScrolled = false;
      }
    });
  },

  watch: {
    role() {
      this.getInboxCount();
    }
  },

  computed: {
    ...mapState('dashboard', ['role'])
  },

  created() {
    this.getInboxCount();
    
    setInterval(() => {
      this.getInboxCount();
    }, 3600000);
  },
  
  methods: {
    ...mapActions('dashboard', ['inboxUpdate']),

    toggle: function( event ) {
      event.target.classList.toggle('is-open');
      
    },

    toggleFooter: function() {
      var element = document.getElementById('myDIV');
      var element2 = document.getElementById('trans');
      var element3 = document.getElementById('mobileUL');
      element.classList.toggle('footerstyle');
      element2.classList.toggle('trans-style');
      element3.classList.toggle('is-open');
    },
    toggleUL: function(event) {
      event.target.classList.toggle('is-open');
      var element3 = document.getElementById('mobileUL');
      var element4 = document.getElementById('mobileICO');
      element3.classList.toggle('is-open');
      element4.classList.toggle('is-open');
      
    },

    async getInboxCount() {
      if (window.location.href.includes('dashboard') && this.role !== 'admin') {
        const { data } = await wrapper(
          api.custom.get(`${this.role !== 'user' ? this.role : ''}/inbox/unread-count`)
        );
  
        this.inboxUpdate(data);
      }
    },

    toggleNav() {
      this.nav.isOpen = !this.nav.isOpen;
    },

    utmSet() {
      const utm_source_in_url = this.getUrlQuery('utm_source');
      const gclid = this.getUrlQuery('gclid') ;

      if (utm_source_in_url) {
        Cookies.set(
          'utm',
          {
            utm_source: gclid ? gclid : utm_source_in_url,
            utm_medium: this.getUrlQuery('utm_medium'),
            utm_content: this.getUrlQuery('utm_content'),
            utm_campain: this.getUrlQuery('utm_campain'),
          },
          { expires: 90 }
        );
      }
    },

    rfrrSet() {
      const rfrr_in_url = this.getUrlQuery('rfrr');

      if (rfrr_in_url) {
        Cookies.set(
          'rfrr',
          rfrr_in_url,
          { expires: 90 }
        );
      }
    },

    metaSet() {
      const gclid = this.getUrlQuery('gclid');
      const click_id = this.getUrlQuery('click_id');
      const tag = this.getUrlQuery('tag');

      if (gclid || click_id || tag) {
        Cookies.set(
          'meta',
          {
            gclid     : gclid && gclid.length < 100 ? gclid : null,
            click_id  : click_id && click_id.length < 100 ? click_id : null,
            tag       : tag && tag.length < 100 ? tag : null,
          },
          { expires: 90 }
        );
      }
    },

    getUrlQuery(name) {
      const queryString = window.location.search;
      const urlParams = new URLSearchParams(queryString);

      return urlParams.get(name);
    },
  }
};
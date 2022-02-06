import { mapState } from 'vuex';

/**
 * Sidebar links array
 */
import userLinks from '@/data/SidebarMenu';

export default {
  name: 'Sidebar',

  components: {
    moreLinks: () => import('@/components/layouts/dashboard/MoreLinks/Index')
  },

  data() {
    return {
      moreLinks: false
    };
  },

  computed: {
    ...mapState('dashboard', ['role', 'inbox']),

    links() {
      const array = [];
      
      userLinks.forEach(link => {
        link.roles.forEach(role => {
          if (this.role === role) {
            array.push(link);
          }
        });
      });

      return array;
    }
  },

  methods: {
    toggleMoreLinks() {
      this.moreLinks = !this.moreLinks;
    },

    closeMoreLinks(e) {
      const btn = this.$refs.moreLinksBtn;

      if (e.target !== btn) {
        this.moreLinks = false;
      }
    },
  },

  mounted () {
    document.addEventListener('click', this.closeMoreLinks);
  },

  beforeDestroy () {
    document.removeEventListener('click', this.closeMoreLinks);
  }
};

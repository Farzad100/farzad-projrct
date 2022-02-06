// Links
import userLinks from '@/data/SidebarMenu';

// Vuex
import { mapActions, mapState } from 'vuex';

export default {
  name: 'MoreLinks',
  
  components: {
    changeRole: () => import('@/components/layouts/dashboard/ChangeRole/Index')
  },

  computed: {
    ...mapState('dashboard', ['role']),
    links() {
      const array = [];

      userLinks.forEach(link => {
        if (link.classes) {
          link.roles.forEach(role => {
            if (this.role === role) {
              array.push(link);
            }
          });
        }
      });

      return array;
    }
  },

  methods: {
    ...mapActions('auth', ['logout']),
    handleLogout() {
      this.logout()
        .then(() => {
          this.$router.push({
            name: 'login'
          });
        });
    }
  }
};

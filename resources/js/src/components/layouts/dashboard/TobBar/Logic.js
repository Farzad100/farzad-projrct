import { mapActions, mapState } from 'vuex';

export default {
  name: 'TopBar',

  components: {
    changeRole: () => import('@/components/layouts/dashboard/ChangeRole/Index')
  },

  data() {
    return {
      roleDrp: false,
      notifDrp: false
    };
  },

  computed: {
    ...mapState('auth', ['roles']),
    ...mapState('dashboard', ['role', 'inbox']),
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
    },

    toggleRoleDrp() {
      this.roleDrp = !this.roleDrp;
    },

    closeRoleDrp(e) {
      if (this.roles && this.roles.length > 1) {
        const btn = this.$refs.changeRole;

        if (e.target !== btn.$el) {
          this.roleDrp = false;
        }
      }
    },

  },

  mounted () {
    document.addEventListener('click', this.closeRoleDrp);
  },

  beforeDestroy () {
    document.removeEventListener('click',this.closeRoleDrp);
  }
};

<template>
  <div class="dashboard">
    <!-- Sidebar (Route links) -->
    <sidebar />

    <!-- Content of page + navbar -->
    <div class="dashboard-wrapper">
      <!-- 
        Component for show
        alerts in top of all
        pages
      -->
      <alert-messages />

      <!-- 
        Navigation and role
        controller
      -->
      <top-bar />

      <!-- Main page content -->
      <router-view
        :class="[
          role === 'admin' ? 'mx-auto col-12 col-xl-11' : 'container',
          'container-wide p-md-4'
        ]"
      />
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex';
import { inArray } from '@/global/Functions';

export default {
  name: 'DashboardLayout',

  components: {
    sidebar: () => import('@/components/layouts/dashboard/Sidebar/Index'),
    topBar: () => import('@/components/layouts/dashboard/TobBar/Index')
  },

  data() {
    return {
      url_data: '',
      url: []
    };
  },

  computed: {
    ...mapState('dashboard', ['role']),
    ...mapState('auth', ['roles'])
  },
  beforeMount() {
    // eslint-disable-next-line no-debugger
    // debugger; UNCX
    this.url_data = this.$route.name;
    this.url = this.url_data.split('-');

    if (inArray(this.url[1], ['admin', 'organ', 'user', 'shop']))
      this.changeRole(this.url[1]);
    else this.changeRole(this.$route.path.split('/')[2]);
  },
  methods: {
    ...mapActions('dashboard', ['changeRole'])
  }
};
</script>

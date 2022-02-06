<template>
  <div>
    <!-- 
      Only users can access
    -->
    <single-user v-if="role === 'user'" />

    <!--
      Accessiable for Admin, Shop, Organ
    -->
    <single-manager v-else />
  </div>
</template>

<script>
import { mapState } from 'vuex';

export default {
  name: 'OrderPage',

  metaInfo () {
    return {
      title: `سفارش ${this.$route.params.id}`
    };
  },
  
  components: {
    SingleUser: () => import('@/components/dashboard/Order/SingleUser/Index'),
    SingleManager: () => import('@/components/dashboard/Order/SingleManager/Index')
  },

  computed: {
    ...mapState('dashboard', ['role'])
  },
};
</script>

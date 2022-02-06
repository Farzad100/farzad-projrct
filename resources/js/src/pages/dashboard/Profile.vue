<template>
  <div>
    <profile-edit />
  </div>
</template>

<script>
export default {
  name: 'ProfilePage',

  metaInfo: {
    title: 'حساب کاربری',
  },
  
  components: {
    ProfileEdit: () => import('@/components/dashboard/ProfileEdit/Index')
  }
};
</script>

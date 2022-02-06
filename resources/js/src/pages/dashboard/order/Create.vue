<template>
  <div>
    <create-order />
  </div>
</template>

<script>
export default {
  name: 'NewOrderPage',

  metaInfo: {
    title: 'سفارش جدید',
  },

  components: {
    createOrder: () => import('@/components/dashboard/Order/Create/Index')
  },
};
</script>

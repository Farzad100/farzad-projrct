<template>
  <div>
    <div class="_reposts-tab">
      <button
        :class="{ 'active': selectedReport === 'users'}"
        @click="changeReport('users')"
      >
        کاربران
      </button>
      <button
        :class="{ 'active': selectedReport === 'orders'}"
        @click="changeReport('orders')"
      >
        سفارشات موفق
      </button>
      <button
        :class="{ 'active': selectedReport === 'sales'}"
        @click="changeReport('sales')"
      >
        فروش
      </button>
      <button
        :class="{ 'active': selectedReport === 'installment'}"
        @click="changeReport('installment')"
      >
        قسط
      </button>
    </div>

    <keep-alive>
      <user-reports v-if="selectedReport === 'users'" />
      <order-reports v-if="selectedReport === 'orders'" />
      <sales-reports v-if="selectedReport === 'sales'" />
      <installment-reports v-if="selectedReport === 'installment'" />
    </keep-alive>
    <router-view />
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>
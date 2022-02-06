<template>
  <div class="row">
    <!-- Active card (order) -->
    <div class="col-12 col-xl-8 mb-3 mb-xl-0">
      <active-card :data="activeCard" />

      <router-link
        :to="{ name: 'dash-user-new-order' }"
        class="btn btn-primary w-100 rounded-pill py-2 mt-4 d-lg-none"
      >
        ثبت سفارش جدید
      </router-link>
    </div>

    <!-- Help links -->
    <div class="col-12 col-xl-4 d-none d-lg-block mb-3 mb-xl-0">
      <help-box />
    </div>

    <div class="col-12">
      <table-list
        list-name="آخرین قسط‌ها"
        :table-list="ghests"
        :loading="ghestsLoading"
        @buttonClick="actionsHandle"
      />
    </div>
    <router-view />
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>
<template>
  <div class="active-card rounded border shadow-sm">
    <div
      class="info mb-4 pt-3 pt-md-0 mb-md-0 text-center text-md-right"
      :class="!data ? 'w-100' : null"
    >
      <h3 class="special-font font-weight-bold mb-3">
        {{ data ? 'آخرین سفارش فعال' : 'ثبت سفارش جدید' }}
      </h3>
      <h5 class="font-weight-light opa-7 mb-md-5 pl-md-4">
        <span v-if="data">
          {{ data.what_to_do }}
        </span>
        <span v-else>
          شما در حال حاضر هیچ سفارش فعالی ندارید، برای درخواست قسطا کارت بر روی
          دکمه زیر کلیک کنید
        </span>
      </h5>
      <router-link
        v-if="data"
        :to="{
          name: 'dash-order-single',
          params: { oid: data.oid, role: 'user' }
        }"
        class="btn btn-primary rounded-pill"
      >
        مشاهده سفارش
      </router-link>

      <router-link
        v-else
        :to="{ name: 'dash-user-new-order' }"
        class="btn btn-primary rounded-pill"
      >
        ثبت سفارش جدید
      </router-link>
    </div>

    <div
      v-if="data"
      class="show-card"
    >
      <order-card :data="data" />
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

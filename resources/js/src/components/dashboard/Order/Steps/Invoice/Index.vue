<template>
  <div class="border rounded shadow-sm mt-3">
    <div class="px-4 pt-4">
      <div
        v-for="(item, i) in $parent.orderData.final_invoice.details"
        :key="i"
        class="d-flex justify-content-between aic border-bottom py-3"
      >
        <span>
          {{ item.title }}
        </span>
        <h5 class="price-style font-weight-bold">
          {{ item.price | moneySeperate }}
          <small>
            تومان
          </small>
        </h5>
      </div>

      <div class="bg-green d-flex justify-content-between aic py-4">
        <span>
          مبلغ قابل پرداخت
        </span>
        <h1 class="price-style text-green font-weight-bold">
          {{ $parent.orderData.final_invoice.total | moneySeperate }}
          <small>
            تومان
          </small>
        </h1>
      </div>
    </div>

    <div
      class="d-flex flex-column flex-sm-row p-4 bg-gray-light border-top justify-content-between"
    >
      <div class="d-flex mb-4 mb-sm-0">
        <input
          v-model="orderDiscount"
          class="form-control ml-2"
          placeholder="کد تخفیف"
        >
        <g-button
          ref="discount"
          text="بررسی"
          sm
          class="rounded"
          @click.native="checkDiscount"
        />
      </div>

      <g-button
        ref="pay"
        text="پرداخت"
        icon-right="credit-card"
        icon-right-space="ml-2"
        color="success"
        center
        @click.native="pay"
      />
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

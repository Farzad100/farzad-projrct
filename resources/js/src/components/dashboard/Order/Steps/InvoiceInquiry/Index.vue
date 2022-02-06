<template>
  <div class="border rounded shadow-sm">
    <div class="p-4">
      <h4 class="special-font">
        صورتحساب
      </h4>
      <p class="font-weight-light opa-7">
        {{ data.description }}
      </p>

      <div
        v-for="(item, index) in data.details"
        :key="index"
        class="d-flex border-bottom justify-content-between align-items-center py-2 mt-5"
      >
        <span>
          {{ item.title }}
        </span>

        <strong class="price-style">
          {{ item.price | moneySeperate }}
          <small class="opa-5">
            تومان
          </small>
        </strong>
      </div>

      <div class="py-4 border-top d-flex justify-content-end">
        <div>
          <small class="d-block">
            مجموع
          </small>
          <h2 class="price-style text-green font-weight-bold">
            {{ data.total | moneySeperate }}
            <small>
              تومان
            </small>
          </h2>
        </div>
      </div>
    </div>

    <div
      class="p-4 bg-gray-light border-top d-flex justify-content-between align-items-center"
    >
      <!-- <div class="d-flex align-items-center">
        <input
          class="form-control ml-2 rounded-pill"
          placeholder="کد تخفیف"
        >
        <g-button
          ref="discount"
          text="بررسی"
          sm
          @click.native="checkCode"
        />
      </div> -->
      <g-button
        ref="pay"
        text="پرداخت صورتحساب"
        color="success"
        @click.native="pay"
      />
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

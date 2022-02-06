<template>
  <div>
    <div class="border rounded shadow-sm">
      <div>
        <div class="p-4 border-bottom">
          <h5 class="special-font font-weight-bold mb-3">
            شارژ اضافی
          </h5>
          <small>
            در صورتی که موجودی قسطاکارت شما برای خرید کالا/خدمات کافی نیست، می
            توانید مبلغ موردنظر خود را تا سقف
            <span class="text-green font-weight-bold">
              پنج میلیون تومان
            </span>
            به قسطا پرداخت کنید تا در قسطاکارت شما شارژ شود.
          </small>
        </div>

        <div class="py-5 my-5 d-flex justify-content-center">
          <amount-ranger
            v-model="extraAmount"
            :min="50000"
            :max="5000000"
            step="50000"
            landing-style
            compressed
            :hide="{
              monthPicker: true,
              seke: true,
              input: true,
              hintCenter: true
            }"
          />
        </div>
      </div>
      <div
        class="p-4 d-flex bg-gray-light border-top
        justify-content-between align-items-center"
      >
        <h2 class="price-style text-green font-weight-bold">
          {{ extraAmount | moneySeperate }}
          <small>
            تومان
          </small>
        </h2>
        <g-button
          ref="payExtra"
          text="پرداخت"
          color="primary"
          :disabled="extraAmount < 10000 || extraAmount > 5000000"
          @click.native="payExtra"
        />
      </div>
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

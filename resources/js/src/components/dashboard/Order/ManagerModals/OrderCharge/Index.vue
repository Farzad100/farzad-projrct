<template>
  <modal
    v-show="orderChargeModal"
    title="بررسی و شارژ قسطاکارت"
    @close="orderChargeModal = false"
  >
    <template #body>
      <div
        v-if="$parent.order.cta && $parent.order.cta.data"
        class="alert alert-warning mb-4"
      >
        <!-- eslint-disable vue/no-v-html -->
        <p
          class="m-0"
          v-html="$parent.order.cta.data.description"
        />
      </div>

      <div class="d-flex aic mb-3">
        <div class="border rounded shadow-sm p-4 w-100">
          <span class="opa-5">
            مبلغ
          </span>
          <h4 class="price-style d-flex font-weight-bold">
            {{ $parent.order.cta.data.amount | moneySeperate }}
            <small>
              تومان
            </small>
          </h4>
        </div>
        <h4 class="mx-4">
          -
        </h4>
        <div class="border rounded shadow-sm p-4 w-100">
          <span class="opa-5">
            کارمزد
          </span>
          <h4 class="price-style d-flex font-weight-bold">
            {{ $parent.order.cta.data.commission | moneySeperate }}
            <small>
              تومان
            </small>
          </h4>
        </div>
      </div>

      <div
        class="border d-flex justify-content-between aic rounded shadow-sm p-4"
      >
        <span class="opa-5 text-green">
          مبلغ قابل شارژ
        </span>
        <h4 class="price-style font-weight-bold text-green">
          {{ $parent.order.cta.data.amount_to_charge | moneySeperate }}
          <small>
            تومان
          </small>
        </h4>
      </div>

      <div class="mt-5 d-flex aic p-3">
        <i class="fad fa-user fa-lg ml-4" />
        <div>
          <h5 class="special-font font-weight-bold mb-0">
            {{ $parent.order.cta.data.name }}
          </h5>
          <small class="opa-5">
            {{ $parent.order.cta.data.mobile }}
          </small>
        </div>
      </div>

      <div class="d-flex aic border-top p-3">
        <i class="fad fa-credit-card fa-lg ml-4" />
        <div>
          <span class="opa-5">
            شماره کارت
          </span>
          <h4>
            {{ $parent.order.cta.data.card_number }}
          </h4>
        </div>
      </div>

      <div class="mt-5">
        <label>
          سری شارژ
        </label>
        <input
          v-model="form.series"
          class="form-control ltr estedad-font text-center form-control-lg mb-4"
        >
        <div class="checkbox-in-list _radio-style px-2">
          <input
            id="charge"
            v-model="form.mode"
            type="radio"
            name="mode"
            value="charge"
          >
          <label for="charge">
            <span>
              شارژ شود
            </span>
          </label>
        </div>

        <div class="checkbox-in-list _radio-style px-2">
          <input
            id="offline"
            v-model="form.mode"
            type="radio"
            name="mode"
            value="offline"
          >
          <label for="offline">
            <span>
              بصورت آفلاین شارژ شده، فقط به دوره چک/اقساط برود
            </span>
          </label>
        </div>

        <div class="checkbox-in-list _radio-style px-2">
          <input
            id="manual"
            v-model="form.mode"
            type="radio"
            name="mode"
            value="manual"
          >
          <label for="manual">
            <span>
              شارژ با اختلال، اما انجام شده، به دوره چک/اقساط برود
            </span>
          </label>
        </div>
      </div>
    </template>

    <template #footer>
      <div class="d-flex w-100 justify-content-between">
        <button
          class="btn btn-light rounded-pill"
          @click="orderChargeModal = false"
        >
          انصراف
        </button>

        <g-button
          ref="chargeGC"
          text="تایید"
          type="button"
          color="warning"
          :disabled="!form.series"
          @click.native="chargeGhestaCard"
        />
      </div>
    </template>
  </modal>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

<template>
  <modal
    v-show="modal"
    title="پیش‌نمایش محاسبه‌گر"
    size="lg"
    @close="modal = false"
  >
    <template #body>
      <div class="ranger">
        <div
          v-if="loading"
          class="page-loading"
        >
          <div class="spinner-border" />
        </div>
        <div v-else>
          <div class="ranger-head border-bottom mb-5">
            <amount-ranger
              v-model="order.amount"
              landing-style
              min="1200000"
              :max="maxes.third"
              :hide="{
                monthPicker: true,
                seke: true,
                hintCenter: true
              }"
            />
          </div>
          <div class="c _in-vue border-0 mt-5">
            <div
              class="d-flex flex-column align-items-center justify-content-between mt-5"
            >
              <div class="d-flex flex-column position-relative w-100">
                <span
                  v-if="
                    order.amount > maxes.first && order.amount <= maxes.second
                  "
                  class="text-danger alert-small"
                >
                  این مبلغ برای سفارش
                  <strong
                    class="special-font border border-danger px-2 rounded-pill"
                  >دوم</strong>
                  به بعد است
                </span>
                <span
                  v-if="order.amount > maxes.second"
                  class="text-danger alert-small"
                >
                  این مبلغ برای سفارش
                  <strong
                    class="special-font border border-danger px-2 rounded-pill"
                  >سوم</strong>
                  به بعد است
                </span>
              </div>

              <div class="btn-group btn-group-sm threeD-style btn-pill">
                <template v-for="(model, mIndex) in payback_models">
                  <template v-for="(ghest, ind) in model.ghests">
                    <input
                      :id="'m' + model.month + 'c' + ghest"
                      :key="`${ind}-${mIndex}`"
                      v-model="refund"
                      :value="{ months: model.month, cheques: ghest }"
                      type="radio"
                      class="btn-check"
                      name="ghestPriod"
                      autocomplete="off"
                    >
                    <label
                      :key="`m${ind}-${mIndex}`"
                      class="btn"
                      :for="'m' + model.month + 'c' + ghest"
                    >
                      {{ model.month }} ماه، {{ ghest }}
                      {{ order.type != 'individual' ? 'قسط' : 'چک' }}
                    </label>
                  </template>
                </template>
              </div>
            </div>

            <div class="row mx-0">
              <div class="s-ca-parent col-12 col-md-6 col-lg-4">
                <div class="s-ca">
                  <div class="py-3">
                    <p class="special-font">
                      مجموع بازپرداخت
                    </p>
                    <h5 class="price-style">
                      {{ ghestify(order, refund).payback | moneySeperate }}
                      <small>تومان</small>
                    </h5>
                  </div>
                </div>
              </div>

              <div class="s-ca-parent col-12 col-md-6 col-lg-4">
                <div class="s-ca">
                  <div class="py-3">
                    <p class="special-font">
                      پیش‌پرداخت
                    </p>
                    <h5 class="price-style d-flex align-items-center">
                      <input
                        v-if="order.manual"
                        v-model="order.prepayment"
                        class="border-0 text-center ltr estedad-font w-100 rounded bg-gray-light"
                      >
                      <span v-else>
                        {{ ghestify(order, refund).prepayment | moneySeperate }}
                      </span>
                      <small>تومان</small>
                    </h5>
                  </div>
                </div>
                <div class="edit d-flex justify-content-center">
                  <g-button
                    :text="order.manual ? 'خودکار' : 'ویرایش دستی'"
                    class="text-blue"
                    sm
                    @click.native="order.manual = !order.manual"
                  />
                </div>
              </div>

              <div class="s-ca-parent col-12 col-md-6 col-lg-4">
                <div class="s-ca">
                  <div class="py-3">
                    <p class="special-font">
                      مبلغ هر
                      {{ order.type != 'individual' ? 'قسط' : 'چک' }}
                    </p>
                    <h5 class="price-style">
                      {{ ghestify(order, refund).ghest | moneySeperate }}
                      <small>تومان</small>
                    </h5>
                  </div>
                </div>
                <small
                  v-if="
                    ghestify(order, refund).ghest >= maxes.gFirst &&
                      ghestify(order, refund).ghest < maxes.gSecond
                  "
                  class="text-danger d-block text-center"
                >
                  این مبلغ چک برای سفارش
                  <strong
                    class="special-font border border-danger px-2 rounded-pill"
                  >دوم</strong>
                  به بعد است
                </small>
                <small
                  v-if="ghestify(order, refund).ghest >= maxes.gSecond"
                  class="text-danger d-block text-center"
                >
                  این مبلغ چک برای سفارش
                  <strong
                    class="special-font border border-danger px-2 rounded-pill"
                  >سوم</strong>
                  به بعد است
                </small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </modal>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

<template>
  <div class="pb-5">
    <div
      v-if="loadingPage"
      class="page-loading"
    >
      <div class="spinner-border" />
    </div>

    <template v-else>
      <div
        v-if="create_control && create_control.is_lock && !order.isOrgan"
        class="rounded border border-warning"
      >
        <div
          class="light-warning-bg text-orange p-4 text-center py-5 special-font font-lg"
        >
          <i class="fad fa-traffic-cone fa-2x mb-5" />
          <div class="font-weight-bold">
            {{ create_control.text }}
          </div>
        </div>
        <div
          class="bg-gray-light p-4 d-flex justify-content-end border-top border-warning"
        >
          <g-button
            text="کد سازمانی دارم"
            sm
            @click.native="order.isOrgan = true"
          />
        </div>
      </div>

      <div
        v-else-if="allowToCreate"
        class="new-order"
        :class="
          checkingOrgOpt.type === 'success' || !order.isOrgan
            ? ''
            : 'organ-wait'
        "
      >
        <template v-if="step === 1">
          <div
            v-if="shopStatus === 'inactive'"
            class="p-4 rounded text-danger bg-red-light"
          >
            <i class="fad fa-traffic-cone fa-lg ml-2" />
            فروشگاه شما غیرفعال است
          </div>
          <div
            v-else
            class="row"
          >
            <div
              class="new-order"
              :class="shopStatus"
            >
              <h3 class="special-font text-brand text-center">
                تعیین مشتری
              </h3>
              <span class="mb-3 mb-lg-5 d-block text-center">
                شماره موبایل مشتری را برای ادامه وارد کنید
              </span>
              <send-otp
                type="shop_order"
                @formSubmit="nextStep"
              />

              <div class="col-12 col-md-4 border-top d-flex mx-auto mt-4 pt-4">
                <g-button
                  text="پیش‌نمایش محاسبه‌گر"
                  sm
                  class="mx-auto"
                  @click.native="$refs.calc.openModal()"
                />
              </div>

              <calculator ref="calc" />
            </div>
          </div>
        </template>

        <template v-if="step === 2">
          <h3 class="special-font text-brand text-center mt-5">
            احراز هویت مشتری
          </h3>
          <span class="mb-3 mb-lg-5 d-block text-center">
            کد ارسال شده برای مشتری را وارد کنید
          </span>
          <check-otp
            type="shop_order"
            :otp-data="otpData"
            @formSubmit="nextStep"
            @changeMobile="showFirstStep"
          />
        </template>

        <template v-if="step === 3">
          <h3 class="special-font text-brand text-center mt-5">
            ثبت‌نام مشتری
          </h3>
          <span class="mb-3 mb-lg-5 d-block text-center">
            اطلاعات مشتری را مطابق کارت ملی او وارد کنید
          </span>
          <user-register-by-seller
            :otp-data="otpData"
            @formSubmit="nextStep"
          />
        </template>

        <template v-if="step === 4">
          <div class="row">
            <div class="col-12 col-xl-8">
              <!-- eslint-disable vue/no-v-html -->
              <div
                v-if="order && order.important_msg"
                class="d-flex alert"
                :class="'alert-' + order.important_msg.color"
              >
                <i
                  class="fal fa-exclamation-circle align-self-center h2 ml-2"
                />
                <span
                  class="text-justify"
                  v-html="order.important_msg.text"
                />
              </div>
              <!-- Tab button to define type of order -->
              <div
                v-if="role !== 'shop'"
                class="new-order-section section-first py-5"
              >
                <div class="description">
                  <h4 class="special-font font-weight-bold text-brand">
                    <span class="opa-3">۱.</span>
                    نوع خرید
                  </h4>
                  <p class="font-weight-lighter">
                    اگر قصد سفارش قسطا کارت برای خودتان را دارید حالت شخصی و اگر
                    از طرف سازمانی که در آن کار می‌کنید کد مخصوص به خودتان را
                    دریافت کردید می‌توانید حالت خرید سازمانی را انتخاب کنید
                  </p>
                </div>

                <div class="btn-group d-flex">
                  <input
                    id="false"
                    v-model="order.isOrgan"
                    :value="false"
                    type="radio"
                    class="btn-check"
                    name="shopType"
                    autocomplete="off"
                  >
                  <label
                    class="btn"
                    for="false"
                  >
                    خرید شخصی
                  </label>

                  <input
                    id="true"
                    v-model="order.isOrgan"
                    :value="true"
                    type="radio"
                    class="btn-check"
                    name="shopType"
                    autocomplete="off"
                  >
                  <label
                    class="btn"
                    for="true"
                  >
                    خرید سازمانی
                  </label>
                </div>

                <div
                  v-if="order.isOrgan"
                  class="mt-4 "
                >
                  <label for="organCde">کد سازمانی</label>
                  <input
                    id="organCde"
                    ref="organCde"
                    v-model="organCode"
                    placeholder="___   ___   ___   ___   ___"
                    class="form-control text-center"
                    maxlength="8"
                    @keydown="keyUpHandle"
                  >
                  <loader-sm
                    :loading="checkingOrgCode"
                    :options="checkingOrgOpt"
                  />
                </div>
              </div>

              <!-- Choose amount -->
              <div class="new-order-section py-5">
                <div class="description mb-1">
                  <h4 class="special-font font-weight-bold text-brand">
                    <span class="opa-3">
                      {{ role === 'shop' ? '۱.' : '۲.' }}
                    </span>
                    اعتبار درخواستی
                  </h4>
                  <p class="font-weight-lighter">
                    در این قسمت مبلغ مورد نیاز را انتخاب کنید.
                  </p>
                </div>

                <amount-ranger
                  v-model="order.amount"
                  min="1200000"
                  :max="maxAllowedAmount"
                  class="my-5"
                  :hide="{
                    monthPicker: true,
                    seke: true,
                    hintCenter: true
                  }"
                  landing-style
                />

                <div
                  v-if="role === 'shop'"
                  class="text-center"
                  style="margin-top: 180px"
                >
                  <div class="d-flex justify-content-between px-3">
                    <label>
                      مبلغ پیش‌پرداخت
                    </label>
                    <div class="form-check form-switch">
                      <input
                        id="flexSwitchCheckDefault"
                        v-model="order.manual"
                        class="form-check-input"
                        type="checkbox"
                      >
                      <label
                        class="form-check-label"
                        for="flexSwitchCheckDefault"
                      />
                    </div>
                  </div>
                  <input
                    v-model="order.prepayment"
                    type="number"
                    class="form-control text-center rounded-pill ltr estedad-font "
                    :disabled="!order.manual"
                  >
                  <small
                    v-if="order.prepayment > order.amount - 1000000"
                    class="text-danger"
                  >
                    پیش‌پرداخت حداقل باید یک میلیون تومان از مبلغ درخواستی کمتر
                    باشد
                  </small>
                  <small
                    v-else-if="order.prepayment < order.amount * order.rpa"
                    class="text-danger"
                  >
                    پیش‌پرداخت باید بیشتر از 15درصد مبلغ درخواستی باشد
                  </small>
                  <small
                    v-else
                    class="d-block mt-1 font-weight-light"
                  >
                    {{
                      !order.prepayment ? '0' : order.prepayment | numToPersian
                    }}
                    <span class="opa-5 pr-1">تومان</span>
                  </small>

                  <div class="d-flex justify-content-between px-3 pt-5">
                    <label>
                      نام کالا یا خدمات مورد فروش را وارد کنید
                    </label>
                  </div>
                  <input
                    v-model="order.product"
                    type="text"
                    class="form-control text-center rounded-pill estedad-font "
                  >
                </div>

                <!-- <small
                  v-if="role === 'user'"
                  class="mx-auto col-12 col-md-8 mt-5 opa-5"
                  style="line-height: 30px"
                >
                  سقف مبلغ درخواستی در سفارش اول
                  <span class="border-bottom font-weight-bold mx-1">
                    {{ maxes.first | moneySeperate }}
                    تومان
                  </span>
                  است، در سفارش دوم
                  <span class="border-bottom font-weight-bold mx-1">
                    {{ maxes.second | moneySeperate }}
                    تومان
                  </span>
                  و از سفارش سوم به بعد
                  <span class="border-bottom font-weight-bold mx-1">
                    {{ maxes.third | moneySeperate }}
                    تومان
                  </span>
                  خواهد بود
                </small> -->
              </div>

              <!-- Refund priods -->
              <div class="new-order-section py-5 mt-5">
                <div class="description mb-4 mt-5">
                  <h4 class="special-font font-weight-bold text-brand">
                    <span class="opa-3">
                      {{ role === 'shop' ? '۲.' : '۳.' }}
                    </span>
                    بازپرداخت
                  </h4>
                </div>
                <selectable-input-group
                  ref="refundSelector"
                  v-model="refund"
                  type="radio"
                  name="myRadioInputs"
                  class="row justify-content-center w-100"
                >
                  <selectable-input
                    v-show="order.isOrgan"
                    id="tenTen"
                    class="col-6 col-md-3 mb-3 refund-priod-card"
                    :value="{ months: 10, cheques: 10 }"
                    :class="
                      ghestify(order, { months: 10, cheques: 10 }).ghest >=
                        maxAllowedGhest
                        ? 'limit'
                        : null
                    "
                  >
                    <div>
                      <span class="text-center">
                        <strong>10</strong>
                        ماه
                      </span>
                      <span class="text-center">
                        <strong>10</strong>
                        قسط
                      </span>
                    </div>
                    <small
                      class="mt-2 d-block text-center"
                    >مجموع همه پرداخت ها</small>
                    <span class="price-style">
                      <strong>{{
                        ghestify(order, { months: 10, cheques: 10 }).total
                          | moneySeperate
                      }}</strong>
                      <small>تومان</small>
                    </span>
                    <div class="limit-alert">
                      <span>
                        غیرقابل انتخاب
                      </span>
                    </div>
                  </selectable-input>

                  <selectable-input
                    id="tenFive"
                    class="col-6 col-md-3 mb-3 refund-priod-card"
                    :value="{ months: 10, cheques: 5 }"
                    :class="
                      ghestify(order, { months: 10, cheques: 5 }).ghest >=
                        maxAllowedGhest
                        ? 'limit'
                        : null
                    "
                  >
                    <div>
                      <span class="text-center">
                        <strong>10</strong>
                        ماه
                      </span>
                      <span class="text-center">
                        <strong>5</strong>
                        قسط
                      </span>
                    </div>
                    <small
                      class="mt-2 d-block text-center"
                    >مجموع همه پرداخت ها</small>
                    <span class="price-style">
                      <strong>{{
                        ghestify(order, { months: 10, cheques: 5 }).total
                          | moneySeperate
                      }}</strong>
                      <small>تومان</small>
                    </span>
                    <div class="limit-alert">
                      <span>
                        غیرقابل انتخاب
                      </span>
                    </div>
                  </selectable-input>

                  <selectable-input
                    id="sixSix"
                    ref="defaultRefund"
                    class="col-6 col-md-3 mb-3 refund-priod-card"
                    :value="{ months: 6, cheques: 6 }"
                    :class="
                      ghestify(order, { months: 6, cheques: 6 }).ghest >=
                        maxAllowedGhest
                        ? 'limit'
                        : null
                    "
                    :checked="true"
                  >
                    <div>
                      <span class="text-center">
                        <strong>6</strong>
                        ماه
                      </span>
                      <span class="text-center">
                        <strong>6</strong>
                        قسط
                      </span>
                    </div>
                    <small
                      class="mt-2 d-block text-center"
                    >مجموع همه پرداخت ها</small>
                    <span class="price-style">
                      <strong>{{
                        ghestify(order, { months: 6, cheques: 6 }).total
                          | moneySeperate
                      }}</strong>
                      <small>تومان</small>
                    </span>
                    <div class="limit-alert">
                      <span>
                        غیرقابل انتخاب
                      </span>
                    </div>
                  </selectable-input>

                  <selectable-input
                    id="sixThree"
                    class="col-6 col-md-3 mb-3 refund-priod-card"
                    :value="{ months: 6, cheques: 3 }"
                    :class="
                      ghestify(order, { months: 6, cheques: 3 }).ghest >=
                        maxAllowedGhest
                        ? 'limit'
                        : null
                    "
                  >
                    <div>
                      <span class="text-center">
                        <strong>6</strong>
                        ماه
                      </span>
                      <span class="text-center">
                        <strong>3</strong>
                        قسط
                      </span>
                    </div>
                    <small
                      class="mt-2 d-block text-center"
                    >مجموع همه پرداخت ها</small>
                    <span class="price-style">
                      <strong>{{
                        ghestify(order, { months: 6, cheques: 3 }).total
                          | moneySeperate
                      }}</strong>
                      <small>تومان</small>
                    </span>
                    <div class="limit-alert">
                      <span>
                        غیرقابل انتخاب
                      </span>
                    </div>
                  </selectable-input>

                  <selectable-input
                    id="threeThree"
                    class="col-6 col-md-3 mb-3 refund-priod-card"
                    :value="{ months: 3, cheques: 3 }"
                    :class="
                      ghestify(order, { months: 3, cheques: 3 }).ghest >=
                        maxAllowedGhest
                        ? 'limit'
                        : null
                    "
                  >
                    <div>
                      <span class="text-center">
                        <strong>3</strong>
                        ماه
                      </span>
                      <span class="text-center">
                        <strong>3</strong>
                        قسط
                      </span>
                    </div>
                    <small
                      class="mt-2 d-block text-center"
                    >مجموع همه پرداخت ها</small>
                    <span class="price-style">
                      <strong>{{
                        ghestify(order, { months: 3, cheques: 3 }).total
                          | moneySeperate
                      }}</strong>
                      <small>تومان</small>
                    </span>
                    <div class="limit-alert">
                      <span>
                        غیرقابل انتخاب
                      </span>
                    </div>
                  </selectable-input>
                </selectable-input-group>
              </div>

              <div class="new-order-footer px-3">
                <div class="mx-auto mt-lg-5 d-flex justify-content-center">
                  <button
                    class="btn btn-success rounded-pill btn-lg"
                    :class="loading ? 'btn-loading' : ''"
                    :disabled="allowCreate"
                    @click="createOrder"
                  >
                    <div
                      class="spinner-border"
                      role="status"
                    >
                      <span class="sr-only">در حال بررسی</span>
                    </div>
                    <span class="btn-text d-flex aic w-100 jcb px-5">
                      ثبت سفارش
                    </span>
                  </button>
                </div>
              </div>
            </div>

            <div class="col-12 col-xl-4">
              <div class="result-of-order new-order-section pb-5">
                <div
                  class="list-view list-view-lg w-100 border shadow-sm rounded p-e-none"
                >
                  <h5
                    class="special-font font-weight-bold border-bottom bg-gray-light text-right px-3 py-4 mb-0"
                  >
                    <i class="fad fa-credit-card fa-lg ml-2" />
                    اطلاعات کلی سفارش
                  </h5>
                  <div
                    v-if="order.custom_card_msg"
                    class="light-success-bg text-green text-right px-3 py-2"
                  >
                    <small>
                      {{ order.custom_card_msg }}
                    </small>
                  </div>
                  <div class="list-view-item mt-2">
                    <div class="d-flex aic flex-wrap">
                      <div class="title">
                        <span>میزان اعتبار</span>
                      </div>
                      <h4 class="mr-auto">
                        {{ order.amount | moneySeperate }}
                        <small>تومان</small>
                      </h4>
                    </div>
                  </div>

                  <div class="list-view-item">
                    <div class="d-flex aic flex-wrap">
                      <div class="title">
                        <span>نحوه باز پرداخت</span>
                      </div>
                      <h4 class="mr-auto">
                        {{ refund.months }}
                        <small class="ml-2">ماه،</small>
                        {{ refund.cheques }}
                        <small>چک</small>
                      </h4>
                    </div>
                  </div>

                  <div class="list-view-item">
                    <div class="d-flex aic flex-wrap">
                      <div class="title">
                        <span>مبلغ هر قسط</span>
                      </div>
                      <h4 class="mr-auto">
                        {{ ghestify(order, refund).ghest | moneySeperate }}
                        <small>تومان</small>
                      </h4>
                    </div>
                  </div>

                  <div class="list-view-item">
                    <div class="d-flex aic flex-wrap">
                      <div class="title">
                        <span>پیش پرداخت</span>
                      </div>
                      <h4 class="mr-auto d-flex flex-column align-items-end">
                        <span class="d-flex aic">
                          {{
                            ghestify(order, refund).prepayment | moneySeperate
                          }}
                          <small>تومان</small>
                        </span>
                      </h4>
                    </div>
                  </div>

                  <div class="list-view-item">
                    <div class="d-flex aic flex-wrap">
                      <div class="title">
                        <span>مجموع اقساط</span>
                      </div>
                      <h4 class="mr-auto">
                        {{ ghestify(order, refund).payback | moneySeperate }}
                        <small>تومان</small>
                      </h4>
                    </div>
                  </div>

                  <div class="list-view-item">
                    <div class="d-flex aic flex-wrap">
                      <div class="title">
                        <span>مجموع همه پرداخت ها</span>
                      </div>
                      <h4 class="mr-auto">
                        {{ ghestify(order, refund).total | moneySeperate }}
                        <small>تومان</small>
                      </h4>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>
      </div>

      <div
        v-else
        class="alert alert-danger rounded p-2"
      >
        <div
          class="border border-danger bg-white text-danger rounded p-3 p-lg-4 d-flex flex-column flex-lg-row align-items-center justify-content-between"
        >
          <div class="d-flex align-items-center">
            <i class="fal fa-info-circle fa-2x" />
            <!-- eslint-disable vue/no-v-html -->
            <p
              class="m-0 pr-3 mt-md-0"
              v-html="allowCreateMsg"
            />
          </div>
          <router-link
            v-if="allowCreateMsgStatus === 'unfinished_order_exists'"
            :to="{ name: 'dash-orders', params: { role: role } }"
            class="btn btn-primary rounded-pill text-nowrap mt-4 mt-lg-0"
          >
            مشاهده سفارشات
          </router-link>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

<style>
.badge-pink {
  background: #e91e63;
  color: #ffffff !important;
}
</style>

<template>
  <div class="landing-layout-inner">
    <div>
      <ValidationObserver v-slot="{invalid}">
        <form action="">
          <h4 class="special-font text-brand mb-5">
            برای ثبت سفارش اطلاعات زیر را تکمیل کنید
          </h4>
          <div class="row">
            <div class="col-12">
              <ValidationProvider
                v-slot="{errors}"
                name="شماره موبایل"
                rules="required|mobileCheck"
              >
                <input
                  v-model="form.mobile"
                  placeholder="شماره موبایل"
                  type="text"
                  class="form-control form-control-lg text-center ltr estedad-font mb-2"
                >
                <small class="text-danger d-block">{{ errors[0] }}</small>
              </ValidationProvider>
            </div>

            <div class="col-12 col-md-6">
              <ValidationProvider
                v-slot="{errors}"
                name="نام"
                rules="required"
              >
                <input
                  v-model="form.fname"
                  placeholder="نام"
                  class="form-control form-control-lg text-center mb-2 mt-3"
                >
                <small class="text-danger d-block">{{ errors[0] }}</small>
              </ValidationProvider>
            </div>

            <div class="col-12 col-md-6">
              <ValidationProvider
                v-slot="{errors}"
                name="نام خانوادگی"
                rules="required"
              >
                <input
                  v-model="form.lname"
                  placeholder="نام خانوادگی"
                  class="form-control form-control-lg text-center mb-2 mt-3"
                >
                <small class="text-danger d-block">{{ errors[0] }}</small>
              </ValidationProvider>
            </div>

            <div class="col-12 d-flex justify-content-between mt-4">
              <a
                href="javascript:history.back()"
                class="btn btn-lg btn-light rounded-pill"
              >
                بازگشت
              </a>

              <button
                type="submit"
                :disabled="invalid"
                :class="loading ? 'btn-loading' : ''"
                class="btn btn-lg btn-primary rounded-pill"
              >
                <div
                  class="spinner-border"
                  role="status"
                >
                  <span class="sr-only">در حال بررسی</span>
                </div>
                <span class="btn-text d-flex aic w-100 jcb">
                  ادامه
                  <i class="fal fa-arrow-left pr-5" />
                </span>
              </button>
            </div>
          </div>
        </form>
      </ValidationObserver>
    </div>
    
    <div>
      <div class="prev-data">
        <div class="row">
          <div class="col-12 col-sm-8 mb-4">
            <div class="pd-c bg-white py-2 px-3">
              <span>مبلغ قسطاکارت</span>
              <h4 class="price-style">
                1,450,000
                <small>تومان</small>
              </h4>
            </div>
          </div>
          <div class="col-12 col-sm-4 mb-4">
            <div class="pd-c bg-white text-center py-2 px-3">
              <span class="o100">۳ چک</span>
              <span class="o100">۳ ماه</span>
            </div>
          </div>
          <div class="col-12 col-sm-4 mb-4">
            <div class="pd-c bg-white py-2 px-3">
              <span>بازپرداخت</span>
              <strong class="price-style">
                1,450,000
                <small>تومان</small>
              </strong>
            </div>
          </div>
          <div class="col-12 col-sm-4 mb-4">
            <div class="pd-c bg-white py-2 px-3">
              <span>پیش‌پرداخت</span>
              <strong class="price-style">
                1,450,000
                <small>تومان</small>
              </strong>
            </div>
          </div>
          <div class="col-12 col-sm-4 mb-4">
            <div class="pd-c bg-white py-2 px-3">
              <span>مبلغ هر قسط</span>
              <strong class="price-style">
                1,450,000
                <small>تومان</small>
              </strong>
            </div>
          </div>
        </div>
      </div>
      <div class="card">
        <div>
          <div class="top">
            <img
              height="30"
              src="@/assets/img/logo-minimal.webp"
              alt=""
            >
            <div>
              <img
                height="30"
                src="@/assets/img/shetab.svg"
                alt=""
              >
              <svg
                width="1em"
                height="1em"
                viewBox="0 0 16 16"
                class="bi bi-caret-left-fill"
                fill="currentColor"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M3.86 8.753l5.482 4.796c.646.566 1.658.106 1.658-.753V3.204a1 1 0 0 0-1.659-.753l-5.48 4.796a1 1 0 0 0 0 1.506z"
                />
              </svg>
            </div>
          </div>
          <div class="bottom">
            <div class="code">
              **** **** 6362 1442
            </div>
            <span>
              {{ form.fname }} 
              {{ form.lname }}
            </span>
            <span class="ccfooter">
              <div>
                انقضا:
                <strong class="code-font">02/05</strong>
              </div>
              <div class="code-font">
                CVV2:
                <strong>1465</strong>
              </div>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import FormValidator from '@/global/FormValidator';

export default {
  name: 'LandingAuth',

  mixins: [FormValidator],

  data() {
    return {
      form: {
        mobile: '',
        fname: '',
        lname: ''
      }
    };
  },

  mounted() {
    this.$parent.noSidebar();
  }
};
</script>
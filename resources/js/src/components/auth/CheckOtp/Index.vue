<template>
  <ValidationObserver v-slot="{ invalid }">
    <form
      class="max-width-360 mx-auto text-center"
      @submit.prevent="submit"
    >
      <div class="border-bottom border-top mb-4 d-flex aic jcb">
        <button class="btn btn-link">
          <small
            class="pr-1 d-none d-sm-inline"
            @click="edit"
          >ویرایش شماره موبایل</small>
        </button>
        <span class="pt-1 px-3">
          {{ otpData.mobile }}
        </span>
      </div>

      <span class="d-block mb-3">
        کد تایید دریافت شده را وارد کنید
      </span>

      <div class="d-flex flex-row-reverse">
        <ValidationProvider
          v-for="i in 5"
          :key="i"
          name="کد تایید"
          rules="required"
          class="mx-1 otp-inputs"
        >
          <input
            v-model="otpCode[i]"
            placeholder="0"
            maxlength="1"
            class="form-control form-control-lg ltr mb-2 text-center"
          >
        </ValidationProvider>
      </div>

      <div>
        <button
          class="btn btn-link no-focus-outline"
          :disabled="!resendCode"
          @click.prevent="resendOtpCode"
        >
          <small class="d-flex aic">
            <div
              v-if="resendingCode"
              class="spinner-border spinner-border-sm d-inline-block ml-2"
              role="status"
            />
            ارسال مجدد کد
          </small>
        </button>
        <small
          v-if="!resendCode"
          class="countdown"
        />
      </div>

      <button
        type="submit"
        :disabled="invalid"
        :class="loading ? 'btn-loading' : ''"
        class="btn btn-lg btn-primary rounded-pill mt-4"
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
    </form>
  </ValidationObserver>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

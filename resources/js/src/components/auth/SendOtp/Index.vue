<template>
  <ValidationObserver v-slot="{ invalid }">
    <form
      class="max-width-360 mx-auto text-center"
      @submit.prevent="handleSubmit"
    >
      <span
        v-if="text"
        class="d-block mb-3"
      >
        {{ text }}
      </span>

      <div
        v-if="who === 'shop'"
        class="alert alert-warning d-flex"
      >
        <div><i class="far fa-exclamation-circle font-lg ml-3" /></div>
        <div
          class="alert-content"
          style="text-align: justify;"
        >
          فروشنده محترم، با توجه به اینکه استعلام مالکیت سیمکارت و تطابق با
          کدملی و... در مراحل بعدی بصورت خودکار بررسی می‌گردد، لطفاً تمامی
          اطلاعات نظیر شماره موبایل و سایر موارد خواسته شده را مطابق با مشخصات
          صاحب فروشگاه وارد نمایید.
        </div>
      </div>

      <ValidationProvider
        v-slot="{ errors }"
        name="شماره موبایل"
        rules="required|mobileCheck"
      >
        <input
          v-model="mobileNumber"
          placeholder="شماره موبایل"
          type="text"
          inputmode="numeric"
          maxlength="11"
          class="form-control form-control-lg text-center ltr estedad-font mb-2"
        >
        <small class="text-danger d-block">{{ errors[0] }}</small>
      </ValidationProvider>

      <g-button
        ref="submit"
        lg
        text="ادامه"
        class="mt-4"
        type="submit"
        color="primary"
        icon-left="arrow-left"
        icon-left-space="pr-5"
        :disabled="invalid"
      />
    </form>
  </ValidationObserver>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

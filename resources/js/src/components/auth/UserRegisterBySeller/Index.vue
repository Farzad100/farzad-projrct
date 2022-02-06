<template>
  <ValidationObserver v-slot="{ invalid }">
    <form
      class="max-width-360 mx-auto text-center"
      autocomplete="off"
      @submit.prevent="handleSubmit"
    >
      <ValidationProvider
        v-slot="{ errors }"
        name="کد ملی"
        rules="nid"
      >
        <label class="d-block mt-4">کد ملی</label>
        <input
          v-model="nid"
          placeholder="کد ملی"
          maxlength="10"
          class="form-control form-control-lg text-center ltr estedad-font"
        >
        <small class="text-danger d-block mt-1 font-weight-light">{{
          errors[0]
        }}</small>
      </ValidationProvider>

      <ValidationProvider
        v-slot="{ errors }"
        name="تاریخ تولد"
        rules="required"
      >
        <label class="d-block mt-4">
          تاریخ تولد
        </label>
        <g-date-picker
          v-model="birth"
          size="lg"
          empty
          no-suggest
          :year-max="1383"
          :year-min="1320"
        />
        <small class="text-danger d-block">{{ errors[0] }}</small>
      </ValidationProvider>

      <button
        type="submit"
        :disabled="invalid"
        :class="loading ? 'btn-loading' : ''"
        class="btn btn-lg btn-success rounded-pill mt-4"
      >
        <div
          class="spinner-border"
          role="status"
        >
          <span class="sr-only">در حال بررسی</span>
        </div>
        <span class="btn-text d-flex aic w-100 jcb">
          ثبت‌نام
        </span>
      </button>
    </form>
  </ValidationObserver>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

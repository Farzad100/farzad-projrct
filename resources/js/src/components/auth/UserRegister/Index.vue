<template>
  <ValidationObserver v-slot="{ invalid }">
    <form
      autocomplete="off"
      class="max-width-360 mx-auto text-center"
      @submit.prevent="handleSubmit"
    >
      <!-- username input just for Google Password API -->
      <input
        v-model="otpData.mobile"
        name="username"
        autocomplete="username"
        class="d-none"
      >
      <ValidationProvider
        v-slot="{ errors }"
        name="کد ملی"
        rules="nid"
      >
        <label class="opa-7">
          <small>
            کد ملی
          </small>
        </label>
        <input
          v-model="registerForm.nid"
          minlength="10"
          maxlength="10"
          class="form-control form-control-lg mb-2 text-center"
          placeholder="کد ملی"
        >
        <small class="text-danger d-block">{{ errors[0] }}</small>
      </ValidationProvider>
      <ValidationProvider
        v-slot="{ errors }"
        name="تاریخ تولد"
        rules="required"
      >
        <label class="opa-7">
          <small>
            تاریخ تولد
          </small>
        </label>
        <g-date-picker
          v-model="registerForm.birth"
          size="lg"
          empty
          no-suggest
          :year-max="1383"
          :year-min="1320"
        />
        <small class="text-danger d-block">{{ errors[0] }}</small>
      </ValidationProvider>

      <ValidationProvider
        v-slot="{ errors }"
        name="پسورد"
        rules="required"
        class="password-input eye-with-label"
        :class="isPassShow ? 'pass-show' : ''"
      >
        <label class="mt-3 opa-7">
          <small>
            پسورد
          </small>
        </label>
        <input
          ref="password"
          v-model="registerForm.password"
          placeholder="پسورد"
          type="password"
          readonly
          class="form-control form-control-lg text-center"
          @focus="passTrick"
        >
        <i
          v-if="isPassShow"
          class="fal fa-eye-slash"
          @click="togglePassShow"
        />
        <i
          v-else
          class="fal fa-eye"
          @click="togglePassShow"
        />
        <small class="text-danger d-block">{{ errors[0] }}</small>
      </ValidationProvider>

      <label
        v-if="!noUtm"
        class="mt-4 opa-7"
      >
        <small>
          کد معرف (اختیاری)
        </small>
      </label>
      <input
        v-if="!noUtm"
        v-model="rfrr"
        placeholder="کد معرف"
        class="form-control form-control-lg text-center mb-2"
      >

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
        <span class="btn-text d-flex aic w-100 px-5 jcb">
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

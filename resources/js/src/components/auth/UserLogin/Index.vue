<template>
  <ValidationObserver v-slot="{invalid}">
    <form
      class="max-width-360 mx-auto text-center"
      autocomplete="off"
      @submit.prevent="handleSubmit"
    >
      <ValidationProvider
        v-slot="{errors}"
        name="شماره موبایل"
        rules="required|mobileCheck"
      >
        <input
          v-model="mobileNumber"
          placeholder="شماره موبایل"
          type="text"
          inputmode="numeric"
          maxlength="11" 
          class="form-control form-control-lg text-center mb-2 ltr estedad-font"
        >
        <small class="text-danger d-block">{{ errors[0] }}</small>
      </ValidationProvider>

      <ValidationProvider
        v-slot="{errors}"
        name="پسورد"
        rules="required"
        class="password-input ltr"
        :class="isPassShow ? 'pass-show' : ''"
      >
        <input
          ref="password"
          v-model="password"
          placeholder="پسورد"
          type="password"
          readonly
          class="form-control form-control-lg text-center mt-3 mb-2"
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
        <small class="text-danger d-block text-center estedad-font">{{ errors[0] }}</small>
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
          ورود به حساب
        </span>
      </button>
    </form>
  </ValidationObserver>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

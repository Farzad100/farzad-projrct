<template>
  <ValidationObserver v-slot="{invalid}">
    <form
      class="max-width-360 mx-auto text-center"
      autocomplete="off"
      @submit.prevent="handleSubmit"
    >
      <span class="d-block mb-3">
        رمز عبور جدید خودتان را وارد کنید
      </span>
      <ValidationProvider
        v-slot="{errors}"
        name="پسورد جدید"
        rules="required"
        class="password-input"
        :class="isPassShow ? 'pass-show' : ''"
      >
        <input
          ref="password"
          v-model="password"
          placeholder="پسورد"
          type="password"
          readonly
          class="form-control form-control-lg text-center mt-3"
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
          ویرایش رمزعبور
        </span>
      </button>
    </form>
  </ValidationObserver>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

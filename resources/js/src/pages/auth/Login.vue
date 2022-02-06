<template>
  <div>
    <h3 class="special-font text-brand mb-3 mb-lg-5 text-center">
      ورود به حساب
    </h3>

    <template v-show="step === 1">
      <user-login @formSubmit="nextStep" />
    </template>

    <div
      v-show="$route.name === 'login'"
      class="auth-or max-width-360"
    >
      <router-link
        to="/register"
        class="btn btn-light btn-sm rounded-pill d-inline-flex mx-1"
      >
        ثبت‌ نام
      </router-link>
      <router-link
        to="/auth/reset-password"
        class="btn btn-light btn-sm rounded-pill d-inline-flex mx-1"
      >
        بازیابی رمز عبور
      </router-link>
    </div>
  </div>
</template>

<script>
// Components
import UserLogin from '@/components/auth/UserLogin/Index';

export default {
  name: 'Login',

  metaInfo: {
    title: 'ورود به حساب',
  },

  components: { UserLogin },

  data() {
    return {
      step: 1,
      loginData: ''
    };
  },

  methods: {
    nextStep(payload) {
      this.step++;
      if (payload) this.loginData = payload;
    }
  }
};
</script>

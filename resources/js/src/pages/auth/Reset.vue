<template>
  <div>
    <h3 class="special-font text-brand mb-3 mb-lg-5 text-center">
      بازیابی رمز عبور
    </h3>

    <template v-if="step === 1">
      <send-otp
        type="password"
        @formSubmit="nextStep"
      />
    </template>

    <template v-if="step === 2">
      <check-otp
        type="password"
        :otp-data="otpData"
        @formSubmit="nextStep"
        @changeMobile="showFirstStep"
      />
    </template>

    <template v-if="step === 3">
      <reset-password
        :otp-data="otpData"
      />
    </template>

    <div
      v-show="$route.name === 'register'"
      class="auth-or max-width-360"
    >
      <router-link
        to="/login"
        class="btn btn-light btn-sm rounded-pill d-inline-flex"
      >
        ورود به حساب
      </router-link>
    </div>
  </div>
</template>

<script>
//components
import SendOtp from '@/components/auth/SendOtp/Index';
import CheckOtp from '@/components/auth/CheckOtp/Index';
import ResetPassword from '@/components/auth/ResetPassword/Index';

export default {
  name: 'Register',

  metaInfo: {
    title: 'بازیابی رمز عبور',
  },

  components: {
    SendOtp, CheckOtp, ResetPassword
  },

  data() {
    return {
      step: 1,
      otpData: ''
    };
  },

  methods: {
    nextStep(payload) {
      this.step++;
      if (payload) this.otpData = payload;
    },
    showFirstStep() {
      this.step = 1;
    }
  }
};
</script>

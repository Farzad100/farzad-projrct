<template>
  <div class="w-100">
    <h3 class="special-font text-brand mb-3 mb-lg-5 text-center">
      ثبت‌نام سازمان‌ها
    </h3>

    <template v-if="step === 1">
      <send-otp
        type="register"
        @formSubmit="nextStep"
      />
    </template>

    <template v-if="step === 2">
      <check-otp
        type="register"
        :otp-data="otpData"
        @formSubmit="nextStep"
        @changeMobile="showFirstStep"
      />
    </template>

    <template v-if="step === 3">
      <user-register
        :otp-data="otpData"
        no-utm
        external-routing
        @formSubmit="nextStep"
      />
    </template>

    <template v-if="step === 4">
      <organ-register :otp-data="otpData" />
    </template>
  </div>
</template>

<script>
import Cookies from 'js-cookie';

export default {
  name: 'OrganRegisterPage',

  metaInfo: {
    title: 'ثبت‌نام سازمان',
  },

  components: {
    sendOtp: () => import('@/components/auth/SendOtp/Index'),
    checkOtp: () => import('@/components/auth/CheckOtp/Index'),
    userRegister: () => import('@/components/auth/UserRegister/Index'),
    organRegister: () => import('@/components/auth/OrganRegister/Index')
  },

  data() {
    return {
      step: 1,
      otpData: ''
    };
  },

  created() {
    const isLoggedIn = Cookies.get('access_token');
    if (isLoggedIn) {
      this.step = 4;
    }
  },

  methods: {
    nextStep(payload) {
      if (payload) this.otpData = payload;

      if (payload.user_exists) {
        Cookies.set('access_token', payload.user.token, { expires: 60 });
        Cookies.set('roles', payload.user.roles, { expires: 60 });
        this.step = 4;
      } else {
        this.step++;
      }
    },
    showFirstStep() {
      this.step = 1;
    }
  }
};
</script>
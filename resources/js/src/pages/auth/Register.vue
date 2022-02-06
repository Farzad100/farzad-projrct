<template>
  <div>
    <h3 class="special-font text-brand mb-3 mb-lg-5 text-center">
      ایجاد حساب کاربری
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
        @formSubmit="nextStep"
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
import { mapActions } from 'vuex';

//components
import SendOtp from '@/components/auth/SendOtp/Index';
import CheckOtp from '@/components/auth/CheckOtp/Index';
import UserRegister from '@/components/auth/UserRegister/Index';

export default {
  name: 'Register',

  metaInfo: {
    title: 'ثبت‌نام',
  },

  components: {
    SendOtp, CheckOtp, UserRegister
  },

  data() {
    return {
      step: 1,
      otpData: ''
    };
  },

  methods: {
    ...mapActions('auth', ['staticLogin']),

    nextStep(payload) {
      if (payload.user_exists) {
        this.staticLogin(payload.user)
          .then(() => {
            this.$router.push({
              name: 'dash-user'
            }).then(r => r);
          });
      } else {
        this.step++;
        if (payload) this.otpData = payload;
      }
    },
    showFirstStep() {
      this.step = 1;
    }
  }
};
</script>

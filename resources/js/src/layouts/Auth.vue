<template>
  <div
    class="auth-layout"
    :class="sidebar ? null : 'landing-layout'"
  >
    <div
      v-if="sidebar"
      class="side"
    >
      <a
        href="/"
        class="auth-back-home btn btn-light btn-sm rounded-pill"
      >
        <i class="fal fa-arrow-right pl-2" />
        صفحه نخست
      </a>

      <img
        height="80"
        class="mt-5"
        src="/images/logo.svg"
        alt="لوگوی قسطا"
      >

      <p class="mt-4 text-brand">
        با قسطا میتونی هر کالا و خدماتی رو از همه فروشگاه های اینترنتی یا فیزیکی سراسر ایران قسطی بخری!
      </p>
    </div>

    <div class="form">
      <alert-messages />
      
      <img
        height="80"
        class="mb-4"
        src="/images/logo.svg"
        alt="لوگوی قسطا"
      >
      <router-view />
    </div>
  </div>
</template>

<script>
export default {
  name: 'AuthenticationLayout',

  data() {
    return {
      sidebar: true
    };
  },

  methods: {
    noSidebar() {
      this.sidebar = false;
    }
  }
};
</script>

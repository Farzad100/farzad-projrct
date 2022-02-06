<template>
  <div class="more-links-wrapper">
    <div class="more-links"> 
      <change-role class="in-mobile" />

      <router-link
        v-for="(link, i) in links"
        :key="i"
        :to="{ name: link.route_name }"
        @click="$parent.toggleMoreLinks()"
      >
        <span class="d-flex aic">
          <i :class="`fad fa-${link.icon} pl-3`" />
          {{ link.title }}
        </span>
        <i class="far fa-chevron-left fa-xs" />
      </router-link>

      <a
        href="tel:02191070092"
        class="border-0"
      >
        <span>
          <i class="far fa-phone pl-3" />
          پشتیبانی تلفنی
        </span>
        <span class="ltr phone-number">
          (021)
          <strong>910</strong>
          <strong>700</strong>
          <strong>92</strong>
        </span>
      </a>

      <button
        class="btn btn-light rounded-pill mt-4 w-100 text-danger"
        @click="handleLogout()"
      >
        <i class="far fa-sign-out pl-3" />
        خروج از حساب
      </button>
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

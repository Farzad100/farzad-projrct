<template>
  <div class="dashboard-sidebar">
    <img
      src="/images/logo.svg"
      alt="brand"
      class="logo"
    >
    <div class="links">
      <router-link
        v-for="(link, i) in links"
        :key="i"
        :to="{ name: link.route_name, params: link.params }"
        :exact="link.exact"
        :class="link.classes"
      >
        <i :class="`fad fa-${link.icon}`" />
        <i :class="`fas fa-${link.icon}`" />
        <span>{{ link.title }}</span>
        <span
          v-if="link.route_name === 'dash-inbox' && inbox >= 1"
          class="_sm-alert"
        >{{ inbox }}</span>
      </router-link>

      <button
        ref="moreLinksBtn"
        class="more-link-btn"
        @click="toggleMoreLinks"
      >
        <i
          class="fal mb-0"
          :class="moreLinks ? 'active fa-times' : 'fa-ellipsis-h-alt'"
        />
        <span>بیشتر</span>
      </button>
    </div>

    <more-links v-show="moreLinks" />
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

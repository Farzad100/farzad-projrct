<template>
  <div>
    <nav class="default-pages-nav">
      <div class="wrapper">
        <div class="controller">
          <a
            href="/"
            class="logo"
          >
            <img
              src="/images/logo.svg"
              alt="brand"
            >
          </a>
          
          <div>
            <button
              class="toggle-button"
              @click="toggleNav"
            >
              <i
                v-if="isNavShow"
                class="fal fa-times"
              />
              <i
                v-else
                class="fal fa-bars"
              />
            </button>

            <router-link
              v-if="!access_token"
              :to="{ name: 'login' }"
              class="btn btn-primary text-white rounded-pill"
            >
              <i class="fas fa-user opa-5" />
              <span class="d-none d-sm-inline pr-2">
                ورود
              </span>
            </router-link>
            <router-link
              v-if="access_token"
              :to="{ name: 'dash-home' }"
              class="btn btn-primary text-white rounded-pill"
            >
              <i class="fas fa-user opa-5" />
              <span class="d-none d-sm-inline pr-2">
                پنل کاربری
              </span>
            </router-link>
          </div>
        </div>

        <div :class="['links', { 'open': isNavShow }]">
          <div class="right">
            <a href="/blog">
              بلاگ
            </a>
            <a href="/faq">
              سوالات متداول
            </a>
            <a href="/help">
              راهنما
            </a>
            <a href="/organ">
              همکاری با سازمان‌ها
            </a>
            <a href="/shop">
              همکاری با فروشگاه‌ها
            </a>
          </div>
          <div class="left">
            <router-link
              v-if="!access_token"
              :to="{ name: 'login' }"
            >
              <i class="fas fa-user" />
              ورود
            </router-link>
            <router-link
              v-if="!access_token"
              :to="{ name: 'register' }"
              class="btn btn-primary text-white rounded-pill"
            >
              دریافت قسطا کارت
            </router-link>
            <router-link
              v-if="access_token"
              :to="{ name: 'dash-home' }"
              class="btn btn-primary text-white rounded-pill"
            >
              <i class="fas fa-user" />
              پنل کاربری
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <router-view />
  </div>
</template>

<script>
import { mapState } from 'vuex';
export default {
  name: 'DefaultLayout',

  data() {
    return {
      isNavShow: false
    };
  },

  computed: {
    ...mapState('auth', ['access_token'])
  },

  methods: {
    toggleNav() {
      this.isNavShow = !this.isNavShow;
    }
  }
};
</script>

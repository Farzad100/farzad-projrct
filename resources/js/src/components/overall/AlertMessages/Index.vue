<template>
  <div>
    <span
      v-if="$alerts.style !== 'modal'"
      v-show="$alerts.visible"
      class="alert-messages alert"
      :class="`alert-${$alerts.type} ${$alerts.style}`"
    >
      <span class="content">
        {{ $alerts.msg }}
      </span>
      <button @click.prevent="$alerts.hide">
        <i class="fal fa-times" />
      </button>
    </span>

    <modal
      v-else
      v-show="$alerts.visible"
      :title="$alerts.msg_title"
      @close="$alerts.hide"
    >
      <template #body>
        <div
          class="d-flex flex-column justify-content-center align-items-center"
        >
          <i
            v-if="$alerts.icon"
            :class="[
              'fas',
              `fa-${$alerts.icon}`,
              'fa-4x mb-4',
              `text-${$alerts.type}`
            ]"
          />
          <!-- eslint-disable vue/no-v-html -->
          <div
            class="text-justify"
            v-html="$alerts.msg"
          />
        </div>
      </template>
      <template #footer>
        <div class="d-flex w-100 justify-content-center">
          <template v-for="(btn, i) in $alerts.buttons">
            <button
              v-if="btn.mode === 'close'"
              :key="i"
              :class="['btn', `btn-${btn.type}`, 'brad-half', i !== 0 ? 'mr-2' : '']"
              @click.prevent="$alerts.hide"
              v-html="btn.text"
            />
            <a
              v-else-if="btn.mode === 'url'"
              :key="i"
              :href="btn.url"
              :target="btn.newtab ? '_blank' : ''"
              :class="['btn', `btn-${btn.type}`, 'brad-half', i !== 0 ? 'mr-2' : '']"
              v-html="btn.text"
            />
          </template>
        </div>
      </template>
    </modal>
  </div>
</template>

<script>
import Vue from 'vue';
Vue.prototype.$alerts = new Vue({
  data() {
    return {
      visible: false,
      msg: '',
      msg_title: '',
      type: 'danger',
      icon: '',
      style: 'static',
      buttons: '',
      duration: 1
    };
  },

  watch: {
    visible(val) {
      if (val === true && this.style !== 'modal') {
        setTimeout(() => {
          this.visible = false;
        }, this.duration);
      }
    }
  },

  methods: {
    show(payload) {
      // eslint-disable-next-line no-debugger
      // debugger; UNCX
      this.msg = payload.msg;
      this.msg_title = payload.msg_title;
      this.type = payload.type;
      this.style = payload.style;
      this.icon = payload.icon;
      this.buttons = payload.buttons;
      (this.duration = payload.duration || 15000), (this.visible = true);
    },

    hide() {
      this.visible = false;
    },

    errHandle(error, style) {
      let msg;
      if (error.response) {
        switch (error.response.status) {
        case 500:
          msg =
              'مشکلی برای سرور پیش آمده! دوباره تلاش کنید یا با پشتیبانی تماس بگیرید';
          break;
        case 400:
          msg = 'یک مشکل فنی رخ داده است.';
          break;
        case 405:
          msg = '405 - مشکل فنی. لطفا به پشتیبانی اطلاع دهید';
          break;
        }
      }

      if (error.response)
        this.show({ msg, type: 'danger', style: style ? style : 'float' });
    }
  }
});

export default {
  name: 'AlertMessages',

  watch: {
    $route() {
      this.$alerts.hide();
    }
  }
};
</script>

<template>
  <div class="small-process">
    <div
      v-show="loading"
      class="loading"
    >
      <div
        class="spinner-border"
        role="status"
      />
      در حال بررسی کد
    </div>
    <div
      v-show="!loading && options.msg"
      class="response"
      :class="options.type"
    >
      <i
        class="fas"
        :class="options.type === 'success' ? 'fa-badge-check' : 'fa-exclamation-triangle'"
      />
      {{ options.msg }}
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

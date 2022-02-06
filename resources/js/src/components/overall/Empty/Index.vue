<template>
  <div class="empty-section">
    <img
      src="@/assets/img/empty.webp"
      alt="no data"
    >
    <span
      v-if="message"
      class="d-block text-center mt-2"
    >
      {{ message }}
    </span>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

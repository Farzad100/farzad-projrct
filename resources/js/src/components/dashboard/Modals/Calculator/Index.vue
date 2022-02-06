<template>
  <modal
    v-show="modal"
    title="محاسبه سفارش"
    size="lg"
    @close="modal = false"
  >
    <template #body />

    <template #footer>
      <div class="d-flex w-100 justify-content-center">
        <button
          class="btn btn-light rounded-pill"
          @click="modal = false"
        >
          انصراف
        </button>
      </div>
    </template>
  </modal>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>
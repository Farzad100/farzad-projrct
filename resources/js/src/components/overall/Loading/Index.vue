<template>
  <div>
    <!-- Order Card Loading -->
    <div
      v-if="type === 'orderCard'"
      class="row"
    >
      <div
        v-for="i in iterateCount"
        :key="i"
        class="col-12 col-md-6 col-xl-4 mb-4"
      >
        <div class="in-loading-card">
          <div class="d-flex justify-content-between _padd _po-1">
            <div class="_l-item _l-2" />
          </div>
          <div class="_l-item _l-3 _padd" />
          <div class="d-flex _padd _po-2">
            <div class="_l-item _l-4" />
            <div class="_l-item _l-5" />
          </div>
          <div class="d-flex justify-content-between align-items-center _po-3 _padd border-top">
            <div class="_l-item _l-6" />
            <div class="_l-item _l-7" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

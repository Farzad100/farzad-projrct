<template>
  <div class="inst-status">
    <div class="d-flex mb-2">
      <span
        v-for="i in renderedStatus"
        :key="i.id"
        :class="i.passed ? 'passed' : ''"
      /> 
    </div>
    <small class="font-weight-light opa-7">
      <strong>
        {{ status.passed }}
        قسط
      </strong>
      از
      <strong>
        {{ status.number }} 
        قسط
      </strong>
      پرداخت شده است
    </small>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>
<template>
  <div class="tab-button-group">
    <input
      id="orderType-1"
      v-model="selectedInput"
      type="radio"
      name="orderType"
      :value="false"
      checked
      hidden
    >
    <label for="orderType-1">
      شخصی
    </label>

    <input
      id="orderType-2"
      v-model="selectedInput"
      type="radio"
      name="orderType"
      :value="true"
      hidden
    >
    <label for="orderType-2">
      سازمانی
    </label>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

<template>
  <apexchart
    ref="apex"
    :height="height"
    :type="type"
    :options="options"
    :series="series"
  />
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

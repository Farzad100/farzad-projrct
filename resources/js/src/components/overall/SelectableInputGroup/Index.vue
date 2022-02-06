<template>
  <div class="selectable-input-group">
    <slot />
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

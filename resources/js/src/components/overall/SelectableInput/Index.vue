<template>
  <div
    class="selectable-input"
    :class="$parent.type"
  >
    <input
      :id="`${$parent.name}-${id}`"
      :type="$parent.type"
      :name="$parent.name"
      :checked="checked"
      hidden
    >
    <label :for="`${$parent.name}-${id}`">
      <slot />
    </label>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

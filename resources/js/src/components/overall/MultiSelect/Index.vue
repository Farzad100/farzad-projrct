<template>
  <div
    v-click-outside="close"
    class="multi-select"
  >
    <input
      ref="input"
      v-model="searchKey"
      class="search"
      :class="['form-control', `form-control-${size}`]"
      :placeholder="isOpen ? 'جستجو...' : selected.length > 0 ? null : 'انتخاب کنید'"
      @focus="isOpen = true"
    >

    <div
      v-show="isOpen"
      class="_list border rounded shadow-sm"
    >
      <div
        v-for="(opt, index) in list"
        :key="index"
        class="checkbox-in-list px-2"
      >
        <input
          :id="`${opt.value}-${index}`"
          :ref="opt.value"
          v-model="selected"
          type="checkbox"
          :value="opt.value"
        >
        <label :for="`${opt.value}-${index}`">
          <span>
            {{ opt.text }}
          </span>
        </label>
      </div>
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

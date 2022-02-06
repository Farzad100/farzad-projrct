<template>
  <div :class="['bank-list-select', { '_selecting': isOpen }]">
    <button
      ref="bankListToggle"
      @click.prevent="isOpen = !isOpen"
    >
      <span v-if="selected.fa">
        <img
          height="20"
          :src="'/images/banks/' + selected.name + '.svg'"
        >
        {{ selected.fa }}
      </span>
      <span v-else>
        بانک خود را انتخاب کنید
      </span>
      <i
        :class="[
          'fal',
          `fa-caret-${isOpen ? 'up' : 'down'}`,
          'mr-3'
        ]"
      />
    </button>

    <div class="_list shadow-sm">
      <div class="_search border-bottom">
        <label for="search">
          <i class="far fa-search" />
        </label>
        <input
          id="search"
          v-model="searchKey"
          placeholder="جستجوی نام بانک..."
          autocomplete="off"
        >
      </div>

      <button
        v-for="(bank, bankIndex) in bankList"
        :key="bankIndex"
        :class="selected.name === bank.name ? 'active' : null"
        @click="select(bank)"
      >
        <img
          height="24"
          :src="'/images/banks/' + bank.name + '.svg'"
        >
        {{ bank.fa }}
      </button>
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

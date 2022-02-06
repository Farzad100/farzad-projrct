<template>
  <button
    :type="type"
    :disabled="disabled || text === null"
    :class="[
      'btn',
      { 'btn-loading': spinner },
      { 'btn-lg': lg },
      { 'btn-sm': sm },
      { 'btn-sm': xs },
      { 'w-100': block },
      color ? `btn-${color}` : 'btn-light',
      'rounded-pill'
    ]"
  >
    <div class="spinner-border" />

    <span
      :class="[
        'btn-text',
        'd-flex',
        'aic',
        'w-100',
        center ? 'jcc' : 'jcb',
        xs ? 'small' : ''
      ]"
    >
      <i
        v-if="iconRight"
        :class="['ml-1 fal', `fa-${iconRight}`, iconRightSpace]"
      />
      {{ text }}
      <i
        v-if="iconLeft"
        :class="['mr-1 fal', `fa-${iconLeft}`, iconLeftSpace]"
      />
      <span
        v-if="badge"
        class="badge badge-sm mr-1"
        :class="'badge-' + (badgeColor || 'info')"
      >
        {{ badge }}
      </span>
    </span>
  </button>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

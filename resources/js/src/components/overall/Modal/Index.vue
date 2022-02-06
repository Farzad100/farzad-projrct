<template>
  <div
    class="modal show"
    style="display: block; background-color: rgba(0,0,0, .8); overflow-y: scroll"
    tabindex="-1"
    aria-hidden="true"
    @click="handleClick($event)"
  >
    <div :class="['modal-dialog modal-dialog-centered', size ? `modal-${size}` : null]">
      <div class="modal-content">
        <div class="modal-header">
          <h5
            id="exampleModalLabel"
            class="modal-title special-font font-weight-bold"
          >
            {{ title }}
          </h5>
          <button
            class="btn pl-1"
            @click="close"
          >
            <i class="fal fa-times" />
          </button>
        </div>

        <div class="modal-body">
          <slot name="body" />
        </div>

        <div class="modal-footer">
          <slot name="footer" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>
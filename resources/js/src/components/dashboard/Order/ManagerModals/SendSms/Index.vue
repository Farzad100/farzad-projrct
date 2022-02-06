<template>
  <!-- Send SMS Modal -->
  <modal
    v-show="smsModal"
    title="ارسال پیام به کاربر"
    @close="smsModal = false"
  >
    <template #body>
      <label>متن پیام</label>
      <textarea
        v-model="smsText"
        rows="3"
        class="form-control form-control-lg"
      />
    </template>

    <template #footer>
      <div class="d-flex w-100 justify-content-between">
        <button
          class="btn btn-light rounded-pill"
          @click="smsModal = false"
        >
          انصراف
        </button>

        <g-button
          ref="sms"
          text="ارسال پیام"
          type="button"
          color="primary"
          @click.native="sendMessage"
        />
      </div>
    </template>
  </modal>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>
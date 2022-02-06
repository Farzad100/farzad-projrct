<template>
  <div>
    <!-- doc_uploaded message -->
    <div
      v-if="status === 'completed'"
      class="alert order-alert alert-success"
    >
      <i class="fad fa-check-circle" />
      <div>
        <h6 class="m-0">
          شما همه قسط‌های این سفارش را با موفقیت به پرداخت کرده‌اید و این سفارش پایان یافته است.
          می‌توانید در صورت نیاز سفارش جدیدی ثبت کنید.
        </h6>
      </div>
    </div>


    <!-- order cancelled message -->
    <div
      v-if="status === 'cancelled'"
      class="alert order-alert alert-danger rounded"
    >
      <i class="fad fa-vote-nay" />
      <div>
        <h6 class="d-block mb-4">
          این سفارش توسط شما لغو شده است
        </h6>
        <small class="d-block opa-7"> دلیل شما برای لغو: </small>
        <span>
          {{ reason ? reason : 'نامشخص' }}
        </span>
      </div>
    </div>

    <!-- order rejected message -->
    <div
      v-if="status === 'rejected'"
      class="alert order-alert alert-danger rounded"
    >
      <i class="fad fa-vote-nay" />

      <div>
        <h6 class="d-block mb-4">
          این سفارش رد شده است
        </h6>
        <small class="d-block opa-7"> علت: </small>
        <span>
          {{ reason ? reason : 'نامشخص' }}
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

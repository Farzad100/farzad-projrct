<template>
  <div class="position-relative pb-5 mb-5">
    <div
      v-if="loading"
      class="page-loading"
    >
      <div class="spinner-border" />
    </div>

    <div
      v-else
      class="order-single"
    >
      <div class="col-12 col-xl-10 mx-auto">
        <!-- eslint-disable vue/no-v-html -->
        <div
          v-if="order && order.important_msg"
          class="d-flex alert"
          :class="'alert-' + order.important_msg.color"
        >
          <i class="fal fa-exclamation-circle align-self-center h2 ml-2" />
          <span
            class="text-justify"
            v-html="order.important_msg.text"
          />
        </div>

        <order-info
          :data="order"
          class="mb-4"
        />

        <orderStatus
          v-if="
            order &&
              (order.status === 'rejected' ||
                order.status === 'cancelled' ||
                order.status === 'completed')
          "
          :status="order.status"
          :reason="order.reason"
        />

        <order-steps
          v-else
          :order-data="order"
        />
      </div>
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

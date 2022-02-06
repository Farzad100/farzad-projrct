<template>
  <div class="rounded border mt-3">
    <div class="px-4 mt-2">
      <div
        v-if="data.created_at"
        class="d-flex align-items-center justify-content-between py-3"
      >
        <span>
          تاریخ صدور
        </span>
        <span>
          {{ data.created_at | jDate }}
        </span>
      </div>

      <div
        v-if="data.sent_at"
        class="d-flex align-items-center border-top justify-content-between pt-3"
      >
        <span>
          تاریخ ارسال
        </span>
        <span>
          {{ data.sent_at | jDate }}
        </span>
      </div>
    </div>

    <div
      v-if="data && data.status === 'sent'"
      class="bg-gray-light border-top p-4 mt-4"
    >
      <div>
        <h5 class="special-font font-weight-bold">
          تایید دریافت قسطا کارت
        </h5>
        <span class="font-weight-light">
          قسطا کارت شما در تاریخ
          <strong>{{ data.sent_at | jDate }}</strong>
          برای شما ارسال شده است. لطفا پس از دریافت، ۴ شماره آخر کارت را در
          فرم زیر وارد کنید
        </span>
        <h3 class="mt-5 text-center nova-font ltr">
          {{ data.card_number | cardNumber }}
        </h3>

        <input
          id="cardNumber"
          v-model="cardNumber"
          style="max-width: 200px"
          placeholder="چهار رقم آخر کارت"
          class="form-control text-center mt-2 rounded-pill ltr estedad-font mx-auto"
          maxlength="4"
          @keydown="keyUpHandle"
        >
        <loader-sm
          class="mx-auto"
          :loading="checkingCardNumber"
          :options="checkingCardOpt"
        />
      </div>
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>
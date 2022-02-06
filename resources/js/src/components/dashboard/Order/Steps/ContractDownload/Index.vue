<template>
  <div class="border rounded shadow-sm mt-3">
    <div class="p-4 bg-gray-light border-bottom d-flex flex-column flex-lg-row align-items-center justify-content-between">
      <span>
        لطفا قرارداد را دانلود و پرینت کنید. 
        {{
          role === 'shop' ? 'این قرارداد باید توسط شما و مشتری امضا شود'
          : role === 'user' && data.shop_id ? 'این قرارداد باید توسط شما و فروشگاه امضا شود'
            : role === 'admin' ? 'این قرارداد باید توسط مشتری امضا شود' 
              : 'این قرارداد باید توسط شما امضا شود'
        }}
      </span>
      <a
        :href="`/orders/${this.$route.params.id}/contract`"
        class="btn btn-primary rounded-pill text-nowrap mt-4 mt-lg-0"
        target="_blank"
      >
        <svg
          width="1em"
          height="1em"
          viewBox="0 0 16 16"
          class="bi bi-download ml-2"
          fill="currentColor"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            fill-rule="evenodd"
            d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"
          />
          <path
            fill-rule="evenodd"
            d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"
          />
        </svg>
        دانلود قرارداد
      </a>
    </div>

    <div
      v-if="!cDlOnly"
      class="p-4"
    >
      <!-- eslint-disable vue/no-v-html -->
      <p v-html="data.send_docs_cta" />
      <p v-html="data.ghesta_address" />
      <small class="d-block mt-5 text-danger">
        بعد از ارسال منتظر بمانید تا مدارک بدست ما برسد. نتیجه از طریق
        پیامک به اطلاعتان خواهد رسید.
      </small>
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

<template>
  <div>
    <div
      v-if="loading"
      class="page-loading"
    >
      <div class="spinner-border" />
    </div>

    <div v-else>
      <div
        class="pay-response"
        :class="status"
      >
        <div class="response">
          <p class="mb-5">
            {{ msg }}
          </p>

          <div class="on-success">
            <small
              v-if="amount"
              class="font-weight-lighter"
            >مبلغ پرداخت شده</small>
            <h2
              v-if="amount"
              class="mb-4 price-style d-flex"
            >
              {{ amount | moneySeperate }}
              <small>تومان</small>
            </h2>

            <small
              v-if="ref_id"
              class="font-weight-lighter"
            >شماره پیگیری</small>
            <h2 v-if="ref_id">
              {{ ref_id }}
            </h2>
          </div>

          <i class="fas fa-check-circle success-icon" />
          <i class="fas fa-exclamation-circle error-icon" />
        </div>
      </div>

      <div class="d-flex jcc mt-4">
        <router-link
          v-if="oid"
          :to="{ name: 'dash-order-single', params: { oid: oid, role: 'user' } }"
          class="btn btn-light d-inline-flex btn-sm rounded-pill mx-2"
        >
          بازگشت به سفارش
        </router-link>
        <!-- <router-link
          to="/dashboard"
          class="btn btn-light d-inline-flex btn-sm rounded-pill mx-2"
        >
          بازگشت به داشبورد
        </router-link> -->
      </div>
    </div>
  </div>
</template>

<script>
import Payment from '@/api/payment';

export default {
  name: 'PayResponse',

  metaInfo: {
    title: 'نتیجه پرداخت'
  },

  data() {
    return {
      msg: '',
      status: 'error',
      oid: '',
      ref_id: '',
      amount: '',

      loading: true
    };
  },

  created() {
    this.getPay();
  },

  methods: {
    getPay() {
      Payment.check
        .result(this.$route.query.Authority)
        .then(r => {
          if (r.data.status) {
            const d = r.data.result;

            this.msg = 'پرداخت شما موفقیت آمیز بود';
            this.oid = d.oid;
            this.ref_id = d.ref_id;
            this.amount = d.amount;
            this.status = 'success';
          } else {
            const d = r.data.error;

            this.msg = d.message;
            this.oid = d.oid;
            this.status = 'error';
          }
          this.loading = false;
        })
        .catch(e => {
          this.loading = false;
          this.$alerts.errHandle(e);
        });
    }
  }
};
</script>

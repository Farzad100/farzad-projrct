<template>
  <div class="row">
    <!-- Users -->
    <div class="col-12 col-xl-6 mb-4">
      <form
        class="border rounded shadow-sm"
        @submit.prevent="downloadUsers"
      >
        <h4 class="special-font mb-4 p-4 font-weight-bold">
          <i class="fad fa-users ml-2" />
          کاربران
        </h4>

        <div class="p-4 row">
          <div class="col-12">
            <g-date-picker
              v-model="users.from_date"
              label="از تاریخ"
              size="lg"
              class="mb-4"
              empty
            />
          </div>

          <div class="col-12 col-lg-6">
            <label for="">
              از کد ملی
            </label>
            <input
              v-model="users.nid"
              class="form-control form-control-lg ltr estedad-font"
            >
          </div>

          <div class="col-12 col-lg-6">
            <label for="">
              فرمت فایل
            </label>
            <select
              v-model="users.type"
              class="form-select form-select-lg"
              aria-label="Default select example"
            >
              <option value="csv">
                CSV
              </option>
              <option value="txt">
                TXT
              </option>
              <option value="xls">
                XLS
              </option>
            </select>
          </div>
        </div>

        <div class="border-top bg-gray-light p-4 d-flex justify-content-end">
          <g-button
            ref="users"
            text="دانلود"
            type="submit"
            color="primary"
            icon-right="cloud-download"
            icon-right-space="pl-2"
          />
        </div>
      </form>
    </div>

    <!-- Cheques -->
    <div class="col-12 col-xl-6 mb-4">
      <form
        class="border rounded shadow-sm"
        @submit.prevent="downloadCheques"
      >
        <h4 class="special-font mb-4 p-4 font-weight-bold">
          <i class="fad fa-folders ml-2" />
          اسناد
        </h4>

        <div class="p-4 row">
          <div class="col-12">
            <g-date-picker
              v-model="cheques.from_date"
              label="از تاریخ"
              size="lg"
              class="mb-4"
              empty
            />
          </div>

          <div class="col-12 col-lg-4">
            <label for="">
              از شماره سفارش
            </label>
            <input
              v-model="cheques.order_number"
              class="form-control form-control-lg ltr estedad-font"
            >
          </div>

          <div class="col-12 col-lg-4">
            <label for="">
              سری شارژ
            </label>
            <input
              v-model="cheques.series"
              class="form-control form-control-lg ltr estedad-font"
            >
          </div>

          <div class="col-12 col-lg-4">
            <label for="">
              فرمت فایل
            </label>
            <select
              v-model="cheques.type"
              class="form-select form-select-lg"
              aria-label="Default select example"
            >
              <option value="csv">
                CSV
              </option>
              <option value="txt">
                TXT
              </option>
              <option value="xls">
                XLS
              </option>
            </select>
          </div>
        </div>

        <div class="border-top bg-gray-light p-4 d-flex justify-content-end">
          <g-button
            ref="cheques"
            text="دانلود"
            type="submit"
            color="primary"
            icon-right="cloud-download"
            icon-right-space="pl-2"
          />
        </div>
      </form>
    </div>

    <!-- Prepayments -->
    <div class="col-12 col-xl-6 mb-4">
      <form
        class="border rounded shadow-sm"
        @submit.prevent="downloadPrepayments"
      >
        <h4 class="special-font mb-4 p-4 font-weight-bold">
          <i class="fad fa-folders ml-2" />
          پیش پرداخت ها
        </h4>

        <div class="p-4 row">
          <div class="col-12">
            <g-date-picker
              v-model="prepayments.from_date"
              label="از تاریخ"
              size="lg"
              class="mb-4"
              empty
            />
          </div>

          <div class="col-12 col-lg-4">
            <label for="">
              فرمت فایل
            </label>
            <select
              v-model="prepayments.type"
              class="form-select form-select-lg"
              aria-label="Default select example"
            >
              <option value="csv">
                CSV
              </option>
              <option value="txt">
                TXT
              </option>
              <option value="xls">
                XLS
              </option>
            </select>
          </div>
        </div>

        <div class="border-top bg-gray-light p-4 d-flex justify-content-end">
          <g-button
            ref="prepayments"
            text="دانلود"
            type="submit"
            color="primary"
            icon-right="cloud-download"
            icon-right-space="pl-2"
          />
        </div>
      </form>
    </div>

    <!-- Charges -->
    <div class="col-12 col-xl-6 mb-4">
      <form
        class="border rounded shadow-sm"
        @submit.prevent="downloadCharges"
      >
        <h4 class="special-font mb-4 p-4 font-weight-bold">
          <i class="fad fa-folders ml-2" />
          شارژها
        </h4>

        <div class="p-4 row">
          <div class="col-12">
            <g-date-picker
              v-model="charges.from_date"
              label="از تاریخ"
              size="lg"
              class="mb-4"
              empty
            />
          </div>

          <div class="col-12 col-lg-4">
            <label for="">
              فرمت فایل
            </label>
            <select
              v-model="charges.type"
              class="form-select form-select-lg"
              aria-label="Default select example"
            >
              <option value="csv">
                CSV
              </option>
              <option value="txt">
                TXT
              </option>
              <option value="xls">
                XLS
              </option>
            </select>
          </div>
        </div>

        <div class="border-top bg-gray-light p-4 d-flex justify-content-end">
          <g-button
            ref="charges"
            text="دانلود"
            type="submit"
            color="primary"
            icon-right="cloud-download"
            icon-right-space="pl-2"
          />
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { api, wrapper } from '@/global/services';

export default {
  name: 'Accounting',

  metaInfo: {
    title: 'کارتابل حسابداری'
  },

  data() {
    return {
      users: {
        from_date: '',
        nid: '',
        type: 'csv'
      },

      cheques: {
        from_date: '',
        order_number: '',
        series: '',
        type: 'csv'
      },

      prepayments: {
        from_date: '',
        order_number: '',
        series: '',
        type: 'csv'
      },

      charges: {
        from_date: '',
        order_number: '',
        series: '',
        type: 'csv'
      }
    };
  },

  methods: {
    async downloadUsers() {
      this.$refs.users.loading('start');
      const url = '/admin/accounting/users';
      const query = `nid=${this.users.nid}&from_date=${this.users.from_date}&type=${this.users.type}`;

      await wrapper(api.custom.get(`${url}?${query}`), 'خطای دانلود');

      this.$refs.users.loading('end');
    },

    async downloadCheques() {
      this.$refs.cheques.loading('start');
      const url = '/admin/accounting/cheques';
      const query = `order_number=${this.cheques.order_number}&from_date=${this.cheques.from_date}&series=${this.cheques.series}&type=${this.cheques.type}`;

      await wrapper(api.custom.get(`${url}?${query}`), 'خطای دانلود');

      this.$refs.cheques.loading('end');
    },

    async downloadPrepayments() {
      this.$refs.prepayments.loading('start');
      const url = '/admin/accounting/prepayments';
      const query = `order_number=${this.prepayments.order_number}&from_date=${this.prepayments.from_date}&series=${this.prepayments.series}&type=${this.prepayments.type}`;

      await wrapper(api.custom.get(`${url}?${query}`), 'خطای دانلود');

      this.$refs.prepayments.loading('end');
    },

    async downloadCharges() {
      this.$refs.charges.loading('start');
      const url = '/admin/accounting/charges';
      const query = `order_number=${this.charges.order_number}&from_date=${this.charges.from_date}&type=${this.charges.type}`;

      await wrapper(api.custom.get(`${url}?${query}`), 'خطای دانلود');

      this.$refs.charges.loading('end');
    }
  }
};
</script>

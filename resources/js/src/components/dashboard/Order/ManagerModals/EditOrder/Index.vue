<template>
  <modal
    v-show="editModal"
    title="ویرایش سفارش"
    @close="editModal = false"
  >
    <template #body>
      <div
        v-if="loading"
        class="page-loading"
      >
        <div class="spinner-border" />
      </div>
      
      <template v-else>
        <div class="row">
          <div class="col-12 col-md-6 mb-4">
            <label>محصول</label>
            <input
              v-model="editFormModels.product"
              class="form-control form-control-lg"
            >
          </div>

          <div class="col-12 col-md-6 mb-4">
            <label>نوع شارژ</label>
            <select
              v-model="editFormModels.charge_type"
              class="form-select form-select-lg"
            >
              <option value="شارژ">
                شارژ
              </option>
              <option value="واریز">
                واریز
              </option>
              <option value="خرید">
                خرید
              </option>
            </select>
          </div>

          <div class="col-12 col-md-6 mb-4">
            <label>سبد خرید</label>
            <input
              v-model="editFormModels.amount"
              class="form-control form-control-lg"
            >
            <small class="d-block mt-1 font-weight-light">
              {{
                !editFormModels.amount
                  ? '0'
                  : editFormModels.amount | numToPersian
              }}
              <span class="opa-5">تومان</span>
            </small>
          </div>

          <div class="col-12 col-md-6 mb-4">
            <label>پیش پرداخت</label>
            <input
              v-model="editFormModels.prepayment"
              class="form-control form-control-lg"
            >
            <small class="d-block mt-1 font-weight-light">
              {{
                !editFormModels.prepayment
                  ? '0'
                  : editFormModels.prepayment | numToPersian
              }}
              <span class="opa-5">تومان</span>
            </small>
          </div>

          <div class="col-12 col-md-6 col-lg-4 mb-4">
            <label>تعداد ماه‌ها</label>
            <input
              v-model="editFormModels.months"
              class="form-control form-control-lg"
            >
          </div>

          <div class="col-12 col-md-6 col-lg-4 mb-4">
            <label>تعداد چک‌ها</label>
            <input
              v-model="editFormModels.cheques"
              class="form-control form-control-lg"
            >
          </div>

          <div class="col-12 col-md-6 col-lg-4 mb-4">
            <label>بهره</label>
            <input
              v-model="editFormModels.gain"
              type="number"
              step="0.000001"
              class="form-control form-control-lg"
            >
          </div>

          <div class="col-12 mb-4">
            <g-date-picker
              v-model="editFormModels.first_ghest_at"
              label="اولین قسط"
              empty
            />
          </div>

          <div class="col-12 col-md-6 mb-4">
            <label>بازپرداخت</label>
            <select
              v-model="editFormModels.payback_type"
              class="form-select form-select-lg"
            >
              <option value="cheque">
                چک
              </option>
              <option value="epay">
                اینترنتی
              </option>
            </select>
          </div>

          <div class="col-12 col-md-6 mb-4">
            <label>سری شارژ</label>
            <input
              v-model="editFormModels.series"
              class="form-control form-control-lg"
            >
          </div>

          <div class="w-100" />
          <div class="col-12 col-md-6 mb-4">
            <label>سری کارت</label>
            <input
              v-model="editFormModels.series_card"
              class="form-control form-control-lg"
            >
          </div>
        </div>

        <div class="list-view bg-white w-100 border rounded m-0 py-2">
          <div class="list-view-item">
            <div class="d-flex aic flex-wrap">
              <div class="title">
                <span class="font-weight-light">پیش پرداخت</span>
              </div>
              <div class="mr-auto price-style font-weight-bold">
                {{
                  $parent.ghestify(editFormModels, editFormModels).prepayment
                    | moneySeperate
                }}
                <small class="opa-5">تومان</small>
              </div>
            </div>
          </div>

          <div class="list-view-item">
            <div class="d-flex aic flex-wrap">
              <div class="title">
                <span class="font-weight-light">مبلغ هر قسط</span>
              </div>
              <div class="mr-auto price-style font-weight-bold">
                {{
                  $parent.ghestify(editFormModels, editFormModels).ghest
                    | moneySeperate
                }}
                <small class="opa-5">تومان</small>
              </div>
            </div>
          </div>

          <div class="list-view-item">
            <div class="d-flex aic flex-wrap">
              <div class="title">
                <span class="font-weight-light">مبلغ کل بازپرداخت</span>
              </div>
              <div class="mr-auto price-style font-weight-bold">
                {{
                  $parent.ghestify(editFormModels, editFormModels).total
                    | moneySeperate
                }}
                <small class="opa-5">تومان</small>
              </div>
            </div>
          </div>
        </div>
      </template>
    </template>

    <template #footer>
      <div class="d-flex w-100 justify-content-between">
        <button
          class="btn btn-light rounded-pill"
          @click="editModal = false"
        >
          انصراف
        </button>

        <g-button
          ref="edit"
          text="اعمال تغییرات"
          type="button"
          color="primary"
          @click.native="editOrder"
        />
      </div>
    </template>
  </modal>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>
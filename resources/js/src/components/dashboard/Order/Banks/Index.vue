<template>
  <div
    :class="[
      'banks-selector shadow-sm border',
      { '_in-uploader': inUploader }
    ]"
  >
    <h6 class="special-font m-0 p-4 pb-0">
      <span class="font-weight-bold text-success">
        شماره شبا
      </span>، 
      <span class="font-weight-bold text-success">
        نام شعبه
      </span>
      و
      <span class="font-weight-bold text-success">
        کد شعبه
      </span>
      بانک(اطلاعات روی چک) را انتخاب یا وارد کنید
      <a
        href="/images/cheques-sample.jpg"
        target="_blank"
        rel="noopener noreferrer"
        class="border border-primary rounded-pill px-2 small"
      >تصویر راهنما
      </a>
    </h6>

    <div class="_list">
      <div
        v-for="(bank, bankIndex) in banks"
        v-show="bank.logo"
        :key="bankIndex"
        class="_bank mb-3"
      >
        <input
          :id="`b-${bankIndex}`"
          v-model="selectedBankIndex"
          :value="bankIndex"
          type="radio"
          name="banks"
        >
        <label :for="`b-${bankIndex}`">
          <!-- Radio -->
          <div class="_radio-select">
            <i class="far fa-circle" />
            <i class="fad fa-check-circle" />
          </div>

          <!-- Bank name -->
          <div>
            <img
              v-if="bank.logo"
              :src="'/images/banks/' + bank.logo + '.svg'"
            >
            <div>
              <strong>
                {{ bank.name }}
              </strong>
              <strong class="special-font d-block">
                <span
                  v-show="bank.iban && bank.iban.length > 0"
                  class="opa-5"
                >IR</span>{{ bank.iban }}
              </strong>
            </div>
          </div>

          <!-- Bank Branch -->
          <div>
            <small>
              نام شعبه
            </small>
            <strong>
              {{ bank.branch_name }}
            </strong>
          </div>

          <!-- Bank Branch Code -->
          <div>
            <small>
              کد شعبه
            </small>
            <strong>
              {{ bank.branch_code }}
            </strong>
          </div>
        </label>
      </div>

      <g-button
        v-if="!isAdding"
        class="mt-3 text-primary"
        text="افزودن یک مورد جدید"
        sm
        @click.native="addNewBank"
      />
    </div>

    <div
      v-if="isAdding"
      class="row border-top mx-0 px-4 pt-4 mt-4 mb-3"
    >
      <div class="col-12 col-lg-6">
        <ValidationProvider
          v-slot="{ errors }"
          name="شماره شبا"
          rules="required|sheba"
        >
          <label class="text-center d-block">
            شماره شبا
          </label>
          <div class="input-group ltr estedad-font rounded-50">
            <span
              class="input-group-text bg-white pl-3 font-weight-bold"
            >IR</span>
            <input
              v-model="banks[banks.length-1].iban"
              type="number"
              maxlength="24"
              oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
              class="form-control form-control-lg"
            >
          </div>
          <small
            class="text-danger d-block mt-1 text-center font-weight-light"
          >{{ errors[0] }}</small>
          <small
            v-if="shebaBankErr"
            class="text-danger d-block mt-1 text-center font-weight-light"
          >بانک شناسایی نشد. شماره شبا اشتباه است</small>
        </ValidationProvider>
      </div>

      <div class="col-12 col-lg-3">
        <ValidationProvider
          v-slot="{ errors }"
          name="نام شعبه بانک"
          rules="required"
        >
          <label class="text-center d-block">
            نام شعبه بانک
          </label>
          <input
            v-model="banks[banks.length-1].branch_name"
            maxlength="30"
            class="form-control form-control-lg ltr estedad-font text-center rounded-pill"
          >
          <small
            class="text-danger d-block mt-1 text-center font-weight-light"
          >{{ errors[0] }}</small>
        </ValidationProvider>
      </div>

      <div class="col-12 col-lg-3">
        <ValidationProvider
          v-slot="{ errors }"
          name="کد شعبه بانک"
          rules="required"
        >
          <label class="text-center d-block">
            کد شعبه بانک
          </label>
          <input
            v-model="banks[banks.length-1].branch_code"
            maxlength="7"
            class="form-control form-control-lg ltr estedad-font text-center rounded-pill"
          >
          <small
            class="text-danger d-block mt-1 text-center font-weight-light"
          >{{ errors[0] }}</small>
        </ValidationProvider>
      </div>
    </div>

    <div class="bg-gray-light border-top p-4 d-flex justify-content-end">
      <g-button
        ref="saveBank"
        text="ثبت اطلاعات بانکی"
        sm
        color="success"
        @click.native="saveSelectedBank"
      />
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

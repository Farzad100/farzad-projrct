<template>
  <div>
    <ValidationObserver>
      <!-- Sheba and Bank Informations -->
      <div class="p-4">
        <!-- Title -->
        <span class="special-font font-weight-light">
          <span class="font-weight-bold opa-5">
            ۱.
          </span>
          <span class="font-weight-bold text-success">
            شماره شبا
          </span>
          و
          <span class="font-weight-bold text-success">
            شعبه بانک
          </span>
          (اطلاعات روی چک) را وارد کنید
        </span>
        <a
          :href="
            `/images/cheques-sample-${
              shabaBank.name === 'Melli'
                ? 'melli'
                : shabaBank.name === 'Mellat'
                  ? 'mellat'
                  : 'all'
            }.jpg`
          "
          target="_blank"
          class="rounded-pill btn btn-light btn-sm d-inline-flex py-1"
        >
          راهنما
        </a>

        <!-- Form -->
        <div class="row mt-5">
          <div class="col-12">
            <ValidationProvider
              v-slot="{ errors }"
              name="شماره شبا"
              rules="required|sheba"
            >
              <label class="text-center d-flex aic jcc">
                شماره شبا
                <span
                  v-if="shabaBank.name"
                  class="d-inline-flex jcc aic mr-2"
                >
                  <img
                    height="20"
                    :src="'/images/banks/' + shabaBank.name + '.svg'"
                  >
                  <span class="mr-1 d-none d-md-inline font-weight-bold">
                    {{ shabaBank.fa }}
                  </span>
                </span>

                <button
                  v-show="role === 'admin' && wfcForm.iban"
                  class="btn btn-link py-0 text-decoration-none"
                  type="button"
                  @click="copyClip(wfcForm.iban)"
                >
                  <small>
                    کپی
                  </small>
                </button>
              </label>
              <div class="d-inline-flex w-100">
                <the-mask
                  v-model="wfcForm.iban"
                  class="form-control form-control-lg ltr estedad-font"
                  :mask="['IR ## #### #### #### #### #### ##']"
                />
                <g-button
                  class="mr-2 mt-2 py-2"
                  text=""
                  icon-left="qrcode"
                  @click.native="qrcode = true"
                />
              </div>
              <small
                class="sheba-errors text-danger d-block mt-1 text-center font-weight-light"
              >{{ errors[0] }}</small>
              <small
                v-if="shebaBankErr"
                class="text-danger d-block mt-1 text-center font-weight-light"
              > شماره شبا اشتباه است</small>
            </ValidationProvider>
          </div>

          <div class="col-12 col-lg-6">
            <ValidationProvider
              v-slot="{ errors }"
              name="نام شعبه بانک"
              rules="required"
            >
              <label class="text-center d-block">
                نام شعبه بانک
              </label>
              <input
                v-model="wfcForm.branch_name"
                maxlength="30"
                class="form-control form-control-lg ltr estedad-font text-center"
              >
              <small
                ref="errorText"
                class="sheba-errors text-danger d-block mt-1 text-center font-weight-light"
              >{{ errors[0] }}</small>
            </ValidationProvider>
          </div>

          <div class="col-12 col-lg-6">
            <ValidationProvider
              v-slot="{ errors }"
              name="کد شعبه بانک"
              rules="required"
            >
              <label class="text-center d-block">
                کد شعبه بانک
              </label>
              <input
                v-model="wfcForm.branch_code"
                type="number"
                class="form-control form-control-lg ltr estedad-font text-center"
              >
              <small
                ref="errorText"
                class="sheba-errors text-danger d-block mt-1 text-center font-weight-light"
              >{{ errors[0] }}</small>
            </ValidationProvider>
          </div>
        </div>
      </div>
    </ValidationObserver>
    <QrReader
      v-if="qrcode"
      v-model="wfcForm.iban"
      return="sheba"
      @close="qrcode= false"
    />
  </div>
</template>
  



<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>
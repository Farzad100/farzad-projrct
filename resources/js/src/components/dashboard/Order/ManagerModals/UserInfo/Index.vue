<template>
  <modal
    v-show="compeleUserInfoModal"
    title="تکمیل اطلاعات کاربر"
    size="lg"
    @close="closeModal()"
  >
    <template #body>
      <!-- User NID -->
      <div v-if="$parent.order.show_nid_form">
        <div class="border rounded p-4 mb-3">
          <div class="row">
            <div class="col-12 col-md-8 col-lg-8">
              <ValidationProvider
                v-slot="{ errors }"
                name="کد ملی"
                rules="nid"
              >
                <label>کد ملی مشتری</label>
                <input
                  v-model="userInfo.nid"
                  maxlength="10"
                  class="form-control form-control-lg"
                >
                <small class="text-danger d-block mt-1 font-weight-light">{{
                  errors[0]
                }}</small>
              </ValidationProvider>
            </div>
            <div
              class="col-12 col-md-4 col-lg-4 d-flex align-items-center mt-4"
            >
              <g-button
                ref="editUserNid"
                text="ثبت کدملی"
                type="button"
                color="primary"
                @click.native="editUserNid"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- User Birth -->
      <div v-if="$parent.order.show_birth_form">
        <div class="border rounded p-4">
          <div class="row">
            <div class="col-12 col-md-8 col-lg-8">
              <g-date-picker
                v-model="userInfo.birth"
                label="تاریخ تولد مشتری"
                empty
                no-suggest
              />
            </div>
            <div
              class="col-12 col-md-4 col-lg-4 d-flex align-items-center mt-4"
            >
              <g-button
                ref="editUserBirth"
                text="ثبت تاریخ تولد"
                type="button"
                color="primary"
                @click.native="editUserBirth"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- User ADDRESS home -->
      <div
        v-if="$parent.order.show_address_form"
        class="profile-form"
      >
        <div
          ref="addressWrapper"
          class="border rounded p-4 w-100 my-5"
        >
          <h4 class="special-font mb-4">
            آدرس منزل
          </h4>

          <div class="row a-section">
            <div class="col-12 col-md-6 mb-4">
              <label>استان</label>
              <select
                v-model="address.home.state"
                class="form-select form-select-lg"
                :disabled="!address.home.editable"
              >
                <option
                  v-for="(state, index) in stateList"
                  :key="index"
                  :value="state"
                >
                  {{ state }}
                </option>
              </select>
            </div>
            <div class="col-12 col-md-6 mb-4">
              <label>شهر</label>
              <input
                v-model="address.home.city"
                class="form-control form-control-lg"
                placeholder="چابهار"
                :disabled="!address.home.editable"
              >
            </div>
            <div class="col-12 col-md-6 mb-4">
              <label>کد پستی</label>
              <input
                v-model="address.home.postal_code"
                class="form-control form-control-lg"
                placeholder="3156176898"
                :disabled="!address.home.editable"
              >
            </div>
            <div class="col-12 col-md-6 mb-4">
              <label>شماره ثابت</label>
              <input
                v-model="address.home.phone"
                class="form-control form-control-lg"
                placeholder="02122345678"
                :disabled="!address.home.editable"
              >
            </div>
            <div class="col-12 mb-4">
              <label>آدرس دقیق</label>
              <textarea
                v-model="address.home.address"
                rows="3"
                class="form-control form-control-lg"
                :disabled="!address.home.editable"
              />
            </div>
          </div>

          <div class="d-flex justify-content-end">
            <g-button
              ref="editAddressHome"
              text="ذخیره آدرس منزل"
              type="button"
              color="primary"
              @click.native="editAddress('home')"
            />
          </div>
        </div>
      </div>

      <!-- User ADDRESS work -->
      <div
        v-if="$parent.order.show_address_form"
        class="profile-form"
      >
        <div
          ref="addressWrapper"
          class="border rounded p-4 w-100 my-5"
        >
          <h4 class="special-font mb-4">
            آدرس محل کار
          </h4>
          <div class="row a-section">
            <div class="col-12 col-md-6 mb-4">
              <label>استان</label>
              <select
                v-model="address.work.state"
                class="form-select form-select-lg"
                :disabled="!address.work.editable"
              >
                <option
                  v-for="(state, index) in stateList"
                  :key="index"
                  :value="state"
                >
                  {{ state }}
                </option>
              </select>
            </div>
            <div class="col-12 col-md-6 mb-4">
              <label>شهر</label>
              <input
                v-model="address.work.city"
                class="form-control form-control-lg"
                placeholder="چابهار"
                :disabled="!address.work.editable"
              >
            </div>
            <div class="col-12 col-md-6 mb-4">
              <label>کد پستی</label>
              <input
                v-model="address.work.postal_code"
                class="form-control form-control-lg"
                placeholder="3156176898"
                :disabled="!address.work.editable"
              >
            </div>
            <div class="col-12 col-md-6 mb-4">
              <label>شماره ثابت</label>
              <input
                v-model="address.work.phone"
                class="form-control form-control-lg"
                placeholder="02122345678"
                :disabled="!address.work.editable"
              >
            </div>
            <div class="col-12">
              <label>آدرس دقیق</label>
              <textarea
                v-model="address.work.address"
                rows="3"
                class="form-control form-control-lg"
                :disabled="!address.work.editable"
              />
            </div>
          </div>

          <div class="mt-4 d-flex justify-content-end">
            <g-button
              ref="editAddressWork"
              text="ذخیره آدرس محل کار"
              type="button"
              color="primary"
              @click.native="editAddress('work')"
            />
          </div>
        </div>
      </div>
    </template>

    <template #footer>
      <div class="d-flex w-100 justify-content-center">
        <button
          class="btn btn-light rounded-pill"
          @click="closeModal()"
        >
          بستن
        </button>
      </div>
    </template>
  </modal>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>
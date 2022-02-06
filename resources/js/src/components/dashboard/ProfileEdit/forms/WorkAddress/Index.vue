<template>
  <div
    id="work-address"
    ref="addressWrapper"
    class="border shadow-sm rounded w-100 mb-5"
  >
    <h5 class="special-font mb-5 font-weight-bold opa-5 px-4 pt-4">
      <i class="fad fa-briefcase fa-lg ml-2" />
      آدرس محل کار
    </h5>

    <ValidationObserver v-slot="{ invalid }">
      <!-- State -->
      <div class="row a-section px-4">
        <div class="col-12 col-md-6 col-lg-3 mb-4">
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


        <!-- City -->
        <div class="col-12 col-md-6 col-lg-3 mb-4">
          <ValidationProvider
            v-slot="{ errors }"
            name="شهر"
            rules="required"
          >
            <label>شهر</label>
            <input
              v-model="address.work.city"
              class="form-control form-control-lg"
              placeholder="چابهار"
              :disabled="!address.work.editable"
            >
            <small class="text-danger d-block mt-1 font-weight-light">
              {{ errors[0] }}
            </small>
          </ValidationProvider>
        </div>


        <!-- Post Code -->
        <div class="col-12 col-md-6 col-lg-3 mb-4">
          <ValidationProvider
            v-slot="{ errors }"
            name="کد پستی"
            rules="postCode|required"
          >
            <label>کد پستی</label>
            <input
              v-model="address.work.postal_code"
              class="form-control form-control-lg"
              placeholder="3156176898"
              type="text"
              maxlength="10"
              :disabled="!address.work.editable"
            >
            <small class="text-danger d-block mt-1 font-weight-light">
              {{ errors[0] }}
            </small>
          </ValidationProvider>
        </div>


        <!-- Phone Number -->
        <div class="col-12 col-md-6 col-lg-3 mb-4">
          <ValidationProvider
            v-slot="{ errors }"
            name="شماره ثابت"
            rules="required"
          >
            <label>شماره ثابت</label>
            <input
              v-model="address.work.phone"
              class="form-control form-control-lg"
              placeholder="02122345678"
              type="text"
              maxlength="11"
              :disabled="!address.work.editable"
            >
            <small class="text-danger d-block mt-1 font-weight-light">
              {{ errors[0] }}
            </small>
          </ValidationProvider>
        </div>


        <!-- Address -->
        <div class="col-12 mb-4">
          <ValidationProvider
            v-slot="{ errors }"
            name="آدرس دقیق"
            rules="required"
          >
            <label>آدرس دقیق</label>
            <textarea
              v-model="address.work.address"
              rows="3"
              class="form-control form-control-lg"
              :disabled="!address.work.editable"
            />
            <small class="text-danger d-block mt-1 font-weight-light">
              {{ errors[0] }}
            </small>
          </ValidationProvider>
        </div>
      </div>

      <div class="p-4 mt-5 bg-gray-light border-top d-flex justify-content-end">
        <g-button
          text="ویرایش آدرس محل کار"
          :disabled="invalid"
          @click.native="saveAddress('work')"
        />
      </div>
    </ValidationObserver>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

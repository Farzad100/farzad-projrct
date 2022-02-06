<template>
  <div
    id="home-address"
    ref="addressWrapper"
    class="border shadow-sm rounded w-100 mb-5"
  >
    <h5 class="special-font font-weight-bold opa-5 mb-5 px-4 pt-4">
      <i class="fad fa-house fa-lg ml-2" />
      آدرس منزل
    </h5>

    <ValidationObserver v-slot="{ invalid }">
      <div class="row a-section px-4">
        <!-- State -->
        <div class="col-12 col-md-6 col-lg-3 mb-4">
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



        <!-- City -->
        <div class="col-12 col-md-6 col-lg-3 mb-4">
          <ValidationProvider
            v-slot="{ errors }"
            name="شهر"
            rules="required"
          >
            <label>شهر</label>
            <input
              v-model="address.home.city"
              class="form-control form-control-lg"
              placeholder="چابهار"
              :disabled="!address.home.editable"
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
              v-model="address.home.postal_code"
              class="form-control form-control-lg"
              placeholder="3156176898"
              type="text"
              maxlength="10"
              :disabled="!address.home.editable"
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
              v-model="address.home.phone"
              class="form-control form-control-lg"
              placeholder="02122345678"
              type="text"
              maxlength="11"
              :disabled="!address.home.editable"
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
              v-model="address.home.address"
              rows="3"
              class="form-control form-control-lg"
              :disabled="!address.home.editable"
            />
            <small class="text-danger d-block mt-1 font-weight-light">
              {{ errors[0] }}
            </small>
          </ValidationProvider>
        </div>
      </div>

      <div class="p-4 mt-5 bg-gray-light border-top d-flex justify-content-end">
        <g-button
          ref="submit"
          text="ویرایش آدرس منزل"
          :disabled="invalid"
          @click.native="saveAddress('home')"
        />
      </div>
    </ValidationObserver>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

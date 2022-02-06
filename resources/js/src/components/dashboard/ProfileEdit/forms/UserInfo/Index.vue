<template>
  <!-- User Info Form -->
  <ValidationObserver
    id="personal-info"
    v-slot="{ invalid }"
  >
    <div class="border shadow-sm rounded pb-0 w-100 mb-5">
      <h5 class="special-font mb-5 font-weight-bold opa-5 px-4 pt-4">
        <i class="fad fa-portrait fa-lg ml-2" />
        اطلاعات کاربری
      </h5>

      <div class="row px-4">
        <!-- First Name -->
        <div class="col-12 col-md-6 col-lg-3 mb-4">
          <label>نام</label>
          <input
            v-model="userInfo.fname"
            class="form-control form-control-lg"
            placeholder="آرش"
            disabled
          >
        </div>


        <!-- Last Name -->
        <div class="col-12 col-md-6 col-lg-3 mb-4">
          <label>نام خانوادگی</label>
          <input
            v-model="userInfo.lname"
            class="form-control form-control-lg"
            placeholder="کمانگیر"
            disabled
          >
        </div>


        <!-- Mobile Number -->
        <div class="col-12 col-md-6 col-lg-3 mb-4">
          <label>شماره موبایل</label>
          <input
            v-model="userInfo.mobile"
            maxlength="11"
            class="form-control form-control-lg ltr estedad-font"
            disabled
          >
        </div>


        <!-- NID -->
        <div class="col-12 col-md-6 col-lg-3 mb-4">
          <ValidationProvider
            v-slot="{ errors }"
            name="کد ملی"
            rules="nid"
          >
            <label>کد ملی</label>
            <input
              v-model="userInfo.nid"
              maxlength="10"
              class="form-control form-control-lg"
              disabled
            >
            <small class="text-danger d-block mt-1 font-weight-light">{{
              errors[0]
            }}</small>
          </ValidationProvider>
        </div>


        <!-- Email -->
        <div class="col-12 col-md-6 col-lg-4 mb-4">
          <label>ایمیل</label>
          <input
            v-model="userInfo.email"
            type="email"
            class="form-control form-control-lg"
            placeholder="example@gmail.com"
          >
        </div>


        <!-- Birthday -->
        <div class="col-12 col-md-8 mb-4">
          <label class="mb-0">تاریخ تولد</label>
          <g-date-picker
            v-model="userInfo.birth"
            size="lg"
            empty
            no-suggest
            disabled
          />
        </div>
      </div>

      <div class="p-4 mt-5 bg-gray-light border-top d-flex justify-content-end">
        <g-button
          ref="submit"
          text="ویرایش اطلاعات کاربری"
          :disabled="invalid"
          :color="userInfo.editable ? 'success' : null"
          @click.native="saveInfo"
        />
      </div>
    </div>
  </ValidationObserver>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

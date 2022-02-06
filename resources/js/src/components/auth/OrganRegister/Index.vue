<template>
  <div class="long-forms">
    <div class="text-center px-5">
      <span class="font-weight-light">
        لطفا تمام مواردی که دایره قرمز
        <label class="n-opt ml-2 mb-0" />
        دارند را پر کنید.
      </span>
    </div>

    <ValidationObserver v-slot="{ invalid }">
      <!-- Organ Info -->
      <div class="long-forms-item border rounded px-5 pt-5 pb-3">
        <h4 class="mb-5 special-font text-brand">
          <span class="opa-3">۱.</span>
          اطلاعات سازمان
        </h4>
        <div class="row">
          <div class="col-12 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="سمت در شرکت یا سازمان"
              rules="required"
            >
              <label class="n-opt">سمت شما بعنوان رابط شرکت یا سازمان</label>
              <select
                v-model="form.agent_position"
                class="form-select form-select-lg"
              >
                <option
                  value="cao"
                  selected
                >
                  رئیس هیئت مدیره
                </option>
                <option value="ceo">
                  مدیرعامل
                </option>
                <option value="cfo">
                  مدیر/معاون امور مالی
                </option>
                <option value="chro">
                  مدیر/معاون منابع انسانی
                </option>
                <option value="hr">
                  کارشناس منابع انسانی/امور مالی
                </option>
              </select>
              <small class="text-danger d-block mt-1 font-weight-light">{{
                errors[0]
              }}</small>
            </ValidationProvider>
          </div>
          <div class="col-12 col-md-6 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="نام سازمان"
              rules="required|isFa"
            >
              <label class="n-opt">نام سازمان</label>
              <input
                v-model="form.name"
                class="form-control form-control-lg"
                placeholder="مثال: پیشگامان اعتبار آفرین شریف"
              >
              <small class="text-danger d-block mt-1 font-weight-light">{{
                errors[0]
              }}</small>
            </ValidationProvider>
          </div>
          <div class="col-12 col-md-6 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="شهرت"
              rules="required|isFa"
            >
              <label class="n-opt">شهرت</label>
              <input
                v-model="form.fame"
                class="form-control form-control-lg"
                placeholder="مثال: قسطا"
              >
              <small class="text-danger d-block mt-1 font-weight-light">{{
                errors[0]
              }}</small>
            </ValidationProvider>
          </div>
          <div class="col-12 col-md-6 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="تعداد کارمندان"
              rules="required"
            >
              <label class="n-opt">تعداد کارمندان</label>
              <select
                v-model="form.employees"
                class="form-select form-select-lg"
              >
                <option value="20">
                  کمتر از 20 نفر
                </option>
                <option value="100">
                  بین 20 تا 100 نفر
                </option>
                <option value="500">
                  بین 100 تا 500 نفر
                </option>
                <option value="2500">
                  بین 500 تا 2500 نفر
                </option>
                <option value="10000">
                  بیشتر از 2500 نفر
                </option>
              </select>
              <small class="text-danger d-block mt-1 font-weight-light">{{
                errors[0]
              }}</small>
            </ValidationProvider>
          </div>
          <div class="col-12 col-md-6 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="سابقه قعالیت"
              rules="required"
            >
              <label class="n-opt">سابقه قعالیت</label>
              <select
                v-model="form.age"
                class="form-select form-select-lg"
              >
                <option value="3">
                  زیر ۳ سال
                </option>
                <option value="10">
                  ۳ - ۱۰ سال
                </option>
                <option value="100">
                  بیشتر از ۱۰ سال
                </option>
              </select>
              <small class="text-danger d-block mt-1 font-weight-light">{{
                errors[0]
              }}</small>
            </ValidationProvider>
          </div>
          <div class="col-12 mb-4">
            <label>شرح حوزه فعالیت</label>
            <textarea
              v-model="form.about"
              class="form-control form-control-lg"
              rows="3"
            />
          </div>
        </div>
      </div>

      <!-- Organ contact data -->
      <div class="long-forms-item border rounded px-5 pt-5 pb-3">
        <h4 class="mb-5 special-font text-brand">
          <span class="opa-3">۲.</span>
          اطلاعات تماس و آدرس
        </h4>
        <div class="row">
          <div class="col-12 col-md-6 mb-4">
            <label>وبسایت</label>
            <input
              v-model="form.website"
              class="form-control form-control-lg ltr"
              placeholder="مثال: www.organ.com"
            >
          </div>
          <div class="col-12 col-md-6 mb-4">
            <label>ایمیل</label>
            <input
              v-model="form.email"
              class="form-control form-control-lg ltr"
              placeholder="مثال: info@organ.com"
            >
          </div>
          <div class="col-12 col-md-8 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="تلفن"
              rules="required|phone"
            >
              <label class="n-opt">تلفن ثابت با کد شهر</label>
              <input
                v-model="form.phone"
                class="form-control form-control-lg ltr estedad-font"
                placeholder="مثال: ۰۲۱۹۱۰۷۰۰۹۲"
                type="text"
                maxlength="11"
              >
              <small class="text-danger d-block mt-1 font-weight-light">{{
                errors[0]
              }}</small>
            </ValidationProvider>
          </div>
          <div class="col-12 col-md-4 mb-4">
            <label>داخلی</label>
            <input
              v-model="form.phone_direct"
              class="form-control form-control-lg ltr estedad-font"
              placeholder="مثال: ۲"
              type="text"
              maxlength="5"
            >
          </div>
          <div class="col-12 col-md-4 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="استان"
              rules="required"
            >
              <label class="n-opt">استان</label>
              <select
                v-model="form.state"
                class="form-select form-select-lg"
              >
                <option
                  v-for="(state, i) in stateList"
                  :key="i"
                  :value="state"
                >
                  {{ state }}
                </option>
              </select>
              <small class="text-danger d-block mt-1 font-weight-light">{{
                errors[0]
              }}</small>
            </ValidationProvider>
          </div>
          <div class="col-12 col-md-4 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="شهر، شهرستان یا روستا"
              rules="required|isFa"
            >
              <label class="n-opt">شهر، شهرستان یا روستا</label>
              <input
                v-model="form.city"
                class="form-control form-control-lg"
                placeholder="مثال: چابهار"
              >
              <small class="text-danger d-block mt-1 font-weight-light">{{
                errors[0]
              }}</small>
            </ValidationProvider>
          </div>
          <div class="col-12 col-md-4 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="کد پستی"
              rules="required|postCode"
            >
              <label class="n-opt">کد پستی</label>
              <input
                v-model="form.postal_code"
                class="form-control form-control-lg ltr estedad-font"
                type="text"
                maxlength="10"
              >
              <small class="text-danger d-block mt-1 font-weight-light">{{
                errors[0]
              }}</small>
            </ValidationProvider>
          </div>
          <div class="col-12 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="آدرس"
              rules="required|isFa"
            >
              <label class="n-opt">آدرس</label>
              <textarea
                v-model="form.address"
                class="form-control form-control-lg"
                rows="3"
              />
              <small class="text-danger d-block mt-1 font-weight-light">{{
                errors[0]
              }}</small>
            </ValidationProvider>
          </div>
        </div>
      </div>


      <!-- Submit button -->
      <div class="d-flex flex-column jcc aic mb-5">
        <div
          v-if="submitDone"
          class="d-flex flex-column justify-content-center align-items-center mb-5"
        >
          <h4 class="text-green">
            درخواست شما با موفقیت ثبت شد
          </h4>
          <span>
            تا چند ثانیه دیگر به داشبورد منتقل خواهید شد
          </span>
          <router-link
            to="/dashboard"
            class="btn btn-light btn-sm rounded-pill mt-4"
          >
            داشبورد
          </router-link>
        </div>

        <button
          v-else
          :disabled="invalid"
          :class="loading ? 'btn-loading' : ''"
          class="btn btn-lg btn-success rounded-pill mb-4"
          @click="register"
        >
          <div
            class="spinner-border"
            role="status"
          >
            <span class="sr-only">در حال بررسی</span>
          </div>
          <span class="btn-text d-flex aic w-100 jcb px-5">
            ثبت اطلاعات
          </span>
        </button>

        <small class="font-weight-light">
          پس از ثبت اطلاعات، برای ادامه فرآیند ثبت سازمان به پنل خود مراجعه
          کنید.
        </small>
      </div>
    </ValidationObserver>

    <div
      v-if="!helpShow"
      class="d-flex justify-content-center"
    >
      <button
        class="btn btn-light rounded-pill btn-sm text-primary"
        @click="helpShow = true"
      >
        در فرایند ثبت‌نام به مشکل خورده‌ام
      </button>
    </div>

    <div
      v-if="helpShow"
      class="anydesk-help"
    >
      <small>
        در صورت نیاز و هماهنگی با پشتبانی می‌توانید از لینک‌های زیر برنامه 
        <a
          href="https://anydesk.com/en"
          class="text-danger text-decoration-underline"
          target="_blank"
        >
          anydesk
        </a>
        را دریافت کنید
      </small>
      <div class="links">
        <a
          href="https://download.anydesk.com/AnyDesk.exe"
          download
        >
          <img src="/images/anydesk/win.svg">
          <span>
            دانلود برای ویندوز
          </span>
        </a>

        <a
          href="https://download.anydesk.com/anydesk.dmg"
          download
        >
          <img src="/images/anydesk/mac.svg">
          <span>
            دانلود برای مک
          </span>
        </a>

        <a
          href="https://anydesk.com/en/downloads/linux"
          target="_blank"
        >
          <img src="/images/anydesk/linux.svg">
          <span>
            دانلود برای لینوکس
          </span>
        </a>
      </div>
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

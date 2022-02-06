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
      <div class="long-forms-item border rounded px-5 pt-5 pb-3">
        <h4 class="mb-5 special-font text-brand">
          <span class="opa-3">۱.</span>
          نوع مالکیت فروشگاه
        </h4>
        <selectable-input-group
          v-model="form.ownershipType"
          type="radio"
          name="myRadioInputs"
          class="h-label d-flex justify-content-center w-100 mb-3"
        >
          <selectable-input
            id="real"
            class="ml-2 w-100"
            value="real"
            :checked="true"
          >
            <span class="d-block text-center">حقیقی</span>
          </selectable-input>
          <selectable-input
            id="legal"
            class="mr-2 w-100"
            value="legal"
          >
            <span class="d-block text-center">حقوقی</span>
          </selectable-input>
        </selectable-input-group>
      </div>

      <!-- Shop Info -->
      <div class="long-forms-item border rounded px-5 pt-5 pb-3">
        <h4 class="mb-5 special-font text-brand">
          <span class="opa-3">۲.</span>
          اطلاعات فروشگاه
        </h4>

        <div class="row">
          <div class="col-12 col-md-6 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="نام فروشگاه"
              rules="required|isFa"
            >
              <label class="n-opt">نام فروشگاه</label>
              <input
                v-model="form.name"
                class="form-control form-control-lg"
              >
              <small class="text-danger d-block mt-1 font-weight-light">{{
                errors[0]
              }}</small>
            </ValidationProvider>
          </div>

          <div class="col-12 col-md-6 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="دسته‌بندی فروشگاه"
              rules="required"
            >
              <label class="n-opt">دسته‌بندی</label>
              <select
                v-model="form.category"
                class="form-select form-select-lg"
              >
                <option value="digital">
                  کالای دیجیتال
                </option>
                <option value="home">
                  خانه و آشپزخانه
                </option>
                <option value="cloth">
                  مد و پوشاک
                </option>
                <option value="travel">
                  گردشگری
                </option>
                <option value="jewel">
                  سکه، طلا و جواهرات
                </option>
                <option value="beauty">
                  سلامت و زیبایی
                </option>
                <option value="service">
                  ابزار و خدمات
                </option>
                <option value="other">
                  سایر موارد
                </option>
              </select>
              <small class="text-danger d-block mt-1 font-weight-light">{{
                errors[0]
              }}</small>
            </ValidationProvider>
          </div>

          <div class="col-12 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="توضیحات"
              rules="required|isFa"
            >
              <label class="n-opt">توضیح کسب و کار</label>
              <textarea
                v-model="form.about"
                class="form-control form-control-lg"
                rows="2"
                maxlength="240"
                placeholder="بطور خلاصه بنویسید که کسب و کار شما در چه زمینه ای است"
              />
              <small class="text-danger d-block mt-1 font-weight-light">{{
                errors[0]
              }}</small>
            </ValidationProvider>
          </div>

          <div class="col-12 col-md-6 mb-4">
            <label class="n-opt">نوع فروشگاه</label>
            <div class="btn-group d-flex">
              <input
                id="danger-outlined"
                v-model="form.type"
                value="offline"
                type="radio"
                class="btn-check"
                name="shopType"
                autocomplete="off"
              >
              <label
                class="btn"
                for="danger-outlined"
              >غیر آنلاین</label>

              <input
                id="success-outlined"
                v-model="form.type"
                value="online"
                type="radio"
                class="btn-check"
                name="shopType"
                autocomplete="off"
              >
              <label
                class="btn"
                for="success-outlined"
              >آنلاین</label>
            </div>
          </div>

          <div class="col-12 col-md-6 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="کد مالیاتی"
              :rules="form.type === 'online' ? required : null"
            >
              <label
                :class="form.type === 'offline' ? '' : 'n-opt'"
              >کد مالیاتی</label>
              <input
                v-model="form.tax_code"
                class="form-control form-control-lg"
                :disabled="form.type === 'offline'"
                maxlength="10"
              >
              <div class="d-flex mt-2 justify-content-between">
                <div>
                  <a
                    target="_blank"
                    href="https://e3.tax.gov.ir/action/do/registrationselector"
                  >سامانه دریافت کد مالیاتی</a>
                </div>
                <div>
                  <a href="#">راهنمای دریافت</a>
                </div>
              </div>
              <small
                v-if="form.type === 'online'"
                class="text-danger d-block mt-1 font-weight-light"
              >{{ errors[0] }}</small>
            </ValidationProvider>
          </div>

          <div
            class="col-12 mb-4"
            :class="form.type === 'online' ? 'col-md-5' : 'col-md-6'"
          >
            <ValidationProvider
              v-slot="{ errors }"
              name="وبسایت"
              :rules="form.type === 'online' ? required : null"
            >
              <label
                :class="form.type === 'offline' ? '' : 'n-opt'"
              >وبسایت</label>
              <input
                v-model="form.website"
                class="form-control form-control-lg ltr text-right"
                placeholder="example.com"
              >
              <small
                v-if="form.type === 'online'"
                class="text-danger d-block mt-1 font-weight-light"
              >{{ errors[0] }}</small>
            </ValidationProvider>
          </div>

          <div
            class="col-12 mb-4"
            :class="form.type === 'online' ? 'col-md-7' : 'col-md-6'"
          >
            <ValidationProvider
              v-slot="{ errors }"
              name="ایمیل"
              :rules="form.type === 'online' ? required : null"
            >
              <label
                :class="form.type === 'offline' ? '' : 'n-opt'"
              >ایمیل
              </label>
              <div class="input-group normal-rounded flex-row-reverse">
                <input
                  v-model="form.email"
                  class="form-control form-control-lg ltr text-right"
                  style="border-radius: 3px 0 0 3px !important;"
                  :disabled="form.type === 'online' && !form.website"
                  :placeholder="
                    form.type === 'online' ? 'info' : 'info@example.com'
                  "
                >
                <span
                  v-if="form.type === 'online' && form.website"
                  class="input-group-text ltr px-2"
                  style="border-radius: 0 3px 3px 0 !important; font-size: 1.25rem;"
                >@ {{ extractHostname(form.website) }}</span>
              </div>
              <small
                v-if="form.type === 'online'"
                class="text-danger d-block mt-1 font-weight-light"
              >{{ errors[0] }}</small>
            </ValidationProvider>
          </div>

          <div
            v-if="form.type === 'online'"
            class="col-12 col-md-12 mb-4"
          >
            <ValidationProvider
              v-slot="{ errors }"
              name="شماره شبا"
              :rules="form.type === 'online' ? required : null"
            >
              <label
                :class="'n-opt'"
              >شماره شبا (به نام صاحب حساب کاربری)</label>
              <input
                v-model="form.sheba"
                class="form-control form-control-lg ltr text-right"
                placeholder="IR123456..."
                maxlength="26"
              >
              <small
                v-if="form.type === 'online'"
                class="text-danger d-block mt-1 font-weight-light"
              >{{ errors[0] }}</small>
            </ValidationProvider>
          </div>

          <div
            v-if="form.ownershipType !== 'legal'"
            class="col-12 col-md-12 mb-3"
          >
            <div class="alert alert-warning d-flex">
              <div><i class="far fa-exclamation-circle font-lg" /></div>
              <div
                class="alert-content"
                style="text-align: justify;"
              >
                <ul>
                  <li class="mb-3">
                    جهت تأیید و فعال شدن درگاه پرداخت توسط سامانه شاپرک، باید
                    اطلاعات کدپستی، شماره تلفن ثابت، کد ملی و تاریخ تولد شما
                    برابر با همین اطلاعات هنگام دریافت کد مالیاتی باشد.
                  </li>
                  <li class="mb-3">
                    اگر کد مالیاتی شما حقوقی است، اینجا هم فروشگاه خود را می
                    بایست حقوقی ثبت نمایید.
                  </li>
                  <li>
                    تلفن ثابت و کدپستی می بایست همخوانی داشته باشند.
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-5 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="تلفن"
              rules="required"
            >
              <label class="n-opt">تلفن ثابت با کد شهر</label>
              <input
                v-model="form.phone"
                type="text"
                maxlength="11"
                class="form-control form-control-lg"
                placeholder="مثال: ۰۲۱۹۱۰۷۰۰۹۲"
              >
              <small class="text-danger d-block mt-1 font-weight-light">{{
                errors[0]
              }}</small>
            </ValidationProvider>
          </div>

          <div class="col-12 col-md-3 mb-4">
            <label>داخلی</label>
            <input
              v-model="form.phone_direct"
              class="form-control form-control-lg"
              placeholder="مثال: ۲"
            >
          </div>

          <div class="col-12 col-md-4 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="کد پستی"
              rules="required"
            >
              <label class="n-opt">کد پستی</label>
              <input
                v-model="form.postal_code"
                type="text"
                maxlength="10"
                class="form-control form-control-lg"
              >
              <small class="text-danger d-block mt-1 font-weight-light">{{
                errors[0]
              }}</small>
            </ValidationProvider>
          </div>

          <div class="col-12 col-md-6 mb-4">
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

          <div class="col-12 col-md-6 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="شهر یا شهرستان "
              rules="required|isFa"
            >
              <label class="n-opt">شهر یا شهرستان </label>
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

      <!-- Legal Info -->
      <div
        v-if="form.ownershipType === 'legal'"
        class="long-forms-item border rounded px-5 pt-5 pb-3"
      >
        <h4 class="mb-5 special-font text-brand">
          <span class="opa-3">۳.</span>
          اطلاعات حقوقی
        </h4>

        <div class="row">
          <div class="col-12 col-md-12 mb-3">
            <div class="alert alert-warning d-flex">
              <div><i class="far fa-exclamation-circle font-lg" /></div>
              <div
                class="alert-content"
                style="text-align: justify;"
              >
                <ul>
                  <li class="mb-3">
                    جهت تأیید و فعال شدن درگاه پرداخت توسط سامانه شاپرک، باید
                    اطلاعات کدپستی، شماره تلفن ثابت، کد ملی و تاریخ تولد شما
                    برابر با همین اطلاعات هنگام دریافت کد مالیاتی باشد.
                  </li>
                  <li>
                    تلفن ثابت و کدپستی می بایست همخوانی داشته باشند.
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-6 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="نام شرکت"
              rules="required|isFa"
            >
              <label class="n-opt">نام شرکت</label>
              <input
                v-model="form.company_name"
                class="form-control form-control-lg"
              >
              <small class="text-danger d-block mt-1 font-weight-light">{{
                errors[0]
              }}</small>
            </ValidationProvider>
          </div>

          <div class="col-12 col-md-6 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="شماره ثبت"
              rules="required"
            >
              <label class="n-opt">شماره ثبت</label>
              <input
                v-model="form.company_rn"
                type="text"
                minlength="1"
                maxlength="7"
                class="form-control form-control-lg"
              >
              <small class="text-danger d-block mt-1 font-weight-light">{{
                errors[0]
              }}</small>
            </ValidationProvider>
          </div>

          <div class="col-12 col-md-4 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="شناسه ملی"
              rules="nic|required"
            >
              <label class="n-opt">شناسه ملی</label>
              <input
                v-model="form.company_nic"
                type="text"
                minlength="11"
                maxlength="11"
                class="form-control form-control-lg"
              >
              <small class="text-danger d-block mt-1 font-weight-light">{{
                errors[0]
              }}</small>
            </ValidationProvider>
          </div>

          <div class="col-12 col-md-4 mb-4">
            <label>کد اقتصادی</label>
            <input
              v-model="form.company_ec"
              type="text"
              minlength="12"
              maxlength="12"
              class="form-control form-control-lg"
            >
          </div>

          <div class="col-12 col-md-4 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="شماره تلفن شرکت"
              rules="required"
            >
              <label class="n-opt">شماره تلفن شرکت</label>
              <input
                v-model="form.company_phone"
                type="text"
                maxlength="11"
                class="form-control form-control-lg"
              >
              <small class="text-danger d-block mt-1 font-weight-light">{{
                errors[0]
              }}</small>
            </ValidationProvider>
          </div>

          <div class="col-12 col-md-4 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="استان"
              rules="required"
            >
              <label class="n-opt">استان</label>
              <select
                v-model="form.company_state"
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
              name="شهر "
              rules="required|isFa"
            >
              <label class="n-opt">شهر ثبت</label>
              <input
                v-model="form.company_city"
                class="form-control form-control-lg"
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
              rules="required"
            >
              <label class="n-opt">کد پستی</label>
              <input
                v-model="form.company_postal_code"
                type="text"
                maxlength="10"
                class="form-control form-control-lg"
              >
              <small class="text-danger d-block mt-1 font-weight-light">{{
                errors[0]
              }}</small>
            </ValidationProvider>
          </div>

          <div class="col-12 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="آدرس شرکت"
              rules="required|isFa"
            >
              <label class="n-opt">آدرس شرکت</label>
              <textarea
                v-model="form.company_address"
                class="form-control form-control-lg"
                rows="3"
              />
              <small class="text-danger d-block mt-1 font-weight-light">{{
                errors[0]
              }}</small>
            </ValidationProvider>
          </div>

          <div class="col-12 col-md-4 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="نام مدیر عامل"
              rules="required|isFa"
            >
              <label class="n-opt">نام مدیر عامل</label>
              <input
                v-model="form.ceo_fname"
                class="form-control form-control-lg"
              >
              <small class="text-danger d-block mt-1 font-weight-light">{{
                errors[0]
              }}</small>
            </ValidationProvider>
          </div>

          <div class="col-12 col-md-4 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="نام خانوادگی مدیر عامل"
              rules="required|isFa"
            >
              <label class="n-opt">نام خانوادگی مدیر عامل</label>
              <input
                v-model="form.ceo_lname"
                class="form-control form-control-lg"
              >
              <small class="text-danger d-block mt-1 font-weight-light">{{
                errors[0]
              }}</small>
            </ValidationProvider>
          </div>

          <div class="col-12 col-md-4 mb-4">
            <ValidationProvider
              v-slot="{ errors }"
              name="کد ملی مدیر عامل"
              rules="nid"
            >
              <label class="n-opt">کد ملی مدیر عامل</label>
              <input
                v-model="form.ceo_nid"
                type="text"
                minlength="10"
                maxlength="10"
                class="form-control form-control-lg"
              >
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
          پس از ثبت اطلاعات، ادامه فرآیند ثبت نام را در پنل خود پیگیری نمایید.
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
        کاربر گرامی، چنانچه تمامی موارد الزامی را بطور صحیح وارد کرده باشید، ثبت
        نام بدون مشکل انجام خواهدشد. بنابراین چنانچه با خطای 400 مواجه شده اید،
        لطفاً قبل از تماس با پشتیبانی، مجدداً تمامی ورودی ها را بررسی و از صحت
        آن اطمینان حاصل نمایید. اگر همچنان با خطای ثبت فروشگاه مواجه هستید،
        لطفاً نرم افزار anydesk را از لینک زیر دریافت و نصب کنید. سپس با
        پشتیبانی فروشگاه های قسطا تماس بگیرید تا مشکل را بصورت آنلاین بررسی و حل
        کنند:
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

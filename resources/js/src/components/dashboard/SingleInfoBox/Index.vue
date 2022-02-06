<template>
  <div class="_order-info shadow-sm rounded border">
    <!-- Preview -->
    <div class="_preview bg-gray-light px-4 pt-4 pb-4">
      <div class="_preview-top mb-3">
        <!-- Organ Code -->
        <span v-if="data.code">
          کد سازمان:
          <span class="ltr nova-font font-weight-bold pr-1">
            {{ data.code }}
          </span>
        </span>

        <!-- Shop legal type -->
        <span v-if="type === 'shop'">
          {{ data.company ? 'حقوقی' : 'حقیقی' }}
        </span>

        <span class="mx-3 opa-5">
          •
        </span>

        <!-- Organ name -->
        <span v-if="type === 'organ' && data.name">
          {{ data.name ? data.name : data.fame }}
        </span>

        <!-- Organ name -->
        <span v-if="type === 'shop' && data.type">
          {{ data.type === 'offline' ? 'فروشگاه فیزیکی' : 'فروشگاه آنلاین' }}
        </span>

        <span class="mx-3 opa-5">
          •
        </span>

        <!-- Create at date -->
        <span v-if="data.created_at">
          تاریخ ثبت‌نام:

          <span class="pr-1">
            {{ data.created_at | jDate }}
          </span>
        </span>

        <!-- Order Status -->
        <span
          class="badge rounded-pill p-2 px-3 mr-auto"
          :class="handleStatus(data.status)"
        >
          {{ data.status_farsi }}
        </span>
      </div>

      <h2 class="special-font font-weight-bold">
        {{ type === 'organ' ? data.fame : data.name }}
      </h2>
    </div>

    <nav>
      <div
        id="nav-tab"
        class="nav nav-tabs px-4"
        role="tablist"
      >
        <a
          id="nav-info-tab"
          class="nav-link active"
          data-bs-toggle="tab"
          href="#nav-info"
          role="tab"
          aria-controls="nav-info"
          aria-selected="true"
        >
          اطلاعات
        </a>
        <a
          v-if="type === 'shop'"
          id="nav-address-tab"
          class="nav-link"
          data-bs-toggle="tab"
          href="#nav-address"
          role="tab"
          aria-controls="nav-address"
          aria-selected="true"
        >
          آدرس
        </a>
        <a
          v-if="data.about"
          id="nav-about-tab"
          class="nav-link"
          data-bs-toggle="tab"
          href="#nav-about"
          role="tab"
          aria-controls="nav-about"
          aria-selected="true"
        >
          {{ type === 'organ' ? 'شرح حوزه فعالیت' : 'درباره فروشگاه' }}
        </a>
      </div>
    </nav>
    <div
      id="nav-tabContent"
      class="tab-content"
    >
      <div
        id="nav-info"
        class="tab-pane fade  show active"
        role="tabpanel"
        aria-labelledby="nav-info-tab"
      >
        <div class="d-flex w-100">
          <ul class="list-group list-group-flush px-0 pt-3 w-100 border-left">
            <li
              v-if="data.employees"
              class="list-group-item d-flex justify-content-between px-4"
            >
              <span class="opa-7 font-weight-light">تعداد کارمندان</span>
              <span>
                {{ employeeCount(data.employees) }}
              </span>
            </li>
            <li
              v-if="data.age"
              class="list-group-item d-flex justify-content-between px-4"
            >
              <span class="opa-7 font-weight-light">سابقه قعالیت</span>
              <span>
                {{ age(data.age) }}
              </span>
            </li>
            <li
              v-if="data.category"
              class="list-group-item d-flex justify-content-between px-4"
            >
              <span class="opa-7 font-weight-light">دسته‌بندی</span>
              <span>
                {{ data.category }}
              </span>
            </li>
            <li
              v-if="data.phone"
              class="list-group-item d-flex justify-content-between px-4"
            >
              <span class="opa-7 font-weight-light">تلفن</span>
              <span class="ltr estedad-font">
                {{ data.phone }}
                {{ data.phone_direct ? `(${data.phone_direct})` : null }}
              </span>
            </li>
          </ul>

          <ul class="list-group list-group-flush px-0 pt-3 w-100">
            <li
              v-if="type === 'shop'"
              class="list-group-item d-flex justify-content-between px-4"
            >
              <span class="opa-7 font-weight-light">تعداد سفارشات</span>
              <span>{{ data.orders_count }}</span>
            </li>
            <li
              v-if="type === 'shop'"
              class="list-group-item d-flex justify-content-between px-4"
            >
              <span class="opa-7 font-weight-light">تعداد سفارشات موفق</span>
              <span>{{ data.orders_successful_count }}</span>
            </li>
            <li
              v-if="type === 'shop'"
              class="list-group-item d-flex justify-content-between px-4"
            >
              <span class="opa-7 font-weight-light">فروش (تومان)</span>
              <span>{{ data.sales_total | moneySeperate }}</span>
            </li>
            <li
              v-if="data.email"
              class="list-group-item d-flex justify-content-between px-4"
            >
              <span class="opa-7 font-weight-light">ایمیل</span>
              <span>{{ data.email }}</span>
            </li>
            <li
              v-if="data.website"
              class="list-group-item d-flex justify-content-between px-4"
            >
              <span class="opa-7 font-weight-light">وبسایت</span>
              <span>
                <a
                  :href="`http://${data.website}`"
                  target="_blank"
                >
                  {{ data.website }}
                </a>
              </span>
            </li>
          </ul>
        </div>
      </div>

      <div
        id="nav-about"
        class="tab-pane fade"
        role="tabpanel"
        aria-labelledby="nav-about-tab"
      >
        <!-- eslint-disable vue/no-v-html -->
        <p
          class="p-4 d-block font-weight-light pb-0"
          v-html="data.about"
        />
      </div>

      <div
        v-if="type === 'shop'"
        id="nav-address"
        class="tab-pane fade"
        role="tabpanel"
        aria-labelledby="nav-address-tab"
      >
        <div class="d-flex border-bottom">
          <div class="border-left px-4 py-3 w-100">
            <span class="d-block opa-5 mb-2">
              استان
            </span>
            <span>
              {{ data.state_name }}
            </span>
          </div>
          <div class="border-left px-4 py-3 w-100">
            <span class="d-block opa-5 mb-2">
              شهر
            </span>
            <span>
              {{ data.city }}
            </span>
          </div>
          <div class="w-100 px-4 py-3">
            <span class="d-block opa-5 mb-2">
              کد پستی
            </span>
            <span>
              {{ data.postal_code }}
            </span>
          </div>
        </div>

        <div class="p-4">
          <span class="d-block opa-5 mb-2">
            آدرس
          </span>
          <span>
            {{ data.address }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

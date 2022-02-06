<template>
  <div class="_user-info shadow-sm border rounded">
    <div class="bg-gray-light p-4">
      <div class="d-flex">
        <i
          :class="[
            'fad pl-3',
            type === 'user'
              ? 'fa-user'
              : type === 'company'
                ? 'fa-hotel'
                : 'fa-user-tie'
          ]"
          style="font-size: 1.4em"
        />
        <div>
          <h4 class="mb-0 special-font">
            <router-link
              v-if="role === 'admin' && info.mobile"
              :to="{
                name: 'dash-admin-users',
                query: { mobile: info.mobile }
              }"
            >
              {{ info.fname }}
              {{ info.lname }}
            </router-link>

            <span v-else>
              {{ info.fname }}
              {{ info.lname }}
            </span>

            <span v-if="type === 'company'">
              {{ info.name }}
            </span>

            <i
              v-if="info.badge"
              :class="['fad', 'fa-badge', 'user-badge', '_lg', info.badge]"
            />
          </h4>
          <span
            v-if="info.mobile"
            class="opa-5"
          >
            {{ info.mobile }}
          </span>
          <router-link
            v-if="role === 'admin' && info.mobile"
            :to="{
              name: 'dash-orders',
              params: { role: 'admin' },
              query: { mobile: info.mobile }
            }"
          >
            <p class="mt-2">
              <i class="far fa-link ml-1" />سفارش‌های این کاربر
            </p>
          </router-link>
        </div>
      </div>
    </div>

    <nav>
      <div
        id="nav-tab"
        class="nav nav-tabs px-4"
        role="tablist"
      >
        <a
          v-if="type !== 'company'"
          id="nav-first-tab"
          class="nav-link active"
          data-bs-toggle="tab"
          href="#nav-first"
          role="tab"
          aria-controls="nav-first"
          aria-selected="true"
        >اطلاعات فردی</a>
        <a
          v-if="type === 'company'"
          id="nav-legal-tab"
          class="nav-link active"
          data-bs-toggle="tab"
          href="#nav-legal"
          role="tab"
          aria-controls="nav-legal"
          aria-selected="true"
        >اطلاعات حقوقی</a>
        <a
          v-if="type === 'company' && info.ceo_nid"
          id="nav-ceo-tab"
          class="nav-link"
          data-bs-toggle="tab"
          href="#nav-ceo"
          role="tab"
          aria-controls="nav-ceo"
          aria-selected="true"
        >مدیر عامل</a>
        <a
          v-if="type === 'company'"
          id="nav-co-address-tab"
          class="nav-link"
          data-bs-toggle="tab"
          href="#nav-co-address"
          role="tab"
          aria-controls="nav-co-address"
          aria-selected="false"
        >آدرس‌ها</a>
        <a
          v-if="info.addresses && info.addresses.length"
          id="nav-second-tab"
          class="nav-link"
          data-bs-toggle="tab"
          href="#nav-second"
          role="tab"
          aria-controls="nav-second"
          aria-selected="false"
        >آدرس‌ها</a>
        <a
          v-if="type !== 'company' && data.ghestacard"
          id="nav-gCart-tab"
          class="nav-link"
          data-bs-toggle="tab"
          href="#nav-gCard"
          role="tab"
          aria-controls="nav-gCard"
          aria-selected="true"
        >وضعیت کارت</a>
      </div>
    </nav>

    <div
      id="nav-tabContent"
      class="tab-content"
    >
      <div
        v-if="type !== 'company'"
        id="nav-first"
        class="tab-pane fade show active"
        role="tabpanel"
        aria-labelledby="nav-first-tab"
      >
        <ul class="list-group list-group-flush px-0 pt-3">
          <li
            v-if="data.user && data.user.roles && role === 'admin'"
            class="list-group-item d-flex justify-content-between px-4"
          >
            <span class="opa-7 font-weight-light">نقش‌ها</span>
            <span>
              <i
                v-for="(roleItem, index) in data.user.roles"
                :key="index"
                :class="[
                  'far pr-3',
                  { 'fa-user': roleItem === 'user' },
                  { 'fa-store': roleItem === 'shop' },
                  { 'fa-building': roleItem === 'organ' },
                  { 'fa-user-crown': roleItem === 'admin' }
                ]"
              />
            </span>
          </li>

          <!-- nid -->
          <li
            v-if="type === 'agent'"
            class="list-group-item d-flex justify-content-between px-4"
          >
            <span class="opa-7 font-weight-light">سمت</span>
            <span>{{ agentPosition }}</span>
          </li>

          <!-- nid -->
          <li
            v-if="info.nid"
            class="list-group-item d-flex justify-content-between px-4"
          >
            <span class="opa-7 font-weight-light">کد ملی</span>
            <span>{{ info.nid }}</span>
          </li>

          <!-- birthdate -->
          <li
            v-if="info.birth"
            class="list-group-item d-flex justify-content-between px-4"
          >
            <span class="opa-7 font-weight-light">تاریخ تولد</span>
            <span>{{ info.birth.replace('-', '/').replace('-', '/') }}</span>
          </li>

          <!-- email -->
          <li
            v-if="info.email"
            class="list-group-item d-flex justify-content-between px-4"
          >
            <span class="opa-7 font-weight-light">ایمیل</span>
            <span>{{ info.email }}</span>
          </li>

          <!-- phone -->
          <li
            v-if="info.phone"
            class="list-group-item d-flex justify-content-between px-4"
          >
            <span class="opa-7 font-weight-light">تلفن ثابت</span>
            <span>{{ info.phone }}</span>
          </li>
        </ul>
      </div>

      <div
        v-if="type === 'company'"
        id="nav-legal"
        class="tab-pane fade show active"
        role="tabpanel"
        aria-labelledby="nav-legal-tab"
      >
        <ul class="list-group list-group-flush px-0 pt-3">
          <!-- EC -->
          <li
            v-if="info.ec"
            class="list-group-item d-flex justify-content-between px-4"
          >
            <span class="opa-7 font-weight-light">کد اقتصادی</span>
            <span>{{ info.ec }}</span>
          </li>

          <!-- NIC -->
          <li
            v-if="info.nic"
            class="list-group-item d-flex justify-content-between px-4"
          >
            <span class="opa-7 font-weight-light">شناسه ملی</span>
            <span>{{ info.nic }}</span>
          </li>

          <!-- RN -->
          <li
            v-if="info.rn"
            class="list-group-item d-flex justify-content-between px-4"
          >
            <span class="opa-7 font-weight-light">شناسه ثبت</span>
            <span>{{ info.rn }}</span>
          </li>

          <!-- PHONE -->
          <li
            v-if="info.phone"
            class="list-group-item d-flex justify-content-between px-4"
          >
            <span class="opa-7 font-weight-light">تلفن ثابت</span>
            <span>{{ info.phone }}</span>
          </li>
        </ul>
      </div>

      <div
        v-if="type === 'company'"
        id="nav-ceo"
        class="tab-pane fade"
        role="tabpanel"
        aria-labelledby="nav-ceo-tab"
      >
        <ul class="list-group list-group-flush px-0 pt-3">
          <!-- CEO -->
          <li
            v-if="info.ceo_fname"
            class="list-group-item d-flex justify-content-between px-4"
          >
            <span class="opa-7 font-weight-light">نام و نام خانوادگی</span>
            <span>
              {{ info.ceo_fname }}
              {{ info.ceo_lname }}
            </span>
          </li>

          <!-- EC -->
          <li
            v-if="info.ceo_nid"
            class="list-group-item d-flex justify-content-between px-4"
          >
            <span class="opa-7 font-weight-light">کد ملی</span>
            <span>{{ info.ceo_nid }}</span>
          </li>

          <!-- RN -->
          <li
            v-if="info.ceo_mobile"
            class="list-group-item d-flex justify-content-between px-4"
          >
            <span class="opa-7 font-weight-light">موبایل</span>
            <span>{{ info.ceo_mobile }}</span>
          </li>
        </ul>
      </div>

      <div
        v-if="type !== 'company' && data.ghestacard"
        id="nav-gCard"
        class="tab-pane fade px-3 pt-3 pb-3 "
        role="tabpanel"
        aria-labelledby="nav-gCard-tab"
      >
        <div
          id="ssd"
          class="d-flex mt-1"
        >
          <span
            v-if="data.ghestacard.status == 'delivered'"
            class="ml-4"
          >
            <small class="d-block opa-7 font-weight-light">
              <span> زمان تحویل </span>
            </small>
            <strong> {{ data.ghestacard.created_at | jDate }} </strong>
          </span>

          <span
            v-if="data.ghestacard.status == 'sent'"
            class="ml-4"
          >
            <small class="d-block opa-7 font-weight-light">
              <span> تاریخ ارسال </span>
            </small>
            <strong> {{ data.ghestacard.created_at | jDate }} </strong>
          </span>

          <span>
            <small class="d-block opa-7 font-weight-light">وضعیت</small>
            <strong> {{ data.ghestacard.status_farsi }} </strong>
          </span>
        </div>

        <div class="d-flex mt-1">
          <span
            v-if="data.ghestacard.series"
            class="ml-4"
          >
            <small class="d-block opa-7 font-weight-light">شماره سری</small>
            <strong> {{ data.ghestacard.series }} </strong>
          </span>

          <span v-if="data.ghestacard.card_number">
            <small class="d-block opa-7 font-weight-light">شماره کارت</small>
            <strong> {{ data.ghestacard.card_number | cardNumber }} </strong>
          </span>
        </div>
      </div>

      <div
        v-if="type === 'company'"
        id="nav-co-address"
        class="tab-pane fade"
        role="tabpanel"
        aria-labelledby="nav-co-address-tab"
      >
        <ul class="list-group list-group-flush px-0 pt-3">
          <li class="list-group-item px-4">
            <div class="title">
              <small class="d-block opa-7 font-weight-light">
                دفتر
              </small>
            </div>
            <strong class="d-block">
              {{ info.address }}

              <div class="d-flex mt-3">
                <span
                  v-if="info.postal_code"
                  class="ml-4"
                >
                  <small class="d-block opa-7 font-weight-light">کد پستی</small>
                  <strong> {{ info.postal_code }} </strong>
                </span>

                <span v-if="info.phone">
                  <small class="d-block opa-7 font-weight-light">تلفن</small>
                  <strong> {{ info.phone }} </strong>
                </span>
              </div>
            </strong>
          </li>
        </ul>
      </div>

      <div
        v-if="info.addresses && info.addresses.length"
        id="nav-second"
        class="tab-pane fade"
        role="tabpanel"
        aria-labelledby="nav-second-tab"
      >
        <ul class="list-group list-group-flush px-0 pt-3">
          <li
            v-for="(address, index) in info.addresses"
            :key="index"
            class="list-group-item px-4"
          >
            <div class="title">
              <small class="d-block opa-7 font-weight-light">
                {{ address.type === 'home' ? 'منزل' : 'محل کار' }}
              </small>
            </div>
            <strong class="d-block">
              {{ address.state }}
              -
              {{ address.city }}
              -
              {{ address.address }}

              <div class="d-flex mt-3">
                <span
                  v-if="address.postal_code"
                  class="ml-4"
                >
                  <small class="d-block opa-7 font-weight-light">کد پستی</small>
                  <strong> {{ address.postal_code }} </strong>
                </span>

                <span v-if="address.phone">
                  <small class="d-block opa-7 font-weight-light">تلفن</small>
                  <strong> {{ address.phone }} </strong>
                </span>
              </div>
            </strong>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default {
  mixins: [Logic]
};
</script>

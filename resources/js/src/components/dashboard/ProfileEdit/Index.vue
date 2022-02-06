<template>
  <div>
    <div
      v-if="loading"
      class="page-loading"
    >
      <div class="spinner-border" />
    </div>

    <div
      v-else
      class="profile-form col-12 col-lg-10 mt-5 position-relative"
    >
      <div class="_p-f-tabs mb-4 mobile-scroller">
        <button
          :class="{ active: tab === 'userInfo' }"
          @click.prevent="selectTab('userInfo')"
        >
          <span class="special-font mr-2 font-weight-bold">
            اطلاعات کاربری
          </span>
          <div>
            <span v-if="formStatus.form_personal !== 100">
              %{{ formStatus.form_personal }}
            </span>
            <i
              v-else
              class="fad fa-check-circle text-primary"
            />
          </div>
        </button>

        <button
          :class="{ active: tab === 'homeAddress' }"
          @click.prevent="selectTab('homeAddress')"
        >
          <span class="special-font mr-2 font-weight-bold">
            آدرس منزل
          </span>
          <div>
            <span v-if="formStatus.form_address_home !== 100">
              %{{ formStatus.form_address_home }}
            </span>
            <i
              v-else
              class="fad fa-check-circle text-primary"
            />
          </div>
        </button>

        <button
          :class="{ active: tab === 'extra' }"
          @click.prevent="selectTab('extra')"
        >
          <span class="special-font mr-2 font-weight-bold">
            اطلاعات تکمیلی
          </span>
          <div>
            <span v-if="formStatus.form_extra !== 100">
              %{{ formStatus.form_extra }}
            </span>
            <i
              v-else
              class="fad fa-check-circle text-primary"
            />
          </div>
        </button>

        <button
          :class="{ active: tab === 'workAddress' }"
          @click.prevent="selectTab('workAddress')"
        >
          <span class="special-font mr-2 font-weight-bold">
            آدرس محل کار
          </span>
          <div>
            <span v-if="formStatus.form_address_work !== 100">
              %{{ formStatus.form_address_work }}
            </span>
            <i
              v-else
              class="fad fa-check-circle text-primary"
            />
          </div>
        </button>
      </div>

      <keep-alive>
        <user-info v-if="tab === 'userInfo'" />
        <home-address v-if="tab === 'homeAddress'" />
        <extra v-if="tab === 'extra'" />
        <work-address v-if="tab === 'workAddress'" />
      </keep-alive>

      <div
        v-if="$route.query.fromOrder"
        :class="
          $route.query.fromOrder && allowToBack.length === 2 ? 'active' : null
        "
        class="float-light-alert flex-column flex-lg-row justify-content-center justify-content-lg-between align-items-center mt-5"
      >
        <span>
          اطلاعات مورد نیاز پروفایل برای ادامه روند سفارش تکمیل شده است
        </span>
        <router-link
          class="btn btn-primary rounded-pill mt-4 mt-lg-0"
          :to="{
            name: 'dash-order-single',
            params: { oid: this.$route.query.fromOrder, role: role }
          }"
        >
          بازگشت به سفارش
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

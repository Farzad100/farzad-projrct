<template>
  <div class="position-relative">
    <div
      v-if="data.tag"
      style="z-index: 1"
      class="position-absolute badge badge-sm badge-danger"
    >
      هست اینجا
    </div>
    <router-link
      :to="{ name: 'dash-order-single', params: { oid: data.oid ? data.oid : '' , role: role} }"
      :class="['order-card', { 'sk-loading': skLoading }]"
    >
      <div class="px-3 py-4">
        <div class="amount d-flex justify-content-between align-items-center">
          <h4 class="font-weight-bolder m-0">
            {{ !data.amount ? '' : data.amount | moneySeperate }}
            <span v-if="data.amount">تومان</span>
          </h4>

          <small v-if="data.user">
            {{ data.user.fname }}
            {{ data.user.lname }}
            <i
              v-if="role === 'admin' && data.user.badge"
              :class="['fas', 'fa-badge', 'user-badge', data.user.badge]"
            />
          </small>
        </div>

        <small
          v-if="data.months"
          class="font-weight-light opa-7 ml-3"
        >
          {{ data.months }}
          ماه،
          {{ data.cheques }}
          قسط
        </small>
        <small
          v-if="data.ghest"
          class="font-weight-light opa-7 ml-3"
        >
          هر قسط
          {{ data.ghest | moneySeperate }}
          تومان
        </small>
      </div>
      <div class="d-flex jcb aic border-top px-3 py-4">
        <small
          v-if="!data"
          class="badge"
        />
        <span
          v-if="data.status_farsi"
          class="badge rounded-pill p-2"
          :class="handleStatus(data.status)"
        >
          {{ data.status_farsi }}
        </span>
        <div>
          <small v-if="role === 'admin' && data.user.roles">
            <i
              v-for="(roleItem, index) in data.user.roles"
              :key="index"
              :class="
                [
                  'far opa-5 pl-2',
                  { 'fa-store': roleItem === 'shop' },
                  { 'fa-building': roleItem === 'organ' },
                  { 'fa-user-crown': roleItem === 'admin' },
                ]
              "
            />
          </small>
          <span
            v-if="data.oid"
            class="font-weight-light ltr nova-font"
          >
            {{ data.oid ? data.oid : '' }}
          </span>
        </div>
      </div>
      <div
        class="border-top d-flex justify-content-between align-items-center px-3 py-2 bg-gray-light"
      >
        <small
          v-if="data.shop_id"
          class="org-shop"
        >
          <small class="pl-1">
            <i class="far fa-store opa-5 pl-1" />
            فروشگاهی
            <span
              v-if="data.shop.name"
              class="font-weight-light pr-1"
            >
              —
            </span>
          </small>
          <small>
            {{ data.shop.name }}
          </small>
        </small>

        <small
          v-else-if="data.organ_id"
          class="org-shop"
        >
          <small class="pl-1">
            <i class="far fa-building opa-5 pl-1" />
            سازمانی
            <span
              v-if="data.organ.fame || data.organ.name"
              class="font-weight-light pr-1"
            >
              —
            </span>
          </small>
          <small>
            {{ data.organ.fame || data.organ.name }}
          </small>
        </small>

        <small
          v-else
          class="org-shop"
        >
          <small class="pl-1">
            <i class="far fa-user opa-5 pl-1" />
            شخصی
          </small>
        </small>

        <small
          v-if="data.date_index_value"
          class="d-flex flex-column align-items-end"
        >
          <small class="d-block opa-5">
            {{ data.date_index_label }}
          </small>
          <small>
            {{ data.date_index_value | jDate }}
          </small>
        </small>

        <small
          v-else
          class="d-flex flex-column align-items-end"
        >
          <small class="d-block opa-5">
            تاریخ ثبت سفارش
          </small>
          <small>
            {{ data.created_at | jDate }}
          </small>
        </small>
      </div>
    </router-link>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

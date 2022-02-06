<template>
  <div>
    <div class="_order-info shadow-sm rounded border">
      <!-- Preview -->
      <div class="_preview bg-gray-light px-4 pt-4 pb-3 border-bottom">
        <div
          v-if="data.debugger"
          class="ltr"
        >
          order_id: {{ data.id }}- user_id: {{ data.user_id }}- extra:
          {{ data.extraData }}
        </div>
        <div class="_preview-top">
          <!-- Order Code -->
          <span class="d-none d-md-inline ltr font-weight-bold nova-font">
            {{ data.oid }}
          </span>

          <span class="d-none d-md-inline mx-3 opa-5">
            •
          </span>

          <!-- Order Type -->
          <span class="d-none d-md-inline">
            <template v-if="order.type">
              <i
                :class="[
                  'far pl-1',
                  order.type.is_shop ? 'fa-store' : 'fa-building'
                ]"
              />
              <span>
                {{ order.type.is_shop ? 'فروشگاه' : 'سازمان' }}
              </span>
              <router-link
                v-if="order.type.is_shop || order.type.is_organ"
                :class="{ 'd-none': role !== 'admin' }"
                :to="{
                  name: order.type.is_shop ? 'dash-shops' : 'dash-organs',
                  query: { name: order.type.name }
                }"
              >
                {{ order.type.name }}
              </router-link>
              <span
                v-if="order.type.is_shop || order.type.is_organ"
                :class="{ 'd-none': role === 'admin' }"
              >
                {{ order.type.name }}
              </span>
            </template>

            <template v-else-if="role === 'admin'">
              <i class="far fa-user pl-1" />
              شخصی
            </template>
          </span>

          <span class="d-none d-md-inline mx-3 opa-5">
            •
          </span>

          <!-- Order Dates -->
          <span class="d-none d-md-inline">
            {{ data.date_index_label }}:

            <span class="pr-1">
              {{ data.date_index_value | jDate }}
            </span>
          </span>

          <span class="mr-3">
            <a
              v-if="role === 'admin'"
              target="_blank"
              :href="'/dashboard/user/orders/' + data.oid"
            >
              <i class="far fa-eye ml-1" />از دید کاربر
            </a>
          </span>

          <div class="mr-auto d-flex aic">
            <!-- Order Status -->
            <span
              class="badge rounded-pill p-2 px-3"
              :class="handleStatus(data.status)"
            >
              {{ data.status_farsi }}
            </span>

            <div
              v-if="showCancel && role === 'user'"
              class="dropdown mr-2"
            >
              <button
                id="moreAction"
                class="btn btn-light btn-sm rounded-pill"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                <i class="fas fa-ellipsis-h" />
              </button>
              <ul
                class="dropdown-menu dropdown-menu-dark"
                aria-labelledby="moreAction"
              >
                <li class="d-flex">
                  <button
                    type="button"
                    class="btn btn-link text-danger text-right text-decoration-none"
                    @click.prevent="cancelModal = true"
                  >
                    <small>لغو سفارش</small>
                  </button>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <h1 class="price-style">
          {{ data.amount | moneySeperate }}
          <small>
            تومان
          </small>
        </h1>
      </div>

      <div class="_mobile-size-data">
        <span class="ltr font-weight-bold nova-font">
          {{ data.oid }}
        </span>

        <span>
          <template v-if="order.type">
            <i
              :class="[
                'far pl-1',
                order.type.is_shop ? 'fa-store' : 'fa-building'
              ]"
            />
            <span>
              {{ order.type.is_shop ? 'فروشگاه' : 'سازمان' }}
            </span>
            <router-link
              v-if="order.type.is_shop || order.type.is_organ"
              :class="{ 'd-none': role !== 'admin' }"
              :to="{
                name: order.type.is_shop ? 'dash-shops' : 'dash-organs',
                query: { name: order.type.name }
              }"
            >
              {{ order.type.name }}
            </router-link>
            <span
              v-if="order.type.is_shop || order.type.is_organ"
              :class="{ 'd-none': role === 'admin' }"
            >
              {{ order.type.name }}
            </span>
          </template>

          <template v-else>
            <i class="far fa-user pl-1" />
            شخصی
          </template>
        </span>

        <span>
          {{ data.date_index_label || 'تاریخ ثبت' }}:

          <span class="pr-1">
            {{ data.date_index_value || data.created_at | jDate }}
          </span>
        </span>
      </div>

      <!-- Payments Info -->
      <div class="_payment-info">
        <div>
          <small class="opa-7">
            پیش‌ پرداخت
            <small
              v-if="data.prepaid_type === 'offline'"
              class="text-danger font-weight-bold pr-1"
            >
              غیر اینترنتی
            </small>
          </small>
          <h4 class="price-style mt-2">
            {{ data.prepayment | moneySeperate }}
            <small class="">
              تومان
            </small>
          </h4>
        </div>

        <div>
          <small class="opa-7">
            هر قسط
          </small>
          <h4 class="price-style mt-2">
            {{ data.ghest | moneySeperate }}
            <small class="">
              تومان
            </small>
          </h4>
        </div>

        <div>
          <small class="opa-7">
            نحوه باز پرداخت
          </small>
          <h4 class="price-style mt-2">
            {{ data.months }}
            <small class="ml-2 ">ماه،</small>
            {{ data.cheques }}
            <small v-if="data.payback_type === 'cheque'">چک</small>
            <small v-else>قسط اینترنتی</small>
          </h4>
        </div>

        <div>
          <small class="opa-7">
            کل بازپرداخت
          </small>
          <h4 class="price-style mt-2">
            {{ data.total | moneySeperate }}
            <small>
              تومان
            </small>
          </h4>
        </div>
      </div>
    </div>

    <div
      v-if="data.hastinja"
      class="alert alert-danger mt-3"
    >
      <i class="fas fa-exclamation-circle ml-2" />
      این مشتری یک سفارش
      <small>(با {{ data.hastinja.cheques }} قسط
        {{ data.hastinja.ghest | moneySeperate }} تومانی)</small>
      در سایت هست‌اینجا دارد که در وضعیت «{{ data.hastinja.status_farsi }}» است.
    </div>

    <modal
      v-show="cancelModal"
      title="لغو سفارش"
      @close="cancelModal = false"
    >
      <template #body>
        <label>دلیل لغو سفارش</label>
        <textarea
          v-model="cancelReason"
          rows="3"
          class="form-control form-control-lg"
        />
      </template>

      <template #footer>
        <div class="d-flex w-100 justify-content-between">
          <button
            class="btn btn-light rounded-pill"
            @click="cancelModal = false"
          >
            انصراف
          </button>

          <g-button
            ref="submit"
            text="لغو سفارش"
            type="button"
            color="danger"
            @click.native="cancel"
          />
        </div>
      </template>
    </modal>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

<template>
  <div class="position-relative">
    <div
      v-if="loading"
      class="page-loading"
    >
      <div class="spinner-border" />
    </div>

    <div
      v-else-if="order.amount"
      class="order-single"
    >
      <div class="d-flex flex-column align-items-start pt-5">
        <!-- Action Buttons -->
        <div class="d-flex mobile-scroller">
          <!-- <button
            v-if="role === 'admin'"
            class="btn btn-sm btn-light rounded-pill text-nowrap"
            @click="openInquiryModal()"
          >
            <i class="far fa-fingerprint" />
            <span class="pr-2">
              استعلام
            </span>
          </button> -->

          <button
            v-if="role === 'admin'"
            class="btn btn-sm btn-light rounded-pill mr-2 text-nowrap"
            @click="transactionsModal = true"
          >
            <i class="far fa-receipt" />
            <span class="pr-2">
              تراکنش‌ها
            </span>
          </button>

          <button
            v-if="role === 'admin'"
            class="btn btn-sm btn-light rounded-pill mr-2 text-nowrap"
            @click="$refs.note.$refs.addNoteModal.openModal('new')"
          >
            <i class="far fa-sticky-note" />
            <span class="pr-2">
              یادداشت
            </span>
          </button>

          <button
            v-if="role === 'admin'"
            class="btn btn-sm btn-light rounded-pill mr-2 text-nowrap"
            @click="$refs.smsModal.openModal()"
          >
            <i class="far fa-comment-lines" />
            <span class="pr-2">
              ارسال پیام
            </span>
          </button>

          <button
            v-if="role === 'admin'"
            class="btn btn-sm btn-light rounded-pill mr-2 text-nowrap"
            @click="$refs.allDatesModal.openModal()"
          >
            <i class="far fa-calendar-day" />
            <span class="pr-2">
              همه تاریخ‌ها
            </span>
          </button>

          <button
            v-if="role === 'admin'"
            class="btn btn-sm btn-light text-primary rounded-pill mr-2 text-nowrap"
            @click="$refs.editOrderModal.openModal()"
          >
            <i class="far fa-edit" />
            <span class="pr-2">
              ویرایش
            </span>
          </button>

          <button
            v-if="role === 'admin'"
            class="btn btn-sm btn-light rounded-pill mr-2 text-nowrap"
            :class="
              order.status !== 'rejected' ? 'text-danger' : 'text-secondary'
            "
            :disabled="order.status === 'rejected'"
            @click="rejectModal = true"
          >
            <i class="far fa-ban" />
            <span class="pr-2">
              رد سفارش
            </span>
          </button>

          <button
            v-if="role === 'shop' && showCancelForShop"
            class="btn btn-sm btn-light rounded-pill mr-2"
            :class="
              order.status !== 'cancelled' ? 'text-danger' : 'text-secondary'
            "
            :disabled="order.status === 'cancelled'"
            @click="rejectModal = true"
          >
            <i class="far fa-ban pl-2" />
            لغو سفارش
          </button>
        </div>
      </div>

      <div class="row flex-column-reverse flex-xl-row py-4">
        <!-- RIGHT -->
        <div class="col-12 col-xl-8">
          <!-- eslint-disable vue/no-v-html -->
          <div
            v-if="order.important_msg && role != 'admin'"
            class="d-flex alert"
            :class="'alert-' + order.important_msg.color"
          >
            <i class="fal fa-exclamation-circle align-self-center h2 ml-2" />
            <span
              class="text-justify"
              v-html="order.important_msg.text"
            />
          </div>

          <order-info :data="order" />

          <orderStatus
            v-if="order.status === 'rejected' || order.status === 'cancelled'"
            :status="order.status"
            :reason="order.reason"
            class="mt-4"
          />

          <template v-else>
            <order-steps
              :order-data="order"
              :type="role === 'shop' ? 'shopAdmin' : null"
            />

            <div
              v-if="role === 'admin'"
              class="mt-5 pt-5 border-top border-st"
            >
              <h5 class="special-font mb-3 font-weight-bold opa-5 d-flex aic">
                <i class="fad fa-address-card ml-2" />
                بررسی مجدد
              </h5>

              <!-- Contract -->
              <contract-download
                v-if="role === 'admin'"
                c-dl-only
                :data="order"
              />

              <!-- Permanent checking -->
              <cheques-info
                v-if="
                  !inArray(order.status, [
                    'draft',
                    'submitted',
                    'docs_uploaded'
                  ])
                "
                :doc-type="role === 'shop' ? 'shopAdmin' : null"
                :payback-type="order.payback_type"
                :order-data="order"
                show-docs-anyway
                manager
                class="mt-3"
              />

              <!-- Card -->
              <div
                v-if="role === 'admin'"
                class="border shadow-sm rounded mt-4 border-top"
              >
                <div class="p-4">
                  <h6>
                    چاپ شناسنامه قسطاکارت
                  </h6>
                  <div class="mt-3">
                    <div>
                      <input
                        id="card-address-home"
                        v-model="cardAddress"
                        value="home"
                        type="radio"
                        name="card-address-home"
                      >
                      <label
                        class="_check-itself _radio"
                        for="card-address-home"
                      >
                        به آدرس منزل:
                        {{
                          `${order.user.addresses[0].state}، ${order.user.addresses[0].city}، ${order.user.addresses[0].address}، کدپستی: ${order.user.addresses[0].postal_code}`
                        }}
                      </label>
                    </div>
                    <div>
                      <input
                        v-if="order.user.addresses[1]"
                        id="card-address-work"
                        v-model="cardAddress"
                        value="work"
                        type="radio"
                        name="card-address-work"
                      >
                      <label
                        v-if="order.user.addresses[1]"
                        class="_check-itself _radio"
                        for="card-address-work"
                      >
                        به آدرس کار:
                        {{
                          `${order.user.addresses[1].state}، ${order.user.addresses[1].city}، ${order.user.addresses[1].address}، کدپستی: ${order.user.addresses[1].postal_code}`
                        }}
                      </label>
                    </div>
                  </div>
                  <div class="d-flex justify-content-between mt-4">
                    <div>
                      <input
                        v-model="cardPassword"
                        maxlength="4"
                        placeholder="رمز کارت"
                        class="form-control form-control-lg ltr estedad-font text-center"
                      >
                    </div>
                    <div>
                      <a
                        class="btn btn-primary rounded-pill"
                        :href="
                          `/orders/${order.oid}/card-stick/${cardAddress}/${cardPassword}`
                        "
                        target="_blank"
                      >پرینت</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </div>

        <!-- LEFT -->
        <div class="col-12 col-xl-4">
          <div
            v-if="
              role !== 'organ' &&
                (order.show_nid_form ||
                  order.show_birth_form ||
                  order.show_address_form)
            "
            class="alert alert-danger rounded mb-3 p-3 "
          >
            <small class="d-block mb-4">
              برخی از اطلاعات مشتری شما ناقص است و برای ادامه حتما لازم است که
              این اطلاعات توسط شما یا مشتری تکمیل شود
            </small>

            <g-button
              text="تکمیل اطلاعات مشتری"
              color="primary"
              class="w-100"
              center
              @click.native="$refs.userInfoModal.openModal()"
            />
          </div>

          <user-info
            :data="order"
            type="user"
            class="mb-4"
          />

          <!-- Some More Action -->
          <div
            v-if="role === 'admin' && order.cta"
            class="border shadow-sm bg-white rounded"
          >
            <p
              v-if="order.cta.description"
              class="special-font font-weight-bold text-center p-4 pb-2"
            >
              {{ order.cta.description }}
            </p>
            <div
              :class="[
                'bg-gray-light p-4',
                { 'border-top': order.cta.description }
              ]"
            >
              <g-button
                ref="orderCta"
                :text="order.cta.text"
                color="primary"
                block
                center
                @click.native="
                  handleOrderCta({
                    method: order.cta.method,
                    url: order.cta.url,
                    type: order.cta.type,
                    modal_name: order.cta.modalName,
                    endpoint: order.cta.url,
                    loader: 'orderCta'
                  })
                "
              />

              <g-button
                v-if="order.cta.url2"
                ref="orderCta2"
                :text="order.cta.text2"
                color="outline-primary mt-2"
                block
                center
                @click.native="
                  handleOrderCta({
                    method: order.cta.method,
                    url: order.cta.url2,
                    loader: 'orderCta2'
                  })
                "
              />
            </div>
          </div>

          <!-- Organ Accept order -->
          <div
            v-if="role === 'organ' && order.status === 'pended_by_organ'"
            class="border shadow-sm rounded mt-4"
          >
            <p
              v-if="order.cta"
              class="special-font font-weight-bold text-center p-4 pb-0"
            >
              {{ order.cta.description }}
            </p>

            <div
              v-if="order.cta.data"
              class="list-view list-view w-100 m-0"
            >
              <div class="list-view-item">
                <div class="d-flex aic flex-wrap">
                  <div class="title">
                    <span class="font-weight-light">اعتبار در دسترس:</span>
                  </div>
                  <h5
                    class="mr-auto price-style font-weight-bold"
                    :class="{ 'text-danger': order.cta.data.available < 0 }"
                  >
                    {{ Math.abs(order.cta.data.available) | moneySeperate }}
                    <small
                      v-if="order.cta.data.available < 0"
                      class="text-danger mr-0"
                    >-</small>
                    <small>تومان</small>
                  </h5>
                </div>
              </div>

              <div class="list-view-item">
                <div class="d-flex aic flex-wrap">
                  <div class="title">
                    <span class="font-weight-light">اعتبار مورد نیاز:</span>
                  </div>
                  <h5 class="mr-auto price-style font-weight-bold">
                    {{ order.payback | moneySeperate }}
                    <small
                      v-if="order.cta.data.available < 0"
                      class="text-danger"
                    />
                    <small>تومان</small>
                  </h5>
                </div>
              </div>
            </div>

            <div
              v-if="order.cta.ok"
              class="d-flex p-4 bg-gray-light border-top mt-1"
            >
              <g-button
                ref="orderAcceptOrgan"
                text="تایید سفارش"
                color="success w-100 ml-2"
                center
                @click.native="
                  handleOrderCta({
                    method: order.cta.method,
                    url: order.cta.url_accept,
                    loader: 'orderAcceptOrgan'
                  })
                "
              />
              <g-button
                ref="orderRejectOrgan"
                text="رد سفارش"
                class="text-danger w-50"
                center
                @click.native="
                  handleOrderCta({
                    method: order.cta.method,
                    url: order.cta.url_reject,
                    loader: 'orderRejectOrgan'
                  })
                "
              />
            </div>
          </div>

          <!-- Notes -->
          <small-note
            ref="note"
            :notes="notes"
          />
        </div>
      </div>

      <!-- Reject Modal -->
      <modal
        v-show="rejectModal"
        title="رد سفارش"
        @close="rejectModal = false"
      >
        <template #body>
          <label>دلیل رد سفارش</label>
          <textarea
            v-model="rejectReason"
            rows="3"
            class="form-control form-control-lg"
          />
        </template>

        <template #footer>
          <div class="d-flex w-100 justify-content-between">
            <button
              class="btn btn-light rounded-pill"
              @click="rejectModal = false"
            >
              انصراف
            </button>

            <g-button
              ref="reject"
              text="رد سفارش"
              type="button"
              color="danger"
              @click.native="reject"
            />
          </div>
        </template>
      </modal>

      <!-- Transactions Modal -->
      <modal
        v-show="transactionsModal"
        title="تراکنش‌ها"
        @close="transactionsModal = false"
      >
        <template #body>
          <div
            v-for="(item, index) in order.payments"
            :key="index"
            :class="[
              'border rounded py-2 pr-3 pl-2 mb-3',
              item.type === 'prepayment' && item.status === 100
                ? 'bg-green-light'
                : 'bg-white',
              item.type === 'prepayment' && item.status === 0
                ? 'bg-red-light'
                : null,
              `border${
                item.status === 100
                  ? '-success'
                  : item.status === 0
                    ? null
                    : '-danger'
              }`
            ]"
          >
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <strong>
                  {{ paymentName(item.type) }}
                  <span
                    v-if="item.amount"
                    class="price-style font-weight-light pr-2"
                  >
                    ({{ item.amount | moneySeperate }} <small> تومان </small>)
                  </span>
                </strong>
                <span
                  v-if="item.discount"
                  class="opa-5 pr-2"
                >
                  با تخفیف
                </span>
                <span
                  v-if="item.ref_id && item.ref_id > 2"
                  class="opa-5 pr-2"
                >
                  اینترنتی
                </span>
                <span
                  v-else-if="item.ref_id && item.ref_id < 2"
                  class="opa-5 pr-2"
                >
                  غیر اینترنتی
                </span>
              </div>

              <g-button
                v-if="item.status === 0"
                text="بررسی مجدد"
                sm
                @click.native="transactionReCheck(item.id)"
              />

              <span
                v-else
                class="badge rounded-pill p-2 px-3 font-weight-light"
                :class="`badge-${item.status === 100 ? 'success' : 'danger'}`"
              >
                {{ item.status === 100 ? 'موفق' : 'ناموفق' }}
              </span>
            </div>

            <div class="d-flex justify-content-between flex-wrap mt-2">
              <div v-if="item.ref_id && item.ref_id > 2">
                <small class="opa-5 pl-2">
                  شماره تراکنش:
                </small>
                <small>
                  {{ item.ref_id }}
                </small>
              </div>

              <div
                v-if="item.paid_at"
                class="pl-2"
              >
                <small>
                  {{ item.paid_at | jDate }}
                  -
                  {{ item.paid_at | jTime }}
                </small>
              </div>
            </div>
          </div>
        </template>

        <template #footer>
          <div class="d-flex w-100 justify-content-center">
            <button
              class="btn btn-light rounded-pill"
              @click="transactionsModal = false"
            >
              انصراف
            </button>
          </div>
        </template>
      </modal>

      <inquiry-modal ref="inquiryModal" />

      <sms-modal ref="smsModal" />

      <order-charge-modal
        v-if="order.cta && order.cta.modalName === 'orderChargeModal'"
        ref="orderChargeModal"
      />

      <user-info-modal ref="userInfoModal" />

      <all-dates-modal ref="allDatesModal" />

      <edit-order-modal ref="editOrderModal" />
    </div>

    <empty
      v-else
      class="my-5"
    />
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

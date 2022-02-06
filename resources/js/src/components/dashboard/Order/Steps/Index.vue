<template>
  <div class="_order-steps">
    <!-----------------------------------------
      Invoice of Inquiries
    ----------------------------------------->
    <div :class="['_step', stepDetector('draft')]">
      <div class="_title">
        <h4>
          استعلام‌های اولیه
        </h4>
      </div>
      <div
        v-if="orderData.status === 'draft'"
        class="_content"
      >
        <small class="opa-5 d-block mt-3 mt-md-0">
          {{ orderData.what_to_do }}
        </small>

        <template v-if="!orderData.user.nid || !orderData.user.birth">
          <ValidationObserver>
            <form
              autocomplete="off"
              class="max-width-360 mx-auto text-center"
              @submit.prevent="regEssentials"
            >
              <ValidationProvider
                v-slot="{ errors }"
                name="کد ملی"
                rules="nid"
              >
                <label class="opa-7">
                  <small> کد ملی {{ role === 'shop' ? 'مشتری' : '' }} </small>
                </label>
                <input
                  v-model="nid"
                  minlength="10"
                  maxlength="10"
                  class="form-control form-control-lg mb-2 text-center"
                  placeholder="کد ملی"
                >
                <small class="text-danger d-block">{{ errors[0] }}</small>
              </ValidationProvider>
              <ValidationProvider
                v-slot="{ errors }"
                name="تاریخ تولد"
                rules="required"
              >
                <label class="opa-7">
                  <small>
                    تاریخ تولد {{ role === 'shop' ? 'مشتری' : '' }}
                  </small>
                </label>
                <g-date-picker
                  v-model="birth"
                  size="lg"
                  empty
                  no-suggest
                />
                <small class="text-danger d-block">{{ errors[0] }}</small>
              </ValidationProvider>

              <div class="mt-3">
                <g-button
                  ref="regEssentials"
                  text="ثبت اطلاعات"
                  color="success"
                  @click.native="regEssentials"
                />
              </div>
            </form>
          </ValidationObserver>
        </template>

        <div v-else-if="orderData.inquiry_paid_at">
          در حال استعلام‌های مورد نیاز هستیم...
        </div>

        <div
          v-else
          id="invoice-inquiry"
        >
          <invoice-inquiry :data="orderData.invoice" />
        </div>
      </div>
    </div>

    <!-----------------------------------------
      Components: [Docs]
    ----------------------------------------->
    <div :class="['_step', stepDetector('submitted')]">
      <div class="_title">
        <h4>
          آپلود مدارک
        </h4>
      </div>
      <div
        v-if="orderData.status === 'submitted'"
        class="_content"
      >
        <small class="opa-5 d-block mt-3 mt-md-0">
          {{ orderData.what_to_do }}
        </small>

        <!-- <div
          v-if="orderData.rt"
          class="rabbit-turtle mt-3 border border-info"
        >
          <div class="mb-3 p-3 text-justify">
            کاربر گرامی، با توجه به راه اندازی سیستم اعتبارسنجی هوشمند، چنانچه
            فایل پرینت حساب خود را با فرمت اکسل از اینترنت بانک دریافت نموده و
            بارگذاری نمایید، اعتبارسنجی به این شیوه سریع‌تر و ارزان‌تر خواهد
            بود. هزینه اعتبارسنجی در انتهای فرآیند سفارش به همراه پیش‌پرداخت
            دریافت می‌گردد.
          </div>
          <div>
            <table class="table table-striped mb-0">
              <thead>
                <th class="pb-2" />
                <th class="pb-2">
                  طرح هوشمند
                </th>
                <th class="pb-2">
                  طرح معمولی
                </th>
              </thead>
              <tbody>
                <tr>
                  <td>فرمت قابل قبول</td>
                  <td>xls , xlsx</td>
                  <td>همه فرمت‌ها</td>
                </tr>
                <tr>
                  <td>زمان بررسی</td>
                  <td>{{ orderData.rt.rabbit.time }}</td>
                  <td>{{ orderData.rt.turtle.time }}</td>
                </tr>
                <tr>
                  <td>هزینه</td>
                  <td>{{ orderData.rt.rabbit.price | moneySeperate }} تومان</td>
                  <td>{{ orderData.rt.turtle.price | moneySeperate }} تومان</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div> -->

        <g-docs :type="type" />
      </div>
    </div>

    <!-----------------------------------------
      Empty Step
    ----------------------------------------->
    <div
      v-if="orderData.organ_id"
      :class="['_step', stepDetector('pended_by_organ', 'pending')]"
    >
      <div class="_title">
        <h4>
          <span class="_normal-title">
            تایید توسط سازمان است
          </span>
          <span class="_pending-title">
            {{
              role === 'admin'
                ? 'سفارش مشتری در انتظار تایید توسط سازمان است'
                : role === 'organ'
                  ? 'سفارش مشتری در انتظار تایید توسط شما است'
                  : 'سفارش شما در انتظار تایید توسط سازمان است'
            }}
          </span>
        </h4>
      </div>
    </div>

    <!-----------------------------------------
      Empty Step
    ----------------------------------------->
    <div :class="['_step', stepDetector('docs_uploaded', 'pending')]">
      <div class="_title">
        <h4>
          <span class="_normal-title">
            اعتبارسنجی مدارک ارسال شده
          </span>
          <span class="_pending-title">
            {{
              role === 'admin'
                ? 'مدارک مشتری در حال اعتبارسنجی توسط شما هستند'
                : 'مدارک شما در حال اعتبارسنجی هستند'
            }}
          </span>
        </h4>
      </div>
      <div
        v-if="orderData.status === 'docs_uploaded'"
        class="_content"
      >
        <small class="opa-5 d-block mt-3 mt-md-0">
          {{ orderData.what_to_do }}
        </small>

        <g-docs :type="type" />
      </div>
    </div>

    <!-----------------------------------------
      Components: [ChequesInfo, ContractDownload]
    ------------------------------------------>
    <div :class="['_step', stepDetector('upload_secondary')]">
      <div class="_title">
        <h4>
          اطلاعات بانکی و تضامین
        </h4>
      </div>
      <div
        v-if="orderData.status === 'upload_secondary'"
        class="_content"
      >
        <cheques-info
          :doc-type="type"
          :payback-type="orderData.payback_type"
          :order-data="orderData"
          class="mt-3"
        />
        <contract-download
          v-if="role === 'admin' || role === 'shop'"
          c-dl-only
          :data="orderData"
        />
      </div>
    </div>

    <!-----------------------------------------
      Components: [ChequesInfo, ContractDownload]
    ------------------------------------------>
    <div :class="['_step', stepDetector('check_secondary', 'pending')]">
      <div class="_title">
        <h4>
          <span class="_normal-title">
            بررسی اطلاعات بانکی و تضامین
          </span>
          <span class="_pending-title">
            {{
              role === 'admin'
                ? 'اطلاعات بانکی و تضامین مشتری در انتظار بررسی توسط شما هستند'
                : 'اطلاعات بانکی و تضامین شما در حال بررسی هستند'
            }}
          </span>
        </h4>
      </div>
      <div
        v-if="orderData.status === 'check_secondary'"
        class="_content"
      >
        <cheques-info
          :doc-type="type"
          :payback-type="orderData.payback_type"
          :order-data="orderData"
          class="mt-3"
        />
        <contract-download
          v-if="role === 'admin' || role === 'shop'"
          c-dl-only
          :data="orderData"
        />
      </div>
    </div>

    <!-----------------------------------------
      Components: [ContractDownload]
    ------------------------------------------>
    <div :class="['_step', stepDetector('wait_for_cheques')]">
      <div class="_title">
        <h4>
          ارسال قرارداد و چک‌ها
        </h4>
      </div>
      <div class="_content">
        <contract-download :data="orderData" />
      </div>
    </div>

    <!-----------------------------------------
      Components: [CardStatus]
    ------------------------------------------>
    <div
      v-if="orderData.ghestacard"
      :class="['_step', stepDetector('wait_for_card', 'pending')]"
    >
      <div class="_title">
        <h4>
          <span class="_normal-title">
            دریافت قسطا کارت
          </span>
          <span class="_pending-title">
            {{
              role === 'admin'
                ? `قسطا کارت مشتری ${orderData.ghestacard.status_farsi} است`
                : `قسطا کارت شما ${orderData.ghestacard.status_farsi} است`
            }}
          </span>
        </h4>
      </div>
      <small
        v-if="orderData.status === 'wait_for_card'"
        class="opa-5 d-block mt-3 mt-md-0"
      >
        {{ orderData.what_to_do }}
      </small>
      <div
        v-if="orderData.ghestacard.created_at"
        class="_content"
      >
        <card-status :data="orderData.ghestacard" />
      </div>
    </div>

    <!-----------------------------------------
      Components: [Invoice]
    ------------------------------------------>
    <div :class="['_step', stepDetector('prepayment')]">
      <div class="_title">
        <h4>
          فاکتور پیش‌پرداخت
        </h4>
      </div>
      <small
        v-if="orderData.status === 'prepayment'"
        class="opa-5 d-block mt-3 mt-md-0"
      >
        {{ orderData.what_to_do }}
      </small>
      <div
        v-if="orderData.status === 'prepayment'"
        class="_content"
      >
        <invoice />
      </div>
    </div>

    <!-----------------------------------------
      Empty Step
    ------------------------------------------>
    <div :class="['_step', stepDetector('prepaid', 'pending')]">
      <div class="_title">
        <h4>
          <span class="_normal-title">
            شارژ قسطا کارت
          </span>
          <span class="_pending-title">
            {{
              role === 'admin'
                ? 'قسطا کارت مشتری در صف شارژ قرار گرفته است'
                : 'قسطا کارت شما در صف شارژ قرار گرفته است'
            }}
          </span>
        </h4>
      </div>
    </div>

    <!-----------------------------------------
      Components: [TableList]
    ------------------------------------------>
    <div
      :class="[
        '_step',
        stepDetector(['cycle_cheque', 'cycle_epay', 'completed'])
      ]"
    >
      <div class="_title">
        <h4>
          سررسید اقساط
        </h4>
      </div>
      <div class="_content">
        <table-list
          :table-list="$parent.ghests"
          @buttonClick="actionsHandle"
        />
        <extra-charge
          v-if="role != 'admin' && orderData.status !== 'completed'"
          class="mt-4"
        />
      </div>
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

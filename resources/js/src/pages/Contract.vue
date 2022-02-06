<template>
  <div
    style="border: 2px solid #414141; min-height: 93vh"
    class="contract-main-pdf p-4"
  >
    <div
      v-if="cont"
      id="content"
    >
      <div class="mb-3">
        <table style="width: 100%">
          <tr>
            <td style="width: 25% !important; text-align: right">
              <img
                src="/images/logo-minimal.webp"
                height="30px"
              >
            </td>
            <td style="width: 50% !important; text-align: center">
              <h1>قرارداد خرید اقساطی کالا و خدمات</h1>
            </td>
            <td style="width: 25% !important; text-align: left">
              <!-- @if(!empty($data['organ_id'])) -->
              <div
                v-if="cont.organ"
                style="border: 1px dashed #cdcdcd; border-radius: 5px; text-align: center; display: inline-block"
              >
                <div style="padding: 3px;">
                  <strong><small>سازمانی</small></strong>
                </div>
                <div style="border-top: 1px dashed #cdcdcd; padding: 6px">
                  <small>
                    {{ cont.organ.name }}
                  </small>
                </div>
              </div>
              <!-- @elseif(!empty($data['shop_id'])) -->
              <div
                v-if="cont.shop"
                style="border: 1px dashed #cdcdcd; border-radius: 5px; text-align: center; display: inline-block"
              >
                <div style="padding: 3px;">
                  <strong><small>فروشگاهی</small></strong>
                </div>
                <div style="border-top: 1px dashed #cdcdcd; padding: 6px">
                  <small>
                    {{ cont.shop.name }}
                  </small>
                </div>
              </div>
              <!-- @endif -->
            </td>
          </tr>
        </table>
      </div>
      <!-- @if(!empty($data['shop_id'])) -->
      <!-- @if($data['shop']['type'] == 'offline') -->
      <p v-if="cont.shop_id && cont.shop.type === 'offline'">
        اینجانب <strong>{{ cont.user_name }}</strong> به شماره ملی
        <strong>{{ cont.user_nid }}</strong>
        کالا/خدمات زیر را به صورت اقساطی از قسطا خریداری می کنم:
        <br>
        طبق این قرارداد قسطا <strong>{{ cont.product }}</strong> را به صورت نقدی
        از فروشگاه
        <strong>{{ cont.shop.name }} ({{ cont.shop.owner_name }})</strong>
        خریداری کرده و فروشگاه آن را به وکالت از قسطا به مشتری تحویل می دهد.
        <br>
        همه مسئولیت کالا/خدمات خریداری شده اعم از سلامت کالا، ضمانت و خدمات پس
        از فروش برعهده فروشگاه می باشد.
      </p>
      <!-- @else -->

      <!-- @endif -->
      <!-- @else -->
      <p v-else>
        اینجانب <strong>{{ cont.user_name }}</strong> به شماره ملی
        <strong>{{ cont.user_nid }}</strong>
        کالا/خدمات مورد نظر خود را به وسیله قسطاکارت با شرایط زیر از قسطا خرید
        می نمایم:
        <br>
        قسطاکارت یک کارت بانکی است که تمام قابلیت های آن بجز خرید اینترنتی
        (درگاه پرداخت) و خرید فیزیکی (دستگاه های POS فروشگاهی) بسته شده و شارژ
        آن تنها در اختیار قسطا است. قسطاکارت به میزان موردنیاز مشتری شارژ می شود
        و مشتری به وکالت از قسطا از هر فروشگاه فیزیکی یا اینترنتی دلخواه، کالا و
        خدمات مورد نیاز را نقداً خریداری می کند؛ قسطا آن کالا/خدمات را به مشتری
        با شرایط اقساطی زیر به فروش می رساند:
      </p>
      <!-- @endif -->
      <div>
        <table style="width: 100%; margin-bottom: 0 !important;">
          <tr>
            <td
              class="pl-2"
              style="width: 50%; vertical-align: baseline;"
            >
              <table
                class="table table-striped"
                cellspacing="0"
                style="border: 1px solid #b0b0b0; margin-bottom: 18px !important;"
              >
                <thead>
                  <tr>
                    <th
                      colspan="2"
                      class="text-center"
                    >
                      اطلاعات سفارش {{ cont.oid }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="p-1">
                      قیمت نقدی(شارژ)
                    </td>
                    <td class="p-1">
                      {{ (cont.amount * 10) | moneySeperate
                      }}<span
                        class="mr-1"
                        style="font-size: small"
                      >ریال</span>
                    </td>
                  </tr>
                  <tr>
                    <td class="p-1">
                      قیمت اقساطی
                    </td>
                    <td class="p-1">
                      {{ (cont.total * 10) | moneySeperate }}
                      <span
                        class="mr-1"
                        style="font-size: small"
                      >ریال</span>
                    </td>
                  </tr>
                  <tr>
                    <td class="p-1">
                      پیش پرداخت
                    </td>
                    <td class="p-1">
                      {{ (cont.prepayment * 10) | moneySeperate
                      }}<span
                        class="mr-1"
                        style="font-size: small"
                      >ریال</span>
                    </td>
                  </tr>
                  <tr>
                    <td class="p-1">
                      بازپرداخت
                    </td>
                    <td class="p-1">
                      <strong>{{ cont.months }}</strong> ماهه،
                      <strong>{{ cont.cheques }}</strong>
                      {{ cont.payback_type === "epay" ? "قسط" : "چک" }}
                    </td>
                  </tr>
                  <tr v-if="cont.payback_type === 'cheque'">
                    <td>صادرکننده چک ها</td>
                    <td>{{ cont.bank_name }}</td>
                  </tr>
                  <tr v-if="cont.payback_type === 'cheque'">
                    <td>نام شعبه</td>
                    <td>{{ cont.bank_branch_name }}</td>
                  </tr>
                  <tr v-if="cont.payback_type === 'cheque'">
                    <td>کد شعبه</td>
                    <td>{{ cont.bank_branch_code }}</td>
                  </tr>
                  <tr v-if="cont.payback_type === 'cheque'">
                    <td>شماره شبا</td>
                    <td>{{ cont.bank_iban }}</td>
                  </tr>
                </tbody>
              </table>
              <table
                v-if="cont.payback_type === 'epay'"
                class="table table-striped"
                cellspacing="0"
                style="border: 1px solid #b0b0b0; margin-bottom: 0 !important;"
              >
                <thead>
                  <tr>
                    <th
                      class="text-center"
                      style="font-weight: normal"
                    >
                      همچنین __ برگ {{ cont.organ.payback_type_farsi }} بعنوان
                      ضمانت جمعاً به مبلغ
                      <strong>{{
                        (cont.cheques_info.guarantee * 10) | moneySeperate
                      }}</strong>
                      ریال دریافت می گردد. شماره
                      {{ cont.organ.payback_type_farsi }} (ها):
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="p-3" />
                  </tr>
                  <tr>
                    <td class="p-3" />
                  </tr>
                  <tr>
                    <td class="p-3" />
                  </tr>
                </tbody>
              </table>
              <!-- @endif -->
            </td>
            <td
              class="pr-2"
              style="vertical-align: baseline;"
            >
              <table
                class="table table-striped mb-0"
                cellspacing="0"
                style="border: 1px solid #b0b0b0;margin-bottom: 0 !important;"
              >
                <thead>
                  <tr>
                    <th class="">
                      تاریخ
                      {{ cont.payback_type === "epay" ? "قسط" : "چک" }}
                    </th>
                    <th
                      v-if="cont.payback_type === 'cheque'"
                      class=""
                    >
                      شماره چک
                    </th>
                    <th class="">
                      مبلغ
                      {{ cont.payback_type === "epay" ? "قسط" : "چک" }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(item, index) in cont.cheques_info.cheques"
                    :key="index"
                  >
                    <td style="padding: 0.5rem 0.6rem">
                      {{ item.date }}
                    </td>
                    <td
                      v-if="cont.payback_type === 'cheque'"
                      style="padding: 0.5rem 0.6rem"
                    >
                      {{ cont.cheque_numbers[index].series }}
                    </td>
                    <td style="padding: 0.5rem 0.6rem">
                      {{ (cont.cheques_info.amount * 10) | moneySeperate
                      }}<span
                        class="mr-1"
                        style="font-size: small"
                      >ریال</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
        </table>
      </div>
      <div
        v-if="cont.payback_type === 'cheque'"
        class="text-center"
        style="margin-top: 1rem"
      >
        چک ها در وجه
        <strong style="margin: 0 2px">{{ cont.cheques_info.name }}</strong> صادر
        شده است.
      </div>
      <div
        v-if="!cont.shop_id"
        style="margin-top: 1rem"
      >
        <div
          class="p-2"
          style="border: 2px dashed gray; border-radius: 4px; height: 70px; line-height: 1.4rem"
        >
          <strong>سلب مسئولیت:</strong> مسئولیت هرگونه خرید غیرقانونی کالا و
          خدمات (اعم از خرید از مراجع غیرقانونی یا خرید کالا و خدماتی که با
          قوانین جمهوری اسلامی ایران مغایرت دارد) به عهده خریدار است.
        </div>
      </div>
      <div style="margin-top: 25px;">
        <div class="col-md-12">
          <p v-if="cont.shop_id">
            فروشگاه:
            <span class="mr-1">{{
              cont.shop.state +
                " - " +
                cont.shop.city +
                " - " +
                cont.shop.address +
                " - " +
                cont.shop.owner_mobile +
                " - " +
                cont.shop.phone
            }}</span>
          </p>
          <p>
            محل سکونت مشتری: <span class="mr-1">{{ cont.home_address }}</span>
          </p>
          <p>
            محل کار مشتری: <span class="mr-1">{{ cont.work_address }}</span>
          </p>
          <div>
            <table style="width: 100%">
              <tr>
                <td>
                  شماره موبایل: <span class="mr-1">{{ cont.user_mobile }}</span>
                </td>
                <td>
                  تلفن محل سکونت:
                  <span class="mr-1">{{ cont.home_phone }}</span>
                </td>
                <td>
                  تلفن محل کار: <span class="mr-1">{{ cont.work_phone }}</span>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
      <div
        class="row mb-1"
        style="margin-top: 2rem"
      >
        <table style="width: 100%">
          <tr>
            <td class="text-center">
              تاریخ و امضای مشتری
            </td>
            <td
              v-if="cont.shop_id"
              class="text-center"
            >
              امضای فروشگاه
            </td>
            <td class="text-center">
              امضای مدیر فروش قسطا
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import Orders from '@/api/orders';

export default {
  name: 'Contract',

  metaInfo: {
    title: 'قرارداد قسطا'
  },

  data() {
    return {
      cont: {
        cheques_info: {
          cheques: []
        }
      }
    };
  },

  created() {
    this.loadData();
  },

  methods: {
    loadData() {
      Orders.get.contract(this.$route.params.id).then(r => {
        this.cont = r.data.result;

        setTimeout(() => {
          window.print();
        }, 1000);
      });
    }
  }
};
</script>

<style scoped>
@page {
  size: "a4"; /* auto is the initial value */
  margin: 0mm; /* this affects the margin in the printer settings */
}
@media print {
  * {
    -webkit-print-color-adjust: exact;
  }
}

.contract-main-pdf {
  direction: rtl;
  width: 720px;
  margin: 32px auto;
  font-size: 12px;
  text-align: justify;
}

.col-md-6 {
  width: 50%;
}

.p-1 {
  padding: 0.5rem !important;
}

.p-2 {
  padding: 0.8rem !important;
}

.p-3 {
  padding: 1.2rem !important;
}

.p-4 {
  padding: 1.6rem !important;
}

.p-5 {
  padding: 2rem !important;
}

.pr-2 {
  padding-right: 0.8rem;
}

.pl-2 {
  padding-left: 0.8rem;
}

.mb-1 {
  margin-bottom: 1rem;
}

.mb-2 {
  margin-bottom: 2rem;
}

h1 {
  font-size: 18px;
}

h2 {
  font-size: 16px;
}

h3 {
  font-size: 14px;
}

h4 {
  font-size: 13px;
}

p {
  font-size: 12px;
  line-height: 1.2rem;
}

.table {
  width: 100%;
  margin-bottom: 1rem;
  color: #212529;
}

.table th,
.table td {
  padding: 0.75rem;
  vertical-align: top;
  border-top: 1px solid #dee2e6 !important;
}

.table-striped tbody tr:nth-of-type(odd) {
  background-color: rgba(0, 0, 0, 0.05) !important;
}

.text-center {
  text-align: center;
}

.text-left {
  text-align: left;
}

.mr-1 {
  margin-right: 0.3rem;
}
</style>

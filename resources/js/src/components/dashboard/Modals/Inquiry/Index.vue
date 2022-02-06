<template>
  <modal
    v-show="modal"
    title="استعلام"
    size="lg"
    @close="modal = false"
  >
    <template #body>
      <!-- 
        Navigation
      -->
      <nav>
        <div
          id="nav-tab"
          class="nav nav-tabs tab-custom bg-gray-light"
          role="tablist"
        >
          <!-- 
            Back Chques
          -->
          <a
            id="nav-cheque-tab"
            class="nav-link"
            data-bs-toggle="tab"
            href="#nav-cheque"
            role="tab"
            aria-controls="nav-cheque"
            aria-selected="false"
            @click="getBackChques()"
          >
            چک برگشتی
          </a>

          <!-- 
            Facilities
          -->
          <a
            id="nav-tashilat-tab"
            class="nav-link"
            data-bs-toggle="tab"
            href="#nav-tashilat"
            role="tab"
            aria-controls="nav-tashilat"
            aria-selected="false"
            @click="getFacilities()"
          >
            تسهیلات جاری
          </a>

          <!-- 
            Bank
          -->
          <a
            id="nav-tashilat-tab"
            class="nav-link"
            data-bs-toggle="tab"
            href="#nav-tashilat"
            role="tab"
            aria-controls="nav-tashilat"
            aria-selected="false"
            @click="getBank()"
          >
            بانکی
          </a>

          <!-- 
            Bank Integrated
          -->
          <a
            id="nav-tashilat-tab"
            class="nav-link"
            data-bs-toggle="tab"
            href="#nav-tashilat"
            role="tab"
            aria-controls="nav-tashilat"
            aria-selected="false"
            @click="getBankIntegrated()"
          >
            بانکی یکپارچه
          </a>

          <!-- 
            N ID
          -->
          <a
            id="nav-tashilat-tab"
            class="nav-link"
            data-bs-toggle="tab"
            href="#nav-tashilat"
            role="tab"
            aria-controls="nav-tashilat"
            aria-selected="false"
            @click="getNID()"
          >
            کارت ملی
          </a>
        </div>
      </nav>

      <div
        id="nav-tabContent"
        class="tab-content"
      >
        <!-- 
            Back Cheques
          -->
        <div
          id="nav-cheque"
          class="tab-pane fade"
          role="tabpanel"
          aria-labelledby="nav-cheque-tab"
        >
          <!-- Content -->
          <div
            id="accordionExample"
            class="accordion mt-4"
          >
            <div class="d-flex flex-column justify-content-center align-items-center my-3">
              <g-button
                ref="bc"
                text="دریافت اطلاعات جدید‌تر"
                @click.native="getBackChques(true)"
              />
              <small class="d-block text-center mt-3 opa-7">
                مشمول هزینه
              </small>
            </div>

            <div
              v-for="(item, index) in dataBC"
              :key="index"
              class="card"
            >
              <div
                :id="'heading-' + index"
                class="card-header p-0"
              >
                <h2 class="mb-0">
                  <button
                    class="btn btn-light btn-block d-flex justify-content-between p-3 rounded-0"
                    type="button"
                    data-bs-toggle="collapse"
                    :data-bs-target="'#collapse' + index"
                    aria-expanded="true"
                    :aria-controls="'collapse' + index"
                  >
                    <span> {{ item.date }} </span>
                  </button>
                </h2>
              </div>

              <div
                v-if="item.sum"
                :id="'collapse' + index"
                class="collapse"
                :aria-labelledby="'heading-' + index"
                data-parent="#accordionExample"
              >
                <div class="card-body p-0">
                  <div class="alert alert-danger text-center rounded-0">
                    <span class="mb-3 d-block font-weight-light">
                      مجموعا
                      <strong>{{ item.sum.count }}</strong>
                      چک برگشتی به ارزش:
                    </span>
                    <h3 class="price-style d-flex justify-content-center">
                      {{ item.sum.amount | moneySeperate }}
                      <small>ریال</small>
                    </h3>
                  </div>

                  <div class="p-3">
                    <div
                      v-for="(ch, i) in item.cheques"
                      :key="i"
                      class="border rounded border-2 mb-3"
                    >
                      <div
                        class="px-3 py-2 border-bottom border-2 text-center"
                      >
                        <strong>{{ ch.bank_name }}</strong>
                      </div>

                      <div
                        class="px-3 py-2 border-bottom border-2 d-flex justify-content-between"
                      >
                        <span class="font-weight-light">مبلغ</span>
                        <strong class="price-style">
                          {{ ch.amount | moneySeperate }}
                          <small>ریال</small>
                        </strong>
                      </div>

                      <div
                        class="px-3 py-2 border-bottom border-2 d-flex justify-content-between"
                      >
                        <span class="font-weight-light">سررسید</span>
                        <strong>
                          {{ ch.back_date }}
                        </strong>
                      </div>

                      <div class="px-3 py-2 d-flex justify-content-between">
                        <span class="font-weight-light">شماره چک</span>
                        <strong>
                          {{ ch.series }}
                        </strong>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div
                v-else
                :id="'collapse' + index"
                class="collapse"
                :aria-labelledby="'heading-' + index"
                data-parent="#accordionExample"
              >
                <div class="card-body p-0">
                  <div class="alert alert-success text-center rounded-0">
                    <span class="mb-3 d-block font-weight-light">
                      هیچ چک برگشتی یافت نشد.
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 
            Facilities
          -->
        <div
          id="nav-tashilat"
          class="tab-pane fade"
          role="tabpanel"
          aria-labelledby="nav-tashilat-tab"
        >
          <div
            id="accordionExample"
            class="accordion mt-4"
          >
            <div class="d-flex flex-column justify-content-center align-items-center my-3">
              <g-button
                ref="faci"
                text="دریافت اطلاعات جدید‌تر"
                @click.native="getFacilities(true)"
              />
              <small class="d-block text-center mt-3 opa-7">
                مشمول هزینه
              </small>
            </div>

            <div
              v-for="(item, index) in dataFaci"
              :key="index"
              class="card"
            >
              <div
                :id="'heading-' + index"
                class="card-header p-0"
              >
                <h2 class="mb-0">
                  <button
                    class="btn btn-light btn-block d-flex justify-content-between p-3 rounded-0"
                    type="button"
                    data-bs-toggle="collapse"
                    :data-bs-target="'#collapse' + index"
                    aria-expanded="true"
                    :aria-controls="'collapse' + index"
                  >
                    <span> {{ item.date }} </span>
                  </button>
                </h2>
              </div>

              <div
                v-if="item.total_amount"
                :id="'collapse' + index"
                class="collapse"
                :aria-labelledby="'heading-' + index"
                data-parent="#accordionExample"
              >
                <div class="card-body p-0">
                  <div class="alert alert-danger text-center rounded-0 px-0">
                    <span
                      class="p-3 d-block font-weight-light border-bottom border-danger d-flex justify-content-between"
                    >
                      مجموع
                      <strong>{{ item.total_amount | moneySeperate }} ریال</strong>
                    </span>
                    <span
                      class="p-3 d-block font-weight-light border-bottom border-danger d-flex justify-content-between"
                    >
                      مجموع بدهی
                      <strong>{{ item.total_debt | moneySeperate }} ریال</strong>
                    </span>
                    <span
                      class="p-3 d-block font-weight-light border-bottom border-danger d-flex justify-content-between"
                    >
                      مجموع مهلت دار
                      <strong>{{
                        item.total_deferred | moneySeperate
                      }}</strong>
                    </span>
                    <span
                      class="p-3 d-block font-weight-light border-bottom border-danger d-flex justify-content-between"
                    >
                      مجموع معوق
                      <strong>{{ item.total_expired | moneySeperate }} ریال</strong>
                    </span>
                    <span
                      class="p-3 d-block font-weight-light d-flex justify-content-between"
                    >
                      مجموع مشکوک الوصول
                      <strong>{{ item.total_suspicious | moneySeperate }}
                      </strong>
                    </span>
                  </div>

                  <div class="p-3">
                    <div
                      v-for="(ch, i) in item.facilities"
                      :key="i"
                      class="border rounded border-2 mb-3"
                    >
                      <div
                        class="px-3 py-2 border-bottom border-2 text-center"
                      >
                        <strong>{{ ch.bank_name }}</strong>
                      </div>

                      <div
                        class="px-3 py-2 border-bottom border-2 d-flex justify-content-between"
                      >
                        <span class="font-weight-light">مبلغ</span>
                        <strong class="price-style">
                          {{ ch.amount | moneySeperate }}
                          <small>ریال</small>
                        </strong>
                      </div>

                      <div
                        class="px-3 py-2 border-bottom border-2 d-flex justify-content-between"
                      >
                        <span class="font-weight-light">سود تسهیلات</span>
                        <strong class="price-style">
                          {{ ch.benefit | moneySeperate }}
                          <small>ریال</small>
                        </strong>
                      </div>

                      <div
                        class="px-3 py-2 border-bottom border-2 d-flex justify-content-between"
                      >
                        <span class="font-weight-light">تاریخ شروع</span>
                        <strong>
                          {{ ch.FacilitySetDate || '' }}
                        </strong>
                      </div>

                      <div
                        class="px-3 py-2 border-bottom border-2 d-flex justify-content-between"
                      >
                        <span class="font-weight-light">تاریخ پایان</span>
                        <strong>
                          {{ ch.FacilityEndDate || '' }}
                        </strong>
                      </div>

                      <div
                        class="px-3 py-2 border-bottom border-2 d-flex justify-content-between"
                      >
                        <span class="font-weight-light">بدهی</span>
                        <strong class="price-style">
                          {{ ch.debt | moneySeperate }}
                          <small>ریال</small>
                        </strong>
                      </div>

                      <div
                        class="px-3 py-2 border-bottom border-2 d-flex justify-content-between"
                      >
                        <span class="font-weight-light">معوق</span>
                        <strong>
                          {{ ch.deferred | moneySeperate }}
                        </strong>
                      </div>

                      <div
                        class="px-3 py-2 border-bottom border-2 d-flex justify-content-between"
                      >
                        <span class="font-weight-light"> مشکوک الوصول</span>
                        <strong>
                          {{ ch.suspicious | moneySeperate }}
                        </strong>
                      </div>

                      <div
                        class="px-3 py-2 border-bottom border-2 d-flex justify-content-between"
                      >
                        <span class="font-weight-light">گذشته</span>
                        <strong class="price-style">
                          {{ ch.expired | moneySeperate }}
                          <small>ریال</small>
                        </strong>
                      </div>

                      <div class="px-3 py-2 d-flex justify-content-between">
                        <span class="font-weight-light">مجموع کل</span>
                        <strong class="price-style">
                          {{ ch.sum | moneySeperate }}
                          <small>ریال</small>
                        </strong>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div
                v-else
                :id="'collapse' + index"
                class="collapse"
                :aria-labelledby="'heading' + index"
                data-parent="#accordionExample"
              >
                <div class="card-body p-0">
                  <div class="alert alert-success text-center rounded-0 px-0">
                    <span
                      class="p-3 d-block font-weight-light border-bottom border-danger d-flex justify-content-between"
                    >
                      هیچ تسهیلاتی یافت نشد
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>

    <template #footer>
      <div class="d-flex w-100 justify-content-center">
        <button
          class="btn btn-light rounded-pill"
          @click="modal = false"
        >
          انصراف
        </button>
      </div>
    </template>
  </modal>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>
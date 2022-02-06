<template>
  <div>
    <g-loading
      v-if="loading"
      type="orderCard"
      :iterate-count="role === 'user' ? 3 : 9"
    />

    <template v-else>
      <!-- Filters -->
      <div
        v-if="role !== 'user'"
        class="w-100 mb-4"
      >
        <button
          class="btn btn-light rounded-pill btn-sm mb-2"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#collapseExample"
          aria-expanded="false"
          aria-controls="collapseExample"
        >
          <i class="far fa-filter ml-2" />
          فیلتر
          <i class="fal fa-caret-down mr-3" />
        </button>
        <div
          id="collapseExample"
          :class="['collapse', role === 'admin' ? 'show' : null]"
        >
          <form
            class="bg-gray-light rounded p-4"
            @submit.prevent="addFiltersToRoute"
          >
            <div class="row px-1">
              <div
                v-if="role === 'admin'"
                class="col-12 mb-4"
              >
                <div class="bg-white rounded p-4 shadow-sm">
                  <div class="btn-group btn-group-sm seperate">
                    <!----------------
                     Start - Check
                     ----------------->
                    <input
                      id="all"
                      v-model="fastFilter"
                      value="all"
                      type="radio"
                      class="btn-check"
                      name="fastFilter"
                      autocomplete="off"
                    >
                    <label
                      class="btn"
                      for="all"
                    >
                      همه
                      <span
                        v-if="filterCount.all"
                        class="badge rounded-pill mr-2"
                      >
                        {{ filterCount.all }}
                      </span>
                    </label>
                    <!----------------
                     End - Check
                     ----------------->

                    <!----------------
                     Start - Check
                     ----------------->
                    <input
                      id="draft"
                      v-model="fastFilter"
                      value="draft"
                      type="radio"
                      class="btn-check"
                      name="fastFilter"
                      autocomplete="off"
                    >
                    <label
                      class="btn"
                      for="draft"
                    >
                      استعلام
                      <span
                        v-if="filterCount.draft"
                        class="badge rounded-pill mr-2"
                      >
                        {{ filterCount.draft }}
                      </span>
                    </label>
                    <!----------------
                     End - Check
                     ----------------->

                    <!----------------
                     Start - Check
                     ----------------->
                    <input
                      id="submitted"
                      v-model="fastFilter"
                      value="submitted"
                      type="radio"
                      class="btn-check"
                      name="fastFilter"
                      autocomplete="off"
                    >
                    <label
                      class="btn"
                      for="submitted"
                    >
                      نیاز به مدارک
                      <span
                        v-if="filterCount.submitted"
                        class="badge rounded-pill mr-2"
                      >
                        {{ filterCount.submitted }}
                      </span>
                    </label>
                    <!----------------
                     End - Check
                     ----------------->

                    <!----------------
                     Start - Check
                     ----------------->
                    <input
                      id="pended_by_organ"
                      v-model="fastFilter"
                      value="pended_by_organ"
                      type="radio"
                      class="btn-check"
                      name="fastFilter"
                      autocomplete="off"
                    >
                    <label
                      class="btn"
                      for="pended_by_organ"
                    >
                      در انتظار تأیید سازمان
                      <span
                        v-if="filterCount.pended_by_organ"
                        class="badge rounded-pill mr-2"
                      >
                        {{ filterCount.pended_by_organ }}
                      </span>
                    </label>
                    <!----------------
                     End - Check
                     ----------------->

                    <!----------------
                     Start - Check
                     ----------------->
                    <input
                      id="docs_uploaded"
                      v-model="fastFilter"
                      value="docs_uploaded"
                      type="radio"
                      class="btn-check"
                      name="fastFilter"
                      autocomplete="off"
                    >
                    <label
                      class="btn"
                      for="docs_uploaded"
                    >
                      در صف اعتبارسنجی
                      <span
                        v-if="filterCount.docs_uploaded"
                        class="badge rounded-pill mr-2"
                      >
                        {{ filterCount.docs_uploaded }}
                      </span>
                    </label>
                    <!----------------
                     End - Check
                     ----------------->

                    <!----------------
                     Start - Check
                     ----------------->
                    <input
                      id="upload_secondary"
                      v-model="fastFilter"
                      value="upload_secondary"
                      type="radio"
                      class="btn-check"
                      name="fastFilter"
                      autocomplete="off"
                    >
                    <label
                      class="btn"
                      for="upload_secondary"
                    >
                      بارگذاری مدارک تکمیلی
                      <span
                        v-if="filterCount.upload_secondary"
                        class="badge rounded-pill mr-2"
                      >
                        {{ filterCount.upload_secondary }}
                      </span>
                    </label>
                    <!----------------
                     End - Check
                     ----------------->

                    <!----------------
                     Start - Check
                     ----------------->
                    <input
                      id="check_secondary"
                      v-model="fastFilter"
                      value="check_secondary"
                      type="radio"
                      class="btn-check"
                      name="fastFilter"
                      autocomplete="off"
                    >
                    <label
                      class="btn"
                      for="check_secondary"
                    >
                      بررسی مدارک تکمیلی
                      <span
                        v-if="filterCount.check_secondary"
                        class="badge rounded-pill mr-2"
                      >
                        {{ filterCount.check_secondary }}
                      </span>
                    </label>
                    <!----------------
                     End - Check
                     ----------------->

                    <!----------------
                     Start - Check
                     ----------------->
                    <input
                      id="wait_for_cheques"
                      v-model="fastFilter"
                      value="wait_for_cheques"
                      type="radio"
                      class="btn-check"
                      name="fastFilter"
                      autocomplete="off"
                    >
                    <label
                      class="btn"
                      for="wait_for_cheques"
                    >
                      در انتظار چک
                      <span
                        v-if="filterCount.wait_for_cheques"
                        class="badge rounded-pill mr-2"
                      >
                        {{ filterCount.wait_for_cheques }}
                      </span>
                    </label>
                    <!----------------
                     End - Check
                     ----------------->

                    <!----------------
                     Start - Check
                     ----------------->
                    <input
                      id="prepayment"
                      v-model="fastFilter"
                      value="prepayment"
                      type="radio"
                      class="btn-check"
                      name="fastFilter"
                      autocomplete="off"
                    >
                    <label
                      class="btn"
                      for="prepayment"
                    >
                      در انتظار پیش پرداخت
                      <span
                        v-if="filterCount.prepayment"
                        class="badge rounded-pill mr-2"
                      >
                        {{ filterCount.prepayment }}
                      </span>
                    </label>
                    <!----------------
                     End - Check
                     ----------------->

                    <!----------------
                     Start - Check
                     ----------------->
                    <input
                      id="prepaid"
                      v-model="fastFilter"
                      value="prepaid"
                      type="radio"
                      class="btn-check"
                      name="fastFilter"
                      autocomplete="off"
                    >
                    <label
                      class="btn"
                      for="prepaid"
                    >
                      در صف شارژ
                      <span
                        v-if="filterCount.prepaid"
                        class="badge rounded-pill mr-2"
                      >
                        {{ filterCount.prepaid }}
                      </span>
                    </label>
                    <!----------------
                     End - Check
                     ----------------->

                    <!----------------
                     Start - Check
                     ----------------->
                    <input
                      id="cycle_epay"
                      v-model="fastFilter"
                      value="cycle_epay"
                      type="radio"
                      class="btn-check"
                      name="fastFilter"
                      autocomplete="off"
                    >
                    <label
                      class="btn"
                      for="cycle_epay"
                    >
                      دوره اقساط
                      <span
                        v-if="filterCount.cycle_epay"
                        class="badge rounded-pill mr-2"
                      >
                        {{ filterCount.cycle_epay }}
                      </span>
                    </label>
                    <!----------------
                     End - Check
                     ----------------->

                    <!----------------
                     Start - Check
                     ----------------->
                    <input
                      id="cycle_cheque"
                      v-model="fastFilter"
                      value="cycle_cheque"
                      type="radio"
                      class="btn-check"
                      name="fastFilter"
                      autocomplete="off"
                    >
                    <label
                      class="btn"
                      for="cycle_cheque"
                    >
                      دوره چک
                      <span
                        v-if="filterCount.cycle_cheque"
                        class="badge rounded-pill mr-2"
                      >
                        {{ filterCount.cycle_cheque }}
                      </span>
                    </label>
                    <!----------------
                     End - Check
                     ----------------->

                    <!----------------
                     Start - Check
                     ----------------->
                    <input
                      id="completed"
                      v-model="fastFilter"
                      value="completed"
                      type="radio"
                      class="btn-check"
                      name="fastFilter"
                      autocomplete="off"
                    >
                    <label
                      class="btn"
                      for="completed"
                    >
                      پایان یافته
                      <span
                        v-if="filterCount.completed"
                        class="badge rounded-pill mr-2"
                      >
                        {{ filterCount.completed }}
                      </span>
                    </label>
                    <!----------------
                     End - Check
                     ----------------->

                    <!----------------
                     Start - Check
                     ----------------->
                    <input
                      id="extra_charge"
                      v-model="fastFilter"
                      value="extra_charge"
                      type="radio"
                      class="btn-check"
                      name="fastFilter"
                      autocomplete="off"
                    >
                    <label
                      class="btn"
                      for="extra_charge"
                    >
                      شارژ اضافی
                      <span
                        v-if="filterCount.extra_charge"
                        class="badge rounded-pill mr-2"
                      >
                        {{ filterCount.extra_charge }}
                      </span>
                    </label>
                    <!----------------
                     End - Check
                     ----------------->

                    <!----------------
                     Start - Check
                     ----------------->
                    <input
                      id="rejected"
                      v-model="fastFilter"
                      value="rejected"
                      type="radio"
                      class="btn-check"
                      name="fastFilter"
                      autocomplete="off"
                    >
                    <label
                      class="btn"
                      for="rejected"
                    >
                      رد شده
                      <span
                        v-if="filterCount.rejected"
                        class="badge rounded-pill mr-2"
                      >
                        {{ filterCount.rejected }}
                      </span>
                    </label>
                    <!----------------
                     End - Check
                     ----------------->

                    <!----------------
                     Start - Check
                     ----------------->
                    <input
                      id="cancelled"
                      v-model="fastFilter"
                      value="cancelled"
                      type="radio"
                      class="btn-check"
                      name="fastFilter"
                      autocomplete="off"
                    >
                    <label
                      class="btn"
                      for="cancelled"
                    >
                      لغو شده
                      <span
                        v-if="filterCount.cancelled"
                        class="badge rounded-pill mr-2"
                      >
                        {{ filterCount.cancelled }}
                      </span>
                    </label>
                    <!----------------
                     End - Check
                     ----------------->
                  </div>
                </div>
              </div>

              <div class="col-12 col-xl-4 mb-4 mb-xl-0">
                <div class="bg-white rounded p-4 shadow-sm">
                  <div class="row">
                    <div class="col-12 col-md-6 mb-3">
                      <label>
                        <small>موبایل</small>
                      </label>
                      <input
                        v-model="filter.mobile"
                        type="text"
                        class="form-control ltr estedad-font"
                      >
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                      <label>
                        <small>کد ملی</small>
                      </label>
                      <input
                        v-model="filter.nid"
                        type="text"
                        class="form-control"
                      >
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                      <label>
                        <small>نام</small>
                      </label>
                      <input
                        v-model="filter.fname"
                        type="text"
                        class="form-control"
                      >
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                      <label>
                        <small>نام خانوادگی</small>
                      </label>
                      <input
                        v-model="filter.lname"
                        type="text"
                        class="form-control"
                      >
                    </div>
                    <div
                      v-if="role === 'admin'"
                      class="col-12 col-md-6 mb-3"
                    >
                      <label>
                        <small>سری کارت</small>
                      </label>
                      <input
                        v-model="filter.series_card"
                        type="text"
                        class="form-control"
                      >
                    </div>
                    <div
                      v-if="role === 'admin'"
                      class="col-12 col-md-6 mb-3"
                    >
                      <label>
                        <small>سری شارژ</small>
                      </label>
                      <input
                        v-model="filter.series"
                        type="text"
                        class="form-control"
                      >
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12 col-xl-4 mb-4 mb-xl-0">
                <div class="bg-white rounded p-4 shadow-sm">
                  <div class="row">
                    <div
                      class="col-12 mb-3"
                      :class="{ 'col-md-6': role === 'admin' }"
                    >
                      <label>
                        <small>شماره سفارش</small>
                      </label>
                      <div class="input-group normal-rounded flex-row-reverse">
                        <span
                          id="orderNumber"
                          class="input-group-text ltr nova-font px-1"
                        >GHC-</span>
                        <input
                          v-model="filter.oid"
                          type="text"
                          class="form-control ltr"
                          aria-label="orderNumber"
                          aria-describedby="orderNumber"
                        >
                      </div>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                      <label>
                        <small>وضعیت</small>
                      </label>
                      <g-multi-select
                        v-model="filter.status"
                        :options="statusOptions"
                        :name="'status'"
                        string-return
                        :max="1"
                      />
                    </div>
                    <div
                      v-if="role === 'admin'"
                      class="col-12 col-md-6 mb-3"
                    >
                      <label>
                        <small>نام یا کد سازمان</small>
                      </label>
                      <input
                        v-model="filter.organ"
                        type="text"
                        class="form-control"
                      >
                    </div>
                    <div
                      v-if="role === 'admin'"
                      class="col-12 col-md-6 mb-3"
                    >
                      <label>
                        <small>نام فروشگاه</small>
                      </label>
                      <input
                        v-model="filter.shop"
                        type="text"
                        class="form-control"
                      >
                    </div>
                    <div
                      v-if="role === 'admin'"
                      class="col-12 col-md-6 mb-3"
                    >
                      <label>
                        <small>ویژگی</small>
                      </label>
                      <g-multi-select
                        v-model="filter.trait"
                        :options="traitOptions"
                        name="trait"
                        string-return
                        :max="1"
                      />
                    </div>
                    <div
                      v-if="role === 'admin'"
                      class="col-12 col-md-6 mb-3"
                    >
                      <label>
                        <small>مبلغ</small>
                      </label>
                      <input
                        v-model="filter.amount"
                        type="text"
                        class="form-control"
                      >
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12 col-xl-4 mb-4 mb-xl-0">
                <div class="bg-white rounded p-4 shadow-sm">
                  <div class="row">
                    <div
                      v-if="role === 'admin'"
                      class="col-12 col-lg-8 pl-lg-1 mb-3"
                    >
                      <label>
                        <small>نوع تاریخ</small>
                      </label>
                      <select
                        v-model="filter.date_type"
                        class="form-select"
                      >
                        <option value="">
                          پیشفرض
                        </option>
                        <option value="created_at">
                          تاریخ ثبت سفارش
                        </option>
                        <option value="charged_at">
                          تاریخ شارژ
                        </option>
                        <option value="first_ghest_at">
                          تاریخ اولین قسط
                        </option>
                        <option value="docs_uploaded_at">
                          تاریخ بارگذاری مدارک
                        </option>
                        <option value="docs_warning_at">
                          مهلت بررسی مدارک
                        </option>
                        <option value="docs_accepted_at">
                          تاریخ تأیید مدارک
                        </option>
                        <option value="secondary_uploaded_at">
                          تاریخ بارگذاری تضامین
                        </option>
                        <option value="docs_received_at">
                          تاریخ تحویل قرارداد و تضامین
                        </option>
                        <option value="prepaid_at">
                          تاریخ پیش پرداخت
                        </option>
                        <option value="charged_at">
                          تاریخ شارژ
                        </option>
                        <option value="closed_at">
                          تاریخ بسته شدن پرونده
                        </option>
                      </select>
                    </div>

                    <div
                      v-if="role === 'admin'"
                      class="col-12 col-lg-4 pr-lg-1 mb-3"
                    >
                      <label class="opa-3">
                        <small>.</small>
                      </label>
                      <select
                        v-model="filter.date_sort"
                        class="form-select"
                      >
                        <option value="">
                          پیشفرض
                        </option>
                        <option value="asc">
                          صعودی
                        </option>
                        <option value="desc">
                          نزولی
                        </option>
                      </select>
                    </div>

                    <div class="col-12 mb-3">
                      <g-date-picker
                        v-model="filter.from_date"
                        :years-from-now="5"
                        label="از تاریخ"
                        empty
                      />
                    </div>

                    <div class="col-12 mb-3">
                      <g-date-picker
                        v-model="filter.to_date"
                        :years-from-now="5"
                        label="تا تاریخ"
                        empty
                      />
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12 d-flex justify-content-between mt-4 aic">
                <button
                  type="button"
                  class="btn btn-light btn-sm rounded-pill"
                  @click="removeFiltersFromRoute"
                >
                  حذف فیلتر
                </button>
                <div
                  v-if="role === 'admin'"
                  class="btn btn-primary btn-sm rounded-pill"
                  @click="loadData(1)"
                >
                  <i class="fas fa-file-excel ml-2" />
                  دریافت اکسل
                </div>
                <button
                  type="submit"
                  class="btn btn-success btn-sm rounded-pill"
                >
                  اعمال فیلتر
                </button>
              </div>
            </div>
          </form>
        </div>

        <div class="d-flex flex-wrap mt-4 ltr">
          <span
            v-for="(item, index) in Object.entries(filter)"
            :key="index"
          >
            <span
              v-if="item[1] && item[0] !== 'page'"
              class="d-flex align-items-center border rounded-pill px-2 py-1 ltr estedad-font mr-2 mb-2"
            >
              <span class="opa-5"> {{ item[0] }}: </span>
              {{ item[1] }}

              <i
                class="far fa-times ml-3 mr-1 cursor-pointer"
                @click="removeSpanFilter(item[0])"
              />
            </span>
          </span>
        </div>
      </div>

      <div
        v-if="td.length"
        class="row"
      >
        <div
          v-for="i in td"
          :key="i.id"
          :is-admin="role === 'admin'"
          class="col-12 col-md-6 col-xl-4 mb-4"
        >
          <order-card
            :data="i"
            :role="role"
          />
        </div>
      </div>

      <pagination
        v-if="role !== 'user' && pagination"
        :data="pagination"
        class="mx-0"
        @onPageChange="changePage"
      />
    </template>

    <empty
      v-if="!loading && !td.length"
      class="mt-5"
      message="هیچ سفارشی وجود ندارد"
    />
  </div>
</template>

<script>
import { mapState } from 'vuex';
import FilteringList from '@/global/FilteringList';
import { traitOptions, statusOptions } from '@/data/FiltersMultiSelects';

// Api
import Orders from '@/api/orders';
import Organ from '@/api/organ';
import Seller from '@/api/seller';
import Admin from '@/api/admin';

// Components
import Pagination from '@/components/overall/pagination';

export default {
  name: 'OrdersPage',

  metaInfo: {
    title: 'سفارش‌ها'
  },

  components: {
    Pagination,
    OrderCard: () => import('@/components/dashboard/Order/Card/Index')
  },

  mixins: [FilteringList],

  data() {
    return {
      loading: true,
      filterCount: {},

      interval: null,
      traitOptions,
      statusOptions
    };
  },

  computed: {
    ...mapState('dashboard', ['role'])
  },

  watch: {
    role() {
      this.loadData();
    }
  },

  created() {
    this.loadData();
    this.getCounts();
    this.dataUpdater();
  },

  beforeDestroy() {
    clearInterval(this.interval);
  },

  methods: {
    loadData(excel = 0) {
      let queries = '';

      Object.keys(this.$route.query).map(i => {
        queries = queries + `${i}=${this.$route.query[i]}&`;
      });

      queries = queries.substring(0, queries.lastIndexOf('&'));

      if (this.role === 'user') {
        Orders.get
          .all(queries)
          .then(r => {
            const { result } = r.data;
            this.td = result;
            this.loading = false;
          })
          .catch(e => {
            this.$alerts.errHandle(e, 'static');
            this.loading = false;
          });
      }

      if (this.role === 'shop') {
        Seller.get
          .orders(queries)
          .then(r => {
            this.td = r.data.data;
            this.pagination = {
              current_page: r.data.current_page,
              last_page: r.data.last_page,
              next_page_url: r.data.next_page_url,
              path: r.data.path,
              per_page: r.data.per_page,
              prev_page_url: r.data.prev_page_url,
              total: r.data.total
            };
            this.loading = false;
          })
          .catch(e => {
            this.$alerts.errHandle(e, 'static');
            this.loading = false;
          });
      }

      if (this.role === 'organ') {
        Organ.get
          .orders(queries)
          .then(r => {
            this.td = r.data.data;
            this.pagination = {
              current_page: r.data.current_page,
              last_page: r.data.last_page,
              next_page_url: r.data.next_page_url,
              path: r.data.path,
              per_page: r.data.per_page,
              prev_page_url: r.data.prev_page_url,
              total: r.data.total
            };
            this.loading = false;
          })
          .catch(e => {
            this.$alerts.errHandle(e, 'static');
            this.loading = false;
          });
      }

      if (this.role === 'admin') {
        if (excel === 1) {
          Admin.orders
            .export(queries)
            .then(() => {
              this.loading = false;
            })
            .catch(e => {
              this.$alerts.errHandle(e, 'static');
              this.loading = false;
            });
        } else {
          Admin.orders
            .all(queries)
            .then(r => {
              this.td = r.data.data;
              this.loading = false;
              this.pagination = {
                current_page: r.data.current_page,
                last_page: r.data.last_page,
                next_page_url: r.data.next_page_url,
                path: r.data.path,
                per_page: r.data.per_page,
                prev_page_url: r.data.prev_page_url,
                total: r.data.total
              };
            })
            .catch(e => {
              this.$alerts.errHandle(e, 'static');
              this.loading = false;
            });
        }
      }
    },

    getCounts() {
      if (this.role === 'admin') {
        Orders.get
          .counts()
          .then(r => {
            this.filterCount = r.data.result;
          })
          .catch(e => {
            this.$alerts.errHandle(e, 'static');
            this.loading = false;
          });
      }
    },

    dataUpdater() {
      if (this.role === 'admin') {
        this.interval = setInterval(() => {
          this.loadData();
          this.getCounts();
        }, 60000);
      }
    }
  }
};
</script>

<template>
  <div>
    <table-list
      list-name="اقساط"
      :table-list="td"
      :pagination="pagination"
      :loading="loading"
      @onPageChange="changePage"
      @buttonClick="actionsHandle"
      @onSort="sort"
    >
      <template
        v-if="role === 'admin'"
        #filter
      >
        <div class="w-100">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <button
              class="btn btn-light rounded-pill btn-sm"
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

            <!-- Fast Filter -->
            <div class="btn-group btn-group-sm btn-pill d-none d-lg-flex">
              <input
                id="all"
                v-model="fastFilter"
                value="all"
                type="radio"
                class="btn-check"
                name="fastFilter"
                autocomplete="off"
                checked
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

              <input
                id="expired"
                v-model="fastFilter"
                value="expired"
                type="radio"
                class="btn-check"
                name="fastFilter"
                autocomplete="off"
              >
              <label
                class="btn"
                for="expired"
              >
                منقضی شده
                <span
                  v-if="filterCount.expired"
                  class="badge rounded-pill mr-2"
                >
                  {{ filterCount.expired }}
                </span>
              </label>

              <input
                id="near"
                v-model="fastFilter"
                value="near"
                type="radio"
                class="btn-check"
                name="fastFilter"
                autocomplete="off"
              >
              <label
                class="btn"
                for="near"
              >
                نزدیک به سررسید
                <span
                  v-if="filterCount.near"
                  class="badge rounded-pill mr-2"
                >
                  {{ filterCount.near }}
                </span>
              </label>

              <input
                id="today"
                v-model="fastFilter"
                value="today"
                type="radio"
                class="btn-check"
                name="fastFilter"
                autocomplete="off"
              >
              <label
                class="btn"
                for="today"
              >
                سررسید امروز
                <span
                  v-if="filterCount.today"
                  class="badge rounded-pill mr-2"
                >
                  {{ filterCount.today }}
                </span>
              </label>
            </div>
          </div>
          <div
            id="collapseExample"
            class="collapse show"
          >
            <div class="bg-gray-light rounded p-4">
              <form
                class="row px-1"
                @submit.prevent="addFiltersToRoute"
              >
                <div class="col-12 col-md-4">
                  <div class="bg-white rounded p-4 shadow-sm">
                    <div class="row">
                      <div class="col-12 col-md-6 mb-3">
                        <label>
                          <small>عنوان یا کد سازمان</small>
                        </label>
                        <input
                          v-model="filter.organ"
                          type="text"
                          class="form-control"
                        >
                      </div>
                      <div class="col-12 col-md-6 mb-3">
                        <label>
                          <small>عنوان فروشگاه</small>
                        </label>
                        <input
                          v-model="filter.shop"
                          type="text"
                          class="form-control"
                        >
                      </div>
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
                    </div>
                  </div>
                </div>

                <div class="col-12 col-md-4">
                  <div class="bg-white rounded p-4 shadow-sm">
                    <div class="row">
                      <div class="col-12 col-md-6 mb-3">
                        <label>
                          <small>حداقل مبلغ</small>
                        </label>
                        <input
                          v-model="filter.min"
                          type="text"
                          class="form-control"
                        >
                      </div>
                      <div class="col-6 col-md-6 mb-3">
                        <label>
                          <small>حداکثر مبلغ</small>
                        </label>
                        <input
                          v-model="filter.max"
                          type="text"
                          class="form-control"
                        >
                      </div>
                      <div class="col-12 col-md-6 mb-3">
                        <label>
                          <small>وضعیت</small>
                        </label>
                        <g-multi-select
                          v-model="filter.status"
                          :options="installmentStatusOptions"
                          string-return
                          :max="1"
                        />
                      </div>

                      <div class="col-12 col-md-6 mb-3">
                        <label>
                          <small>سریال چک</small>
                        </label>
                        <input
                          v-model="filter.series"
                          maxlength="6"
                          type="text"
                          class="form-control"
                        >
                      </div>

                      <div class="col-12 col-md-6 mb-3">
                        <label>
                          <small>شماره سفارش</small>
                        </label>
                        <input
                          v-model="filter.oid"
                          type="text"
                          class="form-control"
                        >
                      </div>
                      <div class="col-12 col-md-6 mb-3">
                        <label>
                          <small>نوع بازپرداخت</small>
                        </label>
                        <select
                          v-model="filter.payback_type"
                          class="form-select"
                        >
                          <option value="">
                            همه
                          </option>
                          <option value="cheque">
                            چک
                          </option>
                          <option value="epay">
                            اینترنتی
                          </option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-12 col-md-4">
                  <div class="bg-white rounded p-4 shadow-sm">
                    <div class="row">
                      <div class="col-12 col-lg-8 pl-lg-1 mb-3">
                        <label>
                          <small>نوع تاریخ</small>
                        </label>
                        <select
                          v-model="filter.date_type"
                          class="form-select"
                        >
                          <option value="">
                            تاریخ سررسید
                          </option>
                        </select>
                      </div>

                      <div class="col-12 col-lg-4 pr-lg-1 mb-3">
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
                    @click="loadAdminData(queries, 1)"
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
              </form>
            </div>
          </div>
        </div>
      </template>

      <template
        v-else
        #filter
      >
        <!-- Filter Dropdown -->
        <div class="btn-group filter-dropdown">
          <button
            type="button"
            class="btn btn-light btn-sm rounded-pill dropdown-toggle"
            @click.prevent="filterShow = !filterShow"
          >
            <i class="far fa-filter ml-2" />
            فیلتر
            <i class="fal fa-caret-down mr-3" />
          </button>

          <div
            class="dropdown-menu dropdown-menu-right shadow mt-2"
            :class="filterShow ? 'show' : ''"
          >
            <div class="scroll-wrapper scroll-300">
              <div class="scroll-wrapper-inner">
                <!-- OID & Mobile Number -->
                <div class="row py-3 px-2 m-0 border-bottom">
                  <div class="col-6">
                    <label>
                      <small>شماره سفارش</small>
                    </label>
                    <div class="input-group flex-row-reverse">
                      <!-- <span
                        id="orderNumber"
                        class="input-group-text ltr nova-font"
                      >GHC-</span> -->
                      <input
                        v-model="filter.oid"
                        type="text"
                        class="form-control ltr"
                        aria-label="orderNumber"
                        aria-describedby="orderNumber"
                      >
                    </div>
                  </div>
                  <div class="col-6">
                    <label>
                      <small>شماره موبایل</small>
                    </label>
                    <input
                      v-model="filter.mobile"
                      type="text"
                      class="form-control ltr estedad-font"
                    >
                  </div>
                </div>

                <!-- First & Last Names -->
                <div class="row pt-4 pb-3 px-2 m-0 border-bottom">
                  <div class="col-6">
                    <label>
                      <small>نام</small>
                    </label>
                    <input
                      v-model="filter.fname"
                      type="text"
                      class="form-control"
                    >
                  </div>
                  <div class="col-6">
                    <label>
                      <small>نام خانوادگی</small>
                    </label>
                    <input
                      v-model="filter.lname"
                      type="text"
                      class="form-control"
                    >
                  </div>
                </div>

                <!-- Status -->
                <div class="row pt-4 pb-3 px-2 m-0 border-bottom">
                  <div class="col-12">
                    <label>
                      <small>وضعیت</small>
                    </label>
                    <g-multi-select
                      v-model="filter.status"
                      :options="installmentStatusOptions"
                      string-return
                      :max="1"
                    />
                  </div>
                </div>

                <!-- Min & Max -->
                <div class="row pt-4 pb-3 px-2 m-0 border-bottom">
                  <div class="col-6">
                    <label>
                      <small>حداقل مبلغ</small>
                    </label>
                    <input
                      v-model="filter.min"
                      type="text"
                      class="form-control"
                    >
                  </div>
                  <div class="col-6">
                    <label>
                      <small>حداکثر مبلغ</small>
                    </label>
                    <input
                      v-model="filter.max"
                      type="text"
                      class="form-control"
                    >
                  </div>
                </div>

                <!-- From & To -->
                <div class="row pt-4 pb-3 px-2 m-0">
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

            <div class="d-flex justify-content-between p-3 pb-2 aic">
              <button
                class="btn btn-light btn-sm rounded-pill"
                @click="removeFiltersFromRoute"
              >
                حذف فیلتر
              </button>
              <button
                class="btn btn-success btn-sm rounded-pill"
                @click="addFiltersToRoute"
              >
                اعمال فیلتر
              </button>
            </div>
          </div>
        </div>

        <!-- Fast Filter -->
        <div class="btn-group btn-group-sm btn-pill d-none d-lg-flex">
          <input
            id="all"
            v-model="fastFilter"
            value="all"
            type="radio"
            class="btn-check"
            name="fastFilter"
            autocomplete="off"
            checked
          >
          <label
            class="btn"
            for="all"
          >
            همه
            <span
              v-if="filterCount"
              class="badge rounded-pill mr-2"
            >
              {{ filterCount.all }}
            </span>
          </label>

          <input
            id="expired"
            v-model="fastFilter"
            value="expired"
            type="radio"
            class="btn-check"
            name="fastFilter"
            autocomplete="off"
          >
          <label
            class="btn"
            for="expired"
          >
            منقضی شده
            <span
              v-if="filterCount"
              class="badge rounded-pill mr-2"
            >
              {{ filterCount.expired }}
            </span>
          </label>

          <input
            id="near"
            v-model="fastFilter"
            value="near"
            type="radio"
            class="btn-check"
            name="fastFilter"
            autocomplete="off"
          >
          <label
            class="btn"
            for="near"
          >
            نزدیک به سررسید
            <span
              v-if="filterCount"
              class="badge rounded-pill mr-2"
            >
              {{ filterCount.near }}
            </span>
          </label>

          <input
            id="today"
            v-model="fastFilter"
            value="today"
            type="radio"
            class="btn-check"
            name="fastFilter"
            autocomplete="off"
          >
          <label
            class="btn"
            for="today"
          >
            سررسید امروز
            <span
              v-if="filterCount"
              class="badge rounded-pill mr-2"
            >
              {{ filterCount.today }}
            </span>
          </label>
        </div>
      </template>
    </table-list>

    <modal
      v-show="modal.backedModal"
      title="برگشت"
      @close="modal.backedModal = false"
    >
      <template #body>
        <h5 class="mb-5">
          در چه زمانی این چک برگشت خورده است؟
        </h5>

        <action-date
          ref="adb"
          v-model="actionDate"
        />
      </template>

      <template #footer>
        <div class="d-flex w-100 justify-content-between">
          <g-button
            text="انصراف"
            @click.native="modal.backedModal = false"
          />
          <g-button
            ref="submit"
            text="ثبت"
            color="success"
            class="px-4"
            @click.native="SendActionDate"
          />
        </div>
      </template>
    </modal>

    <modal
      v-show="modal.passedModal"
      title="پاس شد"
      @close="modal.passedModal = false"
    >
      <template #body>
        <h5 class="mb-5">
          در چه زمانی این چک پاس شده است؟
        </h5>

        <action-date
          ref="adp"
          v-model="actionDate"
        />
      </template>

      <template #footer>
        <div class="d-flex w-100 justify-content-between">
          <g-button
            text="انصراف"
            @click.native="modal.passedModal = false"
          />
          <g-button
            ref="submit"
            text="ثبت"
            color="success"
            class="px-4"
            @click.native="SendActionDate"
          />
        </div>
      </template>
    </modal>
  </div>
</template>

<script>
import Admin from '@/api/admin';
import Seller from '@/api/seller';
import Organ from '@/api/organ';
import Custom from '@/api/custom';
import { mapState } from 'vuex';

import FilteringList from '@/global/FilteringList';
import TableListActions from '@/global/TableListActions';
import { installmentStatusOptions } from '@/data/FiltersMultiSelects';

export default {
  name: 'InstallmentsPage',

  metaInfo: {
    title: 'اقساط'
  },

  components: {
    actionDate: () =>
      import('@/components/dashboard/Installments/ActionDate/Index')
  },

  mixins: [FilteringList, TableListActions],

  data() {
    return {
      filterShow: false,
      actionDate: '',

      installmentStatusOptions
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
  },

  methods: {
    loadAdminData(queries, excel = 0) {
      this.loading = true;

      if (excel === 1) {
        Admin.get
          .installments_export(queries)
          .then(() => {
            this.loading = false;
          })
          .catch(e => {
            this.$alerts.errHandle(e, 'static');
            this.loading = false;
          });
      } else {
        Admin.get
          .installments(queries)
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

            this.setKeysSort(this.td[0].td);
          })
          .catch(e => {
            this.$alerts.errHandle(e);
            this.loading = false;
          });
      }
    },

    loadSellerData(queries) {
      this.loading = true;

      Seller.get
        .installments(queries)
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
          this.$alerts.errHandle(e);
          this.loading = false;
        });
    },

    loadOrganData(queries) {
      this.loading = true;

      Organ.get
        .installments(queries)
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
          this.$alerts.errHandle(e);
          this.loading = false;
        });
    },

    loadData() {
      let queries = '';

      Object.keys(this.$route.query).map(i => {
        queries = queries + `${i}=${this.$route.query[i]}&`;
      });

      queries = queries.substring(0, queries.lastIndexOf('&'));

      if (this.role === 'admin') {
        this.loadAdminData(queries);
      } else if (this.role === 'shop') {
        this.loadSellerData(queries);
      } else if (this.role === 'organ') {
        this.loadOrganData(queries);
        Organ.get.installmentsCounts().then(r => {
          this.filterCount = r.data.result;
        });
      }
    },

    SendActionDate() {
      this.$refs.submit.loading('start');

      Custom.post(this.modal.endpoint, {
        action_date: this.actionDate
      })
        .then(res => {
          if (res.data.status) {
            this.$alerts.show({
              msg: 'تاریخ با موفقیت ذخیره شد',
              type: 'success',
              style: 'float'
            });

            this.$refs.adb.actionDate = 'today';
            this.$refs.adb.actionType = '';

            this.$refs.adp.actionDate = 'today';
            this.$refs.adp.actionType = '';

            this.loadData();
          } else {
            this.$alerts.show({
              msg: 'مشکلی در ذخیره کردن آدرس پیش آمد',
              type: 'danger',
              style: 'float'
            });
          }
          this.$refs.submit.loading('end');
          this.modal[this.modal.name] = false;
        })
        .catch(err => {
          this.$alerts.errHandle(err);
          this.$refs.submit.loading('end');
        });
    }
  }
};
</script>

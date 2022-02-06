<template>
  <div>
    <table-list
      list-name="تراکنش‌ها"
      :table-list="td"
      :pagination="pagination"
      :loading="loading"
      @onPageChange="changePage"
      @buttonClick="actionsHandle"
    >
      <template #filter>
        <div class="w-100">
          <!-- Filter Toggler -->
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
          </div>

          <!-- Filtering inputs -->
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
                      <div class="col-12 col-md-6 mb-3">
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
                          <small>شماره پیگیری</small>
                        </label>
                        <input
                          v-model="filter.ref_id"
                          type="text"
                          class="form-control ltr estedad-font"
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
                      <div class="col-12 mb-3">
                        <label>
                          <small>وضعیت</small>
                        </label>
                        <select
                          v-model="filter.status"
                          class="form-select"
                        >
                          <option value="">
                            همه
                          </option>
                          <option value="successful">
                            موفق
                          </option>
                          <option value="failed">
                            ناموفق
                          </option>
                          <option value="unknown">
                            نامعلوم
                          </option>
                        </select>
                      </div>
                      <div class="col-12 mb-3">
                        <label>
                          <small>نوع تراکنش</small>
                        </label>
                        <select
                          v-model="filter.type"
                          class="form-select"
                        >
                          <option value="">
                            همه
                          </option>
                          <option value="prepayment">
                            پیش پرداخت 
                          </option>
                          <option value="ghest">
                            قسط
                          </option>
                          <option value="extra">
                            شارژ اضافی
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
                            همه
                          </option>
                          <option value="paid_at">
                            تاریخ پرداخت
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
    </table-list>
  </div>
</template>

<script>
import FilteringList from '@/global/FilteringList';
import { mapState } from 'vuex';
import Custom from '@/api/custom';

export default {
  name: 'SmsPage',

  metaInfo: {
    title: 'اقساط'
  },

  mixins: [FilteringList],

  data() {
    return {
      filterShow: false
    };
  },

  computed: {
    ...mapState('dashboard', ['role'])
  },

  created() {
    this.loadData();
  },

  methods: {
    actionsHandle(payload) {
      const { type, modal_name, endpoint, method, confirm_message } = payload;

      if (type === 'confirm') {
        if (confirm(confirm_message ? confirm_message : 'آیا مطمئن هستید؟')) {
          Custom[method](endpoint)
            .then(r => {
              if (r.data.status) {
                this.loadData();
              }
              this.onChangeBtn = payload.e;
            })
            .catch(e => {
              this.$alerts.errHandle(e);
              this.onChangeBtn = payload.e;
            });
        } else {
          this.onChangeBtn = payload.e;
        }
      }

      if (type === 'straight') {
        Custom[method](endpoint)
          .then(r => {
            if (r.data.status) {
              window.location = r.data.result.url;
            }
          })
          .catch(e => {
            this.$alerts.errHandle(e);
          });
      }

      if (type === 'modal') {
        this[modal_name] = true;
        this.currentModalEndpoint = endpoint;
        this.currentModalMethod = method;

        if (modal_name === 'editModal') {
          this.editModalLoading = true;

          Custom.get(endpoint)
            .then(r => {
              const { result } = r.data;
              this.editModalData = result;
              this.editModalLoading = false;
            })
            .catch(e => {
              this.$alerts.errHandle(e);
              this.editModalLoading = false;
            });
        }
      }
    },

    loadData() {
    }
  }
};
</script>

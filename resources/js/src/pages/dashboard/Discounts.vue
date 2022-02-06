<template>
  <div>
    <table-list
      list-name="کدهای تخفیف"
      :table-list="td"
      :pagination="pagination"
      :loading="loading"
      add-button="کد تخفیف"
      @addTrigger="addModal = true"
      @onPageChange="changePage"
      @onSort="sort"
      @buttonClick="actionsHandle"
    >
      <template #filter>
        <div class="w-100">
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
                      <div class="col-12 mb-3">
                        <label>
                          <small>عبارت</small>
                        </label>
                        <input
                          v-model="filter.name"
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
                          <option value="active">
                            فعال
                          </option>
                          <option value="inactive">
                            غیرفعال
                          </option>
                          <option value="expired">
                            منقضی شده
                          </option>
                          <option value="limit">
                            ظرفیت تکمیل
                          </option>
                        </select>
                      </div>
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
                            تاریخ انقضا
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


    <!-- Add Modal -->
    <modal
      v-show="addModal"
      title="کد تخفیف جدید"
      @close="addModal = false"
    >
      <template #body>
        <div class="row">
          <div class="col-12 mb-4">
            <label class="n-opt">کد تخفیف</label>
            <input
              v-model="addData.code"
              class="form-control form-control-lg ltr special-font text-center"
            >
          </div>
          <div class="col-12 col-md-6 mb-4">
            <label class="n-opt">مبلغ</label>
            <input
              v-model="addData.amount"
              class="form-control form-control-lg"
            >
          </div>
          <div class="col-12 col-md-6 mb-4">
            <label class="n-opt">محدودیت</label>
            <input
              v-model="addData.limit"
              class="form-control form-control-lg"
            >
          </div>
          <div class="col-12 col-md-6 mb-4">
            <label>محدودیت به ازای کاربر</label>
            <input
              v-model="addData.limit_per_user"
              class="form-control form-control-lg"
            >
          </div>
          <div class="col-12 col-md-6 mb-4">
            <label>موبایل</label>
            <input
              v-model="addData.mobile"
              class="form-control form-control-lg ltr estedad-font"
            >
          </div>
          <div class="col-12 mb-4">
            <label>تاریخ انقضا</label>
            <div class="row">
              <div class="col-3 pl-1">
                <select
                  v-model="dateConvertor.d"
                  class="form-select form-select-lg"
                >
                  <option
                    selected
                    disabled
                  >
                    روز
                  </option>
                  <option
                    v-for="i in 31"
                    :key="i"
                    :value="i"
                  >
                    {{ i }}
                  </option>
                </select>
              </div>
              <div class="col-5 px-1">
                <select
                  v-model="dateConvertor.m"
                  class="form-select form-select-lg"
                >
                  <option
                    selected
                    disabled
                  >
                    ماه
                  </option>
                  <option value="01">
                    فروردین
                  </option>
                  <option value="02">
                    اردیبهشت
                  </option>
                  <option value="03">
                    خرداد
                  </option>
                  <option value="04">
                    تیر
                  </option>
                  <option value="05">
                    مرداد
                  </option>
                  <option value="06">
                    شهریور
                  </option>
                  <option value="07">
                    مهر
                  </option>
                  <option value="08">
                    آبان
                  </option>
                  <option value="09">
                    آذر
                  </option>
                  <option value="10">
                    دی
                  </option>
                  <option value="11">
                    بهمن
                  </option>
                  <option value="12">
                    اسفند
                  </option>
                </select>
              </div>
              <div class="col-4 pr-1">
                <select
                  v-model="dateConvertor.y"
                  class="form-select form-select-lg"
                >
                  <option
                    selected="true"
                    disabled
                  >
                    سال
                  </option>
                  <option
                    v-for="(y, i) in years"
                    :key="i"
                    :value="y"
                  >
                    {{ y }}
                  </option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </template>

      <template #footer>
        <div class="d-flex justify-content-between w-100">
          <button
            class="btn btn-light rounded-pill"
            @click="addModal = false"
          >
            انصراف
          </button>

          <button
            type="button"
            class="btn btn-primary rounded-pill"
            :class="addLoading ? 'btn-loading' : ''"
            @click="add"
          >
            <div
              class="spinner-border"
              role="status"
            >
              <span class="sr-only">در حال بررسی</span>
            </div>
            <span class="btn-text d-flex aic w-100 jcb">
              ثبت کد تخفیف جدید
            </span>
          </button>
        </div>
      </template>
    </modal>

    <!-- Edit Modal -->
    <modal
      v-show="editModal"
      title="ویرایش کد تخفیف"
      @close="editModal = false"
    >
      <template #body>
        <div
          v-if="editModalLoading"
          class="page-loading"
        >
          <div class="spinner-border" />
        </div>
        <template v-else>
          <form-builder
            v-model="addData"
            :form-data="editModalData"
          />
        </template>
      </template>

      <template #footer>
        <div class="d-flex justify-content-between w-100">
          <button
            class="btn btn-light rounded-pill"
            @click="editModal = false"
          >
            انصراف
          </button>

          <g-button
            ref="add"
            text="ویرایش کد تخفیف"
            type="button"
            color="primary"
            @click.native="edit"
          />
        </div>
      </template>
    </modal>
  </div>
</template>

<script>
import Admin from '@/api/admin';
import Custom from '@/api/custom';
import FilteringList from '@/global/FilteringList';

export default {
  name: 'DiscountsPage',

  metaInfo: {
    title: 'تخفیف‌ها',
  },

  mixins: [FilteringList],

  data() {
    return {
      addModal: false,
      addData: {
        code: '',
        amount: '',
        limit: '',
        limit_per_user: '',
        mobile: '',
        expired_at: '',
        is_active: 1
      },

      editModal: false,
      editModalData: [],
      editModalLoading: false,

      currentModalEndpoint: '',
      currentModalMethod: '',

      dateConvertor: {
        d: '',
        m: '',
        y: ''
      }
    };
  },

  computed: {
    years() {
      const y = [];
      for(let i = 1399; i <= 1420; i++) {
        y.push(i);
      }
      return y;
    }
  },

  watch: {
    role() {
      this.loadData();
    },

    dateConvertor: {
      handler(val) {
        if (val.d && val.m && val.y) {
          this.addData.expired_at = `${val.y}-${val.m}-${val.d}`;
        }
      },
      deep: true,
    },
  },

  created() {
    this.loadData();
  },

  methods: {
    actionsHandle(payload) {

      if (payload.type === 'confirm') {
        if (
          confirm(
            payload.confirm_message
              ? payload.confirm_message
              : 'آیا مطمئن هستید؟'
          )
        ) {
          Custom[payload.method](payload.endpoint)
            .then((r) => {
              if (r.data.status) {
                this.loadData();
              }
              this.onChangeBtn = payload.e;
            })
            .catch((e) => {
              this.$alerts.errHandle(e);
              this.onChangeBtn = payload.e;
            });
        } else {
          this.onChangeBtn = payload.e;
        }
      }

      if (payload.type === 'modal') {
        this[payload.modal_name] = true;
        this.currentModalEndpoint = payload.endpoint;
        this.currentModalMethod = payload.method;

        if (payload.modal_name === 'editModal') {
          this.editModalLoading = true;

          Custom.get(payload.endpoint)
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
      let queries = '';

      Object.keys(this.$route.query).map((i) => {
        queries = queries + `${i}=${this.$route.query[i]}&`;
      });

      queries = queries.substring(0, queries.lastIndexOf('&'));

      this.loading = true;

      Admin.discounts
        .all(queries)
        .then((r) => {
          this.td = r.data.data;
          this.pagination = {
            current_page: r.data.current_page,
            last_page: r.data.last_page,
            next_page_url: r.data.next_page_url,
            path: r.data.path,
            per_page: r.data.per_page,
            prev_page_url: r.data.prev_page_url,
            total: r.data.total,
          };
          this.loading = false;
        })
        .catch((e) => {
          this.$alerts.errHandle(e);
          this.loading = false;
        });
    },

    add() {
      this.$refs.add.loading('start');
      
      Admin.discounts.create(this.addData)
        .then(r => {
          this.$refs.add.loading('end');

          if (r.data.status) {
            this.addModal = false;
            this.loadData();

            this.$alerts.show({
              msg: 'کد تخفیف با موفقیت ساخته شد',
              type: 'success',
              style: 'float'
            });
          } else {
            this.$alerts.show({
              msg: r.data.error.message,
              type: 'danger',
              style: 'float'
            });
          }
        })
        .catch(e => {
          this.$alerts.errHandle(e);
          this.$refs.add.loading('end');
        });
    },
    
    edit() {
      this.editLoading = true;

      Custom[this.currentModalMethod](this.currentModalEndpoint, this.addData)
        .then(r => {
          this.editLoading = false;

          if (r.data.status) {
            this.loadData();
            this.editModal = false;
            this.$alerts.show({
              msg: 'تخفیف با موفقیت ویرایش شد',
              type: 'success',
              style: 'float'
            });
          } else {
            this.$alerts.show({
              msg: 'مشکلی در ویرایش تخفیف پیش آمده است',
              type: 'danger',
              style: 'float'
            });
          }
        })
        .catch(e => {
          this.$alerts.errHandle(e);
          this.editLoading = false;
        });
    },

    changePage(payload) {
      this.filter.page = payload;
      this.addFiltersToRoute();
    },
  },
};
</script>
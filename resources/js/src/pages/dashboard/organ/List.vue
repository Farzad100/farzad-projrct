<template>
  <div>
    <table-list
      list-name="سازمان ها"
      :table-list="td"
      :pagination="pagination"
      :loading="loading"
      add-button="سازمان"
      @addTrigger="addModal = true"
      @onPageChange="changePage"
      @buttonClick="actionsHandle"
      @onSort="sort"
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
            <form
              class="bg-gray-light rounded p-4"
              @submit.prevent="addFiltersToRoute"
            >
              <div class="row px-1">
                <div class="col-12 col-md-4">
                  <div class="bg-white rounded p-4 shadow-sm">
                    <div class="row">
                      <div class="col-12 mb-3">
                        <label>
                          <small>عنوان یا کد سازمان</small>
                        </label>
                        <input
                          v-model="filter.name"
                          type="text"
                          class="form-control"
                        >
                      </div>
                      <div class="col-12 col-md-6 mb-3">
                        <label>
                          <small>موبایل رابط</small>
                        </label>
                        <input
                          v-model="filter.mobile"
                          type="text"
                          class="form-control ltr estedad-font"
                        >
                      </div>
                      <div class="col-12 col-md-6 mb-3">
                        <label>
                          <small>کد ملی رابط</small>
                        </label>
                        <input
                          v-model="filter.nid"
                          type="text"
                          class="form-control"
                        >
                      </div>
                      <div class="col-12 col-md-6 mb-3">
                        <label>
                          <small>نام رابط</small>
                        </label>
                        <input
                          v-model="filter.fname"
                          type="text"
                          class="form-control"
                        >
                      </div>
                      <div class="col-12 col-md-6 mb-3">
                        <label>
                          <small>نام خانوادگی رابط</small>
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
                    <div class="col-12 mb-3">
                      <label>
                        <small>مدل همکاری</small>
                      </label>
                      <select
                        v-model="filter.type"
                        class="form-select"
                      >
                        <option value="">
                          همه
                        </option>
                        <option value="g">
                          ضمانت
                        </option>
                        <option value="i">
                          معرفی
                        </option>
                      </select>
                    </div>

                    <div class="col-12 mb-3">
                      <label>
                        <small>وضعیت</small>
                      </label>
                      <g-multi-select
                        v-model="filter.status"
                        :options="shopStatusOptions"
                        string-return
                        :max="1"
                      />
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
                          <option value="created_at">
                            تاریخ ثبت‌ نام
                          </option>
                          <option value="docs_uploaded_at">
                            تاریخ بارگذاری مدارک 
                          </option>
                          <option value="docs_accepted_at">
                            تاریخ تأیید مدارک
                          </option>
                          <option value="start_at">
                            تاریخ شروع فعالیت
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
        </div>
      </template>
    </table-list>

    <modal
      v-show="addModal"
      title="ایجاد سازمان جدید"
      @close="addModal = false"
    >
      <template #body>
        <div class="row">
          <div class="col-12 col-md-6 mb-4">
            <label class="n-opt">نام کامل سازمان</label>
            <input
              v-model="addData.name"
              class="form-control form-control-lg"
            >
          </div>
          <div class="col-12 col-md-6 mb-4">
            <label>شهرت</label>
            <input
              v-model="addData.fame"
              class="form-control form-control-lg"
            >
          </div>
          <div class="col-12 col-md-6 mb-4">
            <label class="n-opt">نام کاربری</label>
            <input
              v-model="addData.username"
              maxlength="10"
              class="form-control form-control-lg ltr estedad-font text-center"
            >
          </div>
          <div class="col-12 col-md-6 mb-4">
            <label class="n-opt">
              پسورد
              <small class="opa-5">
                بیشتر از ۶ کاراکتر
              </small>
            </label>
            <input
              v-model="addData.password"
              class="form-control form-control-lg ltr text-center"
            >
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
              ثبت سازمان جدید
            </span>
          </button>
        </div>
      </template>
    </modal>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import Admin from '@/api/admin';
import Custom from '@/api/custom';
import FilteringList from '@/global/FilteringList';
import { shopStatusOptions } from '@/data/FiltersMultiSelects';


export default {
  name: 'OrgansPage',

  metaInfo: {
    title: 'سازمان‌ها'
  },


  mixins: [FilteringList],

  data() {
    return {
      addModal: false,
      addData: {
        name: '',
        fame: '',
        username: '',
        password: ''
      },
      addLoading: false,

      shopStatusOptions
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

      if (payload.type === 'modal') {
        this[payload.modal_name] = true;
        this.currentModalEndpoint = payload.endpoint;
        this.currentModalMethod = payload.method;
      }
    },

    loadData(excel = 0) {
      this.loading = true;
      let queries = '';

      Object.keys(this.$route.query).map(i => {
        queries = queries + `${i}=${this.$route.query[i]}&`;
      });

      queries = queries.substring(0, queries.lastIndexOf('&'));

      this.loading = true;

      if (excel === 1) {
        Admin.organs
          .export(queries)
          .then(() => {
            this.loading = false;
          })
          .catch(e => {
            this.$alerts.errHandle(e, 'static');
            this.loading = false;
          });
      } else {
        Admin.organs
          .all(queries)
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
      }
    },

    add() {
      this.addLoading = true;

      Admin.organs
        .create(this.addData)
        .then(r => {
          this.addLoading = false;

          if (r.data.status) {
            this.addModal = false;
            this.loadData();

            this.$alerts.show({
              msg: 'سازمان با موفقیت ساخته شد',
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
          this.addLoading = false;
        });
    },

    changePage(payload) {
      this.filter.page = payload;
      this.addFiltersToRoute();
    }
  }
};
</script>

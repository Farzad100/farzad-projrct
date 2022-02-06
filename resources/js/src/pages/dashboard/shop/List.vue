<template>
  <div>
    <table-list
      list-name="فروشگاه ها"
      :table-list="td"
      :pagination="pagination"
      :loading="loading"
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
                          <small>عنوان فروشگاه</small>
                        </label>
                        <input
                          v-model="filter.name"
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
                          <small>مدل همکاری</small>
                        </label>
                        <select
                          v-model="filter.type"
                          class="form-select"
                        >
                          <option value="">
                            همه
                          </option>
                          <option value="online">
                            اینترنتی
                          </option>
                          <option value="offline">
                            فیزیکی
                          </option>
                        </select>
                      </div>

                      <div class="col-12 col-md-6 mb-3">
                        <label>
                          <small>سایت</small>
                        </label>
                        <input
                          v-model="filter.domain"
                          type="text"
                          class="form-control"
                        >
                      </div>

                      <div class="col-12 mb-3">
                        <label>
                          <small>دسته بندی</small>
                        </label>
                        <g-multi-select
                          v-model="filter.category"
                          :options="shopCategoryOptions"
                          string-return
                          :max="1"
                        />
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
                            تاریخ ثبت نام
                          </option>
                          <option value="agreed_at">
                            تاریخ امضای قرارداد
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
              </form>
            </div>
          </div>
        </div>
      </template>
    </table-list>

    <!-- Edit Modal -->
    <modal
      v-show="editModal"
      title="اطلاعات و ویرایش فروشگاه"
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
          <div
            v-for="(section, sectionIndex) in openModalForm"
            :key="sectionIndex"
            class="row"
          >
            <h4 class="special-font text-brand font-weight-lighter mb-4 opa-3">
              {{ section.section }}
            </h4>

            <div
              v-for="(item, i) in section.fields"
              :key="i"
              :class="['col-12 mb-4', `col-md-${item.width}`]"
            >
              <template v-if="item.type === 'text'">
                <label>
                  {{ item.label }}
                </label>
                <input
                  v-model="modalFormModels[item.v_model]"
                  class="form-control form-control-lg"
                  :disabled="item.disabled"
                >
              </template>

              <template v-if="item.type === 'textarea'">
                <label>
                  {{ item.label }}
                </label>
                <textarea
                  v-model="modalFormModels[item.v_model]"
                  class="form-control form-control-lg"
                  rows="3"
                />
              </template>

              <template v-if="item.type === 'select'">
                <label>
                  {{ item.label }}
                </label>
                <select
                  v-model="modalFormModels[item.v_model]"
                  class="form-select form-select-lg"
                  :disabled="item.disabled"
                >
                  <option disabled>
                    انتخاب کنید
                  </option>
                  <option
                    v-for="(opt, index) in item.options"
                    :key="index"
                    :value="opt.value"
                  >
                    {{ opt.label }}
                  </option>
                </select>
              </template>
            </div>
          </div>
        </template>
      </template>

      <template #footer>
        <div class="d-flex w-100 justify-content-between">
          <button
            class="btn btn-light rounded-pill"
            @click="editModal = false"
          >
            انصراف
          </button>

          <button
            type="button"
            class="btn btn-primary rounded-pill"
            :class="editLoading ? 'btn-loading' : ''"
          >
            <div
              class="spinner-border"
              role="status"
            >
              <span class="sr-only">در حال بررسی</span>
            </div>
            <span class="btn-text d-flex aic w-100 jcb">
              اعمال تغییرات
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
import {
  shopStatusOptions,
  shopCategoryOptions
} from '@/data/FiltersMultiSelects';

export default {
  name: 'Shops',

  metaInfo: {
    title: 'فروشگاه‌ها'
  },

  mixins: [FilteringList],

  data() {
    return {
      editModal: false,
      editLoading: false,
      editModalLoading: true,

      /**
       * Current OPENED modal data
       */
      modalFormModels: {},
      openModalForm: [],
      currentModalEndpoint: '',
      currentModalMethod: '',

      shopStatusOptions,
      shopCategoryOptions
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
        this[payload.modal_name + 'Loading'] = true;
        this.currentModalEndpoint = payload.endpoint;
        this.currentModalMethod = payload.method;

        Custom.get(this.currentModalEndpoint).then(r => {
          this.openModalForm = r.data.result;
          this[payload.modal_name + 'Loading'] = false;

          this.openModalForm.forEach(obj => {
            obj.fields.forEach(f => {
              this.modalFormModels = {
                ...this.modalFormModels,
                [f.v_model]: f.value
              };
            });
          });
        });
      }
    },

    loadData(excel = 0) {
      let queries = '';

      Object.keys(this.$route.query).map(i => {
        queries = queries + `${i}=${this.$route.query[i]}&`;
      });

      queries = queries.substring(0, queries.lastIndexOf('&'));

      this.loading = true;

      if (excel === 1) {
        Admin.shops
          .export(queries)
          .then(() => {
            this.loading = false;
          })
          .catch(e => {
            this.$alerts.errHandle(e, 'static');
            this.loading = false;
          });
      } else {
        Admin.shops
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

    changePage(payload) {
      this.filter.page = payload;
      this.addFiltersToRoute();
    }
  }
};
</script>

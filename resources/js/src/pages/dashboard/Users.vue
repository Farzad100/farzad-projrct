<template>
  <div>
    <table-list
      list-name="کاربران"
      :table-list="td"
      :pagination="pagination"
      :loading="loading"
      @onPageChange="changePage"
      @buttonClick="actionsHandle"
      @onSort=" sort"
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
                      <div class="col-12 col-md-6 mb-3 text-left">
                        <label>
                          <small>UTM Source</small>
                        </label>
                        <input
                          v-model="filter.utm_source"
                          type="text"
                          class="form-control ltr"
                        >
                      </div>

                      <div class="col-12 col-md-6 mb-3 text-left">
                        <label>
                          <small>UTM Medium</small>
                        </label>
                        <input
                          v-model="filter.utm_medium"
                          type="text"
                          class="form-control ltr"
                        >
                      </div>

                      <div class="col-12 col-md-6 mb-3 text-left">
                        <label>
                          <small>UTM Campaign</small>
                        </label>
                        <input
                          v-model="filter.utm_campaign"
                          type="text"
                          class="form-control ltr"
                        >
                      </div>

                      <div class="col-12 col-md-6 mb-3 text-left">
                        <label>
                          <small>UTM Content</small>
                        </label>
                        <input
                          v-model="filter.utm_content"
                          type="text"
                          class="form-control ltr"
                        >
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-12 col-md-4">
                  <div class="bg-white rounded p-4 shadow-sm">
                    <div class="row"> 
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

    <!-- Edit Modal -->
    <modal
      v-show="editModal"
      title="اطلاعات کاربر"
      size="lg"
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
            v-model="editForm"
            :form-data="editModalData"
          />
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
            @click="edit"
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

    <!-- Inquiry Modal -->
    <inquiry ref="inquiry" />

    <g-notes
      v-if="noteModal"
      g-type="user"
      :g-id="noteId"
      :g-endpoint="currentModalEndpoint"
      @close="noteModal = false"
    />
  </div>
</template>

<script>
import { mapState } from 'vuex';
import Admin from '@/api/admin';
import Custom from '@/api/custom';
import FilteringList from '@/global/FilteringList';

export default {
  name: 'UsersPage',

  metaInfo: {
    title: 'کاربران'
  },

  components: {
    inquiry: () => import('@/components/dashboard/Modals/Inquiry/Index')
  },

  mixins: [FilteringList],

  data() {
    return {
      infoModal: false,

      /**
       * Info modal
       */
      editModal: false,
      editModalLoading: false,
      editModalData: [],
      editForm: {},
      editLoading: false,

      /**
       * Note modal
       */
      noteModal: false,
      noteId: '',

      currentModalEndpoint: '',
      currentModalMethod: ''
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
    /**
     * Watch table list action buttons
     * if they clicked
     *
     * @param {object} payload
     */
    actionsHandle(payload) {
      const { type, modal_name, endpoint, method, confirm_message, note_id } = payload;

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

        if (modal_name === 'noteModal') {
          this.noteId = note_id;
        }

        if (modal_name === 'inquiryModal') {
          this.$refs.inquiry.openModal(payload);
        }
      }
    },

    getBackChques(force) {
      this.inquiryModalLoadingBC = true;

      Custom.get(`${this.backChequesEndpoint}/back-cheques${force ? '/1' : ''}`)
        .then(r => {
          const { result } = r.data;
          this.inquiryModalDataBC = result;
          this.inquiryModalLoadingBC = false;
        })
        .catch(e => {
          this.$alerts.errHandle(e);
          this.inquiryModalLoadingBC = false;
        });
    },

    getFacilities(force) {
      this.inquiryModalLoadingFaci = true;

      Custom.get(`${this.currentModalEndpoint}/facilities${force ? '/1' : ''}`)
        .then(r => {
          const { result } = r.data;
          this.inquiryModalDataFaci = result;
          this.inquiryModalLoadingFaci = false;
        })
        .catch(e => {
          this.$alerts.errHandle(e);
          this.inquiryModalLoadingFaci = false;
        });
    },

    /**
     * Edit users
     */
    edit() {
      this.editLoading = true;

      Custom[this.currentModalMethod](this.currentModalEndpoint, this.editForm)
        .then(r => {
          this.editLoading = false;

          if (r.data.status) {
            this.loadData();
            this.editModal = false;
            this.$alerts.show({
              msg: 'اطلاعات با موفقیت ویرایش شد',
              type: 'success',
              style: 'float'
            });
          } else {
            this.$alerts.show({
              msg: 'مشکلی در ویرایش اطلاعات پیش آمده است',
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

    /**
     * Get filters from route queries and
     * pass it to API Endpoint to GET all
     * data
     */
    loadData(excel = 0) {
      let queries = '';

      Object.keys(this.$route.query).map(i => {
        queries = queries + `${i}=${this.$route.query[i]}&`;
      });

      queries = queries.substring(0, queries.lastIndexOf('&'));

      this.loading = true;

      if (excel === 1) {
        Admin.users
          .export(queries)
          .then(() => {
            this.loading = false;
          })
          .catch(e => {
            this.$alerts.errHandle(e, 'static');
            this.loading = false;
          });
      } else {
        Admin.users
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

    /**
     * Get new page number and pass it to
     * filters and route queries
     */
    changePage(payload) {
      this.filter.page = payload;
      this.addFiltersToRoute();
    }
  }
};
</script>

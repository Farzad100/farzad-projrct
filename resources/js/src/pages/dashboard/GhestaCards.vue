<template>
  <div>
    <table-list
      list-name="قسطاکارت‌ها"
      :table-list="td"
      :pagination="pagination"
      :loading="loading"
      add-button="قسطا کارت"
      @addTrigger="addCardModal"
      @onPageChange="changePage"
      @buttonClick="actionsHandle"
      @onSort="sort"
    >
      <template #filter>
        <div class="w-100">
          <div class="d-flex mb-2">
            <!-- Filter button -->
            <button
              class="btn btn-light rounded-pill btn-sm ml-2"
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

            <!-- Actions button -->
            <div class="dropdown">
              <button
                id="dropdownMenuLink"
                class="btn btn-light btn-sm rounded-pill dropdown-toggle"
                href="#"
                role="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                عملیات
                <i class="fal fa-caret-down mr-3" />
              </button>

              <ul
                class="dropdown-menu"
                aria-labelledby="dropdownMenuLink"
              >
                <li>
                  <button
                    class="dropdown-item text-right"
                    @click="loadData(1)"
                  >
                    دانلود اکسل
                  </button>
                </li>
                <li>
                  <button
                    class="dropdown-item text-right"
                    @click="groupUploadModal = true"
                  >
                    آپلود دسته جمعی
                  </button>
                </li>
                <li>
                  <button
                    class="dropdown-item text-right"
                    @click="cardIssuanceModal = true"
                  >
                    کارت‌هایی که باید صادر شوند
                  </button>
                </li>
              </ul>
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
                      <div class="col-12 mb-3">
                        <label>
                          <small>شماره کارت</small>
                        </label>
                        <input
                          v-model="filter.card_number"
                          type="text"
                          class="form-control"
                        >
                      </div>
                      <div class="col-12 mb-3">
                        <label>
                          <small>سری کارت</small>
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
                      <div class="col-12 col-lg-8 pl-lg-1 mb-3">
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
                            تاریخ صدور
                          </option>
                          <option value="sent_at">
                            تاریخ ارسال
                          </option>
                          <option value="delivered_at">
                            تاریخ تحویل
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

    <!-- Charge Modal -->
    <modal
      v-show="chargeModal"
      title="شارژ قسطا کارت"
      @close="chargeModal = false"
    >
      <template #body>
        <div
          v-if="modalLoading"
          class="page-loading my-5"
        >
          <div class="spinner-border" />
        </div>
        <template v-else>
          <div
            class="alert alert-warning"
            role="alert"
          >
            <i class="fas fa-exclamation-triangle ml-2" />
            اطلاعات زیر را با دقت بررسی کنید و از صحت آن مطمئن شوید
          </div>

          <div class="mt-4">
            <div
              class="border-bottom d-flex justify-content-between align-items-center p-3"
            >
              <span>
                نام و نام خانوادگی
              </span>
              <h4 class="special-font m-0">
                {{ chargeModalData.name }}
              </h4>
            </div>
            <div
              class="border-bottom d-flex justify-content-between align-items-center p-3"
            >
              <span>
                شماره موبایل
              </span>
              <h4 class="special-font m-0">
                {{ chargeModalData.mobile }}
              </h4>
            </div>
            <div class="d-flex justify-content-between align-items-center p-3">
              <span>
                شماره کارت
              </span>
              <h4 class="special-font m-0 ltr">
                {{ chargeModalData.card_number | cardNumber }}
              </h4>
            </div>
          </div>

          <div class="mt-4 px-5">
            <input
              v-model="chargeAmount"
              placeholder="مبلغ شارژ"
              class="form-control rounded-pill ltr estedad-font text-center mb-2"
            >
            <small class="d-block text-center">
              {{ !chargeAmount ? '0' : chargeAmount | numToPersian }}
              <span class="mr-1 opa-5">
                تومان
              </span>
            </small>
          </div>
        </template>
      </template>

      <template #footer>
        <div class="d-flex justify-content-between w-100">
          <button
            class="btn btn-light rounded-pill"
            @click="chargeModal = false"
          >
            انصراف
          </button>

          <button
            type="button"
            class="btn btn-warning rounded-pill"
            :class="chargeModalLoading ? 'btn-loading' : ''"
            @click="chargeCard"
          >
            <div
              class="spinner-border"
              role="status"
            >
              <span class="sr-only">در حال بررسی</span>
            </div>
            <span class="btn-text d-flex aic w-100 jcb">
              <i class="fas fa-exclamation-triangle ml-2" />
              شارژ
            </span>
          </button>
        </div>
      </template>
    </modal>

    <!-- Add Cart Modal -->
    <modal
      v-show="addModal"
      title="ایجاد قسطا کارت"
      @close="addModal = false"
    >
      <template #body>
        <div class="row">
          <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
              <label>شماره کارت</label>
              <span class="ltr special-font">
                {{ addData.card_number | cardNumberDash }}
              </span>
            </div>
            <input
              v-model="addData.card_number"
              maxlength="16"
              pattern="[0-9.]+"
              class="form-control form-control-lg ltr estedad-font text-center"
            >
          </div>
          <div class="col-12 col-md-6">
            <ValidationProvider
              v-slot="{ errors }"
              name="شماره موبایل"
              rules="mobileCheck"
            >
              <label>شماره موبایل</label>
              <input
                v-model="addData.mobile"
                class="form-control form-control-lg ltr estedad-font text-center"
              >
              <small class="text-danger pt-1">
                {{ errors[0] }}
              </small>
            </ValidationProvider>
          </div>
          <div class="col-12 col-md-6">
            <label>سری</label>
            <input
              v-model="addData.series"
              class="form-control form-control-lg ltr estedad-font text-center"
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
            @click="addCard"
          >
            <div
              class="spinner-border"
              role="status"
            >
              <span class="sr-only">در حال بررسی</span>
            </div>
            <span class="btn-text d-flex aic w-100 jcb">
              ثبت قسطاکارت جدید
            </span>
          </button>
        </div>
      </template>
    </modal>

    <!-- Edit Modal -->
    <modal
      v-show="editModal"
      title="ویرایش"
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

    <!-- History Modal -->
    <modal
      v-show="txModal"
      title="تاریخچه شارژ"
      @close="txModal = false"
    >
      <template #body>
        <div
          v-if="txModalLoading"
          class="page-loading"
        >
          <div class="spinner-border" />
        </div>
        <template v-else>
          <div
            v-for="(tx, txIndex) in txModalData"
            :key="txIndex"
            :class="[
              'p-3 rounded mb-3',
              tx.status === 1 ? 'light-success-bg' : 'light-danger-bg'
            ]"
          >
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <strong class="price-style">
                  {{ tx.amount | moneySeperate }}
                  <small class="opa-5">
                    تومان
                  </small>
                </strong>
                <span v-if="tx.type">
                  ({{
                    tx.type === 'charge'
                      ? 'شارژ'
                      : tx.type === 'extra'
                        ? 'شارژ اضافی'
                        : null
                  }})
                </span>
              </div>
              <small>
                {{ tx.charged_at | jDate }}
                •
                {{ tx.charged_at | jTime }}
              </small>
            </div>
          </div>
        </template>
      </template>

      <template #footer>
        <div class="d-flex w-100 justify-content-between">
          <button
            class="btn btn-light rounded-pill"
            @click="txModal = false"
          >
            انصراف
          </button>

          <button
            type="button"
            class="btn btn-primary rounded-pill"
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

    <!-- Bon-Card Modal -->
    <modal
      v-show="statementModal"
      title="گردش بن‌ کارت"
      @close="statementModal = false"
    >
      <template #body>
        <div
          v-if="statementModalLoading"
          class="page-loading"
        >
          <div class="spinner-border" />
        </div>
        <template v-else>
          <form-builder
            v-model="statementModalForm"
            :form-data="statementModalData"
          />
        </template>
      </template>

      <template #footer>
        <div class="d-flex w-100 justify-content-between">
          <button
            class="btn btn-light rounded-pill"
            @click="statementModal = false"
          >
            انصراف
          </button>

          <button
            type="button"
            class="btn btn-primary rounded-pill"
            :class="statementLoading ? 'btn-loading' : ''"
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

    <!-- Card issuance -->
    <modal
      v-show="cardIssuanceModal"
      title="کارت‌هایی که باید صادر شوند"
      @close="cardIssuanceModal = false"
    >
      <template #body>
        <label>
          سری کارت
        </label>
        <input
          v-model="cardIssuanceSeries"
          class="form-control form-control-lg"
        >
      </template> 

      <template #footer>
        <div class="d-flex w-100 justify-content-between">
          <button
            class="btn btn-light rounded-pill"
            @click="cardIssuanceModal = false"
          >
            انصراف
          </button>

          <g-button
            ref="cardIssuanceDownloadButton"
            text="دانلود"
            sm
            color="primary"
            @click.native="cardIssuanceDownload"
          />
        </div>
      </template>
    </modal>

    <!-- Group Upload -->
    <modal
      v-show="groupUploadModal"
      title="آپلود دسته‌ جمعی"
      @close="groupUploadModal = false"
    >
      <template #body>
        <label>
          سری کارت
        </label>
        <input
          v-model="groupUploadSeries"
          class="form-control form-control-lg"
        >

        <div class="mt-3">
          <label
            for="formFileMultiple"
            class="form-label"
          >
            انتخاب فایل
          </label>
          <input
            id="import"
            ref="file"
            class="form-control"
            type="file"
            @change="file = $refs.file.files[0]"
          >
        </div>
      </template>

      <template #footer>
        <div class="d-flex w-100 justify-content-between">
          <button
            class="btn btn-light rounded-pill"
            @click="groupUploadModal = false"
          >
            انصراف
          </button>

          <g-button
            ref="groupUploadButton"
            text="آپلود"
            sm
            color="primary"
            @click.native="groupUpload"
          />
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
import { api, wrapper } from '@/global/services';

import FormValidator from '@/global/FormValidator';

export default {
  name: 'CardsPage',

  metaInfo: {
    title: 'قسطا کارت‌ها'
  },

  mixins: [FilteringList, FormValidator],

  data() {
    return {
      chargeModal: false,
      modalLoading: false,
      currentModalEndpoint: '',
      currentModalMethod: '',
      chargeModalLoading: false,
      chargeModalData: {},
      chargeAmount: '',

      /**
       * Edit modal
       */
      editModal: false,
      editModalLoading: false,
      editModalData: [],
      editForm: {},
      editLoading: false,

      /**
       * Add cart modal
       */
      addModal: false,
      addLoading: false,
      addData: {
        card_number: '',
        mobile: '',
        series: ''
      },

      /**
       * TX modal
       */
      txModal: false,
      txModalLoading: false,
      txModalData: [],

      /**
       * TX modal
       */
      statementModal: false,
      statementModalLoading: false,
      statementModalData: [],
      statementModalForm: {},
      statementLoading: false,

      /**
       * Card Issuance modal
       */
      cardIssuanceModal: false,
      cardIssuanceSeries: '',

      /**
       * Group Upload modal
       */
      groupUploadModal: false,
      file: '',
      groupUploadSeries: ''
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
        this.modalLoading = true;
        this[payload.modal_name] = true;
        this.currentModalEndpoint = payload.endpoint;
        this.currentModalMethod = payload.method;

        if (payload.modal_name === 'txModal') {
          this.txModalLoading = true;

          Custom.get(payload.endpoint)
            .then(r => {
              this.txModalLoading = false;
              const { result } = r.data;
              this.txModalData = result;
            })
            .catch(e => {
              this.$alerts.errHandle(e);
              this.txModalLoading = false;
            });
        }

        if (payload.modal_name === 'statementModal') {
          this.statementModalLoading = true;

          Custom.get(payload.endpoint)
            .then(r => {
              const { result } = r.data;
              this.statementModalData = result;
              this.statementModalLoading = false;
            })
            .catch(e => {
              this.$alerts.errHandle(e);
              this.statementModalLoading = false;
            });
        }

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

        if (payload.modal_name === 'chargeModal') {
          Custom.get(this.currentModalEndpoint)
            .then(r => {
              const { card_number, mobile, name } = r.data.result;
              this.chargeModalData = { card_number, mobile, name };
              this.modalLoading = false;
            })
            .catch(e => {
              this.$alerts.errHandle(e);
              this.modalLoading = false;
            });
        }
      }
    },

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

    chargeCard() {
      if (
        confirm('آیــــــــــا از شارژ این شماره کارت مطمئن هستیـــــــــد؟؟؟؟')
      ) {
        this.chargeModalLoading = true;

        Custom.post(`${this.currentModalEndpoint}/charge`, {
          amount: this.chargeAmount,
          card_number: this.chargeModalData.card_number
        })
          .then(() => {
            this.chargeModalLoading = false;
            this.chargeModal = false;
          })
          .catch(e => {
            this.$alerts.errHandle(e);
            this.chargeModalLoading = false;
          });
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
        Admin.cards
          .export(queries)
          .then(() => {
            this.loading = false;
          })
          .catch(e => {
            this.$alerts.errHandle(e, 'static');
            this.loading = false;
          });
      } else {
        Admin.cards
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

    addCardModal() {
      this.addModal = true;
    },

    addCard() {
      this.addLoading = true;
      Admin.cards
        .create(this.addData)
        .then(r => {
          this.addLoading = false;

          if (r.data.status) {
            this.addModal = false;
            this.loadData();

            this.$alerts.show({
              msg: 'قسطاکارت با موفقیت ساخته شد',
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
    },

    async cardIssuanceDownload() {
      this.$refs.cardIssuanceDownloadButton.loading('start');
      const { data } = await wrapper(
        api.custom.post('admin/cards/export-cards-to-print', {
          series: this.cardIssuanceSeries
        })
      );

      this.$refs.cardIssuanceDownloadButton.loading('end');
      if (data) {
        this.cardIssuanceModal = false;
      }
    },

    async groupUpload() {
      this.$refs.groupUploadButton.loading('start');

      let formData = new FormData();
      formData.append('file', this.file);
      formData.append('series', this.groupUploadSeries);

      const { data } = await wrapper(
        api.custom.post('admin/cards/import-cards-to-submit', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
      );

      this.$refs.groupUploadButton.loading('end');
      if (data) {
        this.groupUploadModal = false;
      }
    },

    checkDate() {
      const file = this.file;
      console.log(new Date(file.lastModified));
    }
  }
};
</script>

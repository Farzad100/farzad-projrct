<template>
  <div>
    <div
      v-if="loading"
      class="page-loading"
    >
      <div class="spinner-border" />
    </div>

    <div v-else>
      <!-- Action Buttons -->
      <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex">
          <button
            class="btn btn-sm btn-light rounded-pill mr-2"
            @click="$refs.note.$refs.addNoteModal.openModal('new')"
          >
            <i class="far fa-sticky-note pl-2" />
            یادداشت
          </button>

          <button
            class="btn btn-sm btn-light rounded-pill mr-2"
            @click="smsModal = true"
          >
            <i class="far fa-comment-lines pl-2" />
            ارسال پیام
          </button>

          <button
            class="btn btn-sm btn-light rounded-pill mr-2"
            @click="allDatesInfoModal = true"
          >
            <i class="far fa-calendar-day pl-2" />
            همه تاریخ‌ها
          </button>

          <button
            class="btn btn-sm btn-light text-primary rounded-pill mr-2"
            @click="editModalOpen"
          >
            <i class="far fa-edit pl-2" />
            ویرایش
          </button>

          <button
            class="btn btn-sm btn-light text-danger rounded-pill mr-2"
            @click="deleteOrgan"
          >
            <i class="far fa-ban pl-2" />
            حذف
          </button>
        </div>

        <div class="btn-group btn-group-sm btn-pill d-none d-lg-flex mr-2">
          <input
            id="inactive"
            v-model="organ.status"
            value="inactive"
            type="radio"
            class="btn-check"
            name="activity"
            autocomplete="off"
            checked
          >
          <label
            class="btn"
            for="inactive"
          >
            غیر فعال
          </label>

          <input
            id="active"
            v-model="organ.status"
            value="active"
            type="radio"
            class="btn-check"
            name="activity"
            autocomplete="off"
          >
          <label
            class="btn"
            for="active"
          >
            فعال
          </label>
        </div>
      </div>

      <div class="row py-4">
        <!-- RIGHT -->
        <div class="col-12 col-lg-8">
          <single-info-box
            :data="organ"
            type="organ"
            class="mb-4"
          />

          <!-- Docs -->
          <template v-if="docs.length">
            <h5 class="special-font mt-5 mb-3 font-weight-bold opa-5">
              بررسی مدارک
            </h5>

            <div
              v-for="(doc, i) in docs"
              :key="i"
            >
              <uploader
                v-if="doc.status === 'void' || doc.status === 'rejected'"
                :file-name="doc.title"
                :skip-url="doc.skip_url"
                :description="doc.description"
                :reject-msg="doc.status === 'rejected' ? doc.message : null"
                :index="i"
                :info="{
                  name: doc.name,
                  is_bank_needed: doc.is_bank_needed
                }"
                class="mt-3"
                :option="{
                  url: doc.url,
                  concatUrl: doc.concatUrl,
                  maxFiles: doc.maxFiles,
                  acceptedFiles: doc.acceptedFiles ? doc.acceptedFiles : null
                }"
              />

              <div
                v-else-if="doc.status === 'pending'"
                class="doc-alert _checking mt-3"
              >
                <h4 class="special-font m-0">
                  {{ doc.title }}
                </h4>

                <span
                  v-if="role !== 'admin'"
                  class="_status"
                >
                  {{ doc.message }}

                  <div class="icon">
                    <i class="fas fa-barcode-scan" />
                  </div>
                </span>

                <div
                  v-else
                  class="d-flex align-items-center"
                >
                  <g-button
                    :text="`دانلود - ${doc.format}`"
                    color="outline-primary"
                    sm
                    class="ml-2"
                    @click.native="downloadCurrentDoc(doc)"
                  />
                  
                  <g-button
                    text="بررسی مدارک"
                    color="warning"
                    sm
                    @click.native="checkDoc(doc)"
                  />
                </div>
              </div>

              <div
                v-else-if="doc.status === 'verified'"
                class="doc-alert _success mt-3"
              >
                <h4 class="special-font m-0">
                  {{ doc.title }}
                </h4>

                <span
                  v-if="role !== 'admin'"
                  class="_status"
                >
                  {{ doc.message }}

                  <div class="icon">
                    <i class="fas fa-check" />
                  </div>
                </span>

                <div
                  v-else
                  class="d-flex align-items-center"
                >
                  <g-button
                    :text="`دانلود - ${doc.format}`"
                    color="outline-primary"
                    sm
                    class="ml-2"
                    @click.native="downloadCurrentDoc(doc)"
                  />

                  <g-button
                    text="بررسی مجدد"
                    color="primary"
                    sm
                    @click.native="checkDoc(doc)"
                  />
                </div>
              </div>
            </div>
          </template>
        </div>

        <!-- LEFT -->
        <div class="col-12 col-lg-4">
          <user-info
            :data="organ"
            type="agent"
            class="mb-4"
          />

          <user-info
            :data="organ"
            type="company"
            class="mb-4"
          />

          <!-- Notes -->
          <small-note
            ref="note"
            type="organ"
            :notes="notes"
          />
        </div>
      </div>
    </div>

    <!-- Edit Modal -->
    <modal
      v-show="editModal"
      size="lg"
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
          <form-builder
            v-model="modalFormModels"
            :form-data="openModalForm"
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
            @click="editAction"
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

    <!-- Send SMS Modal -->
    <modal
      v-show="smsModal"
      title="ارسال پیام به کاربر"
      @close="smsModal = false"
    >
      <template #body>
        <label>متن پیام</label>
        <textarea
          v-model="smsText"
          rows="3"
          class="form-control form-control-lg"
        />
      </template>

      <template #footer>
        <div class="d-flex w-100 justify-content-between">
          <button
            class="btn btn-light rounded-pill"
            @click="smsModal = false"
          >
            انصراف
          </button>

          <g-button
            ref="sms"
            text="ارسال پیام"
            type="button"
            color="primary"
            @click.native="sendMessage"
          />
        </div>
      </template>
    </modal>

    <!-- Check Docs Modal -->
    <modal
      v-show="docsCheckModal"
      :title="docsCheckModalName"
      @close="docsCheckModal = false"
    >
      <template #body>
        <!-- Download Doc -->
        <button
          class="btn btn-light d-flex justify-content-between w-100 py-3"
          @click="downloadCurrentDoc(null)"
        >
          <div class="text-right">
            <span class="special-font font-weight-bold d-block">
              دانلود
              {{ docsCheckModalName }}
            </span>
            <!-- <small class="opa-5">
                تصویر
              </small> -->
          </div>

          <div
            v-if="docsCheckModalFileLinksLoading"
            class="spinner-border d-block"
            role="status"
          />

          <i
            v-else
            class="far fa-cloud-download fa-lg"
          />
        </button>

        <!-- Accept Doc -->
        <div class="light-success-bg mt-4 py-4 px-3">
          <button
            class="btn btn-success rounded-pill w-100"
            :class="docsCheckModalAcceptLoading ? 'btn-loading' : ''"
            @click="acceptDoc"
          >
            <div
              class="spinner-border"
              role="status"
            >
              <span class="sr-only">در حال بررسی</span>
            </div>
            <span class="btn-text d-flex justify-content-center aic w-100 jcb">
              تایید این مدرک
            </span>
          </button>
        </div>

        <!-- Reject Doc -->
        <div class="light-danger-bg border-top border-dark py-4 px-3">
          <label>دلیل رد سفارش</label>
          <textarea
            v-model="docsCheckModalRejectReason"
            class="form-control mt-2"
            rows="3"
          />
          <p class="my-4 special-font font-weight-light">
            قسطا،
            <br>
            <span class="font-weight-bold">
              کاربر گرامی
            </span>
            ، تصویر
            <span class="font-weight-bold">
              {{ docsCheckModalName }}
            </span>
            شما به دلیل
            <span class="font-weight-bold">
              {{
                docsCheckModalRejectReason ? docsCheckModalRejectReason : '...'
              }}
            </span>
            رد شد. لطفاً به پنل فروشگاهی خود مراجعه کرده و مجدداً آن را بارگذاری
            نمایید.
            <br>
            باتشکر
          </p>

          <button
            class="btn btn-danger rounded-pill w-100"
            :class="docsCheckModalRejectLoading ? 'btn-loading' : ''"
            @click="rejectDoc"
          >
            <div
              class="spinner-border"
              role="status"
            >
              <span class="sr-only">در حال بررسی</span>
            </div>
            <span class="btn-text d-flex justify-content-center aic w-100 jcb">
              رد این مدرک
            </span>
          </button>

          <!-- <div
            class="form-check form-switch d-flex justify-content-center mt-3"
          >
            <label
              class="form-check-label ml-3"
              for="flexSwitchCheckDefault"
            >
              بیصدا
            </label>
            <input
              id="flexSwitchCheckDefault"
              v-model="docsCheckModalRejectSilent"
              class="form-check-input"
              type="checkbox"
            >
          </div> -->
        </div>
      </template>

      <template #footer>
        <div class="d-flex w-100 justify-content-center">
          <button
            class="btn btn-light rounded-pill"
            @click="docsCheckModal = false"
          >
            انصراف
          </button>
        </div>
      </template>
    </modal>

    <!-- All Dates Modal -->
    <modal
      v-show="allDatesInfoModal"
      title="تاریخ‌ها"
      @close="allDatesInfoModal = false"
    >
      <template #body>
        <div class="list-view border w-100 rounded m-0">
          <div
            v-if="organ.created_at"
            class="list-view-item"
          >
            <div class="d-flex aic flex-wrap">
              <div class="title">
                <span class="font-weight-light">ثبت سازمان</span>
              </div>
              <div class="mr-auto">
                <strong>
                  {{ organ.created_at | jDate }}
                </strong>
                <small class="opa-7 mr-1">
                  {{ organ.created_at | jTime }}
                </small>
              </div>
            </div>
          </div>

          <div
            v-if="organ.docs_uploaded_at"
            class="list-view-item"
          >
            <div class="d-flex aic flex-wrap">
              <div class="title">
                <span class="font-weight-light">بارگذاری مدارک</span>
              </div>
              <div class="mr-auto">
                <strong>
                  {{ organ.docs_uploaded_at | jDate }}
                </strong>
                <small class="opa-7 mr-1">
                  {{ organ.docs_uploaded_at | jTime }}
                </small>
              </div>
            </div>
          </div>

          <div
            v-if="organ.docs_accepted_at"
            class="list-view-item"
          >
            <div class="d-flex aic flex-wrap">
              <div class="title">
                <span class="font-weight-light">تایید مدارک</span>
              </div>
              <div class="mr-auto">
                <strong>
                  {{ organ.docs_accepted_at | jDate }}
                </strong>
                <small class="opa-7 mr-1">
                  {{ organ.docs_accepted_at | jTime }}
                </small>
              </div>
            </div>
          </div>

          <div
            v-if="organ.start_at"
            class="list-view-item"
          >
            <div class="d-flex aic flex-wrap">
              <div class="title">
                <span class="font-weight-light">شروع فعالیت</span>
              </div>
              <div class="mr-auto">
                <strong>
                  {{ organ.start_at | jDate }}
                </strong>
                <small class="opa-7 mr-1">
                  {{ organ.start_at | jTime }}
                </small>
              </div>
            </div>
          </div>
        </div>
      </template>

      <template #footer>
        <div class="d-flex w-100 justify-content-center">
          <button
            class="btn btn-light rounded-pill"
            @click="allDatesInfoModal = false"
          >
            انصراف
          </button>
        </div>
      </template>
    </modal>
  </div>
</template>

<script>
import Admin from '@/api/admin';
import StatusColors from '@/global/StatusColors';
import Custom from '@/api/custom';
import { mapState } from 'vuex';

export default {
  name: 'OrgansPage',

  components: {
    UserInfo: () => import('@/components/dashboard/UserInfo/Index'),
    SingleInfoBox: () => import('@/components/dashboard/SingleInfoBox/Index'),
    SmallNote: () => import('@/components/dashboard/SmallNote/Index.vue'),
  },

  metaInfo() {
    return {
      title: `سازمان: ${this.organ.fame ? this.organ.fame : this.organ.name}`
    };
  },

  mixins: [StatusColors],

  data() {
    return {
      organ: {},
      docs: [],
      loading: true,

      modalFormModels: {},
      openModalForm: [],

      editModal: false,
      editModalForm: [],
      editLoading: false,
      editModalLoading: false,

      /**
       * Notes
       * -------------------------------------
       */
      notesModal: false,
      notes: [],
      noteProccess: '',

      /**
       * SMS
       * -------------------------------------
       */
      smsModal: false,
      smsText: '',

      /**
       * Doc Check
       * -------------------------------------
       */
      docsCheckModal: false,
      docsCheckModalLoading: false,
      docsCheckModalName: '',
      docsCheckModalRejectReason: '',
      docsCheckModalFileLinks: null,
      docsCheckModalFileLinksLoading: false,
      docsCheckModalAcceptLoading: false,
      docsCheckModalRejectLoading: false,
      docsCheckModalRejectSilent: false,
      allDatesInfoModal: false
    };
  },

  computed: {
    ...mapState('dashboard', ['role'])
  },

  watch: {
    'organ.status'(n, o) {
      if (o !== undefined) {
        if (n === 'active' | n === 'inactive') {
          const status = n === 'active' ? 'active' : 'inactive';
          this.activeToggle(status);
        }
      }
    }
  },

  created() {
    this.loadData();
    this.loadNotes();
  },

  methods: {
    async sendMessage() {
      this.$refs.sms.loading('start');

      try {
        const r = await Admin.organs.sendMessage(this.$route.params.id, {
          message: this.smsText
        });
        if (r.data.status) {
          this.$alerts.show({
            msg: 'پیام با موفقیت ارسال شد',
            type: 'success',
            style: 'float'
          });
        } else {
          this.$alerts.show({
            msg: 'پیام ارسال نشد',
            type: 'danger',
            style: 'float'
          });
        }
        this.$refs.sms.loading('end');
      } catch (e) {
        this.$refs.sms.loading('end');
        this.$alerts.errHandle(e);
      }
    },

    loadData() {
      Admin.organs
        .single(this.$route.params.id)
        .then(r => {
          this.loading = false;
          this.isActive = r.data.result.status;
          this.organ = r.data.result;
          this.docs = r.data.result.docs;
        })
        .catch(e => {
          this.loading = false;
          this.$alerts.errHandle(e);
        });
    },

    editModalOpen() {
      this.editModal = true;
      this.editModalLoading = true;

      Admin.organs
        .editForm(this.$route.params.id)
        .then(r => {
          this.editModalLoading = false;
          this.openModalForm = r.data.result;

          this.openModalForm.forEach(obj => {
            obj.fields.forEach(f => {
              this.modalFormModels = {
                ...this.modalFormModels,
                [f.v_model]: f.value
              };
            });
          });
        })
        .catch(e => {
          this.editModalLoading = false;
          this.$alerts.errHandle(e);
        });
    },

    activeToggle(status) {
      if (
        confirm(
          `آیا از ${
            status === 'active' ? 'فعال' : 'غیرفعال'
          } کردن این سازمان اطمینان دارید؟`
        )
      ) {
        return new Promise((resolve, reject) => {
          Admin.organs
            .changeActivity(this.$route.params.id, status)
            .then(() => {
              resolve();
              this.loadData();
            })
            .catch(e => {
              this.$alerts.errHandle(e);
              reject();
            });
        });
      }
    },

    deleteOrgan() {
      if (confirm('آیا از حذف این سازمان اطمینان دارید؟')) {
        Admin.organs
          .delete(this.$route.params.id)
          .then(r => {
            if (r.data.status) {
              this.$router.push({
                name: 'dash-organs'
              });
            }
          })
          .catch(e => {
            this.$alerts.errHandle(e);
          });
      }
    },

    loadNotes() {
      Admin.notes.all(this.$route.params.id, 'organs').then(r => {
        const { result } = r.data;
        this.notes = result;
      });
    },

    deleteNote(id) {
      Admin.notes
        .delete(id)
        .then(r => {
          if (r.data.status) {
            this.notesModal = false;
            this.loadNotes();

            this.$alerts.show({
              msg: 'یادداشت با موفقیت حذف شد',
              type: 'success',
              style: 'float'
            });
          } else {
            this.$alerts.show({
              msg: 'مشکلی در حذف یادداشت بوجود آمد',
              type: 'danger',
              style: 'float'
            });
          }
        })
        .catch(e => {
          this.$alerts.errHandle(e);
          this.notesDeleteLoading = false;
        });
    },

    editNote() {
      Admin.notes
        .edit(this.newNote.id, {
          title: this.newNote.title,
          caption: this.newNote.caption
        })
        .then(r => {
          this.notesLoading = false;

          if (r.data.status) {
            this.notesModal = false;
            this.loadNotes();

            this.$alerts.show({
              msg: 'یادداشت با موفقیت ویرایش شد',
              type: 'success',
              style: 'float'
            });
          } else {
            this.$alerts.show({
              msg: 'مشکلی در ویرایش یادداشت بوجود آمد',
              type: 'danger',
              style: 'float'
            });
          }
        })
        .catch(e => {
          this.$alerts.errHandle(e);
          this.notesLoading = false;
        });
    },

    editAction() {
      this.editLoading = true;

      Admin.organs
        .edit(this.$route.params.id, this.modalFormModels)
        .then(r => {
          if (r.data.status) {
            this.$alerts.show({
              msg: 'سازمان با موفقیت ویرایش شد',
              type: 'success',
              style: 'float'
            });
            this.editModal = false;
            this.editLoading = false;

            this.loadData();
          } else {
            this.$alerts.show({
              msg: 'ویرایش انجام نشد !',
              type: 'danger',
              style: 'float'
            });
            this.editLoading = false;
          }
        })
        .catch(e => {
          this.editLoading = false;
          this.$alerts.errHandle(e);
        });
    },

    checkDoc(payload) {
      const { title, link } = payload;
      this.docsCheckModal = true;
      this.docsCheckModalName = title;
      this.docsCheckModalFileLinks = link;
    },

    acceptDoc() {
      this.docsCheckModalAcceptLoading = true;

      Custom.post(`${this.docsCheckModalFileLinks}/accept`)
        .then(r => {
          if (r.data.status) {
            this.docsCheckModalAcceptLoading = false;
            this.docsCheckModal = false;
            this.loadData();

            this.$alerts.show({
              msg: `${this.docsCheckModalName} با موفقیت تایید شد`,
              type: 'success',
              style: 'float'
            });
          }
        })
        .catch(e => {
          this.$alerts.errHandle(e);
          this.docsCheckModalAcceptLoading = false;
        });
    },

    rejectDoc() {
      this.docsCheckModalRejectLoading = true;

      Custom.post(`${this.docsCheckModalFileLinks}/reject`, {
        silent: this.docsCheckModalRejectSilent,
        reason: this.docsCheckModalRejectReason
      })
        .then(r => {
          if (r.data.status) {
            this.docsCheckModalRejectLoading = false;
            this.docsCheckModal = false;
            this.loadData();

            this.$alerts.show({
              msg: `${this.docsCheckModalName} با موفقیت رد شد`,
              type: 'success',
              style: 'float'
            });
          }
        })
        .catch(e => {
          this.$alerts.errHandle(e);
          this.docsCheckModalRejectLoading = false;
        });
    },

    openModal(type, index) {
      this.notesModal = true;

      if (type === 'new') {
        this.noteProccess = 'adding';

        this.newNote.id = this.$route.params.id;
        this.newNote.title = '';
        this.newNote.caption = '';
      } else if (type === 'edit') {
        this.noteProccess = 'editing';

        const { id, title, caption } = this.notes[index];
        this.newNote.id = id;
        this.newNote.title = title;
        this.newNote.caption = caption;
      }
    },

    downloadCurrentDoc(payload) {
      this.docsCheckModalFileLinksLoading = true;

      const link = payload ? payload.link : this.docsCheckModalFileLinks;

      Custom.get(link)
        .then(r => {
          window.open(r.data);
          this.docsCheckModalFileLinksLoading = false;
        })
        .catch(e => {
          this.docsCheckModalFileLinksLoading = false;
          this.$alerts.errHandle(e);
        });
    }
  }
};
</script>

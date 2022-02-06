<template>
  <div>
    <table-list
      v-if="role === 'admin'"
      list-name="اطلاعیه‌ها"
      :table-list="td"
      :pagination="pagination"
      :loading="loading"
      add-button="اطلاعیه"
      @addTrigger="addModal = true"
      @onPageChange="changePage"
      @buttonClick="actionsHandle"
      @onSort=" sort"
    />

    <table-list
      v-else
      list-name="اطلاعیه‌ها"
      :table-list="td"
      :pagination="pagination"
      :loading="loading"
      @onPageChange="changePage"
      @buttonClick="actionsHandle"
      @onSort=" sort"
    />

    <modal
      v-show="addModal"
      title="ایجاد اطلاعیه جدید"
      @close="addModal = false"
    >
      <template #body>
        <div class="row">
          <div class="col-12 mb-4">
            <label>عنوان</label>
            <input
              v-model="addDataModels.title"
              class="form-control form-control-lg"
            >
          </div>
          <div class="col-12 mb-4">
            <label>نوع</label>
            <select
              v-model="addDataModels.type"
              class="form-select form-select-lg"
            >
              <option value="all">
                همه
              </option>
              <option value="shops">
                فروشگاه‌ها
              </option>
              <option value="organs">
                سازمان‌ها
              </option>
              <option value="users">
                مشتری‌ها
              </option>
            </select>
          </div>
          <div class="col-12 mb-4">
            <label>شماره موبایل</label>
            <input
              v-model="addDataModels.mobile"
              class="form-control form-control-lg ltr estedad-font"
            >
          </div>
          <div class="col-12 mb-4">
            <label>متن</label>
            <textarea
              v-model="addDataModels.caption"
              rows="5"
              class="form-control form-control-lg"
            />
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
              ثبت اطلاعیه جدید
            </span>
          </button>
        </div>
      </template>
    </modal>
  </div>
</template>

<script>
import Custom from '@/api/custom';
import Inbox from '@/api/inbox';
import { mapState } from 'vuex';
import FilteringList from '@/global/FilteringList';

export default {
  name: 'InboxPage',

  metaInfo: {
    title: 'اطلاعیه‌ها',
  },
  mixins: [FilteringList],

  data() {
    return {
      filterShow: false,
      pagination: {},
      td: [],
      loading: true,

      /**
       * New 
       */
      addModal: false,
      addLoading: false,
      addDataModels: {
        title: '',
        type: 'all',
        mobile: '',
        caption: ''
      }
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
    loadData() {
      Inbox.get.all(`${this.role !== 'user' ? '/'+this.role : ''}`)
        .then(r => {
          this.td = r.data.data.reverse();
          this.loading = false;

          this.pagination = {
            current_page: r.data.current_page,
            last_page: r.data.last_page,
            next_page_url: r.data.next_page_url,
            path: r.data.path,
            per_page: r.data.per_page,
            prev_page_url: r.data.prev_page_url,
            total: r.data.total,
          };
        })
        .catch(e => {
          this.$alerts.errHandle(e);
          this.loading = false;
        });
    },

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
      }
    },

    // TODO: change it
    add() {
      this.addLoading = true;

      Inbox.create(this.addDataModels)
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
    },
  }
};
</script>
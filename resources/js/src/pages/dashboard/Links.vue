<template>
  <div>
    <table-list
      list-name="لینک های کوتاه"
      :table-list="td"
      :pagination="pagination"
      :loading="loading"
      add-button="لینک کوتاه"
      @addTrigger="addModal = true"
      @onPageChange="changePage"
      @buttonClick="actionsHandle"
      @onSort=" sort"
    /> 

    <modal
      v-show="addModal"
      title="لینک کوتاه جدید"
      @close="addModal = false"
    >
      <template #body>
        <div class="row">
          <div class="col-12">
            <label>لینک طولانی</label>
            <input
              v-model="addData.url"
              type="text"
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
              ثبت لینک کوتاه جدید
            </span>
          </button>
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
  name: 'LinksPage',

  metaInfo: {
    title: 'لینک‌های کوتاه',
  },

  mixins: [FilteringList],

  data() {
    return {
      addModal: false,
      addLoading: false,
      addData: {
        url: ''
      }
    };
  },

  watch: {
    role() {
      this.loadData();
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
      }
    },

    loadData() {
      let queries = '';

      Object.keys(this.$route.query).map((i) => {
        queries = queries + `${i}=${this.$route.query[i]}&`;
      });

      queries = queries.substring(0, queries.lastIndexOf('&'));

      this.loading = true;

      Admin.links
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
      this.addLoading = true;
      
      Admin.links.create(this.addData)
        .then(r => {
          this.addLoading = false;

          if (r.data.status) {
            this.addModal = false;
            this.loadData();

            this.$alerts.show({
              msg: 'لینک کوتاه با موفقیت ساخته شد',
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
  },
};
</script>
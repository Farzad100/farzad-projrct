<template>
  <div>
    <table-list
      list-name="نظرات کاربران"
      :table-list="td"
      :pagination="pagination"
      :loading="loading"
      @onPageChange="changePage"
      @buttonClick="actionsHandle"
      @onSort=" sort"
    />

    <!-- reply Modal -->
    <modal
      v-show="replyModal"
      title="پاسخ"
      @close="replyModal = false"
    >
      <template #body>
        <div
          v-if="replyModalLoading"
          class="page-loading"
        >
          <div class="spinner-border" />
        </div>
        <template v-else>
          <form-builder
            v-model="replyData"
            :form-data="replyModalData"
          />
        </template>
      </template>

      <template #footer>
        <div class="d-flex justify-content-between w-100">
          <button
            class="btn btn-light rounded-pill"
            @click="replyModal = false"
          >
            انصراف
          </button>

          <g-button
            ref="reply"
            text="اعمال تغییرات"
            type="button"
            color="primary"
            @click.native="reply"
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
  name: 'CommentsPage',

  metaInfo: {
    title: 'نظرات',
  },
  mixins: [FilteringList],


  data() {
    return {
      td: [],
      pagination: {},
      loading: false,

      replyData: {},
      replyModal: false,
      replyModalData: [],
      replyModalLoading: false,

      currentModalEndpoint: '',
      currentModalMethod: '',
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

        if (payload.modal_name === 'replyModal') {
          this.replyModalLoading = true;

          Custom.get(payload.endpoint)
            .then(r => {
              const { result } = r.data;
              this.replyModalData = result;
              this.replyModalLoading = false;
            })
            .catch(e => {
              this.$alerts.errHandle(e);
              this.replyModalLoading = false;
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

      Admin.comments
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

    reply() {
      this.$refs.reply.loading('start');

      Custom[this.currentModalMethod](this.currentModalEndpoint, this.replyData)
        .then(r => {
          this.$refs.reply.loading('end');

          if (r.data.status) {
            this.loadData();
            this.replyModal = false;
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
          this.$refs.reply.loading('end');
        });
    },

    changePage(payload) {
      this.filter.page = payload;
      this.addFiltersToRoute();
    },
  },
};
</script>
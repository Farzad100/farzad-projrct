<template>
  <div>
    <div
      v-if="loading"
      class="page-loading"
    >
      <div class="spinner-border" />
    </div>

    <div
      v-else
      class="col-12 col-md-10 col-lg-8 mx-auto"
    >
      <div class="d-flex align-items-center">
        <router-link
          to="/dashboard/inbox"
          class="btn btn-light btn-sm rounded-pill ml-3"
        >
          <i class="far fa-arrow-right pl-2" />
          بازگشت
        </router-link>
        <h4>
          {{ data.title }}
        </h4>
      </div>

      <div class="border rounded mt-4">
        <p class="p-3 font-weight-light mb-0">
          {{ data.caption }}
        </p>
      </div>
      <small class="font-weight-light opa-7 mt-3 d-block">
        تاریخ ارسال:
        <strong>{{ data.created_at }}</strong>
      </small>
    </div>
  </div>
</template>

<script>
import Inbox from '@/api/inbox';
import { mapState } from 'vuex';

export default {
  name: 'InboxSinglePage',

  data() {
    return {
      data: null,
      loading: true
    };
  },

  computed : {
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
      Inbox.get.single(`${this.role !== 'user' ? '/'+this.role : ''}`, this.$route.params.id)
        .then(r => {
          this.$root.getInboxCount();
          this.data = r.data.result;
          this.loading = false;

          this.$metaInfo.title = `اطلاعیه: ${this.data.title}`;

        })
        .catch(e => {
          this.$alerts.errHandle(e);
          this.loading = false;
        });
    }
  }
};
</script>
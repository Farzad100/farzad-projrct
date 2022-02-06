import { mapState } from 'vuex';
import Banks from '@/data/Banks';
import { api, wrapper } from '@/global/services';

export default {
  name: 'Docs',

  props: {
    type: {
      type: String
    }
  },

  data() {
    return {
      cta: null,
      docs: null,
      loading: true,
      Banks,

      /**
       * Doc Check
       */
      docInfo: {},
      docsCheckModal: false,
      docsCheckModalLoading: false,
      docsCheckModalRejectReason: '',
      docsCheckModalFileLinks: null,
      docsCheckModalFileLinksLoading: false,
      docsCheckModalAcceptLoading: false,
      docsCheckModalRejectLoading: false,
      docsCheckModalRejectSilent: false,
    };
  },

  computed: {
    ...mapState('dashboard', ['role']),

    GET_DOCS() {
      return this.role === 'admin'
        ? api.Admin.orders.docs
        : this.role === 'user'
          ? api.Orders.get.docs
          : this.role === 'organ'
            ? api.Organ.get.docs
            : this.role === 'shop' && this.type === 'shopAdmin'
              ? api.Shop.orders.docs
              : this.role === 'shop' && this.type !== 'shopAdmin'
                ? api.Shop.get.docs
                : null;
    }
  },

  created() {
    this.loadDocs();
  },

  methods: {
    async loadDocs(type, index) {
      const { data } = await wrapper(
        this.GET_DOCS(this.$route.params.id, this.role),
        'مشکل در بارگذاری مدارک پیش آمد'
      );

      this.loading = false;

      if (data) {
        const { cta, docs } = data.result;

        this.cta = cta;

        if (type === 'update') {
          this.$set(this.docs, index, docs[index]);
          this.$parent.loadData();
        } else {
          this.docs = docs;
        }
      }
    },

    async accept() {
      this.$refs.acceptButton.loading('start');

      const { data } = await wrapper(
        api.custom.post(`${this.docInfo.link}/accept`),
        'مشکلی در تایید مدارک پیش آمده است'
      );

      if (data) {
        this.$refs.acceptButton.loading('end');
        const { status } = data;

        if (status) {
          this.docsCheckModal = false;
          this.loadDocs().then(() => {
            this.$parent.loadData();
          });

          this.$alerts.show({
            msg: `${this.docInfo.title} با موفقیت تایید شد`,
            type: 'success',
            style: 'float'
          });
        }
      }
    },

    async reject() {
      this.$refs.rejectButton.loading('start');

      const { data } = await wrapper(
        api.custom.post(`${this.docInfo.link}/reject`, {
          silent: this.docsCheckModalRejectSilent,
          reason: this.docsCheckModalRejectReason
        }),
        'مشکلی در تایید مدارک پیش آمده است'
      );

      if (data) {
        this.$refs.rejectButton.loading('end');
        const { status } = data;

        if (status) {
          this.docsCheckModal = false;
          this.loadDocs().then(() => {
            this.$parent.loadData();
          });

          this.$alerts.show({
            msg: `${this.docInfo.title} با موفقیت رد شد`,
            type: 'success',
            style: 'float'
          });
        }
      }
    },

    async download(payload) {
      const link = payload ? payload.link : this.docInfo.link;

      const { data } = await wrapper(
        api.custom.get(link),
        'مشکلی در دانلود مدارک پیش آمده است'
      );

      if (data) {
        window.open(data);
      }
    },

    bankName(bankId) {
      const result = Banks.find(obj => obj.id == bankId);
      return result.fa;
    },

    check(payload) {
      this.docInfo = payload;
      this.docsCheckModal = true;
    }
  }
};

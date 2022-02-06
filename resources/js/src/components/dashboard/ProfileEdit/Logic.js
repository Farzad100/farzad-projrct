import { api, wrapper } from '@/global/services';

export default {
  name: 'EditForm',

  components: {
    Extra: () => import('./forms/Extra/Index'),
    UserInfo: () => import('./forms/UserInfo/Index'),
    WorkAddress: () => import('./forms/WorkAddress/Index'),
    HomeAddress: () => import('./forms/HomeAddress/Index'),
  },

  data() {
    return {
      tab: 'userInfo',
      allowToBack: [],
      formStatus: {},
      loading: true
    };
  },

  created() {
    this.getFormStatus();
  },

  methods: {
    async getFormStatus() {
      const { data } = await wrapper(
        api.Account.get.formsStatus()
      );

      this.loading = false;

      if (data) {
        const { result } = data;
        this.formStatus = result;
      }
    },

    selectTab(tabName) {
      this.tab = tabName;
    },

    backToOrder() {
      console.log(this.$route.query.fromOrder);
    }
  },
};

<template>
  <div>
    <organ-home v-if="role === 'organ'" />
    <router-view />
  </div>
</template>

<script>
import { mapState } from 'vuex';
import Custom from '@/api/custom';
import Cookies from 'js-cookie';

export default {
  name: 'DashboardPage',

  metaInfo: {
    title: 'داشبورد'
  },

  components: {
    organHome: () => import('@/components/dashboard/Home/Organ/Index'),
  },


  data() {
    return {
      onChangeBtn: null,
      currentModalEndpoint: '',
      currentModalMethod: ''
    };
  },

  computed: {
    ...mapState('dashboard', ['role'])
  },

  created() {
    if (!Cookies.get('first')) {
      Cookies.set('first', 'pashmak');
      window.location.reload();
    }

    if (this.$route.query.passChanged) {
      this.$alerts.show({
        msg: 'پسورد شما با موفقیت ویرایش شد',
        type: 'success',
        style: 'float'
      });
    }
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
                this.loadOrderPending();
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

      if (payload.type === 'straight') {
        Custom[payload.method](payload.endpoint)
          .then(r => {
            if (r.data.status) {
              window.location = r.data.result.url;
            }
          })
          .catch(e => {
            this.$alerts.errHandle(e);
          });
      }

      if (payload.type === 'modal') {
        this[payload.modal_name] = true;
        this.currentModalEndpoint = payload.endpoint;
        this.currentModalMethod = payload.method;
      }
    }
  }
};
</script>

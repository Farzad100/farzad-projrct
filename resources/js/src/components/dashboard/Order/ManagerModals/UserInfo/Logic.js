import { api, wrapper } from '@/global/services';
import stateList from '@/data/States';
import Seller from '@/api/seller';

export default {
  name: 'UserInfo',

  data() {
    return {
      compeleUserInfoModal: false,

      address: {
        home: {
          address: '',
          city: '',
          editable: true,
          phone: '',
          postal_code: '',
          state: '',
        },
        work: {
          address: '',
          city: '',
          editable: true,
          phone: '',
          postal_code: '',
          state: '',
        },
      },

      userInfo: {
        nid: '',
        birth: ''
      },

      stateList
    };
  },

  methods: {
    editUserNid() {
      this.$refs.editUserNid.loading('start');

      Seller.editUser(this.$route.params.id, {'nid':this.userInfo.nid})
        .then(r => {
          if (r.data.status) {
            this.$alerts.show({
              msg: 'کدملی ثبت شد',
              type: 'success',
              style: 'float'
            });
            this.$parent.loadData(); 
          } else {
            this.$alerts.show({
              msg: r.data.error.message,
              type: 'danger',
              style: 'float'
            });
          }

          this.$refs.editUserNid.loading('end');
        })
        .catch(e => {
          this.$alerts.errHandle(e);
          this.$refs.editUserNid.loading('end');
        });
    },

    async editUserBirth() {
      this.$refs.editUserBirth.loading('start');

      const { data } = await wrapper(
        api.Shop.editUser(
          this.$route.params.id,
          { 'birth':this.userInfo.birth }
        )
      );
      
      this.$refs.editUserBirth.loading('end');

      if (data) {
        const { status } = data;

        if (status) {
          this.$alerts.show({
            msg: 'تاریخ تولد ثبت شد',
            type: 'success',
            style: 'float'
          });
          this.$parent.loadData();
        }
      }
    },

    async editAddress(type) {
      if (type === 'home') {
        this.$refs.editAddressHome.loading('start');
      } else {
        this.$refs.editAddressWork.loading('start');
      }

      const work = {
        ...this.address.work,
        type: 'work'
      };

      const home = {
        ...this.address.home,
        type: 'home'
      };

      const { data } = await wrapper(
        api.Shop.editAddress(
          this.$route.params.id,
          type === 'home' ? home : work
        )
      );

      this.$refs.editAddressHome.loading('end');
      this.$refs.editAddressWork.loading('end');

      if (data) {
        const { status } = data;

        if (status) {
          this.$alerts.show({
            msg: 'آدرس کاربر ذخیره شد',
            type: 'success',
            style: 'float'
          });
        }
      }
    },

    openModal() {
      this.compeleUserInfoModal = true;
    },

    closeModal() {
      this.compeleUserInfoModal = false;
      window.location.reload();
    }
  }
};

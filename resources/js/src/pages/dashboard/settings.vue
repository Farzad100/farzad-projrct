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
      class="col-12 col-lg-10 border rounded mx-auto overflow-hidden"
    >
      <form-builder
        v-model="form"
        class="px-4"
        :form-data="formData"
      />

      <div class="border-top p-4 d-flex justify-content-end mt-5 bg-gray-light">
        <g-button
          ref="save"
          text="ذخیره تغییرات"
          color="success"
          @click.native="saveChanges"
        />
      </div>
    </div>
  </div>
</template>

<script>
/**
 * API Services
 */
import Admin from '@/api/admin';

export default {
  name: 'SettingPage',

  metaInfo: {
    title: 'تنطیمات',
  },

  data() {
    return {
      loading: true,

      form: {},
      formData: []
    };
  },

  created() {
    this.loadData();
  },
  
  methods: {
    loadData() {
      Admin.settings.get()
        .then(res => {
          const { result } = res.data;
          this.formData = result;

          this.loading = false;
        })
        .catch(err => {
          this.$alerts.errHandle(err);
          this.loading = false;
        });
    },

    saveChanges() {
      this.$refs.save.loading('start');

      Admin.settings.save(this.form)
        .then(res => {
          const { status } = res.data;

          if (status) {
            // Show success alert
            this.$alerts.show({
              msg: 'تغییرات با موفقیت ذخیره شده‌اند',
              type: 'success',
              style: 'float'
            });
          } else {
            // Show error alert
            this.$alerts.show({
              msg: 'مشکلی در ذخیره تغییرات پیش آمده',
              type: 'danger',
              style: 'float'
            });
          }

          this.$refs.save.loading('end');
        })
        .catch(err => {
          this.$alerts.errHandle(err);
          this.$refs.save.loading('end');
        });
    }
  }
};
</script>

<template>
  <div>
    <table-list
      list-name="درآمدها و کارمزدها"
      :table-list="td"
      :pagination="pagination"
      :loading="loading"
      @onPageChange="changePage"
      @buttonClick="actionsHandle"
    >
      <template #filter>
        <div class="w-100">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <button
              class="btn btn-light rounded-pill btn-sm"
              type="button"
              data-bs-toggle="collapse"
              data-bs-target="#collapseExample"
              aria-expanded="false"
              aria-controls="collapseExample"
            >
              <i class="far fa-filter ml-2" />
              فیلتر
              <i class="fal fa-caret-down mr-3" />
            </button>
          </div>
          <div
            id="collapseExample"
            class="collapse show"
          >
            <div class="bg-gray-light rounded p-4">
              <form
                class="row px-1"
                @submit.prevent="addFiltersToRoute"
              >
                <div class="col-12 col-md-4">
                  <div class="bg-white rounded p-4 shadow-sm">
                    <div class="row">
                      <div class="col-12 col-md-6 mb-3">
                        <label>
                          <small>مبلغ سفارش از</small>
                        </label>
                        <input
                          v-model="filter.amount_min"
                          type="text"
                          class="form-control"
                        >
                      </div>
                      <div class="col-12 col-md-6 mb-3">
                        <label>
                          <small>مبلغ سفارش تا</small>
                        </label>
                        <input
                          v-model="filter.amount_max"
                          type="text"
                          class="form-control"
                        >
                      </div>
                      <div class="col-12 col-md-6 mb-3">
                        <label>
                          <small>شماره سفارش</small>
                        </label>
                        <input
                          v-model="filter.oid"
                          type="text"
                          class="form-control ltr estedad-font"
                        >
                      </div>
                      <div
                        v-show="role === 'admin'"
                        class="col-12 col-md-6 mb-3"
                      >
                        <label>
                          <small>نام فروشگاه</small>
                        </label>
                        <input
                          v-model="filter.shop"
                          type="text"
                          class="form-control ltr estedad-font"
                        >
                      </div>
                      <div
                        v-show="role === 'admin'"
                        class="col-12 mb-3"
                      >
                        <label>
                          <small>شماره قسطا کارت</small>
                        </label>
                        <input
                          v-model="filter.nid"
                          type="text"
                          class="form-control"
                        >
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-12 col-md-4">
                  <div class="bg-white rounded p-4 shadow-sm">
                    <div class="row">
                      <div class="col-12 mb-3">
                        <g-date-picker
                          v-model="filter.from_date"
                          :years-from-now="5"
                          label="از تاریخ"
                          empty
                        />
                      </div>

                      <div class="col-12 mb-3">
                        <g-date-picker
                          v-model="filter.to_date"
                          :years-from-now="5"
                          label="تا تاریخ"
                          empty
                        />
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-12 d-flex justify-content-between mt-4 aic">
                  <button
                    type="button"
                    class="btn btn-light btn-sm rounded-pill"
                    @click="removeFiltersFromRoute"
                  >
                    حذف فیلتر
                  </button>
                  <div
                    v-if="role === 'admin'"
                    class="btn btn-primary btn-sm rounded-pill"
                    @click="loadAdminData(queries, 1)"
                  >
                    <i class="fas fa-file-excel ml-2" />
                    دریافت اکسل
                  </div>
                  <button
                    type="submit"
                    class="btn btn-success btn-sm rounded-pill"
                  >
                    اعمال فیلتر
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </template>
    </table-list> 
  </div>
</template>

<script>
import { mapState } from 'vuex';
import FilteringList from '@/global/FilteringList';
import { api, wrapper } from '@/global/services';

export default {
  name: 'Incomes',

  metaInfo: {
    title: 'درآمدها و کارمزدها',
  },

  mixins: [FilteringList],
  
  data() {
    return {
      loading: true
    };
  },

  computed: {
    ...mapState('dashboard', ['role'])
  },


  created() {
    this.loadData();
  },

  methods: {
    async loadData() {
      const { data } = await wrapper(
        api.custom.get(`${this.role}/commissions`),
        'مشکلی در دریافت اطلاعات پیش آمد'
      );

      if (data) {
        const { result } = data;
        this.td = result;
      }
    },
  },
};
</script>
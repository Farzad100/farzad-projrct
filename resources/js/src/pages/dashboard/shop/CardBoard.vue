<template>
  <div>
    <table-list
      list-name="کارتابل سفارش‌های سازمانی"
      :table-list="td"
      :pagination="pagination"
      :loading="loading"
      @onPageChange="changePage"
      @buttonClick="actionsHandle"
    /> 
  </div>
</template>

<script>
import FilteringList from '@/global/FilteringList';

export default {
  name: 'CardBoard',

  metaInfo: {
    title: 'کارتابل سفارش‌های سازمانی',
  },

  mixins: [FilteringList],
  
  data() {
    return {
      loading: true
    };
  },

  created() {
    this.loadData();
  },

  methods: {
    loadData() {
    },
  },
};
</script>
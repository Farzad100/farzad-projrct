<template>
  <div class="_pagination d-flex justify-content-between m-3 mt-5">
    <button
      class="btn btn-light btn-sm rounded-pill mx-1"
      :disabled="pagination.current_page <= 1"
      @click.prevent="changePage(pagination.current_page - 1)"
    >
      <i class="fal fa-arrow-right ml-sm-2" />
      <span class="d-none d-sm-inline">صفحه قبل</span>
    </button>

    <template v-if="pagination.last_page <= 10">
      <div class="d-flex align-items-center">
        <g-button
          v-for="(p, pIndex) in pagination.last_page"
          :key="pIndex"
          :text="`${p}`"
          :class="[
            'mx-2',
            pagination.current_page === p ? 'active' : null
          ]"
          sm
          @click.native="changePage(p)"
        />
      </div>
    </template>

    <template v-else>
      <div class="d-flex align-items-center">
        <g-button
          v-if="pagination.current_page > 5"
          text="1"
          class="mx-2"
          sm
          @click.native="changePage(1)"
        />
        <span v-if="pagination.current_page > 5">
          ...
        </span>
        <g-button
          v-for="(p, pIndex) in pageNumbers"
          :key="pIndex"
          :text="`${p}`"
          :class="[
            'mx-2',
            pagination.current_page === p ? 'active' : null
          ]"
          sm
          @click.native="changePage(p)"
        />
        <span v-if="pagination.current_page < pagination.last_page-3">
          ...
        </span>
        <g-button
          v-if="pagination.current_page < pagination.last_page-3"
          :text="`${pagination.last_page}`"
          class="mx-2"
          sm
          @click.native="changePage(pagination.last_page)"
        />
      </div>
    </template>

    <button
      class="btn btn-light btn-sm rounded-pill mx-1"
      :disabled="pagination.current_page >= pagination.last_page"
      @click.prevent="changePage(pagination.current_page + 1)"
    >
      <span class="d-none d-sm-inline">صفحه بعد</span>
      <i class="fal fa-arrow-left mr-sm-2" />
    </button>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

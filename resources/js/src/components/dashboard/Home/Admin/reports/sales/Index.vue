<template>
  <div class="row mb-5 pb-5">
    <div class="col-12 col-lg-6 col-xl-8 pl-lg-0">
      <table-list
        list-name="اطلاعات کلی"
        :table-list="td"
        minimal
      />
    </div>

    <div class="col-12 col-lg-6 col-xl-4 border-right">
      <g-chart
        ref="pie"
        type="donut"
        :options="pie.options"
        :series="pie.series"
      />
    </div>

    <div class="col-12 border-top">
      <div class="btn-group btn-group-sm btn-pill my-4 mr-3">
        <input
          id="one"
          v-model="lineFilter"
          value="1"
          type="radio"
          class="btn-check"
          name="lineFilter"
          autocomplete="off"
          checked
        >
        <label
          class="btn"
          for="one"
        >
          یک ماه اخیر
        </label>

        <input
          id="three"
          v-model="lineFilter"
          value="3"
          type="radio"
          class="btn-check"
          name="lineFilter"
          autocomplete="off"
        >
        <label
          class="btn"
          for="three"
        >
          سه ماه اخیر
        </label>

        <input
          id="six"
          v-model="lineFilter"
          value="6"
          type="radio"
          class="btn-check"
          name="lineFilter"
          autocomplete="off"
        >
        <label
          class="btn"
          for="six"
        >
          شش ماه اخیر
        </label>

        <!-- <input
          id="year"
          v-model="lineFilter"
          value="12"
          type="radio"
          class="btn-check"
          name="lineFilter"
          autocomplete="off"
        >
        <label
          class="btn"
          for="year"
        >
          یک سال اخیر
        </label> -->
      </div>

      <div class="btn-group filter-dropdown mr-3">
        <button
          type="button"
          class="btn btn-light btn-sm rounded-pill dropdown-toggle"
          @click.prevent="filterShow = !filterShow"
        >
          <i class="far fa-filter ml-2" />
          فیلترهای بیشتر
          <i class="fal fa-caret-down mr-3" />
        </button>

        <div
          class="dropdown-menu dropdown-menu-right shadow-sm border mt-2 pb-0"
          :class="filterShow ? 'show' : ''"
        >
          <div class="scroll-wrapper scroll-300">
            <div class="scroll-wrapper-inner">
              <div class="p-3">
                <g-date-picker
                  v-model="dateFilter.from_date"
                  :years-from-now="5"
                  label="از تاریخ"
                />
                <g-date-picker
                  v-model="dateFilter.to_date"
                  class="mt-3"
                  :years-from-now="5"
                  label="تا تاریخ"
                />
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end p-3 aic bg-gray-light border-top">
            <g-button
              text="اعمال فیلتر"
              color="success"
              sm
              @click.native="filterDate"
            />
          </div>
        </div>
      </div>
      
      <div class="px-2">
        <g-chart
          v-if="area.series"
          ref="chart"
          height="350"
          :options="area.options"
          :series="area.series"
        />
      </div>
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>
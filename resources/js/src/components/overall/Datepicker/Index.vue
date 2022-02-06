<template>
  <div>
    <label class="d-flex justify-content-between">
      {{ label }}
      <div
        v-if="!noSuggest"
        class="_date-picker-fast"
      >
        <button
          class="_btn"
          type="button"
          @click="suggestedDate(0)"
        >
          امروز
        </button>
        <button
          class="_btn"
          type="button"
          @click="suggestedDate(-1)"
        >
          دیروز
        </button>
        <button
          class="_btn"
          type="button"
          @click="suggestedDate(-30)"
        >
          ماه پیش
        </button>
        <div class="btn-group">
          <button
            class="_btn"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
          >
            <i class="fas fa-ellipsis-h ml-1" />
            بیشتر
          </button>
          <ul class="dropdown-menu">
            <li>
              <button
                class="dropdown-item text-right"
                type="button"
                @click="suggestedDate(-2)"
              >
                پریروز
              </button>
            </li>
            <li>
              <button
                class="dropdown-item text-right"
                type="button"
                @click="suggestedDate(-7)"
              >
                هفته پیش
              </button>
            </li>
            <li>
              <button
                class="dropdown-item text-right"
                type="button"
                @click="suggestedDate(-90)"
              >
                سه ماه پیش
              </button>
            </li>
            <li>
              <button
                class="dropdown-item text-right"
                type="button"
                @click="suggestedDate(-180)"
              >
                شش ماه پیش
              </button>
            </li>
            <li>
              <button
                class="dropdown-item text-right"
                type="button"
                @click="suggestedDate(-365)"
              >
                یک سال پیش
              </button>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <button
                class="dropdown-item text-right"
                type="button"
                @click="reset"
              >
                حذف
              </button>
            </li>
          </ul>
        </div>
      </div>
    </label>

    <div class="row">
      <div class="col-3 pl-1">
        <select
          v-model="date.d"
          :class="['form-select', size ? `form-select-${size}` : null]"
          :disabled="disabled"
        >
          <option
            value=""
            disabled
          >
            روز
          </option>
          <option
            v-for="i in 31"
            :key="i"
            :value="i"
          >
            {{ i }}
          </option>
        </select>
      </div>

      <div class="col-5 px-1">
        <select
          v-model="date.m"
          :class="['form-select', size ? `form-select-${size}` : null]"
          :disabled="disabled"
        >
          <option
            value=""
            disabled
          >
            ماه
          </option>
          <option :value="1">
            فروردین
          </option>
          <option :value="2">
            اردیبهشت
          </option>
          <option :value="3">
            خرداد
          </option>
          <option :value="4">
            تیر
          </option>
          <option :value="5">
            مرداد
          </option>
          <option :value="6">
            شهریور
          </option>
          <option :value="7">
            مهر
          </option>
          <option :value="8">
            آبان
          </option>
          <option :value="9">
            آذر
          </option>
          <option :value="10">
            دی
          </option>
          <option :value="11">
            بهمن
          </option>
          <option :value="12">
            اسفند
          </option>
        </select>
      </div>

      <div class="col-4 pr-1">
        <select
          v-model="date.y"
          :class="['form-select', size ? `form-select-${size}` : null]"
          :disabled="disabled"
        >
          <option
            value=""
            disabled
          >
            سال
          </option>
          <option
            v-for="i in years"
            :key="i"
            :value="i"
          >
            {{ i }}
          </option>
        </select>
      </div>
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

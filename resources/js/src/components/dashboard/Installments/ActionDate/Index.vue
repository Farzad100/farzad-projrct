<template>
  <div>
    <label for="">
      زمان
    </label>
    <select
      v-model="actionDate"
      class="form-select form-select-lg mb-4"
      @change="handleType"
    >
      <option value="today">
        امروز
      </option>
      <option value="yesterday">
        دیروز
      </option>
      <option value="expiration">
        سر موقع و بدون تأخیر
      </option>
      <option value="manual">
        انتخاب دستی
      </option>
    </select>

    <g-date-picker
      v-if="actionType === 'manual'"
      v-model="actionDate"
      label="تاریخ دستی"
    />
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

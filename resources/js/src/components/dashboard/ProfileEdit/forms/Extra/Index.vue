<template>
  <div
    id="extra-information"
    class="border shadow-sm rounded"
  >
    <h5 class="special-font mb-5 font-weight-bold opa-5 px-4 py-4">
      <i class="fad fa-person-sign fa-lg ml-2" />
      اطلاعات تکمیلی
    </h5>

    <form-builder
      ref="fb"
      v-model="formModel"
      :form-data="formData"
    />

    <div class="p-4 mt-5 bg-gray-light border-top d-flex justify-content-end">
      <g-button
        ref="metaSave"
        text="ثبت اطلاعات"
        color="primary"
        @click.native="onSubmitMeta"
      />
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

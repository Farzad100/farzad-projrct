<template>
  <div class="_single">
    <template v-if="id">
      <div class="_actions">
        <g-button
          v-if="canEdit > 1 && editing"
          ref="edit"
          :disabled="!note.title"
          text="ذخیره تغییرات"
          class="ml-2"
          color="success"
          sm
          @click.native="edit"
        />

        <g-button
          ref="remove"
          text="حذف"
          color="outline-danger"
          icon-right="trash"
          icon-right-space="pl-2"
          sm
          @click.native="remove"
        />

        <g-button
          :text="editing ? 'انصراف از ویرایش' : 'ویرایش'"
          class="mr-2"
          color="outline-info"
          :icon-right="editing ? 'times' : 'pen'"
          icon-right-space="pl-2"
          sm
          @click.native="toggleEdit"
        />
      </div>

      <div :class="['_editable', { '_editing': editing }]">
        <input
          v-model="note.title"
          class="title"
          placeholder="عنوان یادداشت"
          value="این یک عنوان تستی برای این یادداشت است"
        >

        <textarea
          v-model="note.caption"
          placeholder="متن یادداشت"
          rows="10"
          class="text"
        />
      </div>
    </template>

    <empty
      v-else
      class="mt-5 pt-5"
      message="یک یادداشت برای مشاهده انتخاب کنید"
    />
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

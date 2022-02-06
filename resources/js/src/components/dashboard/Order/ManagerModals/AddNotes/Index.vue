<template>
  <modal
    v-show="notesModal"
    title="افزودن یادداشت"
    @close="notesModal = false"
  >
    <template #body>
      <label>عنوان</label>
      <input
        v-model="newNote.title"
        class="form-control form-control-lg mb-4"
      >

      <label>متن یادداشت</label>
      <textarea
        v-model="newNote.caption"
        rows="3"
        class="form-control form-control-lg"
      />
    </template>

    <template #footer>
      <div class="d-flex w-100 justify-content-between">
        <button
          class="btn btn-light rounded-pill"
          @click="notesModal = false"
        >
          انصراف
        </button>

        <g-button
          ref="notes"
          text="ذخیره یادداشت"
          type="button"
          color="primary"
          @click.native="addNote"
        />
      </div>
    </template>
  </modal>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>
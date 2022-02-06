<template>
  <div>
    <div
      v-if="role === 'admin' && notes.length"
      class="notes mt-5"
    >
      <h5 class="special-font mb-3 font-weight-bold opa-5 d-flex aic">
        <i class="fad fa-sticky-note ml-2" />
        یادداشت‌ها
      </h5>
      <div
        v-for="(note, noteIndex) in notes"
        :key="noteIndex"
        class="note mb-3"
      >
        <h5
          v-if="note.title"
          class="special-font font-weight-bold mb-2"
        >
          {{ note.title }}
        </h5>
        <!-- eslint-disable vue/no-v-html -->
        <div
          class="mb-3 p"
          v-html="note.caption"
        />

        <div class="d-flex align-items-end mt-4">
          <button
            v-if="note.editable"
            class="btn btn-link text-danger btn-sm text-decoration-none p-0 ml-3"
            @click="deleteNote(note.id)"
          >
            <small>
              حذف
            </small>
          </button>
          <button
            v-if="note.editable"
            class="btn btn-link text-primary btn-sm text-decoration-none p-0"
            @click="$refs.addNoteModal.openModal('edit', noteIndex)"
          >
            <small>
              ویرایش
            </small>
          </button>

          <small class="mr-auto opa-5">
            {{ note && note.creator && note.creator.full_name }}
            <span
              v-if="note && note.creator"
              class="px-1 opa-3"
            >•</span>
            {{ note.created_at | jDate }}
            <span class="px-1 opa-3">•</span>
            {{ note.created_at | jTime }}
          </small>
        </div>

        <div
          v-if="note.editor"
          class="d-flex align-items-end border-top mt-2 pt-2"
        >
          <small class="opa-5">
            آخرین ویرایش:
            <strong> {{ note.updated_at | jDate }}، </strong>
            ساعت
            <strong class="pl-2">
              {{ note.updated_at | jTime }}
            </strong>
            توسط:
            <strong>
              {{ note.editor.full_name }}
            </strong>
          </small>
        </div>
      </div>
    </div>

    <add-note-modal
      ref="addNoteModal"
      :type="type"
    />
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

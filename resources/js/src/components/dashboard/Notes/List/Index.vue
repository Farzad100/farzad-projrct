<template>
  <div class="_list">
    <!-- Note card -->
    <button
      v-for="(note, index) in notes"
      :key="index"
      type="button"
      class="_note-card"
      @click="read(note)"
    >
      <span>
        {{ note.title }}
      </span>

      <small>
        {{ note.created_at | jDate }}
        -
        {{ note.created_at | jTime }}
      </small>
    </button>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

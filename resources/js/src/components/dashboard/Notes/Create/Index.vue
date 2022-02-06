<template>
  <form
    class="_create"
    @submit.prevent="add"
  >
    <input
      v-model="newNote.title"
      placeholder="عنوان یادداشت"
    >

    <textarea
      v-model="newNote.caption"
      placeholder="متن یادداشت"
      rows="3"
    />

    <div class="d-flex justify-content-end">
      <g-button
        ref="submit"
        text=""
        icon-right="plus"
        type="submit"
        color="primary"
        class="_create-button"
        :disabled="newNote.title.length <= 1"
      />
    </div>
  </form>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

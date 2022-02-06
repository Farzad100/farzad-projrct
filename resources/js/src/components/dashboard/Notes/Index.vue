<template>
  <div class="_notes">
    <div class="_notes-content">
      <div class="_notes-sidebar">
        <!-- Title and Close -->
        <div class="_head">
          <!-- Close button -->
          <button
            class="shadow"
            @click="close"
          >
            <i class="fal fa-times" />
          </button>

          <!-- Title -->
          <h5 class="special-font m-0 font-weight-bold">
            یادداشت‌ها
          </h5>
        </div>

        <!-- Create note form -->
        <create
          :g-type="gType"
          :g-id="gId"
        />

        <!-- List of notes -->
        <list
          ref="noteList"
          :g-endpoint="gEndpoint"
        />
      </div>

      <div class="_notes-text">
        <!--
          Content of note
          + delete
          + edit
        -->
        <single
          ref="noteSingle"
          :g-type="gType"
          :g-id="gId"
        />
      </div>
    </div>
  </div>
</template>

<script>
/**
 * Components
 */
import Create from './Create/Index';
import Single from './Single/Index';
import List from './List/Index';

export default {
  name: 'Notes',

  components: {
    Create,
    Single,
    List,
  },

  props: {
    gType: {
      type: String,
      default: ''
    },

    gEndpoint: {
      type: String,
      default: ''
    },

    gId: {
      type: [String, Number],
      default: ''
    }
  },

  methods: {
    close() {
      this.$emit('close');
    }
  }
};
</script>
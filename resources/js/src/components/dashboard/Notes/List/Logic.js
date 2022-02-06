/**
 * API Services
 */
import Custom from '@/api/custom';

export default {
  name: 'NotesList',

  props: {
    gEndpoint: {
      type: String,
    },
  },

  data() {
    return {
      notes: []
    };
  },

  created() {
    this.loadData();
  },

  methods: {
    /**
     * Fetch list of notes
     */
    loadData() {
      Custom.get(this.gEndpoint)
        .then(res => {
          const { result } = res.data;
          this.notes = result;
        })
        .catch(err => {
          this.$alerts.errHandle(err);
        });
    },

    /**
     * Show note data in
     * single component
     */
    read(payload) {
      const { id, title, caption } = payload;

      const single = this.$parent.$refs.noteSingle;
      single.canEdit = 0;
      single.id = id;
      single.note.title = title;
      single.note.caption = caption;
      single.constantNote.title = title;
      single.constantNote.caption = caption;
    }
  }
};
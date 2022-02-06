import { mapState } from 'vuex';
import Admin from '@/api/admin';

export default {
  name: 'SmallNote',

  props: {
    notes: {
      type: Array,
      default: []
    },
    type: {
      type: String,
      default: 'order'
    },
  },

  components: {
    AddNoteModal: () => import('@/components/dashboard/Order/ManagerModals/AddNotes/Index'),
  },

  computed: {
    ...mapState('dashboard', ['role'])
  },

  methods: {
    loadNotes() {
      this.$parent.loadNotes();
    },

    deleteNote(id) {
      Admin.notes
        .delete(id)
        .then(r => {
          if (r.data.status) {
            this.notesModal = false;
            this.loadNotes();

            this.$alerts.show({
              msg: 'یادداشت با موفقیت حذف شد',
              type: 'success',
              style: 'float'
            });
          } else {
            this.$alerts.show({
              msg: 'مشکلی در حذف یادداشت بوجود آمد',
              type: 'danger',
              style: 'float'
            });
          }
        })
        .catch(e => {
          this.$alerts.errHandle(e);
          this.notesDeleteLoading = false;
        });
    },
  }
};

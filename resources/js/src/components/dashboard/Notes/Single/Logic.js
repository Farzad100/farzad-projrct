/**
 * API Services
 */
import Admin from '@/api/admin';

export default {
  name: 'NotesSingle',

  props: {
    gType: {
      type: String,
    },

    gId: {
      type: [String, Number],
    },
  },

  data() {
    return {
      editing: false,
      canEdit: 0,

      id: '',
      note: {
        title: '',
        caption: ''
      },

      constantNote: {
        title: '',
        caption: ''
      }
    };
  },

  watch: {
    note: {
      handler() {
        this.canEdit++;
      },

      deep: true
    }
  },

  methods: {
    /**
     * Save changes
     */
    edit() {
      // Show loading
      this.$refs.edit.loading('start');

      Admin.notes
        .edit(this.id, this.note)
        .then(res => {
          const { status } = res.data;

          if (status) {

            // Hide edit button
            this.canEdit = 0;

            // Update note in list
            this.$parent.$refs.noteList.loadData();

            // Show success alert
            this.$alerts.show({
              msg: 'یادداشت با موفقیت ویرایش شد',
              type: 'success',
              style: 'float'
            });

            this.editing = false;

          } else {

            // Show error alert
            this.$alerts.show({
              msg: 'مشکلی در ذخیره تغییرات بوجود آمد',
              type: 'danger',
              style: 'float'
            });
          }

          // Hide loading
          this.$refs.edit.loading('end');
        })
        .catch(err => {
          this.$alerts.errHandle(err);
          this.$refs.edit.loading('end');
        });
    },

    /**
     * Remove current note
     */
    remove() {
      // Show loading
      this.$refs.remove.loading('start');

      // Confirmation
      confirm('آیا از حذف این یادداشت مطمئن هستید؟');

      Admin.notes
        .delete(this.id)
        .then(res => {
          const { status } = res.data;

          if (status) {

            // Empty single content
            this.id = null;

            // Update note list
            this.$parent.$refs.noteList.loadData();

            // Show success alert
            this.$alerts.show({
              msg: 'یادداشت با موفقیت حذف شد',
              type: 'success',
              style: 'float'
            });

          } else {

            // Show error alert
            this.$alerts.show({
              msg: 'مشکلی در حذف این یادداشت بوجود آمد',
              type: 'danger',
              style: 'float'
            });
          }

          // Hide loading
          this.$refs.remove.loading('end');
        })
        .catch(err => {
          this.$alerts.errHandle(err);
          this.$refs.remove.loading('end');
        });
    },

    toggleEdit() {
      const lastNote = this.constantNote;

      if (this.editing) {
        this.note = lastNote;
      }

      this.editing = !this.editing;
    }
  }
};
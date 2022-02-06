/**
 * API Services
 */
import Admin from '@/api/admin';

export default {
  name: 'NotesCreate',

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
      newNote: {
        id: this.gId,
        type: this.gType,
        title: '',
        caption: ''
      },
    };
  },

  methods: {
    add() {
      this.$refs.submit.loading('start');

      Admin.notes.create(this.newNote)
        .then(res => {
          const { status } = res.data;

          if (status) {
            // Reset inputs
            this.newNote.title = '';
            this.newNote.caption = '';

            // Show note in list
            this.$parent.$refs.noteList.loadData();

            // Show success alert
            this.$alerts.show({
              msg: 'یادداشت با موفقیت افزوده شد',
              type: 'success',
              style: 'float'
            });

          } else {

            // Show error alert
            this.$alerts.show({
              msg: 'مشکلی در ذخیره یادداشت بوجود آمد',
              type: 'danger',
              style: 'float'
            });
          }

          // Hide loading
          this.$refs.submit.loading('end');
        })
        .catch(err => {
          this.$alerts.errHandle(err);
          this.$refs.submit.loading('end');
        });
    },
  }
};
import { api, wrapper } from '@/global/services';

export default {
  name: 'AddNote',

  props: {
    type: {
      type: String
    }
  },

  data() {
    return {
      notesModal: false,
      noteProccess: '',
      newNote: {
        id: this.$route.params.id,
        type: this.type,
        title: '',
        caption: ''
      },
    };
  },

  methods: {
    async addNote() {
      this.$refs.notes.loading('start');

      if (this.noteProccess === 'adding') {
        const { data } = await wrapper(
          api.Admin.notes.create(this.newNote),
          'یادداشت ذخیره نشد'
        );

        this.$refs.notes.loading('end');
        
        if (data) {
          const { status } = data;

          if (status) {
            this.$parent.loadNotes();
            this.notesModal = false;
  
            this.$alerts.show({
              msg: 'یادداشت با موفقیت افزوده شد',
              type: 'success',
              style: 'float'
            });
          }
        }
      } else {
        this.editNote();
      }

    },

    async editNote() {
      const { data } = await wrapper(
        api.Admin.notes.edit(this.newNote.id, {
          title: this.newNote.title,
          caption: this.newNote.caption,
        }),
        'یادداشت ذخیره نشد'
      );

      this.$refs.notes.loading('end');
      
      if (data) {
        const { status } = data;

        if (status) {
          this.$parent.loadNotes();
          this.notesModal = false;

          this.$alerts.show({
            msg: 'یادداشت با موفقیت ویرایش شد',
            type: 'success',
            style: 'float'
          });
        }
      }
    },

    openModal(type, index) {
      this.notesModal = true;

      if (type === 'new') {
        this.noteProccess = 'adding';

        this.newNote.id = this.$route.params.id;
        this.newNote.title = '';
        this.newNote.caption = '';

      } else if (type === 'edit') {
        this.noteProccess = 'editing';

        const { id, title, caption } = this.$parent.notes[index];
        this.newNote.id = id;
        this.newNote.title = title;
        this.newNote.caption = caption;
      }
    }
  }
};

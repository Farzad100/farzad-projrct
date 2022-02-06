import ClickOutside from 'vue-click-outside';

export default {
  name: 'MultiSelect',

  props: {
    options: {
      type: Array,
      required: true
    },

    stringReturn: {
      type: Boolean
    },

    size: {
      type: String
    },

    name: {
      type: String,
    },

    max: {
      type: Number
    },

    /**
     * VueModel
     */
    value: {
      type: [Array, String],
      default: []
    },
  },

  data() {
    return {
      selected: this.getter() || [],
      list: this.options,
      searchKey: '',
      isOpen: false
    };
  },

  watch: {
    selected(val) {
      this.stringReturn
        ? this.$emit('input', val.join(''))
        : this.$emit('input', val);

      this.selectLimitControl(val);
    },

    searchKey(val) {
      if (val) {
        this.list = [];
        for (let opt of this.options) {
          if (opt.text.includes(val)) {
            this.list.push(opt);
          }
        }
      } else {
        this.list = this.options;
      }

      setTimeout(() => {
        this.selectLimitControl(this.selected);
      }, 10);
    },

    isOpen(val) {
      if (!val && this.selected.length) {
        this.selectedItemsShow();
        this.selectLimitControl(this.selected);
      } else {
        this.searchKey = '';
      }
    },

    value() {
      this.getter();
    }
  },

  mounted() {
    if (this.selected.length) {
      this.selectedItemsShow();
    }
  },

  methods: {
    getter() {
      if (this.value && this.value !== 'all') {
        return this.stringReturn
          ? this.value.split(',')
          : this.value;
      } else {
        this.selected = [];
        this.searchKey = '';
      }
    },

    selectedItemsShow() {
      if (this.selected.length > 1) {
        this.searchKey = this.selected.length + ' گزینه انتخاب شده';
      } else {
        this.searchKey = this.options.map(i => {
          if (i.value === this.selected[0]) {
            return i.text;
          }
        }).join(' ');
      }
    },

    selectLimitControl(array) {
      if (this.max <= array.length) {
        this.list.forEach(opt => {
          this.$refs[opt.value][0].setAttribute('disabled', true);

          array.forEach(val => {
            if (val === opt.value) {
              this.$refs[opt.value][0].removeAttribute('disabled');
            } 
          });
        });
      } else {
        this.list.forEach(opt => {
          this.$refs[opt.value][0].removeAttribute('disabled');
        });
      }
    },

    close() {
      this.isOpen = false;
    },
    
    focus() {
      this.isOpen = true;
      this.$refs.input.focus();
    }
  },

  directives: {
    ClickOutside
  }
};
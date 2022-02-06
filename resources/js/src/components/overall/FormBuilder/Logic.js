export default {
  name: 'FormBuilder',

  props: {
    formData: {
      type: Array,
      required: true
    }
  },

  watch: {
    models: {
      handler(val) {
        this.$emit('input', val);
      },

      deep: true
    },

    formData(val) {
      this.getModels(val);
    }
  },

  data() {
    return {
      models: {},
      canSubmit: false,
    };
  },

  computed: {
    years() {
      const y = [];
      for(let i = 1410; i >= 1200; i--) {
        y.push(i);
      }
      return y;
    },
  },

  mounted() {
    this.getModels(this.formData);

    this.$watch(
      '$refs.observer.flags',
      (val) => {
        this.$emit('valid', val.passed);
      }
    );
  },

  methods: {
    getModels(array) {
      array.forEach(obj => {
        obj.fields.forEach(f => {
          this.models = {
            ...this.models,
            [f.v_model]: f.value
          };
        });
      });
    },
  }
  
};
import moment from 'moment-jalaali';
import { toEnglishDigits } from '@/global/Functions';

export default {
  name: 'DatePicker',

  props: {
    label: {
      type: String
    },

    size: {
      type: String
    },

    yearsFromNow: {
      type: Number
    },

    yearMax: {
      type: Number
    },

    yearMin: {
      type: Number
    },

    empty: {
      type: Boolean
    },

    value: {
      type: String
    },

    noSuggest: {
      type: Boolean
    },

    disabled: {
      type: Boolean
    }
  },

  data() {
    return {
      date: {
        d: this.empty ? '' : this.now().d,
        m: this.empty ? '' : this.now().m,
        y: this.empty ? '' : this.now().y
      }
    };
  },

  computed: {
    years() {
      const now = parseInt(toEnglishDigits(moment(new Date()).format('jYYYY')));
      const max = this.yearMax ? this.yearMax : now;
      const result = [];

      const untilYear = this.yearMin
        ? this.yearMin
        : this.yearsFromNow
          ? now - this.yearsFromNow
          : 1320;

      for (let i = max; i > untilYear; i--) {
        result.push(i);
      }

      return result;
    }
  },

  watch: {
    date: {
      handler(val) {
        if (val.d && val.m && val.y) {
          this.$emit('input', `${val.y}-${val.m}-${val.d}`);
        }
      },
      deep: true
    },

    value() {
      this.getter();
    }
  },

  created() {
    if (!this.empty) {
      this.$emit('input', `${this.date.y}-${this.date.m}-${this.date.d}`);
    }
  },

  mounted() {
    this.getter();
  },

  methods: {
    now() {
      return {
        d: parseInt(toEnglishDigits(moment(new Date()).format('jD'))),
        m: parseInt(toEnglishDigits(moment(new Date()).format('jM'))),
        y: parseInt(toEnglishDigits(moment(new Date()).format('jYYYY')))
      };
    },

    suggestedDate(payload) {
      this.date.d = parseInt(
        toEnglishDigits(
          moment(new Date())
            .add(payload, 'days')
            .format('jD')
        )
      );
      this.date.m = parseInt(
        toEnglishDigits(
          moment(new Date())
            .add(payload, 'days')
            .format('jM')
        )
      );
      this.date.y = parseInt(
        toEnglishDigits(
          moment(new Date())
            .add(payload, 'days')
            .format('jYYYY')
        )
      );
    },

    getter() {
      this.value ? this.placeValues() : this.emptyFields();
    },

    placeValues() {
      const newDate = this.value.split('-');

      this.date = {
        d: parseInt(newDate[2]),
        m: parseInt(newDate[1]),
        y: parseInt(newDate[0])
      };
    },

    emptyFields() {
      this.date = {
        d: '',
        m: '',
        y: ''
      };
    },

    reset() {
      this.emptyFields();
      this.$emit('input', '');
    }
  }
};

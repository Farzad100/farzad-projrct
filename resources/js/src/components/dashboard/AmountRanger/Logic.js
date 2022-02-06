import $ from 'jquery';
export default {
  name: 'AmountRanger',

  props: {
    type: {
      type: String
    },
    min: {
      type: [String, Number]
    },

    max: {
      type: [String, Number]
    },

    step: {
      type: [String, Number]
    },
    hide: {
      type: Object,
      default: function() {
        return {};
      }
    },
    landingStyle: {
      type: Boolean
    },

    simpleStyle: {
      type: Boolean
    },

    compressed: {
      type: Boolean
    }
  },

  watch: {
    range(val) {
      this.$emit('input', val);
      this.movePriceAboveThumb(val);
      let percentage = (100 / (this.max - 1900000)) * (this.range - 1100000);
      let sliderWidth = $('.range-slider').width();
      let tooltipWidth = $('.range-slider-tooltip').width();
      let calc = sliderWidth - tooltipWidth - 32;
      let positionCalc = (percentage / 100) * calc;

      $('.range-slider-tooltip').css('left', positionCalc);
    }
  },

  data() {
    return {
      monthIndex: 1,
      chequeIndex: 1,

      fa2Num: [
        {
          label: 'یک',
          value: 1
        },
        {
          label: 'دو',
          value: 2
        },
        {
          label: 'سه',
          value: 3
        },
        {
          label: 'چهار',
          value: 4
        },
        {
          label: 'پنج',
          value: 5
        },
        {
          label: 'شش',
          value: 6
        },
        {
          label: 'هفت',
          value: 7
        },
        {
          label: 'هشت',
          value: 8
        },
        {
          label: 'نه',
          value: 9
        },
        {
          label: 'ده',
          value: 10
        },
        {
          label: 'یازده',
          value: 11
        },
        {
          label: 'دوازده',
          value: 12
        }
      ],

      range: '1200000',
      limit: 'type-10m',
      value: 20
    };
  },

  computed: {
    data() {
      return [
        {
          currentIndex: 1,
          flex: 3,
          list: ['سه چک', 'شش چک'],
          textAlign: 'center',
          className: 'row-group'
        },
        {
          currentIndex: 1,
          flex: 3,
          list:
            this.type === 'organ2'
              ? ['سه ماهه', 'شش ماهه', 'ده ماهه', 'دوازده ماهه']
              : ['سه ماهه', 'شش ماهه', 'ده ماهه'],
          textAlign: 'center',
          className: 'item-group'
        }
      ];
    },

    months() {
      return [
        {
          label: 'سه ماهه',
          value: 3,
          cheques: this.type === 'organ1' ? ['سه قسط'] : ['سه چک']
        },
        {
          label: 'شش ماهه',
          value: 6,
          cheques:
            this.type === 'organ1' || this.type === 'organ2'
              ? ['سه قسط', 'شش قسط']
              : ['سه چک', 'شش چک']
        },
        {
          label: 'ده ماهه',
          value: 10,
          cheques:
            this.type === 'organ1' || this.type === 'organ2'
              ? ['پنج قسط', 'ده قسط']
              : ['پنج چک']
        },
        {
          label: 'دوازده ماهه',
          value: 12,
          cheques:
            this.type === 'organ2' ? ['شش قسط', 'دوازده قسط'] : ['شش قسط']
        }
      ];
    },

    checkLimit() {
      switch (this.max) {
      case '10000000':
        return 'type-10m';
      case '14000000':
        return 'type-14m';
      default:
        break;
      }
    }
  },

  methods: {

    // scrolll() {
    //   this.smoothPicker.scrollBy({ top: 100, left: 0, behavior: 'smooth' });
    // },
    movePriceAboveThumb(payload) {
      let range = this.$refs.range,
        position = Number(
          ((payload - range.min) * 100) / (range.max - range.min)
        );
      let amount = this.$refs.amount;
      let neP = 10 - position * 0.3;
      amount.style.left = `calc(${position}% + (${neP}px))`;
    },

    dataChange(gIndex, iIndex) {
      if (gIndex === 0) this.chequeIndex = iIndex;
      else {
        this.monthIndex = iIndex;
        const m = this.months[this.monthIndex].value;
        let obj = this.months.find(obj => obj.value == m);
        this.$refs.smoothPicker.setGroupData(
          0,
          Object.assign({}, this.data[0], {
            currentIndex: obj.value === 6 ? 1 : 0,
            list: obj.cheques
          })
        );
        this.chequeIndex = obj.value === 6 ? 1 : 0;
      }

      setTimeout(() => {
        const cFa = this.data[0].list[this.chequeIndex];
        const chequeFaNum = cFa.split(' ')[0];
        let objC = this.fa2Num.find(obj => obj.label == chequeFaNum).value;
        let objM = this.months[this.monthIndex].value;
        setTimeout(() => {
          this.$emit('mc', { months: objM, cheques: objC });
        }, 25);
      }, 25);
    }
  },

  mounted() {
    let obj = this.months.find(obj => obj.value == 6);
    this.$refs.smoothPicker.setGroupData(
      0,
      Object.assign({}, this.data[0], {
        currentIndex: obj.value === 6 ? 1 : 0,
        list: obj.cheques
      })
    );
  }
};

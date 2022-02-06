import { QrcodeStream } from 'vue-qrcode-reader';

export default {
  name: 'QrReader',

  components: { QrcodeStream },

  props: {
    return: {
      type: String,
      required: true
    }
  },

  data () {
    return {
      error: '',
      loading: false
    };
  },

  methods: {
    onDecode (result) {
      this.$emit('close');
      const array = result.split(/\r?\n/);

      let shoudEmitResult;

      switch (this.return) {
      case 'isbn':
        shoudEmitResult = this.decodeIsbn(array);
        break;
      case 'sheba':
        shoudEmitResult = this.decodeSheba(array);
        break;
      default:
        break;
      }
          
      this.$emit('input', shoudEmitResult);
    },

    paintOutline (detectedCodes, ctx) {
      for (const detectedCode of detectedCodes) {
        const [ firstPoint, ...otherPoints ] = detectedCode.cornerPoints;

        ctx.strokeStyle = 'red';

        ctx.beginPath();
        ctx.moveTo(firstPoint.x, firstPoint.y);
        for (const { x, y } of otherPoints) {
          ctx.lineTo(x, y);
        }
        ctx.lineTo(firstPoint.x, firstPoint.y);
        ctx.closePath();
        ctx.stroke();
      }
    },

    close() {
      this.$emit('close');
    },

    async onInit (promise) {
      this.loading = true;

      try {
        await promise;
      } catch (error) {
        if (error.name === 'NotAllowedError') {
          this.error = 'دسترسی به دوربین امکان پذیر نبود';
        } else if (error.name === 'NotFoundError') {
          this.error = 'هیچ دوربینی در این دستگاه یافت نشد\n\nبرای اسکن بارکد روی چک، می توانید با گوشی هوشمند یا سایر دستگاه‌های دوربین‌دار وارد صفحه سفارش شوید';
        } else if (error.name === 'NotSupportedError') {
          this.error = 'ERROR: secure context required (HTTPS, localhost)';
        } else if (error.name === 'NotReadableError') {
          this.error = 'دوربین در حال استفاده توسط دستگاه دیگری است؟';
        } else if (error.name === 'OverconstrainedError') {
          this.error = 'از دوربین این دستگاه پشتیبانی نمی‌شود';
        } else if (error.name === 'StreamApiNotSupportedError') {
          this.error = 'قابلیت Stream روی این دستگاه پشتیبانی نمی‌شود';
        }
      } finally {
        this.loading = false;
      }
    },

    decodeIsbn(array) {
      return array.map(number => number.length == 16 ? number : null).join('');
    },

    decodeSheba(array) {
      return array.map(number => number.length == 24 ? number : null).join('');
    }
  }
};

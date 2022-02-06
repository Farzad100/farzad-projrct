import FormValidator from '@/global/FormValidator';
import Auth from '@/api/auth';

export default {
  name: 'GetOtpCode',

  mixins: [FormValidator],

  watch: {
    /**
     * Get all OTP inputs
     * value and merged them
     * 
     * @param {String} val 
     */
    otpCode(val) {
      if (val.length > 5) {
        this.otpCodeMerged = val.slice(1, 6).toString().replace(/,/g, '');
      }
    }
  },

  props: {
    /**
     * Get required data from
     * previous component
     */
    otpData: {
      type: Object,
      required: true
    },

    /**
     * This props required to specify
     * type of OTP like 'register'
     * for user registeration or 
     * 'password' for reset password
     * 'shop_order' for order created by a shop
     */
    type: {
      type: String,
      required: true
    },

    who: {
      type: String
    }
  },

  data() {
    return {
      otpCode       : [],
      otpCodeMerged : '',
      resendCode    : false,
      resendingCode : false,
      loading       : false,
      new_track_id  : null
    };
  },

  methods: {
    /**
     * Local function that $emit this
     * component specified keys
     * 
     * @param {Object} payload 
     */
    emit(payload) {
      const {
        tracker,
        user,
        user_exists
      } = payload;

      this.$emit('formSubmit', {
        tracker : tracker,
        user  : user,
        user_exists: user_exists,
        mobile: this.otpData.mobile
      });
    },
    

    /**
     * Send another HTTP request to
     * Auth.sendOtp to send new OTP
     * code with SMS
     */
    resendOtpCode() {
      if (!this.resendingCode) {
        this.resendingCode = true;

        const customEndpoint = this.$route.path === '/dashboard/shop/new-order' ? 'sendOtpShop' : 'sendOtp';

        Auth[customEndpoint]({
          username: this.otpData.mobile,
          type: this.type
        })
          /**
           * Success Response
           */
          .then(r => {
            this.new_track_id = r.data.result.track_id;
            this.resendCode = false;
            this.resendingCode = false;
            this.otpCode = [];
            this.resendLock();
          });
      }
    },

    /**
     * This function just trigger a
     * custom event that helps the
     * parent to change auth step
     */
    edit() {
      this.$emit('changeMobile');
    },

    /**
     * This submit() send a http request
     * to Auth.checkOtp to check if OTP Code
     * is correct
     */
    submit() {
      this.loading = true;

      const httpRequestBody = {
        username: this.otpData.mobile,
        track_id: this.new_track_id ? this.new_track_id : this.otpData.trackId,
        otp     : this.otpCodeMerged
      };

      Auth.checkOtp(httpRequestBody)
        /**
         * Success Response
         */
        .then(r => {

          const { result, status } = r.data;

          if (status) {
            this.emit(result);
          }

          this.loading = false;

          this.$alerts.show({
            msg: r.data.error.message,
            type: 'danger',
            style: 'float'
          });

        })
        /**
         * Failed Response
         */
        .catch(e => { 

          this.loading = false;
          this.$alerts.errHandle(e);

        });
    },

    /**
     * Check OTP inputs value and focus on
     * the first empty input & Moves focus
     * to next or prev input based on whether
     * input has value or not
     */
    inputController() {
      const inputs = this.$el.querySelectorAll('.otp-inputs > input');
      inputs.forEach((el, i) => {

        // Check OTP inputs value and focus on the first empty input
        el.addEventListener('focus', () => {
          if (i > 0 && !inputs[i - 1].value) inputs[i - 1].focus();
        });

        // Moves focus to next or prev input based on whether input has value or not
        el.addEventListener('input', () => {
          if (i < 4 && el.value) inputs[i + 1].focus();
        });

        // Remove
        let backspaceCounter = 0;
        el.addEventListener('keydown', (e) => {
          if (e.keyCode === 8) {
            backspaceCounter = backspaceCounter + 1;

            if (el.value && backspaceCounter > 1) {
              inputs[i - 1].focus();
              backspaceCounter = 0;
            } else if (!el.value) {
              inputs[i - 1].focus();
              backspaceCounter = 0;
            }
          }
        });
      });
    },



    /**
     * Countdown for active or inactive resend
     * code button
     */
    resendLock() {
      let countDownDate = 120000;

      let x = setInterval(() => {

        let minutes = Math.floor((countDownDate % (1000 * 60 * 60)) / (1000 * 60));
        let seconds = Math.floor((countDownDate % (1000 * 60)) / 1000);

        // Display the result in the element with id="demo"
        this.$el.querySelector('.countdown').innerHTML = minutes + ':' + seconds;

        // If the count down is finished, write some text
        if (countDownDate < 0) {
          clearInterval(x);
          this.resendCode = true;
        }

        countDownDate = countDownDate - 1000;
      }, 1000);
    }
  },

  mounted() {
    this.inputController();
    this.resendLock();
  }
};

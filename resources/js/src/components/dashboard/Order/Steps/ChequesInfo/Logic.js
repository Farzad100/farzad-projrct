import { api, wrapper } from '@/global/services';
import Banks from '@/data/Banks';
import { toEnglishDigits, copyToClipboard } from '@/global/Functions';
import { mapState } from 'vuex';
import Orders from '@/api/orders';
import Admin from '@/api/admin';

export default {
  name: 'ChequesInfo',

  components: {
    QrReader: () => import('@/components/overall/QrReader/Index'),
    Uploader: () => import('@/components/overall/Uploader/Index')
  },

  props: {
    manager: {
      type: Boolean
    },

    docType: {
      type: String
    },

    paybackType: {
      type: String
    },

    showDocsAnyway: {
      type: Boolean
    },

    orderStatus: {
      type: String
    },

    orderData: {
      type: Object
    }
  },

  data() {
    return {
      wfcForm: {
        cheque_numbers: [],
        prepend: '',
        append: '',
        isbn: ''
      },

      cheques_numbers_sm: {
        default: ''
      },

      chequesPreview: null,
      Banks,

      shebaBankErr: false,
      bankId: '',

      showDocs: false,
      chequesSuggested: false,

      qrData: '',
      qrActive: {},

      checkModal: false,
      qrcode: false,

      itemNumber: '',
      saveChequesFileURL: '',
      file: '',

      item: '',

      /**
       * Doc Check
       */
      rejectReason: '',
      cta: null,
      docs: null,
      loading: true,
      docsCheckModal: false,
      docsCheckModalLoading: false,
      docsCheckModalName: '',
      docsCheckModalFormat: null,
      docsCheckModalFileLinks: null,
      docsCheckModalFileLinksLoading: false,
      docsCheckModalAcceptLoading: false,
      docsCheckModalRejectLoading: false,
      docsCheckModalRejectSilent: false,
      docIsReadableUrl: null,
      docIsReadable: null,
      showRejectBtn: true,
      cannotReject: 0,
      allVerified: 0,

      rejectOptions: []
    };
  },

  watch: {
    wfcForm: {
      handler(val) {
        const firstPrepend = val.prepend;
        const firstAppend = val.append;
        const firstSeries = val.cheque_numbers[0].series;
        const lengthEqualFive = firstSeries && firstSeries.length == 6;

        if (lengthEqualFive) {
          const fourFirstDigits = firstSeries.slice(0, 4);
          const twoLastDigits = firstSeries.slice(4, 6);

          this.wfcForm.cheque_numbers.forEach((item, i) => {
            if (i !== 0 && !item.series) {
              item.series = `${toEnglishDigits(fourFirstDigits)}${parseInt(
                toEnglishDigits(twoLastDigits)
              ) + i}`;
            }
          });

          this.chequesSuggested = true;
        }

        this.wfcForm.cheque_numbers.forEach(item => {
          if (
            this.chequesPreview.bank_id === '017' &&
            firstAppend &&
            firstAppend.length > 0
          )
            item.append = firstAppend;

          if (firstPrepend && firstPrepend.length > 0)
            item.prepend = firstPrepend;
        });
      },

      deep: true
    },

    'chequesPreview.cheques': {
      handler(val) {
        this.cannotReject = 0;
        this.allVerified = 0;
        val.forEach((item, i) => {
          if (item.status === 'pending') ++this.cannotReject;
          if (item.status === 'verified') ++this.allVerified;

          if (item.isbn_locked)
            this.wfcForm.cheque_numbers[i].isbn = item.isbn_locked;

          if (item.prepend) this.wfcForm.prepend = item.prepend;

          if (item.append && !this.wfcForm.cheque_numbers[i].append)
            this.wfcForm.cheque_numbers[i].append = item.append;

          if (item.series && !this.wfcForm.cheque_numbers[i].series)
            this.wfcForm.cheque_numbers[i].series = item.series;
        });
      },
      deep: true
    }
  },

  computed: {
    ...mapState('dashboard', ['role']),

    chequesNumberList() {
      const result = this.chequesPreview.cheques.filter((cheque, index) => {
        return index === this.itemNumber;
      });

      return [...result];
    },

    shabaBank() {
      if (this.chequesPreview && this.chequesPreview.bank_id) {
        return Banks.find(obj => obj.id == this.chequesPreview.bank_id);
      }
    }
  },

  mounted() {
    /* this.checkShowAssets(); */
  },

  created() {
    this.loadCheques();
  },

  methods: {
    openModal(payload) {
      this.checkModal = true;
      this.item = payload.item;
      this.itemNumber = payload.i;
      this.saveChequesFileURL = payload.item.url;
    },

    fillIsbn(val) {
      const item = this.itemNumber;
      if (val != 'x') this.wfcForm.cheque_numbers[item].isbn = val;
      this.loadData();
      this.loadCheques();
    },

    checkTheCheque() {
      const item = this.itemNumber;
      const cheque_numbers = this.wfcForm.cheque_numbers[item];

      if (
        cheque_numbers.series &&
        cheque_numbers.isbn &&
        this.wfcForm.prepend &&
        cheque_numbers.is_submitted
      )
        this.submitCheque(cheque_numbers);
      else if (!cheque_numbers.is_submitted)
        alert('چک باید در سامانه صیاد ثبت شده باشد');
      else alert('لطفاً اطلاعات چک را بطور کامل وارد نمایید');
    },

    editTheCheque() {
      const item = this.itemNumber;
      const cheque_numbers = this.wfcForm.cheque_numbers[item];

      if (
        cheque_numbers.series &&
        cheque_numbers.isbn &&
        this.wfcForm.prepend
      )
        this.submitCheque(cheque_numbers);
      else alert('لطفاً اطلاعات چک را بطور کامل وارد نمایید');
    },

    /* checkShowAssets() {
      if (this.showDocsAnyway) {
        if (this.wfcForm.iban) {
          this.showDocs = false;
        } else {
          this.showDocs = true;
        }
      }
    }, */

    submitCheque(chequeData) {
      const item = this.itemNumber;
      if (
        !this.inArray(this.chequesPreview.cheques[item].status, [
          'pending',
          'verified'
        ]) ||
        this.role === 'admin'
      )
        Orders.submitCheque(this.$route.params.id, item, {
          series: chequeData.series,
          append: chequeData.append,
          prepend: chequeData.prepend,
          isbn: chequeData.isbn
        })
          .then(r => {
            if (r.data.status) {
              this.$alerts.show({
                msg: 'اطلاعات ثبت شد',
                type: 'success',
                style: 'float'
              });
              this.checkModal = false;
            } else {
              this.$alerts.show({
                msg: r.data.error.message,
                type: 'danger',
                style: 'float'
              });
            }
          })
          .catch(e => {
            this.$alerts.errHandle(e);
          })
          .finally(() => {
            this.loadData();
            this.loadCheques();
          });
      else this.checkModal = false;
    },

    async sendCheques() {
      this.$refs.submit.loading('start');

      const { data } = await wrapper(
        api.Orders.submitCheques(this.$route.params.id, this.wfcForm),
        'مشکلی در ذخیره سازی اطلاعات پیش آمد'
      );

      if (data) {
        this.$refs.submit.loading('end');
        this.$alerts.show({
          msg: 'اطلاعات ارسالی با موفقیت ثبت شد',
          type: 'success',
          style: 'float'
        });
        this.$parent.loadData();
        this.showDocs = true;
      }
    },

    async loadCheques() {
      const { data } = await wrapper(
        api.Orders.get.chequesPreview(this.$route.params.id),
        'مشکلی در دریافت اطلاعات پیش آمد'
      );

      if (data) {
        const { result } = data;
        this.chequesPreview = result;

        result.cheques.forEach((cheque, index) => {
          this.wfcForm.cheque_numbers.push({
            date: cheque.date,
            series: cheque.series,
            append: cheque.append,
            isbn: cheque.isbn,
            is_submitted: cheque.is_submitted
          });

          this.qrActive = {
            ...this.qrActive,
            [`scanner${index}`]: false
          };
        });

        this.wfcForm.append = result.append;
        this.wfcForm.prepend = result.prepend;

        if (this.paybackType === 'epay') this.showDocs = true;

        if (this.role === 'admin') this.rejectOptions = result.reject_options;
      }
    },

    copyClip(value) {
      copyToClipboard.copy(value);
    },

    loadData() {
      this.$parent.loadData();
    },

    async submitFile() {
      console.log(this.saveChequesFileURL);
      let formData = new FormData();
      formData.append('file', this.file);
      const { data } = await wrapper(
        api.custom.post(this.saveChequesFileURL, formData)
      );

      if (data) this.cardIssuanceModal = false;
    },

    handleFileUpload() {
      this.file = this.$refs.file.files[0];
    },

    async loadDocs(type, index) {
      const { data } = await wrapper(
        this.GET_DOCS(this.$route.params.id, this.role),
        'مشکل در بارگذاری مدارک پیش آمد'
      );

      this.loading = false;

      if (data) {
        const { cta, docs } = data.result;

        this.cta = cta;

        if (type === 'update') {
          this.$set(this.docs, index, docs[index]);
          this.$parent.loadData();
        } else {
          this.docs = docs;
        }
      }
    },

    async accept(param) {
      const i = this.item.badge;
      if (param === 'photo') this.$refs.acceptButtonPhoto.loading('start');
      else this.$refs.acceptButtonSubmit.loading('start');

      const { data } = await wrapper(
        api.custom.post(`${this.item.decision_link}/accept/${param}`),
        'مشکلی در تایید مدارک پیش آمده است'
      );

      if (data) {
        const { status } = data;

        if (status) {
          this.loadCheques().then(() => {
            this.item = this.chequesPreview.cheques[i];
          });

          this.$alerts.show({
            msg: `${this.docsCheckModalName} با موفقیت تایید شد`,
            type: 'success',
            style: 'float'
          });
        }
      }

      if (param === 'photo') this.$refs.acceptButtonPhoto.loading('end');
      else this.$refs.acceptButtonSubmit.loading('end');
    },

    async reject() {
      this.$refs.rejectButton.loading('start');

      const { data } = await wrapper(
        api.custom.post(`${this.item.decision_link}/reject`, {
          reason: this.rejectReason
        }),
        'مشکلی در رد مدارک پیش آمده است'
      );

      if (data) {
        this.$refs.rejectButton.loading('end');
        const { status } = data;

        if (status) {
          this.rejectReason = '';
          this.checkModal = false;
          this.loadCheques();

          this.$alerts.show({
            msg: 'رد شد',
            type: 'success',
            style: 'float'
          });
        }
      }
    },

    sendBack() {
      if (this.cannotReject > 0) alert('اول همه چک ها را تعیین وضعیت کنید');
      else if (this.allVerified === this.orderData.cheques)
        alert('همه چک‌ها تأیید شده اند! سفارش را ارتقا دهید.');
      else {
        this.$refs.rejectBtn.loading('start');
        Admin.orders
          .changeStatus(this.$route.params.id, 'rollback_upload_secondary')
          .then(r => {
            if (r.data.status) {
              this.$alerts.show({
                msg: 'موارد به کاربر اطلاع رسانی شد',
                type: 'success',
                style: 'float'
              });
              this.checkModal = false;
            } else {
              this.$alerts.show({
                msg: r.data.error.message,
                type: 'danger',
                style: 'float'
              });
            }
          })
          .catch(e => {
            this.$alerts.errHandle(e);
          })
          .finally(() => {
            this.loadData();
            this.loadCheques();
            this.$refs.rejectBtn.loading('end');
          });
      }
    },

    async download(payload, type = null) {
      let link;
      if (type === 'back') link = payload.link_back;
      else link = payload.link;

      const { data } = await wrapper(
        api.custom.get(link),
        'مشکلی در دانلود مدارک پیش آمده است'
      );

      if (data) {
        window.open(data);
      }
    },

    bankName(bankId) {
      const result = Banks.find(obj => obj.id == bankId);
      return result.fa;
    },

    check(payload) {
      const { title, link, format } = payload;

      this.docsCheckModal = true;
      this.docsCheckModalName = title;
      this.docsCheckModalFileLinks = link;
      this.docsCheckModalFormat = format;

      this.docIsReadableUrl = payload.is_readable_url;
      this.docIsReadable = payload.is_readable;
    },

    itemClass(item) {
      switch (item.status) {
      case 'rejected':
        return {
          color: 'danger',
          icon: 'times-circle'
        };
      case 'pending':
        return {
          color: 'muted',
          icon: 'stopwatch'
        };
      case 'verified':
        return {
          color: 'success',
          icon: 'check-circle'
        };
      case 'attention':
        return {
          color: 'danger',
          icon: 'exclamation-triangle'
        };
      case 'void':
      default:
        return {
          color: 'primary',
          icon: ''
        };
      }
    },

    inArray(member, array) {
      let length = array.length;
      for (let i = 0; i < length; i++) {
        if (array[i] === member) return true;
      }
      return false;
    }
  }
};

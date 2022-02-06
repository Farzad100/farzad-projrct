import vue2Dropzone from 'vue2-dropzone';
import 'vue2-dropzone/dist/vue2Dropzone.min.css';
import Cookies from 'js-cookie';

// Api
import Upload from '@/api/upload';
import Custom from '@/api/custom';

export default {
  name: 'Uploader',

  components: {
    vueDropzone: vue2Dropzone,
    banks: () => import('@/components/dashboard/order/Banks/Index'),
    ShebaHandler: () =>
      import('@/components/dashboard/order/steps/ShebaHandler/Index')
  },

  props: {
    fileName: {
      type: String,
      required: true
    },

    description: {
      type: String,
      required: true
    },

    skipUrl: {
      type: String
    },

    option: {
      type: Object
    },

    index: {
      type: Number
    },

    rejectMsg: {
      type: String
    },

    info: {
      type: Object
    },

    chequeData: {}
  },

  data() {
    return {
      dropzoneOptions: {
        url: this.option.url ?? '/api/upload',
        headers: {
          Authorization: ''
        },
        previewTemplate: this.template(),
        dictFileTooBig: 'حجم فایل بیش از حد مجاز است',
        dictInvalidFileType: 'فرمت فایل غیر مجاز است',
        parallelUploads: 1,
        maxFiles: this.option.maxFiles ?? 99,
        chunking: this.option.chunking ?? true,
        chunkSize: 20000000,
        forceChunking: this.option.forceChunking ?? true,
        parallelChunkUploads: this.option.parallelChunkUploads ?? false,
        retryChunks: this.option.retryChunks ?? true,
        acceptedFiles: this.option.acceptedFiles ?? 'image/*,.pdf,.zip,.rar',
        resizeWidth: this.option.resizeWidth ?? null,
        resizeHeight: this.option.resizeHeight ?? null,
        resizeQuality: this.option.resizeQuality ?? 0.8,
        autoProcessQueue: this.option.autoProcessQueue ?? true
      },

      concatUrl:
        this.option && this.option.concatUrl
          ? this.option.concatUrl
          : '/api/ok',
      filesStatus: [],
      resStatus: false,
      fileCount: 0,
      trackIds: [],
      canSendBtn: false,

      selectedBank: '',

      shebaModal: false,
      shebaStatus: true,
      shebaBankErr: true,
      fieldError: true,
      handleManual: this.option.handleManual ?? false,

      wfcform: {
        iban: '',
        branch_name: '',
        branch_code: ''
      }
    };
  },

  computed: {
    isBankSelected() {
      return this.info.is_bank_needed && this.selectedBank ? true : false;
    }
  },

  watch: {
    filesStatus(val) {
      if (this.handleManual) this.resStatus = false;
      else if (val.length > 0) {
        const hasError = val.some(i => i.isSuccess === 0);
        this.resStatus = hasError ? false : true;
      } else {
        this.resStatus = false;
        this.canSendBtn = false;
      }
    }
  },

  mounted() {
    this.dropzoneOptions.headers.Authorization =
      'Bearer ' + Cookies.get('access_token');
    const descNode = this.$refs.fileDesc;
    const aTag = descNode.querySelectorAll('a');
    const dz = this.$refs.dz;

    if (aTag.length) {
      aTag.forEach(e => {
        e.addEventListener('click', () => {
          dz.disable();
          setTimeout(() => dz.enable(), 1);
        });
      });
    }
  },

  methods: {
    openModal() {
      this.shebaModal = true;
      /* if (this.chequeData) {
        this.wfcform.branch_name = this.chequeData.branch_name;
      } */
    },

    saveSheba() {
      this.shebaModal = false;
      this.shebaStatus = false;
    },

    template() {
      return `
        <div class="dz-preview dz-file-preview shadow-sm">
          <div class="file-details">
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex">
                <span class="size text-center text-md-left ltr roboto-mono-font pl-2" data-dz-size></span>
                <div class="d-flex aic footer-dz">
                  <a class="dz-remove" href="javascript:undefined;" data-dz-remove>
                    <i class="fas fa-trash"></i>
                  </a>
                  <span class="badge badge-pill badge-success py-1 px-2 mr-auto d-none">فایل معتبر است</span>
                  <span class="badge badge-pill badge-danger py-1 px-2 mr-auto d-none"></span>
                </div>
              </div>
              <span class="d-block text-center text-md-left ltr roboto-mono-font m-0 file-name" data-dz-name></span>
            </div>
            <div class="progress mt-3">
              <span class="progress-bar" data-dz-uploadprogress></span>
            </div>
            
          </div>
        </div>
      `;
    },

    beforeSend() {},

    beforeSave() {
      if (this.handleManual) {
        setTimeout(() => {
          /* const w = file.width;
          const h = file.height;
          console.log(w);
          console.log(h);
          if (w > h && w > 500) {
            this.dropzoneOptions.resizeWidth = 500;
          } else if (h > w && h > 500) {
            this.dropzoneOptions.resizeHeight = 500;
          } */
          const dz = this.$refs.dz;
          dz.processQueue();
        }, 100);
      }
    },

    success(file, res) {
      let j, len;
      const dz = this.$refs.dz;

      const progress = file.previewElement.querySelectorAll(
        '.dz-preview .progress'
      );
      const success = file.previewElement.querySelectorAll(
        '.dz-preview .badge-success'
      );

      for (j = 0, len = progress.length; j < len; j++) {
        progress[j].style.display = 'none';
        success[j].classList.remove('d-none');
      }

      if (this.option.chequeForProcess) {
        if (res.result && res.result.isbn) this.$emit('isbn', res.result.isbn);
        else if (res.result && res.result.chb) this.$emit('chb', 1);
        else {
          this.$emit('isbn', 'x');
          /* this.description =
            '<span class="text-danger ml-2">به نظر می‌رسد که تصویر خوبی از چک ارسال نکرده‌اید. با توجه به اینکه تصویر ارسالی توسط سیستم پردازش و صحت سنجی می‌شود، لطفاً از چک خود از زاویه مستقیم و واضح عکس بگیرید.</span>'; */
          dz.removeAllFiles();
        }
      }

      this.filesStatus.push({ uid: file.upload.uuid, isSuccess: 1 });
      this.fileCount = this.fileCount + 1;

      const r = JSON.parse(file.xhr.response);
      this.trackIds.push(r.result.track_id);
    },

    error(file, response) {
      let j, len;
      const progress = file.previewElement.querySelectorAll(
        '.dz-preview .progress'
      );
      const error = file.previewElement.querySelectorAll(
        '.dz-preview .badge-danger'
      );

      for (j = 0, len = progress.length; j < len; j++) {
        progress[j].style.display = 'none';
        error[j].classList.remove('d-none');

        if (typeof response === 'string') {
          error[j].innerHTML = response;
        } else {
          let msg = '';

          switch (response.message) {
          case 'Unauthenticated.':
            msg = 'خطا 401';
            break;
          case 'Format is invalid!':
            msg = 'فرمت فایل قابل قبول نیست';
            break;
          case 'Filename is too long!':
            msg = 'نام فایل طولانی است';
            break;
          case 'You can not upload any more files.':
            msg = 'شما بیش از حد مجاز فایل آپلود کرده اید. یکی را پاک کنید.';
            break;
          default:
            break;
          }

          error[j].innerHTML = msg;
        }
      }

      this.filesStatus.push({ uid: file.upload.uuid, isSuccess: 0 });
    },

    queueComplete() {
      if (!this.handleManual) this.canSendBtn = true;
    },

    saveFiles() {
      this.$refs.submit.loading('start');

      Upload.chunkConcat(this.concatUrl, {
        files: this.trackIds,
        bank_id: this.selectedBank.id,
        ...this.wfcform
      })
        .then(() => {
          this.resStatus = 'saved';
          this.$alerts.show({
            msg: 'فایل‌ها با موفقیت ارسال شده اند',
            type: 'success',
            style: 'float'
          });

          this.$parent.loadDocs('update', this.index).then(() => {
            this.$refs.submit.loading('end');
          });
        })
        .catch(error => {
          this.$alerts.errHandle(error);
        })
        .finally(() => {
          this.$refs.submit.loading('end');
        });
    },

    removed(file) {
      this.fileCount = this.fileCount - 1;

      this.filesStatus.forEach((item, i) => {
        if (item.uid === file.upload.uuid) {
          this.filesStatus.splice(i, 1);
        }
      });
    },

    handleSkip() {
      Custom.post(this.skipUrl)
        .then(r => {
          if (r.data.status) {
            this.$alerts.show({
              msg: 'درخواست شما انجام شد',
              type: 'success',
              style: 'float'
            });
            this.$parent.loadDocs('update', this.index);
          } else {
            this.$alerts.show({
              msg: 'متاسفانه درخواست شما انجام نشد',
              type: 'danger',
              style: 'float'
            });
          }
        })
        .catch(error => {
          this.loading = false;
          this.$alerts.errHandle(error);
        });
    },

    shebaErr(payload) {
      this.shebaBankErr = payload;
    },

    fieldErr(payload) {
      this.fieldError = payload;
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

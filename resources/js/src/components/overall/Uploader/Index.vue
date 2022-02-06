<template>
  <div>
    <vue-dropzone
      id="dropzone"
      ref="dz"
      :options="dropzoneOptions"
      :use-custom-slot="true"
      :class="[
        fileCount >= dropzoneOptions.maxFiles ? 'control-event' : null,
        resStatus ? 'file-success' : null
      ]"
      @vdropzone-sending="beforeSend"
      @vdropzone-file-added="beforeSave"
      @vdropzone-success="success"
      @vdropzone-error="error"
      @vdropzone-removed-file="removed"
      @vdropzone-queue-complete="queueComplete"
    >
      <div class="text-center">
        <!-- eslint-disable vue/no-v-html -->
        <h5
          class="title d-flex justify-content-center aic special-font"
          v-html="fileName"
        />

        <small
          v-if="rejectMsg"
          class="text-danger d-block"
        >
          {{ rejectMsg }}
        </small>

        <small
          ref="fileDesc"
          class="hint font-weight-light"
        >
          <!-- eslint-disable vue/no-v-html -->
          <span v-html="description" />
        </small>

        <div
          class="action-buttons d-flex flex-column flex-md-row align-items-center justify-content-center mt-4"
        >
          <g-bank-list
            v-if="info.is_bank_needed"
            v-model="selectedBank"
            class="mb-4 mb-md-0 ml-md-2"
            @click.native.stop
          />

          <button
            v-if="info.name === 'cheque'"
            v-show="shebaStatus == true"
            class="btn btn-sm rounded-pill ml-2 mb-md-0 btn-outline-primary"
            @click.stop="openModal()"
          >
            وروداطلاعات چک
          </button>
          <span v-show="shebaStatus == false">
            <i class="far fa-user-edit" />
          </span>
          <g-button
            v-if="info.name === 'cheque'"
            v-show="shebaStatus == false"
            ref="submit"
            text="ویرایش اطلاعات چک"
            color="#fff"
            class="mx-2 btn-outline-primary btn-transparent"
            sm
            @click.native.stop="openModal()"
          />

          <button
            v-if="fileCount < option.maxFiles"
            class="btn btn-sm rounded-pill ml-2 mb-md-0"
            :class="fileCount >= 1 ? 'btn-outline-primary' : 'btn-primary'"
          >
            <span v-if="fileCount >= 1">
              افزودن <span class="d-none d-md-inline">فایل‌های</span> بیشتر
            </span>
            <span v-else-if="option.handleManual">انتخاب و ارسال فایل</span>
            <span v-else>افزودن فایل</span>
          </button>

          <g-button
            v-show="canSendBtn && resStatus"
            ref="submit"
            :icon-right="
              (inArray(info.name, ['backup']) && !isBankSelected) ||
                (info.name == 'cheque' && (shebaBankErr || fieldError))
                ? 'arrow-right'
                : ''
            "
            :text="
              (inArray(info.name, ['backup']) && !isBankSelected) ||
                (info.name == 'cheque' && (shebaBankErr || fieldError))
                ? 'اطلاعات را تکمیل کنید'
                : 'ارسال برای بررسی'
            "
            :color="
              (inArray(info.name, ['backup']) && !isBankSelected) ||
                (info.name == 'cheque' && (shebaBankErr || fieldError))
                ? 'secondary'
                : 'success'
            "
            class="mr-md-2"
            :disabled="
              (inArray(info.name, ['backup']) && !isBankSelected) ||
                (info.name == 'cheque' && (shebaBankErr || fieldError))
            "
            sm
            @click.native.stop="saveFiles"
          />
        </div>

        <small
          v-if="info.name === 'backup' && skipUrl"
          class="mt-4 pt-4 border-top d-block"
        >
          <strong>
            پرینت حساب پشتیبان ندارید؟
          </strong>
          <span class="font-weight-light opa-7">
            در صورتی که پرینت حساب دیگری به جز حساب جاری خود ندارید، روی دکمه
            زیر کلیک کنید؛ در نظر داشته باشید که در اینصورت، احتمال کاهش اعتبار
            شما (کاهش سقف مجاز اقساط) وجود دارد.
          </span>

          <div class="d-flex justify-content-center mt-4">
            <g-button
              text="حساب پشتیبان ندارم"
              sm
              @click.native.stop="handleSkip"
            />
          </div>
        </small>

        <div
          v-if="canSendBtn && !resStatus"
          class="alert alert-danger py-2"
        >
          <small>
            بعضی‌ از مدارک دارای مشکل هستند، لطفا آن‌ها را حذف کنید و فایل دیگری
            را اضافه کنید
          </small>
        </div>
      </div>
    </vue-dropzone>

    <!-- sheba handler Modal -->
    <modal
      v-show="shebaModal"
      title="بررسی کد شبا"
      @close="shebaModal = false"
    >
      <template #body>
        <sheba-handler
          v-model="wfcform"
          :cheque-data="chequeData"
          @sheba-error="shebaErr"
          @field-error="fieldErr"
        />
      </template>

      <template #footer>
        <div class="d-flex w-100 justify-content-center">
          <g-button
            ref="submit"
            text="ثبت اطلاعات"
            color="success"
            class=" rounded-pill mb-4 mb-md-0 btn-primary mr-md-2"
            sm
            :disabled="fieldError || shebaBankErr"
            @click.native.stop="saveSheba"
          />
        </div>
      </template>
    </modal>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

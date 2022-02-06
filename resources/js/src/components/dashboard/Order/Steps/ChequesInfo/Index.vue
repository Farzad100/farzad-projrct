<template>
  <div>
    <g-docs
      v-if="showDocs || manager"
      :type="docType"
    />

    <div
      v-if="orderData.payback_type === 'cheque'"
      class="border shadow-sm rounded mt-4"
    >
      <div
        id="chequesForm"
        :class="[
          'border-top',
          {
            'show border-top-0': !$parent[manager ? 'order' : 'orderData']
              .account
          }
        ]"
      >
        <ValidationObserver v-if="chequesPreview">
          <!-- Cheques Information -->
          <div class="p-4 border-top">
            <!-- Title -->
            <div v-if="orderData.status === 'upload_secondary'">
              <span class="special-font font-weight-light">
                <span class="font-weight-bold opa-5">
                  ۲.
                </span>
                لطفا
                <span class="font-weight-bold text-success">
                  {{ chequesPreview.cheques.length | numToPersian }}
                  چک</span>، هر چک به مبلغ
                <span class="font-weight-bold text-success">
                  <span class="estedad-font">
                    {{
                      ($parent[manager ? 'order' : 'orderData'].ghest * 10)
                        | moneySeperate
                    }}
                  </span>
                  (<span class="text-success ">
                    {{
                      ($parent[manager ? 'order' : 'orderData'].ghest * 10)
                        | numToPersian
                    }} </span>) ریال
                </span>
                ، در وجه
                <span class="font-weight-bold estedad-font text-success">
                  {{ chequesPreview.name }}
                </span>
                ، شناسه ملی
                <span class="font-weight-bold estedad-font text-success">
                  {{ chequesPreview.nic }}
                </span>
                و تاریخ‌های فرم زیر آماده کنید
              </span>
              <div class="alert alert-info bg-white">
                <i class="fas fa-info" />
                <span
                  class="special-font mr-1"
                >نوشتن شناسه ملی در چک ها الزامی است. همچنین از بکاربردن انواع
                  چسب روی چک ها خودداری نمایید، چک های چسب خورده قابل قبول
                  نیست</span>
              </div>

              <div class="d-flex align-items-center mt-5">
                <span class="special-font">
                  لطفاً با کلیک روی هر مورد، اطلاعات چک موردنظر را وارد نمایید:
                </span>
              </div>
            </div>
            <div v-else>
              <span class="special-font font-weight-light">
                در حال بررسی چک‌های بارگذاری شده هستیم. نتیجه از طریق پیامک به
                اطلاعتان خواهد رسید.
              </span>
            </div>

            <!-- Form -->
            <div class="row">
              <div
                v-for="(item, i) in chequesPreview.cheques"
                :key="i"
                class="
                  col-12 col-md-4
                  d-inline-block
                  mt-3 pb-2
                "
              >
                <!-- Title -->
                <div class="d-flex align-items-center">
                  <button
                    class="ml-2 btn btn-sm btn-outline-primary rounded-pill w-100 "
                    :class="[
                      'border-' + itemClass(item).color,
                      'text-' + itemClass(item).color
                    ]"
                    @click="openModal({ i, item })"
                  >
                    <i
                      class="fas pl-2"
                      :class="'fa-' + itemClass(item).icon"
                    />
                    <strong class="special-font">
                      {{ item.oum }}
                    </strong>
                    <span class="px-2">⟵</span>
                    <strong class="ltr estedad-font d-inline-flex">{{
                      item.date
                    }}</strong>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </ValidationObserver>
      </div>

      <div
        v-if="
          role === 'admin' &&
            orderData.status === 'check_secondary' &&
            showRejectBtn
        "
        class="text-center my-5"
      >
        <g-button
          ref="rejectBtn"
          text="اطلاع‌رسانی موارد رد شده به مشتری"
          color="danger"
          sm
          @click.native="sendBack"
        />
      </div>
    </div>

    <!-- cheque handler Modal -->
    <modal
      v-if="checkModal"
      title=""
      @close="checkModal = false"
    >
      <template #body>
        <div
          v-for="(item, i) in chequesNumberList"
          :key="i"
        >
          <template>
            <div class="container">
              <div class="large-12 medium-12 small-12 cell">
                <!-- Void || Rejected -->
                <div
                  v-if="item.status === 'void' || item.status === 'rejected'"
                >
                  <!-- Rejected -->
                  <div
                    v-if="item.comment && item.status === 'rejected'"
                    class="text-danger mt-3 text-justify"
                    style="line-height: 2;"
                  >
                    <!-- eslint-disable vue/no-v-html -->
                    <ul v-html="item.comment" />
                  </div>

                  <uploader
                    :reject-msg="
                      item.status === 'rejected' ? item.message : null
                    "
                    :index="i"
                    :file-name="
                      'تصویر ' +
                        item.oum +
                        '<span class=\'ltr estedad-font d-inline-flex mr-2\'> (' +
                        item.date +
                        ')</span>'
                    "
                    :description="item.description"
                    class="mt-3"
                    :info="{
                      name: 'تصویر ' + item.oum,
                      is_bank_needed: item.is_bank_needed
                    }"
                    :option="{
                      resizeWidth: 1920,
                      chunking: false,
                      forceChunking: false,
                      retryChunks: false,
                      url: item.url,
                      maxFiles: 1,
                      acceptedFiles: 'image/*',
                      autoProcessQueue: false,
                      handleManual: true,
                      chequeForProcess: true
                    }"
                    @isbn="fillIsbn"
                  />
                  <div class="text-center mt-2">
                    <a
                      href="/images/cheque-sample.jpg"
                      target="_blank"
                    >مشاهده نمونه تصویری</a>
                  </div>
                </div>

                <!-- Attention -->
                <div
                  v-else-if="item.status === 'attention'"
                  class="mt-3"
                >
                  <!-- Admin actions -->
                  <div
                    v-if="role === 'admin'"
                    class="d-flex align-items-center"
                  >
                    <g-button
                      v-if="item.format !== null"
                      text="دانلود تصویر چک"
                      color="outline-primary"
                      sm
                      :badge="item.bank_id ? bankName(item.bank_id) : null"
                      class="ml-2"
                      @click.native="download(item)"
                    />
                    <g-button
                      v-if="item.need_chb && item.chb_uploaded"
                      text="دانلود پشت چک"
                      color="outline-primary"
                      sm
                      class="ml-2"
                      @click.native="download(item, 'back')"
                    />
                  </div>

                  <div class="text-center">
                    <p class="text-primary mb-0">
                      شما تصویر {{ item.oum }} به تاریخ
                      <strong class="ltr estedad-font d-inline-flex">{{
                        item.date
                      }}</strong>
                      را بارگذاری کرده اید، اما:
                    </p>
                    <div
                      v-if="item.comment"
                      class="text-danger mt-3 text-justify"
                      style="line-height: 2;"
                    >
                      <!-- eslint-disable vue/no-v-html -->
                      <ul v-html="item.comment" />
                    </div>
                    <p
                      v-else
                      class="text-danger h5 mt-3"
                    >
                      {{ item.message }}
                    </p>
                  </div>
                </div>

                <!-- Pending -->
                <div v-else-if="item.status === 'pending'">
                  <div class="doc-alert _checking mt-3">
                    <!-- Title -->
                    <h6 class="special-font m-0">
                      تصویر {{ item.oum }}
                      <span class="ltr estedad-font d-inline-flex">
                        ({{ item.date }})</span>
                    </h6>

                    <!-- not(admin) status -->
                    <span
                      v-if="role !== 'admin'"
                      class="_status"
                    >
                      در صف بررسی
                      <div class="icon">
                        <i class="fas fa-barcode-scan" />
                      </div>
                    </span>

                    <!-- Admin actions -->
                    <div
                      v-else
                      class="d-flex align-items-center"
                    >
                      <g-button
                        v-if="item.format !== null"
                        text="دانلود تصویر چک"
                        color="outline-primary"
                        sm
                        :badge="item.bank_id ? bankName(item.bank_id) : null"
                        class="ml-2"
                        @click.native="download(item)"
                      />
                      <g-button
                        v-if="item.need_chb && item.chb_uploaded"
                        text="دانلود پشت چک"
                        color="outline-primary"
                        sm
                        class="ml-2"
                        @click.native="download(item, 'back')"
                      />
                    </div>
                  </div>
                  <div
                    v-if="role === 'admin'"
                    class="text-center mt-3"
                  />
                  <div
                    v-else
                    class="text-center mt-3"
                  >
                    پس از بررسی چک‌ها نتیجه از طریق پیامک اطلاع داده می‌شود.
                  </div>
                </div>

                <!-- Verified -->
                <div
                  v-else-if="item.status === 'verified'"
                  class="doc-alert _success mt-3"
                >
                  <!-- Title -->
                  <h6 class="special-font m-0">
                    تصویر {{ item.oum }}
                    <span class="ltr estedad-font d-inline-flex">
                      ({{ item.date }})</span>
                  </h6>

                  <!-- not(admin) status -->
                  <span
                    v-if="role !== 'admin'"
                    class="_status"
                  >
                    {{ item.message }}
                    <div class="icon">
                      <i class="fas fa-check" />
                    </div>
                  </span>

                  <!-- Admin actions -->
                  <div
                    v-else
                    class="d-flex align-items-center"
                  >
                    <g-button
                      v-if="item.format !== null"
                      text="دانلود تصویر چک"
                      color="outline-primary"
                      sm
                      :badge="item.bank_id ? bankName(item.bank_id) : null"
                      class="ml-2"
                      @click.native="download(item)"
                    />
                    <g-button
                      v-if="item.need_chb && item.chb_uploaded"
                      text="دانلود پشت چک"
                      color="outline-primary"
                      sm
                      class="ml-2"
                      @click.native="download(item, 'back')"
                    />
                  </div>
                </div>

                <uploader
                  v-if="item.need_chb && !item.chb_uploaded"
                  :index="i"
                  :file-name="
                    'تصویر پشت ' +
                      item.oum +
                      '<span class=\'ltr estedad-font d-inline-flex mr-2\'> (' +
                      item.date +
                      ')</span>'
                  "
                  :description="
                    'بعد از اینکه موارد را پشت نویسی کردید، از زاویه واضح از آن عکس بگیرید و تصویر آن را بارگذاری کنید.'
                  "
                  class="mt-3"
                  :info="{
                    name: 'تصویر پشت ' + item.oum
                  }"
                  :option="{
                    resizeWidth: 1920,
                    chunking: false,
                    forceChunking: false,
                    retryChunks: false,
                    url: item.url_back,
                    maxFiles: 1,
                    acceptedFiles: 'image/*',
                    autoProcessQueue: false,
                    handleManual: true,
                    chequeForProcess: true
                  }"
                />
              </div>

              <div class="border border-1 text-center p-3 mt-3">
                <div
                  class="
                  d-flex
                  flex-column
                  flex-sm-row
                  align-items-start
                  pb-2 
                "
                >
                  <div
                    v-if="
                      !inArray(item.status, ['pending', 'verified']) ||
                        role === 'admin'
                    "
                    class="_c-s-inputs w-100"
                  >
                    <div class="mb-2">
                      شماره سریال چک:
                    </div>
                    <!-- 017 - Melli bank -->
                    <template v-if="shabaBank.name === 'Melli'">
                      <div class="def-cheques ltr estedad-font">
                        <ValidationProvider
                          v-slot="{ errors }"
                          rules="required"
                          class="_right-slash"
                        >
                          <input
                            v-model="wfcForm.prepend"
                            maxlength="2"
                            placeholder="07"
                            :class="{ 'is-invalid': errors.length }"
                            :disabled="item.badge !== 0"
                            class="form-control"
                          >
                        </ValidationProvider>

                        <ValidationProvider
                          v-slot="{ errors }"
                          rules="required"
                          class="_right-slash"
                        >
                          <input
                            v-model="wfcForm.append"
                            :disabled="item.badge !== 0"
                            maxlength="6"
                            placeholder="9905"
                            :class="{ 'is-invalid': errors.length }"
                            class="form-control"
                          >
                        </ValidationProvider>

                        <ValidationProvider
                          v-slot="{ errors }"
                          rules="required|chequeNumber"
                          class="_validation-wrapper"
                        >
                          <input
                            v-model="wfcForm.cheque_numbers[item.badge].series"
                            maxlength="6"
                            :class="{ 'is-invalid': errors.length }"
                            placeholder="123456"
                            class="form-control"
                          >
                        </ValidationProvider>
                      </div>
                    </template>

                    <!-- 012 - Mellat bank -->
                    <template v-else-if="shabaBank.name === 'Mellat'">
                      <div class="def-cheques ltr estedad-font">
                        <ValidationProvider
                          v-slot="{ errors }"
                          rules="required"
                          class="_right-slash"
                        >
                          <input
                            v-model="wfcForm.prepend"
                            maxlength="6"
                            placeholder="9103"
                            :class="{ 'is-invalid': errors.length }"
                            :disabled="item.badge !== 0"
                            class="form-control"
                          >
                        </ValidationProvider>

                        <ValidationProvider
                          v-slot="{ errors }"
                          :name="`شماره ${item.oum}`"
                          rules="required|chequeNumber"
                          class="_validation-wrapper"
                        >
                          <input
                            v-model="wfcForm.cheque_numbers[item.badge].series"
                            maxlength="6"
                            :class="{ 'is-invalid': errors.length }"
                            placeholder="123456"
                            class="form-control"
                          >
                        </ValidationProvider>

                        <ValidationProvider
                          v-slot="{ errors }"
                          rules="required"
                          class="_left-slash"
                        >
                          <input
                            v-model="wfcForm.cheque_numbers[item.badge].append"
                            maxlength="2"
                            placeholder="57"
                            :class="{ 'is-invalid': errors.length }"
                            class="form-control"
                          >
                        </ValidationProvider>
                      </div>
                    </template>

                    <!-- 021 - Post bank -->
                    <template v-else-if="shabaBank.name === 'Postbank'">
                      <div class="post-cheques-series ltr estedad-font">
                        <div class="_right-slash">
                          <ValidationProvider
                            v-slot="{ errors }"
                            rules="required"
                            class="_sm-2 _below-line"
                          >
                            <input
                              v-model="wfcForm.prepend"
                              :disabled="i !== 0"
                              placeholder="1314"
                              maxlength="6"
                              :class="{ 'is-invalid': errors.length }"
                              class="form-control"
                            >
                          </ValidationProvider>
                          <input
                            value="21"
                            disabled
                          >
                        </div>

                        <ValidationProvider
                          v-slot="{ errors }"
                          rules="required|chequeNumber"
                          class="_validation-wrapper"
                        >
                          <input
                            v-model="wfcForm.cheque_numbers[item.badge].series"
                            maxlength="6"
                            :class="{ 'is-invalid': errors.length }"
                            placeholder="123456"
                            class="form-control"
                          >
                        </ValidationProvider>
                      </div>
                    </template>

                    <!-- Other banks -->
                    <template v-else>
                      <div class="def-cheques ltr estedad-font">
                        <ValidationProvider
                          v-slot="{ errors }"
                          rules="required"
                          class="_right-slash"
                        >
                          <input
                            v-model="wfcForm.prepend"
                            maxlength="6"
                            :placeholder="i === 0 ? '4313' : null"
                            :class="{ 'is-invalid': errors.length }"
                            :disabled="i !== 0"
                            class="form-control"
                          >
                        </ValidationProvider>

                        <ValidationProvider
                          v-slot="{ errors }"
                          rules="required|chequeNumber"
                          class="_validation-wrapper"
                        >
                          <input
                            v-model="wfcForm.cheque_numbers[item.badge].series"
                            maxlength="6"
                            :class="{ 'is-invalid': errors.length }"
                            :placeholder="'123456'"
                            class="form-control"
                          >
                        </ValidationProvider>
                      </div>
                    </template>

                    <div class="d-flex align-items-end">
                      <ValidationProvider
                        v-slot="{ errors }"
                        class="w-100"
                        rules="required|sayadi"
                        name="شماره صیادی"
                      >
                        <div class="mt-4">
                          شماره صیادی 16 رقمی روی چک:
                        </div>
                        <the-mask
                          v-model="wfcForm.cheque_numbers[item.badge].isbn"
                          class="form-control mt-2 ltr text-center estedad-font"
                          :mask="['#### #### #### ####']"
                          :placeholder="`شماره صیادی ${item.oum}`"
                          :disabled="
                            item.is_readable === 1 && item.isbn_verified_at
                          "
                        />
                        <div class="d-flex justify-content-between">
                          <small
                            v-if="
                              item.is_readable === 1 && !item.isbn_verified_at
                            "
                            class="text-danger"
                          >
                            <i class="far fa-exclamation-triangle ml-1" />
                            احتمالاً یک خطای کوچک در این شماره هست
                          </small>
                          <small
                            v-else-if="
                              !item.isbn_verified_at && role === 'admin'
                            "
                            class="text-danger"
                          >
                            توسط کاربر وارد شده و قابل اطمینان نیست
                          </small>
                          <small
                            v-else
                            class="text-danger"
                          >
                            {{ errors[0] }}
                          </small>
                          <button
                            v-show="
                              role === 'admin' &&
                                wfcForm.cheque_numbers[item.badge].isbn
                            "
                            class="btn btn-link p-0 text-decoration-none"
                            type="button"
                            @click="
                              copyClip(wfcForm.cheque_numbers[item.badge].isbn)
                            "
                          >
                            <small>
                              کپی
                            </small>
                          </button>
                        </div>
                      </ValidationProvider>

                      <div
                        v-if="role != 'admin'"
                        :class="
                          item.is_readable === null ||
                            (item.is_readable === 1 && item.isbn_verified_at)
                            ? 'd-none'
                            : ''
                        "
                      >
                        <g-button
                          class="mr-2 mt-2 py-2"
                          text=""
                          icon-left="qrcode"
                          @click.native="qrcode = true"
                        />
                      </div>
                    </div>

                    <QrReader
                      v-if="qrcode"
                      return="isbn"
                      @close="qrcode = false"
                    />
                  </div>
                </div>

                <g-button
                  v-if="role === 'admin'"
                  ref="submit"
                  text="اصلاح اطلاعات چک (اگر نیاز است)"
                  color="light"
                  class=" rounded-pill mb-4 mb-md-0 btn-primary mr-md-2"
                  sm
                  @click.native="editTheCheque"
                />
              </div>

              <div
                v-if="!inArray(item.status, ['pending', 'verified'])"
                class="border border-1 mt-3 p-3"
                style="border-radius: 4px;"
                :class="
                  wfcForm.cheque_numbers[item.badge].is_submitted
                    ? 'border-success'
                    : 'border-danger'
                "
              >
                <div class="form-check form-switch cursor-pointer">
                  <input
                    id="flexSwitchCheckDefault"
                    v-model="wfcForm.cheque_numbers[item.badge].is_submitted"
                    class="form-check-input"
                    type="checkbox"
                  >
                  <label
                    class="form-check-label"
                    for="flexSwitchCheckDefault"
                  >
                    اطلاعات این چک را در سامانه صیاد ثبت کرده ام</label>
                </div>
                <hr>
                <div
                  class="text-justifyx"
                  style="text-align: justify;"
                >
                  <small>چک‌هایی که در سامانه صیاد ثبت نشده باشند، فاقد ارزش هستند.
                    اگر از چک ‌های قدیمی استفاده می‌کنید، نیازی به ثبت نیست و
                    می‌توانید بدون ثبت صیادی، این گزینه را تیک بزنید.</small>
                </div>
              </div>
            </div>
          </template>
        </div>
      </template>

      <template #footer>
        <div
          v-if="orderData.status === 'upload_secondary'"
          class="d-flex w-100 justify-content-center"
        >
          <g-button
            ref="submit"
            :text="
              !inArray(item.status, ['pending', 'verified']) || role === 'admin'
                ? 'ثبت اطلاعات'
                : 'بستن'
            "
            :color="
              !inArray(item.status, ['pending', 'verified']) || role === 'admin'
                ? 'success'
                : 'light'
            "
            class=" rounded-pill mb-4 mb-md-0 btn-primary mr-md-2"
            sm
            @click.native="checkTheCheque"
          />
        </div>
        <div
          v-else-if="orderData.status === 'check_secondary' && role === 'admin'"
          class="w-100"
        >
          <!-- Reject Doc -->
          <div class="light-danger-bg border-top border-dark py-4 px-3">
            <div class="col-12 mb-4">
              <label>دلیل رد را انتخاب کنید:</label>
              <g-multi-select
                v-model="rejectReason"
                :options="rejectOptions"
                :name="'status'"
                :max="5"
              />
            </div>
            <g-button
              ref="rejectButton"
              text="رد این مدرک"
              color="danger"
              block
              center
              @click.native="reject"
            />
          </div>
          <!-- Accept Doc -->
          <div class="light-success-bg py-4 px-3 d-flex justify-content-evenly">
            <g-button
              v-if="!item.photo_verified_at"
              ref="acceptButtonPhoto"
              text="تأیید تصویر چک"
              color="success"
              center
              @click.native="accept('photo')"
            />
            <g-button
              v-if="!item.submit_verified_at"
              ref="acceptButtonSubmit"
              text="تأیید ثبت صیاد"
              color="success"
              center
              @click.native="accept('submit')"
            />
            <g-button
              v-if="item.submit_verified_at && item.photo_verified_at"
              ref="acceptButtonSubmit"
              text="حذف تأییدیه‌ها"
              color="outline-secondary"
              center
              @click.native="accept('reset')"
            />
          </div>
        </div>
      </template>
    </modal>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

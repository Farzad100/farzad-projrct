<template>
  <div>
    <div
      v-if="loading"
      class="page-loading my-3"
    >
      <div class="spinner-border" />
    </div>

    <div
      v-else
      class="mt-3"
    >
      <!-- CTA -->
      <div
        v-if="cta"
        :class="cta ? 'active' : null"
        class="
        shadow-sm border rounded p-4
        d-flex flex-column flex-lg-row justify-content-center
        justify-content-lg-between align-items-center
      "
      >
        <!-- Message -->
        <span v-if="cta && cta.description">
          {{ cta.description }}
        </span>

        <!-- CTA button -->
        <div class="mr-5">
          <!-- Route changer -->
          <router-link
            v-if="cta.method === 'redirect'"
            class="btn btn-primary rounded-pill text-nowrap"
            :to="{
              name: cta.url,
              query: { fromOrder: $route.params.id }
            }"
          >
            {{ cta.text }}
          </router-link>

          <!-- Action -->
          <g-button
            v-else
            ref="ctaButton"
            :text="cta.text"
            @click.native="submitReq(cta.url)"
          />
        </div>
      </div>

      <!-- Docs -->
      <div
        v-for="(doc, i) in docs"
        :key="i"
      >
        <!-- Uploader -->
        <uploader
          v-if="doc.status === 'void' || doc.status === 'rejected'"
          :file-name="doc.title"
          :description="doc.description"
          :skip-url="doc.skip_url"
          :reject-msg="doc.status === 'rejected' ? doc.message : null"
          :index="i"
          :info="{
            name: doc.name,
            is_bank_needed: doc.is_bank_needed
          }"
          class="mt-3"
          :option="{
            url: doc.url,
            concatUrl: doc.concatUrl,
            maxFiles: doc.maxFiles,
            acceptedFiles: doc.acceptedFiles ? doc.acceptedFiles : null
          }"
          :cheque-data="doc.account"
        />

        <!-- Pending -->
        <div
          v-else-if="doc.status === 'pending'"
          class="doc-alert _checking mt-3"
        >
          <!-- Title -->
          <h4 class="special-font m-0">
            {{ doc.title }}
          </h4>

          <!-- not(admin) status -->
          <span
            v-if="role !== 'admin'"
            class="_status"
          >
            {{ doc.message }}
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
              v-if="doc.format !== null"
              :text="
                `دانلود - ${doc.format}`
              "
              color="outline-primary"
              sm
              :badge="doc.bank_id ? bankName(doc.bank_id) : null"
              class="ml-2"
              @click.native="download(doc)"
            />
            <g-button
              text="بررسی مدرک"
              color="warning"
              sm
              @click.native="check(doc)"
            />
          </div>
        </div>

        <!-- Verified -->
        <div
          v-else-if="doc.status === 'verified'"
          class="doc-alert _success mt-3"
        >
          <!-- Title -->
          <h4 class="special-font m-0">
            {{ doc.title }}
          </h4>

          <!-- not(admin) status -->
          <span
            v-if="role !== 'admin'"
            class="_status"
          >
            {{ doc.message }}
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
              v-if="doc.format !== null"
              :text="
                `دانلود - ${doc.format}`
              "
              color="outline-primary"
              sm
              :badge="doc.bank_id ? bankName(doc.bank_id) : null"
              class="ml-2"
              @click.native="download(doc)"
            />
            <g-button
              text="بررسی مجدد"
              color="primary"
              sm
              @click.native="check(doc)"
            />
          </div>
        </div>
      </div>

      <!-- Check Docs Modal -->
      <modal
        v-show="docsCheckModal"
        :title="docInfo.title"
        @close="docsCheckModal = false"
      >
        <template #body>
          <!-- Download Doc -->
          <button
            v-if="docInfo.format"
            class="btn btn-light d-flex justify-content-between w-100 py-3 mb-4"
            @click="download(null)"
          >
            <div class="text-right">
              <span class="special-font font-weight-bold d-block">
                دانلود
                {{ docInfo.title }}
              </span>
            </div>

            <div
              v-if="docsCheckModalFileLinksLoading"
              class="spinner-border d-block"
              role="status"
            />

            <i
              v-else
              class="far fa-cloud-download fa-lg"
            />
          </button>
          <div
            v-else
            class="mb-3 alert alert-info"
          >
            <i class="fas fa-info-circle ml-1" />
            کاربر اظهار کرده نیازی به بارگذاری این مدرک ندارد
          </div>

          <div
            v-if="docInfo.name === 'cheque' && docInfo.account"
            class="mb-3"
          >
            <table class="table table-striped">
              <tr>
                <td>شماره شبا</td>
                <td class="h5 pb-2">
                  {{ docInfo.account.iban | cardNumber }}
                </td>
              </tr>
              <tr>
                <td>نام شعبه</td>
                <td class="h5 pb-2">
                  {{ docInfo.account.branch_name }}
                </td>
              </tr>
              <tr>
                <td>شماره شعبه</td>
                <td class="h5">
                  {{ docInfo.account.branch_code }}
                </td>
              </tr>
            </table>
          </div>

          <div
            v-if="docInfo.is_readable_url"
            class="border d-flex align-items-center justify-content-center mb-4 shadow-sm rounded px-3"
          >
            <div class="btn-group btn-group-sm btn-pill my-4 mr-3">
              <input
                id="yes"
                v-model="docInfo.is_readable"
                :value="1"
                type="radio"
                class="btn-check"
                name="machine_readabe"
                autocomplete="off"
                @change="docIsReadableChange(1)"
              >
              <label
                class="btn"
                for="yes"
              >
                اینترنت بانک
              </label>

              <input
                id="no"
                v-model="docInfo.is_readable"
                :value="0"
                type="radio"
                class="btn-check"
                name="machine_readabe"
                autocomplete="off"
                checked
                @change="docIsReadableChange(0)"
              >
              <label
                class="btn"
                for="no"
              >
                غیر اینترنت بانک
              </label>
            </div>
          </div>

          <!-- Accept Doc -->
          <div class="light-success-bg py-4 px-3">
            <g-button
              ref="acceptButton"
              text="تایید این مدرک"
              color="success"
              block
              center
              @click.native="accept"
            />
          </div>

          <!-- Reject Doc -->
          <div class="light-danger-bg border-top border-dark py-4 px-3">
            <label>دلیل رد سفارش</label>
            <textarea
              v-model="docsCheckModalRejectReason"
              class="form-control mt-2"
              rows="3"
            />
            <p class="my-4 special-font font-weight-light">
              قسطا،
              <br>
              <span
                v-if="$parent.order && $parent.order.user"
                class="font-weight-bold"
              >
                {{ $parent.order.user.fname }}
              </span>
              عزیز، تصویر
              <span class="font-weight-bold">
                {{ docInfo.title }}
              </span>
              شما به دلیل
              <span class="font-weight-bold">
                {{
                  docsCheckModalRejectReason
                    ? docsCheckModalRejectReason
                    : '...'
                }}
              </span>
              رد شد. لطفاً به سفارش شماره
              <span class="font-weight-bold">
                {{ $route.params.id }}
              </span>
              مراجعه کرده و مجدداً آن را بارگذاری نمایید.
              <br>
              باتشکر
            </p>

            <g-button
              ref="rejectButton"
              text="رد این مدرک"
              color="danger"
              block
              center
              @click.native="reject"
            />
          </div>
        </template>

        <template #footer>
          <div class="d-flex w-100 justify-content-center">
            <button
              class="btn btn-light rounded-pill"
              @click="docsCheckModal = false"
            >
              انصراف
            </button>
          </div>
        </template>
      </modal>
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

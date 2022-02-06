<template>
  <modal
    v-show="allDatesInfoModal"
    title="تاریخ‌ها"
    @close="allDatesInfoModal = false"
  >
    <template #body>
      <div
        v-if="$parent.order.all_dates"
        class="_timeline"
      >
        <div
          v-for="(date, dateIndex) in allDates"
          :key="dateIndex"
        >
          <div
            :class="['shadow-sm rounded border', date.color]"
          >
            <div
              v-if="date.icon"
              class="_icon"
            >
              <i :class="['fad', `fa-${date.icon}`]" />
            </div>
            <div class="_text">
              <small>
                {{ date.date | jDate }}
              </small>
              <small class="opa-7 mr-1">
                {{ date.date | jTime }}
              </small>
              <span class="special-font">
                {{ date.title }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- TODO: remove code below -->
      <div
        v-else
        class="list-view border w-100 rounded m-0"
      >
        <div
          v-if="$parent.order.created_at"
          class="list-view-item"
        >
          <div class="d-flex aic flex-wrap">
            <div class="title">
              <span class="font-weight-light">ثبت</span>
            </div>
            <div class="mr-auto">
              <strong>
                {{ $parent.order.created_at | jDate }}
              </strong>
              <small class="opa-7 mr-1">
                {{ $parent.order.created_at | jTime }}
              </small>
            </div>
          </div>
        </div>

        <div
          v-if="$parent.order.docs_uploaded_at"
          class="list-view-item"
        >
          <div class="d-flex aic flex-wrap">
            <div class="title">
              <span class="font-weight-light">بارگذاری مدارک اولیه</span>
            </div>
            <div class="mr-auto">
              <strong>
                {{ $parent.order.docs_uploaded_at | jDate }}
              </strong>
              <small class="opa-7 mr-1">
                {{ $parent.order.docs_uploaded_at | jTime }}
              </small>
            </div>
          </div>
        </div>

        <div
          v-if="$parent.order.organ_accepted_at"
          class="list-view-item"
        >
          <div class="d-flex aic flex-wrap">
            <div class="title">
              <span class="font-weight-light">تایید توسط سازمان</span>
            </div>

            <div class="mr-auto">
              <strong>
                {{ $parent.order.organ_accepted_at | jDate }}
              </strong>
              <small class="opa-7 mr-1">
                {{ $parent.order.organ_accepted_at | jTime }}
              </small>
            </div>
          </div>
        </div>

        <div
          v-if="$parent.order.docs_warning_at"
          class="list-view-item"
        >
          <div class="d-flex aic flex-wrap">
            <div class="title">
              <span class="font-weight-light">مهلت بررسی مدارک</span>
            </div>
            <div class="mr-auto">
              <strong>
                {{ $parent.order.docs_warning_at | jDate }}
              </strong>
              <small class="opa-7 mr-1">
                {{ $parent.order.docs_warning_at | jTime }}
              </small>
            </div>
          </div>
        </div>

        <div
          v-if="$parent.order.docs_accepted_at"
          class="list-view-item"
        >
          <div class="d-flex aic flex-wrap">
            <div class="title">
              <span class="font-weight-light">تایید مدارک اولیه</span>
            </div>
            <div class="mr-auto">
              <strong>
                {{ $parent.order.docs_accepted_at | jDate }}
              </strong>
              <small class="opa-7 mr-1">
                {{ $parent.order.docs_accepted_at | jTime }}
              </small>
            </div>
          </div>
        </div>

        <div
          v-if="$parent.order.secondary_uploaded_at"
          class="list-view-item"
        >
          <div class="d-flex aic flex-wrap">
            <div class="title">
              <span class="font-weight-light">بارگذاری تضامین</span>
            </div>
            <div class="mr-auto">
              <strong>
                {{ $parent.order.secondary_uploaded_at | jDate }}
              </strong>
              <small class="opa-7 mr-1">
                {{ $parent.order.secondary_uploaded_at | jTime }}
              </small>
            </div>
          </div>
        </div>

        <div
          v-if="$parent.order.secondary_accepted_at"
          class="list-view-item"
        >
          <div class="d-flex aic flex-wrap">
            <div class="title">
              <span class="font-weight-light">تایید تضامین</span>
            </div>
            <div class="mr-auto">
              <strong>
                {{ $parent.order.secondary_accepted_at | jDate }}
              </strong>
              <small class="opa-7 mr-1">
                {{ $parent.order.secondary_accepted_at | jTime }}
              </small>
            </div>
          </div>
        </div>

        <div
          v-if="$parent.order.docs_received_at"
          class="list-view-item"
        >
          <div class="d-flex aic flex-wrap">
            <div class="title">
              <span class="font-weight-light">تحویل مدارک</span>
            </div>
            <div class="mr-auto">
              <strong>
                {{ $parent.order.docs_received_at | jDate }}
              </strong>
              <small class="opa-7 mr-1">
                {{ $parent.order.docs_received_at | jTime }}
              </small>
            </div>
          </div>
        </div>

        <div
          v-if="$parent.order.prepaid_at"
          class="list-view-item"
        >
          <div class="d-flex aic flex-wrap">
            <div class="title">
              <span class="font-weight-light">پیش پرداخت</span>
            </div>
            <div class="mr-auto">
              <strong>
                {{ $parent.order.prepaid_at | jDate }}
              </strong>
              <small class="opa-7 mr-1">
                {{ $parent.order.prepaid_at | jTime }}
              </small>
            </div>
          </div>
        </div>

        <div
          v-if="$parent.order.charged_at"
          class="list-view-item"
        >
          <div class="d-flex aic flex-wrap">
            <div class="title">
              <span class="font-weight-light">شارژ</span>
            </div>
            <div class="mr-auto">
              <strong>
                {{ $parent.order.charged_at | jDate }}
              </strong>
              <small class="opa-7 mr-1">
                {{ $parent.order.charged_at | jTime }}
              </small>
            </div>
          </div>
        </div>

        <div
          v-if="$parent.order.first_ghest_at"
          class="list-view-item"
        >
          <div class="d-flex aic flex-wrap">
            <div class="title">
              <span class="font-weight-light">اولین قسط</span>
            </div>
            <div class="mr-auto">
              <strong>
                {{ $parent.order.first_ghest_at | jDate }}
              </strong>
              <small class="opa-7 mr-1">
                {{ $parent.order.first_ghest_at | jTime }}
              </small>
            </div>
          </div>
        </div>

        <div
          v-if="$parent.order.closed_at"
          class="list-view-item"
        >
          <div class="d-flex aic flex-wrap">
            <div class="title">
              <span class="font-weight-light">بسته شدن پرونده</span>
            </div>
            <div class="mr-auto">
              <strong>
                {{ $parent.order.closed_at | jDate }}
              </strong>
              <small class="opa-7 mr-1">
                {{ $parent.order.closed_at | jTime }}
              </small>
            </div>
          </div>
        </div>

        <div
          v-if="$parent.order.deleted_at"
          class="list-view-item"
        >
          <div class="d-flex aic flex-wrap">
            <div class="title">
              <span class="font-weight-light">حذف</span>
            </div>
            <div class="mr-auto">
              <strong>
                {{ $parent.order.deleted_at | jDate }}
              </strong>
              <small class="opa-7 mr-1">
                {{ $parent.order.deleted_at | jTime }}
              </small>
            </div>
          </div>
        </div>
      </div>
    </template>

    <template #footer>
      <div class="d-flex w-100 justify-content-center">
        <button
          class="btn btn-light rounded-pill"
          @click="allDatesInfoModal = false"
        >
          انصراف
        </button>
      </div>
    </template>
  </modal>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>
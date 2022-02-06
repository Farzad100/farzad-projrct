<template>
  <div>
    <template v-if="info.status === 'uploading' || info.status === 'docs_uploaded'">
      <h5
        v-if="info.status === 'uploading'"
        class="special-font mb-5"
      >
        لطفا مدارک مورد نیاز جهت اعتبار سنجی را آپلود کنید
      </h5>

      <h5
        v-else-if="info.status === 'docs_uploaded'"
        class="alert alert-info mb-5"
      >
        مدارک در دست بررسی هستند
      </h5>
      
      <g-docs />
    </template>

    <template v-else>
      <div class="d-flex justify-content-between align-items-end mb-4">
        <div class="mb-4 mb-xl-0">
          <h6 class="opa-5">
            {{ info.name }}
          </h6>
          <h1
            v-if="info.fame"
            class="font-weight-bold special-font m-0 mt-3"
          >
            {{ info.fame }}
          </h1>
        </div>

        <div class="d-flex">
          <span class="border rounded py-2 px-3 font-weight-bold">
            <span class="opa-5 ml-2">
              کد سازمانی
            </span>
            {{ info.code }}
          </span>
        </div>
      </div>
          
      <div class="border rounded shadow-sm">
        <div class="d-flex justify-content-between align-items-center bg-gray-light rounded p-4">
          <h4 class="special-font m-0 text-brand">
            اطلاعات سازمان
          </h4>

          <g-button
            text="مشاهده"
            sm
            color="primary"
            data-bs-toggle="collapse"
            data-bs-target="#OrganInfo"
            aria-expanded="false"
            aria-controls="collapseExample" 
          />
        </div>

        <div
          id="OrganInfo"
          class="collapse"
        >
          <div
            v-if="info.status_farsi"
            class="list-view list-view-lg w-100 m-0 py-2"
          >
            <div class="list-view-item">
              <div class="d-flex aic flex-wrap">
                <div class="title w-25">
                  <span class="font-weight-light">وضعیت</span>
                </div>
                <div>
                  <span
                    class="badge rounded-pill p-2 px-3 font-weight-light"
                    :class="handleStatus(info.status)"
                  >
                    {{ info.status_farsi.farsi }}
                  </span>
                </div>
              </div>
            </div>

            <div
              v-if="info.company.phone"
              class="list-view-item"
            >
              <div class="d-flex aic flex-wrap">
                <div class="title w-25">
                  <span class="font-weight-light">تلفن</span>
                </div>
                <div class="font-weight-bold">
                  {{ info.company.phone }}
                </div>
              </div>
            </div>

            <div
              v-if="info.email"
              class="list-view-item"
            >
              <div class="d-flex aic flex-wrap">
                <div class="title w-25">
                  <span class="font-weight-light">ایمیل</span>
                </div>
                <div class="font-weight-bold">
                  {{ info.email }}
                </div>
              </div>
            </div>

            <div
              v-if="info.website"
              class="list-view-item"
            >
              <div class="d-flex aic flex-wrap">
                <div class="title w-25">
                  <span class="font-weight-light">وبسایت</span>
                </div>
                <div class="font-weight-bold">
                  {{ info.website }}
                </div>
              </div>
            </div>

            <div
              v-if="info.company.address"
              class="list-view-item"
            >
              <div class="d-flex aic flex-wrap">
                <div class="title w-25">
                  <span class="font-weight-light">آدرس</span>
                </div>
                <div class="font-weight-bold">
                  {{ info.company.address }}
                </div>
              </div>
            </div>

            <div
              v-if="info.about"
              class="list-view-item"
            >
              <div class="d-flex aic flex-wrap">
                <div class="title w-25">
                  <span class="font-weight-light">درباره سازمان</span>
                </div>
                <div class="mb-4 font-weight-bold">
                  {{ info.about }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
        

      <table-list
        list-name="سفارشات معلق"
        :loading="tableListLoading"
        :table-list="pendingOrders"
        @buttonClick="actionsHandle"
      />
    </template>
    <router-view />
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>
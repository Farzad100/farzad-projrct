<template>
  <div>
    <div>
      asdlfgdflkghjfdkljdflkjdflkdfjlkh
    </div>
    <!-- Welcome message -->
    <template v-if="info && info.mode === 'show_message'">
      <div
        class="
          border
          rounded
          p-4 p-lg-5
          d-flex
          flex-column flex-lg-row
          align-items-center
        "
        :class="info.color ? 'text-' + info.color : 'text-green'"
      >
        <i class="fal fa-info-circle fa-2x" />
        <!-- eslint-disable vue/no-v-html -->
        <p
          class="m-0 pr-5 mt-4 mt-lg-0"
          v-html="info.message"
        />
      </div>
    </template>

    <template
      v-else-if="
        info &&
          info.type === 'online' &&
          (!info.domain_verified_at || !info.email_verified_at)
      "
    >
      <div class="row d-flex justify-content-center">
        <div
          v-if="!info.domain_verified_at"
          class="col-8"
        >
          <div
            class="alert alert-info text-justify bg-white border border-info"
          >
            مالکیت دامنه شما هنوز تأیید نشده است. لطفاً یک فایل خالی با نام
            ghesta-{{ info.user_id + 1859 }}.txt در ریشه سایت خود ایجاد کنید.
            بطوری که وقتی روی لینک زیر کلیک می کنید، فایل موردنظر دانلود یا اجرا
            شود:
            <br>
            <div class="text-center">
              <a
                :href="
                  'https://' +
                    info.domain +
                    '/ghesta-' +
                    (info.user_id + 1859) +
                    '.txt'
                "
                target="_blank"
              >{{
                'https://' +
                  info.domain +
                  '/ghesta-' +
                  (info.user_id + 1859) +
                  '.txt'
              }}</a>
            </div>
            <br>
            سپس روی دکمه زیر کلیک کنید تا مالکیت دامنه شما احراز شود.
            <br>
            <div class="text-center">
              <span @click="verifyDomain">
                <g-button
                  ref="verifyDomain"
                  text="بررسی و احراز دامنه"
                  class="mt-4"
                  type="submit"
                  color="primary"
                />
              </span>
            </div>
          </div>
        </div>

        <div
          v-if="!info.email_verified_at"
          class="col-8"
        >
          <div
            class="alert alert-info text-justify bg-white border border-info"
          >
            <br>
            <div class="form">
              <form
                class="max-width-360 mx-auto text-center"
                @submit.prevent="handleSubmit"
              >
                <span class="d-block mb-3">
                  کد ارسال شده به ایمیل خود را در این کادر وارد نمایید:
                </span>
                <input
                  v-model="emailCode"
                  placeholder="کد اعتبارسنجی"
                  type="text"
                  inputmode="numeric"
                  maxlength="6"
                  class="form-control form-control-lg text-center ltr estedad-font mb-2"
                >
                <div class="d-flex justify-content-around">
                  <div @click="sendEmailAgain">
                    <g-button
                      ref="sendEmailAgain"
                      text="ارسال دوباره"
                      class="mt-4"
                    />
                  </div>
                  <div @click="verifyEmail">
                    <g-button
                      ref="verifyEmail"
                      text="احراز آدرس ایمیل"
                      class="mt-4"
                      color="primary"
                    />
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- Contract agreement -->
    <template v-else-if="info && info.status === 'agreement'">
      <div
        class="border rounded shadow-sm p-4 p-lg-5 font-weight-light"
        style="line-height: 31px"
      >
        <h5 class="text-center mb-5">
          باسمه تعالی
        </h5>
        <p>
          <strong>فروشنده عزیز،</strong>
          <br>
          در راستای اجرای بهینه قرارداد فی مابین و تسهیل ارائه خدمات بهتر به
          مشتریان، قرارداد همکاری با قسطا به صورت الکترونیک در اختیار شما قرار
          گرفته و در صورت مطالعه و تایید شما، از لحاظ قانونی الزام آور می باشد.
          <br>
          قسطا حق تغییر مفاد قرارداد را در هر زمانی داشته و این تغییرات و همچنین
          اطلاعیه ها و ابلاغیه ها توسط قسطا در پنل فروشندگان اعلام می شود که به
          منزله ی ابلاغ به فروشنده می باشد. فروشندگان متعهد هستند تمامی اطلاعیه
          ها را با دقت مطالعه نموده و طبق آن عمل نمایند. تغییرات به طور خودکار
          روی پنل اختصاصی فروشنده قرار گرفته و به منزله ی اصلاحیه و الحاقیه
          قرارداد محسوب شده و فروشنده متعهد به رعایت مقررات به روز شده می باشد و
          نیاز به هیچگونه تشریفات دیگری جهت امضا و ابلاغ نمی باشد.
          <br>
          لطفا توجه فرمایید که عدم پذیرش قرارداد همکاری به منزله عدم تمایل شما
          به همکاری با قسطا تلقی شده و تا زمان پذیرش قرارداد امکان استفاده از
          پنل فروشندگان برای شما میسر نمی باشد.
        </p>
        <p v-if="info.company_id">
          <strong>ماده1- طرفین قرارداد</strong>
          <br>
          این قرارداد بین <strong>{{ info.company.name }}</strong> به شناسه ملی
          <strong>{{ info.company.nic }}</strong> ، شماره ثبت
          <strong>{{ info.company.rn }}</strong> ، محل ثبت
          <strong>{{ info.company.state_name }}</strong> ، كد اقتصادی شماره
          <strong>{{ info.company.ec }}</strong> به نشانی
          <strong>{{ info.company.address }}</strong> و كدپستی
          <strong>{{ info.company.postal_code }}</strong> به نمایندگی آقا/خانم
          <strong>{{
            info.company.ceo_fname + ' ' + info.company.ceo_lname
          }}</strong>
          به سمت مدیرعامل که از این پس در این قرارداد "فروشنده" نـامیـده می‌شود
          از یك طرف و شركت پیشگامان اعتبارآفرین شریف که از این پس در این قرارداد
          "قسطا" نـامیـده می شود از سوی دیگر، طبق مقررات و شرایطی كه در مفاد این
          قرارداد درج شده است، منعقد گردید.
        </p>
        <p v-else>
          <strong>ماده1- طرفین قرارداد</strong>
          <br>
          این قرارداد بین آقا/خانم
          <strong>{{ info.owner.name }}</strong> به کدملی
          <strong>{{ info.owner.nid }}</strong> ، به نشانی
          <strong>{{
            info.state_name + '، ' + info.city + '، ' + info.address
          }}</strong>
          و كدپستی <strong>{{ info.postal_code }}</strong>
          که از این پس "فروشنده" نامیده می‌شود از یک طرف و شرکت پیشگامان
          اعتبارآفرین شریف که از این پس در این قرارداد "قسطا" نامیده می‌شود از
          سوی دیگر، طبق مقررات و شرایطی که در مفاد این قرارداد درج شده است،
          منعقد گردید.
        </p>
        <p>
          <strong>ماده 2- تعریف</strong>
          <br>
          قسطاکارت: یک کارت بانکی عضو شبکه شتاب است که با آن می‌توان از
          پایانه‌های فروشگاهی حقیقی یا پایانه‌های فروش اینترنتی خرید کرد و امکان
          دریافت پول و کارت به کارت از/به آن وجود ندارد.
          <br>
          قرارداد فروش اقساطی: قراردادی است که مشتریان از سایت قسطا دریافت
          می‌کنند و شرایط فروش اقساطی و مبالغ خرید، پیش‌پرداخت و اقساط و همچنین
          تاریخ سررسید اقساط در آن آمده‌است.
        </p>
        <p>
          <strong>ماده 3- موضوع قرارداد</strong>
          <br>
          فروش اقساطی کالا/خدمات فروشنده با همکاری قسطا در چارچوب مقررات این
          قرارداد و مقررات کلی شرایط فروش اقساطی مندرج در سایت قسطا.
        </p>
        <p>
          <strong>ماده 4- مدت قرارداد</strong>
          <br>
          مدت زمان این قرارداد از تاریخ {{ new Date() | jDate }} تا
          {{ new Date() | jYear }}/12/29 بوده و در پایان هر سال به صورت خودکار
          تا یک سال دیگر تمدید می‌شود.
        </p>
        <p>
          <strong>ماده 5- شرایط فروش اقساطی</strong>
          <br>
          5-1- پس از بارگذاری مدارک موردنیاز در پنل فروشگاه برای فروشنده یک
          قسطاکارت به نام نماینده قانونی آن صادر و تحویل می‌شود.
          <br>
          5-2- سقف خرید برای هریک از مشتریان طبق اعتبارسنجی قسطا تعیین می‌گردد.
          این سقف طبق شرایط عمومی قسطا بصورت پلکانی از خرید اول تا سوم افزایش می
          یابد.
          <br>
          5-3- مدت زمان بازپرداخت اقساط توسط مشتریان طبق شرایط عمومی قسطا تعیین
          می‌شود.
          <br>
          5-4- بازپرداخت اقساط با چک و به صورت ماهانه بوده و مبلغ اقساط براساس
          ماشین‌حساب موجود در سایت قسطا محاسبه و تعیین می‌شود.
          <br>
          5-5- مبلغ خرید، پیش‌پرداخت و اقساط پس از اعتبارسنجی توسط قسطا در تعامل
          با مشتریان نهایی می‌شوند.
          <br>
          5-6- فروشنده پس از تحویل کالا/خدمات به مشتری، چک‌های اقساط را به همراه
          قرارداد فروش اقساطی و فاکتور و رسید تحویل کالا برای قسطا ارسال کرده و
          مبلغ پیش‌پرداخت را از طریق درگاه پرداخت سایت به حساب قسطا واریز
          می‌کند. قسطا به‌ترتیب ذیل اقدام به تسویه مبلغ خرید می‌کند:
          <br>
          <span class="pl-3">
            1. در ماه اول همکاری با دریافت چک‌ها و پیش‌پرداخت، مبلغ خرید به‌صورت
            آنی تسویه و به قسطاکارت فروشنده واریز می‌شود.
          </span>
          <br>
          <span class="pl-3">
            2. از ماه دوم همکاری به بعد، یک درصد از مبلغ کل خرید به عنوان کارمزد
            از مبلغ واریزی کسر می‌گردد.
          </span>
        </p>
        <p class="text-justify mb-0">
          <strong>ماده 6- فرآیند فروش اقساطی</strong>
        </p>
        <p class="text-justify pl-2">
          1) مشتری: مراجعه به فروشنده و آشنایی با خرید قسطی.
          <br>
          2) فروشنده: مراجعه به پنل فروشگاه در سایت قسطا و ثبت سفارش به نام
          مشتری و بارگذاری مدارک.
          <br>
          3) قسطا: اعتبارسنجی و نهایی کردن میزان پیش‌پرداخت، تعداد و مبلغ هر قسط
          و اطلاع‌رسانی نتیجه اعتبارسنجی به مشتری.
          <br>
          4) فروشنده: بارگذاری تصویر چک‌ها، قرارداد، فاکتور و رسید تحویل کالا در
          سایت.
          <br>
          5) قسطا: تأیید چک‌ها و نمایش تأییدیه مشتری در اکانت فروشگاه.
          <br>
          6) فروشنده: دریافت چک‌ها و قرارداد امضا شده از مشتری، دریافت پیش
          پرداخت و تحویل کالا/خدمات به مشتری، پرداخت پیش‌پرداخت از طریق درگاه
          پرداخت سایت قسطا و دریافت کالا/خدمات.
          <br>
          7) فروشنده: ارسال چک‌ها، قرارداد، فاکتور و رسید تحویل مشتری برای قسطا.
          <br>
          8) قسطا: دریافت چک‌ها و واریز مبلغ خرید به قسطاکارت فروشنده.
          <br>
        </p>
        <p class="text-justify">
          <strong>ماده 7- تعهدات فروشنده</strong>
          <br>
          7-1- فروشنده متعهد است پس از دریافت چک‌های تأییدشده و قرارداد فروش
          اقساطی از مشتریان و پرداخت پیش‌پرداخت، محصول خریداری‌شده را به آنان
          تحویل دهد.
          <br>
          7-2- فروشنده متعهد است چک‌های دریافتی از مشتریان را به همراه قرارداد،
          فاکتور فروش و رسید تحویل کالا ظرف 5 روز کاری برای قسطا ارسال نماید.
          <br>
          7-3- همه مسئولیت کالا/خدمات اعم از سالم بودن، ضمانت‌نامه و خدمات پس از
          فروش برعهده فروشنده می‌باشد.
        </p>
        <p>
          <strong>ماده 8- تعهدات قسطا</strong>
          <br>
          8-1- قسطا متعهد می‌شود تسهیلات خرید اقساطی را در قالب قسطاکارت تا مبلغ
          تابع موضوع ماده (5) این قرارداد برای مشتریان فروشنده تأمین نماید.
          <br>
          8-2- قسطا متعهد است پس از دریافت چک‌های اقساط مشتریان، قرارداد، فاکتور
          و رسید تحویل کالا مطابق با موضوع بند 5-6 این قرارداد اقدام به تسویه
          مبلغ خرید مشتریان کند.
        </p>
        <p>
          <strong>ماده 9- فسخ قرارداد</strong>
          <br>
          9-1- فسخ این قرارداد با رضایت کتبی طرفین بلامانع است.
          <br>
        </p>
        <p>
          <strong>ماده 10- نشانی طرفین قرارداد</strong>
          <br>
          10-1- آدرس‌ طرفین قرارداد به شرح ذیل است:
          <br>
          فروشنده:
          <br>
          <span>
            نشانی:
            {{
              info.state_name +
                '، ' +
                info.city +
                '، ' +
                info.address +
                '، کدپستی:' +
                info.postal_code
            }}
          </span>
          <br>
          تلفن: {{ info.phone }}
          <br>
          تلفکس: {{ info.phone }}
          <br>
          قسطا:
          <br>
          نشانی: تهران، میدان آزادی، اتوبان لشکری، جنب متروی بیمه، کارخانه
          نوآوری آزادی
          <br>
          تلفن: 91070092-021
          <br>
          فکس: 91070092-021
          <br>
          10-2- طرفین قرارداد موظفند در صورت تغییر آدرس و یا شماره های تلفن و
          فكس، مراتب را حداكثر ظرف دو هفته به یكدیگر اطلاع دهند. بنابراین در
          صورتیكه طرفین یكدیگر را از تغییر آدرس مطلع ننمایند، ارسال مكاتبات به
          نشانی‌های فوق ابلاغ شده تلقی خواهد شد.
        </p>
        <p>
          <strong>ماده 11- قانون حاکم و نحوه حل اختلاف</strong>
          <br>
          قرارداد تابع قوانین جمهوری اسلامی ایران می‌باشد و باتوجه به محل انعقاد
          قرارداد، کلیه اختلافات و دعاوی ناشی از آن و یا راجع به آن به مراجع
          صالح قضایی جمهوری اسلامی ایران در شهر تهران ارجاع خواهد شد. این
          قرارداد در 11 ماده با تأیید قرارداد مورد تأیید قرار می‌گیرد و فروشنده
          با تأیید این قرارداد متعهد بدان خواهد بود.
        </p>
      </div>

      <div class="d-flex flex-column align-items-center my-5">
        <button
          class="btn btn-success rounded-pill btn-lg mb-4"
          :class="loadingAgreement ? 'btn-loading' : ''"
          @click="acceptAgreement"
        >
          <div
            class="spinner-border"
            role="status"
          >
            <span class="sr-only">در حال بررسی</span>
          </div>
          <span class="btn-text d-flex aic w-100 jcb px-5"> قبول قرارداد </span>
        </button>

        <small class="text-danger">
          قبول قرارداد به منزله این است که شما متن بالا را به دقت مطالعه
          کرده‌اید
        </small>
      </div>
    </template>

    <!-- Upload docmuments -->
    <template
      v-else-if="
        (info && info.status === 'uploading') ||
          (info && info.status === 'docs_uploaded')
      "
    >
      <h5
        v-if="info.status === 'uploading'"
        class="special-font mb-5"
      >
        لطفا مدارک مورد نیاز جهت اعتبار سنجی را آپلود کنید
      </h5>
      <h5
        v-else-if="info.status === 'docs_uploaded'"
        class="special-font mb-5"
      >
        مدارک شما در حال بررسی توسط کارشناسان قسطا می باشد.
      </h5>

      <g-docs />
    </template>

    <template v-else>
      <div class="d-flex justify-content-between align-items-end mb-4">
        <div class="mb-4 mb-xl-0">
          <h1
            v-if="info.name"
            class="font-weight-bold special-font m-0 mt-3"
          >
            {{ info.name }}
          </h1>
        </div>
      </div>

      <div class="d-flex flex-column justify-content-between">
        <div
          v-if="info"
          class="d-flex flex-column flex-md-row justify-content-xl-between"
        >
          <div class="in-chart-card ml-md-3 mb-3 mb-md-0">
            <span class="font-weight-light">مجموع کل فروش</span>
            <h2 class="price-style font-weight-bold mb-0 mt-3">
              {{ info.sales_total | moneySeperate }}
              <small>تومان</small>
            </h2>
          </div>

          <div class="in-chart-card ml-md-3 mb-3 mb-md-0">
            <span class="font-weight-light"> سفارشات معلق</span>
            <h2 class="font-weight-bold mb-0 mt-3">
              {{ info.orders_count - info.orders_successful_count }}
            </h2>
          </div>

          <div class="in-chart-card ml-md-3 mb-3 mb-md-0">
            <span class="font-weight-light">تعداد سفارشات</span>
            <h2 class="font-weight-bold mb-0 mt-3">
              {{ info.orders_successful_count }}
            </h2>
          </div>

          <div class="in-chart-card">
            <span class="font-weight-light">تعداد مشتریان</span>
            <h2 class="font-weight-bold mb-0 mt-3">
              {{ info.users_count }}
            </h2>
          </div>
        </div>
      </div>

      <div class="border rounded shadow-sm mt-4">
        <div
          class="
            d-flex
            justify-content-between
            align-items-center
            bg-gray-light
            rounded
            p-4
          "
        >
          <h4 class="special-font m-0 text-brand">
            اطلاعات فروشگاه
          </h4>

          <g-button
            text="مشاهده"
            sm
            color="primary"
            data-bs-toggle="collapse"
            data-bs-target="#ShopInfo"
            aria-expanded="false"
            aria-controls="collapseExample"
          />
        </div>

        <div
          id="ShopInfo"
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
                    {{ info.status_farsi }}
                  </span>
                </div>
              </div>
            </div>

            <div
              v-if="info.phone"
              class="list-view-item"
            >
              <div class="d-flex aic flex-wrap">
                <div class="title w-25">
                  <span class="font-weight-light">تلفن</span>
                </div>
                <div class="font-weight-bold">
                  {{ info.phone }}
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
              v-if="info.address"
              class="list-view-item"
            >
              <div class="d-flex aic flex-wrap">
                <div class="title w-25">
                  <span class="font-weight-light">آدرس</span>
                </div>
                <div class="font-weight-bold">
                  {{ info.address }}
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
        list-name="تراکنش‌ها"
        :table-list="[]"
      />
    </template>

    <!-- Reject Pending Order Modal -->
    <modal
      v-show="rejectModal"
      title="رد سفارش"
      @close="rejectModal = false"
    >
      <template #body>
        <label> دلیل رد سفارش </label>
        <textarea
          v-model="rejectReason"
          class="form-control"
          rows="3"
          placeholder="لطفا دلیل خودتان برای رد این سفارش را بنویسید..."
        />
      </template>

      <template #footer>
        <div class="w-100 d-flex justify-content-between">
          <button
            class="btn btn-light rounded-pill"
            @click="rejectModal = false"
          >
            انصراف
          </button>
          <button
            class="btn btn-danger rounded-pill"
            @click="rejectOrder(currentModalEndpoint, currentModalMethod)"
          >
            رد سفارش
          </button>
        </div>
      </template>
    </modal>
    <router-view />
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

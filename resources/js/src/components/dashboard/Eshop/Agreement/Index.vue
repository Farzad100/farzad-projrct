<template>
  <div>
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
        منزله ی ابلاغ به فروشنده می باشد. فروشندگان متعهد هستند تمامی اطلاعیه ها
        را با دقت مطالعه نموده و طبق آن عمل نمایند. تغییرات به طور خودکار روی
        پنل اختصاصی فروشنده قرار گرفته و به منزله ی اصلاحیه و الحاقیه قرارداد
        محسوب شده و فروشنده متعهد به رعایت مقررات به روز شده می باشد و نیاز به
        هیچگونه تشریفات دیگری جهت امضا و ابلاغ نمی باشد.
        <br>
        لطفا توجه فرمایید که عدم پذیرش قرارداد همکاری به منزله عدم تمایل شما به
        همکاری با قسطا تلقی شده و تا زمان پذیرش قرارداد امکان استفاده از پنل
        فروشندگان برای شما میسر نمی باشد.
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
        به سمت مدیرعامل که از این پس در این قرارداد "فروشنده" نـامیـده می‌شود از
        یك طرف و شركت پیشگامان اعتبارآفرین شریف که از این پس در این قرارداد
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
        اعتبارآفرین شریف که از این پس در این قرارداد "قسطا" نامیده می‌شود از سوی
        دیگر، طبق مقررات و شرایطی که در مفاد این قرارداد درج شده است، منعقد
        گردید.
      </p>
      <p>
        <strong>ماده 2- تعریف</strong>
        <br>
        قسطاکارت: یک کارت بانکی عضو شبکه شتاب است که با آن می‌توان از پایانه‌های
        فروشگاهی حقیقی یا پایانه‌های فروش اینترنتی خرید کرد و امکان دریافت پول و
        کارت به کارت از/به آن وجود ندارد.
        <br>
        قرارداد فروش اقساطی: قراردادی است که مشتریان از سایت قسطا دریافت می‌کنند
        و شرایط فروش اقساطی و مبالغ خرید، پیش‌پرداخت و اقساط و همچنین تاریخ
        سررسید اقساط در آن آمده‌است.
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
        {{ new Date() | jYear }}/12/29 بوده و در پایان هر سال به صورت خودکار تا
        یک سال دیگر تمدید می‌شود.
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
        مبلغ پیش‌پرداخت را از طریق درگاه پرداخت سایت به حساب قسطا واریز می‌کند.
        قسطا به‌ترتیب ذیل اقدام به تسویه مبلغ خرید می‌کند:
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
        2) فروشنده: مراجعه به پنل فروشگاه در سایت قسطا و ثبت سفارش به نام مشتری
        و بارگذاری مدارک.
        <br>
        3) قسطا: اعتبارسنجی و نهایی کردن میزان پیش‌پرداخت، تعداد و مبلغ هر قسط و
        اطلاع‌رسانی نتیجه اعتبارسنجی به مشتری.
        <br>
        4) فروشنده: بارگذاری تصویر چک‌ها، قرارداد، فاکتور و رسید تحویل کالا در
        سایت.
        <br>
        5) قسطا: تأیید چک‌ها و نمایش تأییدیه مشتری در اکانت فروشگاه.
        <br>
        6) فروشنده: دریافت چک‌ها و قرارداد امضا شده از مشتری، دریافت پیش پرداخت
        و تحویل کالا/خدمات به مشتری، پرداخت پیش‌پرداخت از طریق درگاه پرداخت سایت
        قسطا و دریافت کالا/خدمات.
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
        8-2- قسطا متعهد است پس از دریافت چک‌های اقساط مشتریان، قرارداد، فاکتور و
        رسید تحویل کالا مطابق با موضوع بند 5-6 این قرارداد اقدام به تسویه مبلغ
        خرید مشتریان کند.
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
        نشانی: تهران، میدان آزادی، اتوبان لشکری، جنب متروی بیمه، کارخانه نوآوری
        آزادی
        <br>
        تلفن: 91070092-021
        <br>
        فکس: 91070092-021
        <br>
        10-2- طرفین قرارداد موظفند در صورت تغییر آدرس و یا شماره های تلفن و فكس،
        مراتب را حداكثر ظرف دو هفته به یكدیگر اطلاع دهند. بنابراین در صورتیكه
        طرفین یكدیگر را از تغییر آدرس مطلع ننمایند، ارسال مكاتبات به نشانی‌های
        فوق ابلاغ شده تلقی خواهد شد.
      </p>
      <p>
        <strong>ماده 11- قانون حاکم و نحوه حل اختلاف</strong>
        <br>
        قرارداد تابع قوانین جمهوری اسلامی ایران می‌باشد و باتوجه به محل انعقاد
        قرارداد، کلیه اختلافات و دعاوی ناشی از آن و یا راجع به آن به مراجع صالح
        قضایی جمهوری اسلامی ایران در شهر تهران ارجاع خواهد شد. این قرارداد در 11
        ماده با تأیید قرارداد مورد تأیید قرار می‌گیرد و فروشنده با تأیید این
        قرارداد متعهد بدان خواهد بود.
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
        قبول قرارداد به منزله این است که شما متن بالا را به دقت مطالعه کرده‌اید
      </small>
    </div>
  </div>
</template>

<script>
import Logic from './Logic';
export default { mixins: [Logic] };
</script>

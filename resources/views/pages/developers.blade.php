@extends('layouts.app')
@section('content')

@push('header')

@php($title='قسطا | راهنمای توسعه دهندگان')
@php($description='سریع ترین روش برای خرید اقساطی از کلیه فروشگاه‌های آنلاین و فیزیکی - با قسطا در حداکثر 4 روز هر چیزی رو قسطی تهیه کن')

<!-- Primary Meta Tags -->
<title>{{$title}}</title>
<meta name="title" content="{{$title}}">
<meta name="description" content="{{$description}}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="https://ghesta.ir/shop">
<meta property="og:title" content="{{$title}}">
<meta property="og:description" content="{{$description}}">
<meta property="og:image" content="">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="https://ghesta.ir/shop">
<meta property="twitter:title" content="{{$title}}">
<meta property="twitter:description" content="{{$description}}">
<meta property="twitter:image" content="">

@endpush

@push('style')
<link rel="stylesheet" href="/libs/swiper.min.css">
<link rel="stylesheet" href="/css/shop.css">
@endpushs

<header class="header-style-2">
  <div class="container text-center">
    <h1 class="special-font mb-3 font-weight-bold">
      راهنمای توسعه دهندگان
    </h1>
    <h5 class="mx-auto col-12 col-lg-6 font-weight-bold">
      قسطا به عنوان پلتفرم فروش اقساطی، با تامین اعتبار به شما کمک می‌کند محصولات خود را به صورت قسطی به مشتریان خود بفروشید و هزینه آن را در زمان خرید به صورت نقد از ما دریافت کنید!
    </h5>

    <div class="d-flex justify-content-center mt-5">
      <a href="/seller/register" class="btn btn-primary rounded-pill">
        ثبت نام برای فروش اقساطی
      </a>
    </div>
  </div>
  <img src="/images/shop-bg.jpg" alt="">
</header>

<div class="container my-5 py-5">
  <h3 class="special-font">مزایای همکاری</h3>
  <span class="opa-7">همکاری با ما چه سودی برای شما دارد</span>

  <div class="row mt-5 pt-5">
    <div class="col-12 col-lg-4 mb-4">
      <div class="card-w-3d-icon border rounded p-4">
        <img src="/images/shopIcons/income.svg" alt="">
        <h4 class="special-font">
          افزایش میزان فروش
        </h4>
        <p class="m-0 opa-7">
          با افزایش تعداد مشتریانی که درخواست خرید اقساطی دارند می‌توانید فروش خود را افزایش دهید.
        </p>
      </div>
    </div>
    <div class="col-12 col-lg-4 mb-4">
      <div class="card-w-3d-icon border rounded p-4">
        <img src="/images/shopIcons/money-flow.svg" alt="">
        <h4 class="special-font">
          بدون ریسک بازپرداخت اقساط
        </h4>
        <p class="m-0 opa-7">
          هزینه محصول به طور کامل به حساب شما واریز می‌شود و اقساط خرید به قسطا پرداخت می‌شود.
        </p>
      </div>
    </div>
    <div class="col-12 col-lg-4 mb-4">
      <div class="card-w-3d-icon border rounded p-4">
        <img src="/images/shopIcons/money.svg" alt="">
        <h4 class="special-font">
          بدون نیاز به نقدینگی اضافی
        </h4>
        <p class="m-0 opa-7">
          اعتبار این خرید توسط قسطا تامین می‌شود و نیازی به نقدینگی اضافی برای فورش اقساطی ندارید.
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container d-flex flex-column flex-lg-row align-items-center justify-content-between my-5 pt-5">
  <div class="text-center mb-4 text-lg-right">
    <h3 class="special-font">
      مراحل همکاری
    </h3>
    <span class="text-c-l ">
      چه کارهایی برای شروع همکاری با ما باید انجام دهید
    </span>
  </div>

  <div class="d-flex">
    <div class="swiperButton swiperButton-prev">
      <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-right" fill="currentColor"
        xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd"
          d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
      </svg>
    </div>
    <div class="swiperButton swiperButton-next mr-4">
      <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-left" fill="currentColor"
        xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd"
          d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
      </svg>
    </div>
  </div>
</div>

<div class="get-steps mb-5 pb-5">
  <div class="gra bef"></div>
  <div class="swiper-container container pt-3" dir="rtl">
    <div class="swiper-wrapper">

      <div class="swiper-slide">
        <div class="dot">
          <i class="fas fa-angle-left"></i>
        </div>
        <div class="t">
          <h4 class="special-font">
            <span>۱.</span>
            ثبت نام 
          </h4>
          <p>
            در قدم اول همکاری فرم ثبت نام را پُر کنید.
          </p>
        </div>
      </div>

      <div class="swiper-slide">
        <div class="dot">
          <i class="fas fa-angle-left"></i>
        </div>
        <div class="t">
          <h4 class="special-font">
            <span>۲.</span>
            تأیید/امضای قرارداد 
          </h4>
          <p>
            بعد از ثبت نام یک پنل برای فروشگاه شما در سایت قسطا ایجاد می شود. در پنل شما قرارداد همکاری نمایش داده می شود. تأیید این قرارداد به منزله امضای آن خواهد بود.
          </p>
        </div>
      </div>

      <div class="swiper-slide">
        <div class="dot">
          <i class="fas fa-angle-left"></i>
        </div>
        <div class="t">
          <h4 class="special-font">
            <span>۳.</span>
            بارگذاری مدارک 
          </h4>
          <p>
            بعد از تأیید/امضای قرارداد برای اعتبارسنجی فروشگاه کافیست این مدارک را بارگذاری کنید: تصویر پشت و روی کارت ملی/جواز کسب/ تصویر فروشگاه/سند مکانی یا اجاره نامه فروشگاه.
          </p>
        </div>
      </div>

      <div class="swiper-slide">
        <div class="dot">
          <i class="fas fa-angle-left"></i>
        </div>
        <div class="t">
          <h4 class="special-font">
            <span>۴.</span>
            اعتبارسنجی فروشگاه 
          </h4>
          <p>
            بعد از بارگذاری مدارک باید منتظر تأیید آن ها باشید. بررسی مدارک شما حداکثر یک روز کاری زمان خواهد برد.
          </p>
        </div>
      </div>

      <div class="swiper-slide">
        <div class="dot">
          <i class="fas fa-angle-left"></i>
        </div>
        <div class="t">
          <h4 class="special-font">
            <span>۵.</span>
            صدور قسطاکارت و فعالسازی پنل 
          </h4>
          <p>
            بعد از اعتبارسنجی و تأیید فروشگاه، یک قسطاکارت به نام فروشنده صادر و برای شما ارسال خواهد شد و همزمان پنل فروشگاه شما نیز در قسطا فعال می شود.
          </p>
        </div>
      </div>

      <div class="swiper-slide">
        <div class="dot">
        </div>
        <div class="t">
          <h4 class="special-font">
            <span>۶.</span>
            ثبت سفارش 
          </h4>
          <p>
            از الان به بعد می توانید به تعداد نامحدود درخواست  خرید اقساطی مشتریان خود را ثبت کنید
          </p>
        </div>
      </div>

    </div>
  </div>
  <div class="gra aft"></div>
</div>

<div
  id="foggy"
  class="mt-5"
  style="
    background-image:url('/images/bg-2.jpg');
    background-repeat:no-repeat;
    background-size:cover;
  "
>
  <div class="container py-5">
    <div class="row">
      <div class="col-12 col-lg-6 d-flex align-items-center justify-content-center justify-content-lg-start">
        <h2 class="special-font font-weight-bold text-white text-center text-lg-right mb-4 mb-lg-0">
          همین الان در قسطا ثبت‌نام کنید و محصولات‌ خود را قسطی بفروشید
        </h2>
      </div>
      <div class="col-12 col-lg-6 d-flex align-items-center justify-content-center justify-content-lg-end">
        <a href="/seller/register" class="btn btn-light btn-lg rounded-pill px-5">
          ثبت‌نام
        </a>
      </div>
    </div>
  </div>
</div>

<div class="d-none container my-5 pt-5">
  <h3 class="special-font">نظرات فروشگاه‌ها</h3>
  <span class="opa-7">فروشگاه‌های دیگر از همکاری با ما چه حسی دارند</span>

  <div class="row mt-5">
    <div class="col-12 col-lg-6 mb-4">
      <div class="border rounded p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <span class="special-font m-0">
            ستاره جنوب
          </span>
          <small class="opa-7">
            شیراز
          </small>
        </div>

        <p class="m-0">
          طراح گرافیک از این متن به عنوان عنصری از ترکیب بندی برای پر کردن صفحه و ارایه اولیه شکل ظاهری و کلی طرح سفارش گرفته شده استفاده می نماید
        </p>
      </div>
    </div>
    <div class="col-12 col-lg-6 mb-4">
      <div class="border rounded p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <span class="special-font m-0">
            دیجی‌میجی
          </span>
          <small class="opa-7">
            مشهد
          </small>
        </div>

        <p class="m-0">
          طراح گرافیک از این متن به عنوان عنصری از ترکیب بندی برای پر کردن صفحه و ارایه اولیه شکل ظاهری و کلی طرح سفارش گرفته شده استفاده می نماید
        </p>
      </div>
    </div>
  </div>
</div>


@push('scripts')
<script src="/libs/swiper.min.js"></script>
<script>
  new Swiper('.swiper-container', {
    slidesPerView: 'auto',
    grabCursor: true,
    spaceBetween: 0,
    navigation: {
      nextEl: '.swiperButton-next',
      prevEl: '.swiperButton-prev',
    },
  });

  window.vm.getGlobalsData();
</script>
@endpush
@endsection

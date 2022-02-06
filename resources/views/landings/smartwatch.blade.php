@extends('layouts.landing')
@section('content')

@push('header')
@php($title='قسطا | خرید اقساطی هر کالا و خدماتی')
@php($description='سریع ترین روش برای خرید اقساطی از کلیه فروشگاه‌های آنلاین و فیزیکی - با قسطا در حداکثر 4 روز هر چیزی رو قسطی تهیه کن')

<!-- Primary Meta Tags -->
<title>{{$title}}</title>
<meta name="title" content="{{$title}}">
<meta name="description" content="{{$description}}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="https://ghesta.ir/">
<meta property="og:title" content="{{$title}}">
<meta property="og:description" content="{{$description}}">
<meta property="og:image" content="">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="https://ghesta.ir/">
<meta property="twitter:title" content="{{$title}}">
<meta property="twitter:description" content="{{$description}}">
<meta property="twitter:image" content="">

<meta name="robots" content="noindex, nofollow">
<link rel="stylesheet" href="/css/landing.css">

@endpush

<!-- Navigation -->
<nav>
  <div class="container-fluid">
    <a href="" class="logo">
      <img src="/images/logo.svg" alt="قسطا">
    </a>
    <a href="/register" class="btn btn-primary ml-md-2 rounded-pill">
      ثبت‌ سفارش
    </a>
  </div>
</nav>
<!-- / Navigation -->

<header class="gradient-light">
  <div class="container">
    <img style="max-height: 240px" class="d-block mx-auto my-4 d-lg-none" src="/images/landings/header/{{$data['img']}}-sm.png" alt="عکس محصول">

    <div class="col-12 col-lg-5 mr-auto text-center text-lg-right">
      <h1 class="mb-3 font-weight-bold title">
        خرید قسطی 
        {{ $data['type'] }}
      </h1>
      <h3 class="desc">
        با قسطا کارت بدون ضامن و در کمتر از ۵ روز انواع {{ $data['type'] }} رو قسطی بخرید
      </h3>
      <div class="d-flex justify-content-center justify-content-lg-start">
        <a href="/register" class="btn btn-primary btn-lg d-none d-md-inline rounded-pill px-4 mt-4">
          دریافت قسطا کارت
        </a>
        <a href="/register" class="btn btn-primary btn-sm d-md-none rounded-pill px-4 mt-4">
          دریافت قسطا کارت
        </a>
      </div>
    </div>
  </div>

  <img class="d-none d-lg-block" src="/images/landings/header/{{$data['img']}}.png" alt="عکس محصول">

  <svg width="1366" height="35" viewBox="0 0 1366 35" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path
      d="M26.6345 9.74458C11.4476 12.0691 -2.68726 15.7243 -16.9038 18.6002C-16.1889 21.5757 -11.7287 24.1615 -11.7287 27.4947C-11.7287 28.6925 -13.7463 31.9589 -9.97163 32.6943C-2.05 34.2374 13.5249 34.7561 22.681 34.7561H124.153H897.274H1285.01H1345.63C1351.32 34.7561 1382.52 35.807 1382.52 33.5459V29.467V19.6058C1382.52 16.1619 1386.03 10.881 1382.23 7.6827C1377.23 3.4683 1374.67 0.258595 1356.75 0.0626869C1343.85 -0.0783615 1330.86 0.0626869 1317.95 0.0626869C1304.46 0.0626869 1293.35 4.09681 1278.42 4.09681C1270.93 4.09681 1263.49 1.77406 1254.55 1.67633C1238.78 1.50396 1222.89 1.67633 1207.11 1.67633C1189.47 1.67633 1174.89 3.08562 1158.64 5.26222C1138.09 8.01521 1107.35 4.09681 1086.16 4.09681C1067 4.09681 1038.77 8.48524 1023.49 4.2761C1020.48 3.44552 1004.25 2.6161 1000.5 2.52798C991.939 2.32631 984.985 2.66226 976.783 3.28998C934.643 6.51497 892.151 4.90363 848.808 4.90363H794.924C781.413 4.90363 770.783 7.28444 757.878 8.13093C746.963 8.84694 736.712 8.93776 725.665 8.93776H636.639C629.272 8.93776 624.179 7.95042 617.164 7.32411C600.033 5.79452 581.967 5.7537 564.254 5.71368L562.841 5.71046C527.298 5.62803 496.542 8.47783 461.808 10.1928C441.616 11.1898 420.023 10.5514 399.578 10.5514C387.979 10.5514 376.867 7.67056 366.193 6.51728C342.34 3.94015 319.353 2.30973 294.006 3.82787C260.222 5.85127 227.429 10.4383 193.851 12.9719C175.554 14.3524 158.144 14.5855 139.528 14.5855C111.238 14.5855 86.5497 9.36545 58.4086 8.98258C48.4813 8.84752 35.8457 8.3347 26.6345 9.74458Z"
      fill="#fff" />
  </svg>
</header>


<div class="container pt-5">

  <!-- Ghesta card itself -->
  <div class="row flex-md-row-reverse py-5">
    <div class="col-12 col-md-6 col-lg-6 text-left">
      <img class="ghesta-card" src="/images/gc.png" alt="قسطا کارت">
    </div>

    <div class="col-12 col-md-6 col-lg-6 pl-md-5 d-flex align-items-center">
      <div>
        <h3 class="dana text-brand font-weight-bold mb-4">
          قسطاکارت چیست؟
        </h3>
        <p>
          قسطاکارت یه کارت اعتباریه که به وسیله اون می‌تونین هر کالایی رو قسطی بخرین. از هر فروشگاهی، آنلاین یا فیزیکی به راحتی اقساطی خرید کنین. {{ $data['type'] }} فقط یکی از هزاران کالائیه که می‌تونین به صورت قسطی صاحبش بشین.
        </p>
      </div>
    </div>
  </div>
</div>

<!-- features -->
<div class="features pt-0 home-style">
  <div class="container">
    <div class="sec-title mb-5 d-md-none">
      <h3 class="special-font">
        ویژگی‌های قسطا کارت
      </h3>
      <span class="text-c-l">
        قسطاکارت،
        بهترین راه حل خرید اقساطی
      </span>
    </div>

    <div class="row">
      <div class="col-12 col-md-6 col-lg-4 mt-5 pt-4">
        <div class="card-w-3d-icon border rounded p-4">
          <img src="/images/featuresIcons/ضامن.png" alt="">
          <h4 class="special-font">
            بدون نیاز به ضامن
          </h4>
          <p class="m-0 opa-7">
            تنها با ارائه چک می تونید از تمامی خدمات قسطاکارت بهره مند بشین
          </p>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4 mt-5 pt-4">
        <div class="card-w-3d-icon border rounded p-4">

          <img src="/images/featuresIcons/فروشگاه_فیزیکی.png" alt="">
          <h4 class="special-font">
            خرید از تمام فروشگاه‌های فیزیکی
          </h4>
          <p class="m-0 opa-7">
            مثل کارت بانکی خودتون از هر فروشگاه فیزیکی که دوست دارید خرید کرده و با قسطاکارت پرداخت کنید
          </p>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4 mt-5 pt-4">
        <div class="card-w-3d-icon border rounded p-4">
          <img src="/images/featuresIcons/فروشگاه_اینترنتی.png" alt="">
          <h4 class="special-font">
            خرید از تمامی فروشگاه‌های آنلاین
          </h4>
          <p class="m-0 opa-7">
            با فعالسازی رمز اینترنتی قسطاکارت، کالا و خدمات مورد نیازتون رو از هر سایتی قسطی بخرید
          </p>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4 mt-5 pt-4">
        <div class="card-w-3d-icon border rounded p-4">
          <img src="/images/featuresIcons/اعتبار_سنجی.png" alt="">
          <h4 class="special-font">
            اعتبارسنجی سریع
          </h4>
          <p class="m-0 opa-7">
            بعد از ارائه مدارک لازم حداکثر ظرف 1 روز کاری، اعتبارسنجی انجام شده و نتیجه اعلام می‌شود
          </p>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4 mt-5 pt-4">
        <div class="card-w-3d-icon border rounded p-4">
          <img src="/images/featuresIcons/ایران.png" alt="">
          <h4 class="special-font">
            امکان خرید از سراسر کشور
          </h4>
          <p class="m-0 opa-7">
            از هر شهری در کشور عزیزمون که هستید می تونید قسطاکارت سفارش بدین و از خدماتش استفاده کنید
          </p>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4 mt-5 pt-4">
        <div class="card-w-3d-icon border rounded p-4">
          <img src="/images/featuresIcons/مراجعه_حضوری.png" alt="">
          <h4 class="special-font">
            بدون نیاز به مراجعه حضوری
          </h4>
          <p class="m-0 opa-7">
            تمامی مراحل خرید قسطاکارت بصورت غیرحضوری و اینترنتی، خیلی ساده و بی دردسر انجام میشه
          </p>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- how to get card -->
<div class="container d-flex flex-column flex-lg-row align-items-center justify-content-between mt-5 pt-5">
  <div class="sec-title text-center mb-4 text-lg-right">
    <h3 class="special-font">
      چگونگی دریافت قسطا کارت
    </h3>
    <span class="text-c-l ">
      برای دریافت قسطاکارت کافیه این چندتا مرحله رو طی کنید
    </span>
  </div>

  <div class="d-none d-md-flex">
    <div class="swiperButton swiperButton-prev">
      <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
      </svg>
    </div>
    <div class="swiperButton swiperButton-next mr-4">
      <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-left" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
      </svg>
    </div>
  </div>
</div>

<div class="get-steps mb-lg-5 pb-lg-5">
  <div class="gra bef"></div>
  <div class="swiper-container container pt-3" dir="rtl">
    <div class="swiper-wrapper">

      <div class="swiper-slide">
        <div class="dot">
          <i class="fas fa-angle-left"></i>
        </div>
        <div class="t">
          <h4 class="special-font">
            <span>1.</span>
            ثبت سفارش
          </h4>
          <p>
            سفارش رو با تعیین مبلغ مورد نیاز، تعداد اقساط و میزان پیش‌پرداخت ثبت کنین.
          </p>
        </div>
      </div>

      <div class="swiper-slide">
        <div class="dot">
          <i class="fas fa-angle-left"></i>
        </div>
        <div class="t">
          <h4 class="special-font">
            <span>2.</span>
            بارگذاری مدارک
          </h4>
          <p>
            مدارک لازم شامل یک برگ چک صیادی (خالی)، گزارش حساب و کارت ملی رو بارگذاری کنین.
          </p>
        </div>
      </div>

      <div class="swiper-slide">
        <div class="dot">
          <i class="fas fa-angle-left"></i>
        </div>
        <div class="t">
          <h4 class="special-font">
            <span>3.</span>
            بررسی مدارک
          </h4>
          <p>
            مدارک شما در ظرف 1 روز کاری برای اعتبارسنجی بررسی می‌شه.
          </p>
        </div>
      </div>

      <div class="swiper-slide">
        <div class="dot">
          <i class="fas fa-angle-left"></i>
        </div>
        <div class="t">
          <h4 class="special-font">
            <span>4.</span>
            تحویل چک‌ها و دریافت قسطاکارت
          </h4>
          <p>
            به ازای اقساط چک صادر می‌کنین و به صورت حضوری یا با پست به ما تحویل می‌دین. ما هم قسطاکارت رو برای شما ارسال می‌کنیم.
          </p>
        </div>
      </div>

      <div class="swiper-slide">
        <div class="dot"></div>
        <div class="t">
          <h4 class="special-font">
            <span>5.</span>
            پیش پرداخت و شارژ قسطاکارت
          </h4>
          <p>
            بعد از دریافت قسطاکارت پیش‌پرداخت تعیین شده رو انجام می‌دین.قسطاکارت شما به میزان مبلغ درخواستی‌تون شارژ می‌شه  و می‌تونین خریدتون رو از هر جایی انجام بدین.
          </p>
        </div>
      </div>

    </div>
  </div>
  <div class="gra aft"></div>
</div>

<div class="container d-flex d-md-none justify-content-center">
  <div class="d-flex">
    <div class="swiperButton swiperButton-prev">
      <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
      </svg>
    </div>
    <div class="swiperButton swiperButton-next mr-4">
      <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-left" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
      </svg>
    </div>
  </div>
</div>

<!-- calculator -->
<div class="calculator" id="cal">
  <div id="foggy">
  </div>
  <div class="container my-5 pt-5">
    <div class="sec-title mt-5 mb-5">
      <h3 class="special-font">
        محاسبه اقساط
      </h3>
      <span class="text-c-l">
        شرایط خرید اقساطی رو خود شما تعیین می کنید
      </span>
    </div>

    <div class="ranger">

      <div class="hint-help" v-if="isHintShow" style="top: -110px;">
        <span>
          این رو چپ و راست کنید تا قیمت تغییر کنه
        </span>
        <svg width="44" height="114" viewBox="0 0 44 114" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M1 0.5C12.6667 15.6667 36.2 59.4 37 113M37 113L30.5 104.5H43L37 113Z" stroke="black" />
        </svg>
      </div>

      <div class="ranger-head border-bottom p-4">

        {{-- <div class="btn-group btn-group-sm threeD-style d-flex mb-5">
          <input id="individual" value="individual" v-model="order.type" type="radio" class="btn-check" name="ghestType" autocomplete="off" checked>
          <label class="btn" for="individual">
            سفارش شخصی
          </label>

          <input id="organ1" value="organ1" v-model="order.type" type="radio" class="btn-check" name="ghestType" autocomplete="off">
          <label class="btn" for="organ1">
            سفارش سازمانی
          </label>
        </div> --}}

        <amount-ranger v-model="order.amount" landing-style min="1200000" :max="maxes.third" />
      </div>

      <div class="c">

        <div class="d-flex flex-column align-items-center justify-content-between">
          <div class="d-flex flex-column position-relative w-100">
            <span class="text-danger alert-small" v-if="order.amount > maxes.first && order.amount <= maxes.second">
              این مبلغ برای سفارش
              <strong class="special-font border border-danger px-2 rounded-pill">دوم</strong>
              به بعد است
            </span>
            <span class="text-danger alert-small" v-if="order.amount > maxes.second">
              این مبلغ برای سفارش
              <strong class="special-font border border-danger px-2 rounded-pill">سوم</strong>
              به بعد است
            </span>
            <h1 class="price-style display-1 justify-content-center font-weight-bold m-0">
              @{{ order.amount | moneySeperate }}
              <small>تومان</small>
            </h1>
          </div>

          <div class="btn-group btn-group-sm threeD-style btn-pill mt-5">
            <template v-for="model in payback_models">
              <template v-for="ghest in model.ghests"> 
                <input :id="'m'+model.month+'c'+ghest" v-model="refund" :value="{months: model.month, cheques: ghest}" type="radio" class="btn-check" name="ghestPriod" autocomplete="off">
              <label class="btn" :for="'m'+model.month+'c'+ghest">
                @{{model.month}} ماه، @{{ghest}} 
                @{{ order.type != 'individual' ? 'قسط' : 'چک' }}
              </label>
              </template>
            </template>
          </div>
        </div>

        <div class="row mx-0 mt-5">

          <div class="s-ca-parent col-12 col-lg-4">
            <div class="s-ca">
              <img src="/images/cal/1.svg" alt="">
              <div>
                <span class="special-font">مجموع اقساط</span>
                <h4 class="price-style">
                  @{{ ghestify(order, refund).payback | moneySeperate }}
                  <small>تومان</small>
                </h4>
              </div>
            </div>
          </div>

          <div class="s-ca-parent col-12 col-md-6 col-lg-4">
            <div class="s-ca">
              <img src="/images/cal/2.svg" alt="">
              <div>
                <span class="special-font">پیش‌پرداخت</span>
                <h4 class="price-style">
                  @{{ ghestify(order, refund).prepayment | moneySeperate }}
                  <small>تومان</small>
                </h4>
              </div>
            </div>
          </div>

          <div class="s-ca-parent col-12 col-md-6 col-lg-4">
            <div class="s-ca">
              <img src="/images/cal/3.svg" alt="">
              <div>
                <span class="special-font">مبلغ هر
                  @{{ order.type != 'individual' ? 'قسط' : 'چک' }}
                </span>
                <h4 class="price-style">
                  @{{ ghestify(order, refund).ghest | moneySeperate }}
                  <small>تومان</small>
                </h4>
              </div>
            </div>
            <small class="text-danger d-block text-center" v-if="ghestify(order, refund).ghest >= maxes.gFirst && ghestify(order, refund).ghest < maxes.gSecond">
              این مبلغ چک برای سفارش
              <strong class="special-font border border-danger px-2 rounded-pill">دوم</strong>
              به بعد است
            </small>
            <small class="text-danger d-block text-center" v-if="ghestify(order, refund).ghest >= maxes.gSecond">
              این مبلغ چک برای سفارش
              <strong class="special-font border border-danger px-2 rounded-pill">سوم</strong>
              به بعد است
            </small>
          </div>

        </div>
      </div>
    </div>

  </div>
</div>  


@endsection

@push('scripts')
<script>
  window.vm.getGlobalsData();
</script>
@endpush
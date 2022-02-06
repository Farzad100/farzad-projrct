@extends('layouts.landing')
@section('content')

@push('header')
@php($title='قسطا | ...')
@php($description='...')

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
<nav class="w-d-logo">
  <div class="container">
    <a href="" class="logo">
      <img class="light-logo" src="/images/logo-w.svg" alt="قسطا">
      <img class="dark-logo" src="/images/logo.svg" alt="قسطا">
    </a>
    <a href="/register" class="btn btn-light ml-md-2 rounded-pill">
      خرید محصول
    </a>
  </div>
</nav>
<!-- / Navigation -->


<!-- Hero section -->
<div class="came-shop-info" style="
  background-image:url('/images/h-header2.jpg');
  background-repeat:no-repeat;
  background-size:cover;
  background-position: center center;
">
  <div>
    <div class="shop-card">
      <span class="mb-4 font-weight-bold opa-5">
        خرید گوشی موبایل از
      </span>
      <div class="logo mb-4">
        <img id="logo" src="https://digido.ir/img/digido.svg" alt="">
      </div>
      <h2 class="special-font mb-2">دیجی‌نو</h2>
      <a rel="nofollow" target="_blank" href="https://digido.ir/" class="ltr font-weight-bold">
        digido.ir
        <i class="far fa-external-link-alt ml-1 opa-7"></i>
      </a>

      <img class="blury" src="https://digido.ir/img/digido.svg" alt="">
    </div>

    <div class="d-flex justify-content-center w-100 mt-5">
      <a href="/register" class="btn btn-primary btn-lg-lg ml-2 font-weight-light">
        خرید محصول
      </a>
      <a href="#cal" class="btn btn-outline-light btn-lg-lg font-weight-light">
        محاسبه‌گر اقساط
      </a>
    </div>
  </div>
</div>
<!-- / Hero section -->


<!-- Description -->
<div class="col-12 col-lg-8 mx-auto mt-5 pt-5">
  <h4 class="special-font mb-4">
    توضیحات تکمیلی
  </h4>
  <p>
    سامانه اعتبارسنجی بای چک پس از احراز شما و در صورت معتبر بودن حساب معرفی شده در ثبت نام زیر ، ثبت نام شما را تائید و
    فروشگاه دیجی دو با توجه به تائیدیه بای چک ، چک ، سفته و یا کسر از حقوق شما را خواهد پذیرفت و شما به راحتی کالا یا
    خدمت مورد نظر را خریداری و دریافت خواهید نمود.
    <br>
    <br>
    لطفا برای ادامه روند اعتبارسنجی اطلاعات فیلدهای زیر را با دقت تکمیل نموده و کد رهگیری که همان کد ملی شماست دریافت و
    پس از حداقل 24 ساعت و یا حداکثر 72 ساعت از طریق تماس با فروشگاه دیجی دو و یا با مراجعه به سایت فروشگاه دیجی دو و
    صفحه پیگیری ثبت نام اقساطی ، با وارد کردن کد ملی خود از نتیجه ثبت نام مطلع شوید . ضمنا نتایج از طریق پیامک و ایمیل
    نیز اطلاع رسانی میگردد لذا خواهشمند است تلفن همراه و ایمیل خود را با دقت وارد نمایید. دقیق بودن اطلاعات به سرعت روند
    اعتبارسنجی کمک خواهد نمود.در صورت داشتن هرگونه سوال لطفا با تلفن 42487000 تماس حاصل فرمایید.
    <br>
    <br>
    تذکر 1 : درصورتیکه قصد خرید بوسیله سفته یا کسر از حقوق را دارید پر کردن فیلد "شماره حساب حقوق یا پس انداز" الزامیست
    در صورت وارد نکردن اطلاعات مربوط به فیلد مذکور ثبت نام شما رسیدگی نخواهد شد.با معرفی حسابهای دارای گردش و معدل
    بالاتر از شانس قبولی بیشتری برخوردار خواهید بود.
    <br>
    <br>
    تذکر 2 : در صورت خرید بوسیله چک هدف از پر کردن فیلد " شماره حساب حقوق یا پس انداز " این است که کارشناس اعتبارسنجی
    توان اعتباری حساب مذکور را نیز سنجیده تا در صورتیکه حساب جاری شما به تنهایی دارای حد نصاب کافی جهت قبولی مبلغ مورد
    تقاضای شما نباشد از قدرت حساب پس انداز و یا حقوق شما جهت رسیدن به حدنصاب قبولی استفاده نماید چرا که در بسیاری موارد
    افراد به جای توقف پول در حساب جاری ، بیشتر در حساب پس انداز گردش ایجاد مینمایند و طبعا علاوه بر دریافت سود معدل
    بهتری ایجاد میگردد بنابراین با معرفی حسابهای قویتر شانس بیشتری در قبولی ثبت نام خواهید داشت.
    <br>
    <br>
    تذکر3: در صورت ارائه کسر از حقوق و پذیرش آن توسط بای چک ، هنگام خرید یکبرگ سفته نیز از شما دریافت میگردد و بدیهی است
    پس از پایان اقساط سفته به شما عودت داده میشود. در نظر داشته باشید که در حالت خرید با گواهی کسر از حقوق پردازنده
    اقساط خود شما هستید و قسط از حقوق شما کسر نمیگردد و اخذ گواهی توسط بای چک تنها جهت تضمین پرداخت قسط توسط شما میباشد.
  </p>
</div>
<!-- / Description -->


<!-- Calculator -->
<div class="calculator" v-if="canCalShow" id="cal">
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

      <div class="hint-help" v-if="isHintShow">
        <span>
          این رو چپ و راست کنید تا قیمت تغییر کنه
        </span>
        <svg width="44" height="114" viewBox="0 0 44 114" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M1 0.5C12.6667 15.6667 36.2 59.4 37 113M37 113L30.5 104.5H43L37 113Z" stroke="black" />
        </svg>
      </div>

      <div class="ranger-head border-bottom p-4">

        <div class="btn-group btn-group-sm threeD-style d-flex mb-5">
          <input id="individual" value="individual" v-model="order.type" type="radio" class="btn-check" name="ghestType"
            autocomplete="off" checked>
          <label class="btn" for="individual">
            سفارش شخصی
          </label>

          <input id="organ1" value="organ1" v-model="order.type" type="radio" class="btn-check" name="ghestType"
            autocomplete="off">
          <label class="btn" for="organ1">
            سفارش سازمانی
          </label>
        </div>

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
                <input :id="'m'+model.month+'c'+ghest" v-model="refund" :value="{months: model.month, cheques: ghest}"
                  type="radio" class="btn-check" name="ghestPriod" autocomplete="off">
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
            <small class="text-danger d-block text-center"
              v-if="ghestify(order, refund).ghest >= maxes.gFirst && ghestify(order, refund).ghest < maxes.gSecond">
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
<!-- / Calculator -->


<!-- CTA -->
<div class="mt-5" style="
      background-image:url('/images/h-header2.jpg');
      background-repeat:no-repeat;
      background-size:cover;
      background-position: center center;
    ">
  <div class="container py-5">
    <div class="row">
      <div class="col-12 col-lg-6 d-flex align-items-center justify-content-center justify-content-lg-start">
        <h2 class="special-font font-weight-bold text-white text-center text-lg-right mb-4 mb-lg-0">
          همین حالا سفارش خود را ثبت کنید
        </h2>
      </div>
      <div class="col-12 col-lg-6 d-flex align-items-center justify-content-center justify-content-lg-end">
        <template>
          <a href="/dashboard/new-order" class="btn btn-light rounded-pill btn-lg mx-1">
            خرید محصول
          </a>
        </template>
      </div>
    </div>
  </div>
</div>
<!-- / CTA -->

@endsection

@push('scripts')
<script>
  window.vm.order.type = "individual";
  window.vm.getGlobalsData();

</script>
@endpush

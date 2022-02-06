@extends('layouts.app')

@push('header')

@php($title='قسطا | سوالات متداول')
@php($description='سریع ترین روش برای خرید اقساطی از کلیه فروشگاه‌های آنلاین و فیزیکی - با قسطا در حداکثر 4 روز هر چیزی
رو قسطی تهیه کن')

<!-- Primary Meta Tags -->
<title>{{$title}}</title>
<meta name="title" content="{{$title}}">
<meta name="description" content="{{$description}}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="https://ghesta.ir/faq">
<meta property="og:title" content="{{$title}}">
<meta property="og:description" content="{{$description}}">
<meta property="og:image" content="">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="https://ghesta.ir/faq">
<meta property="twitter:title" content="{{$title}}">
<meta property="twitter:description" content="{{$description}}">
<meta property="twitter:image" content="">
@endpush

@section('content')
<div class="page-header d-flex align-items-center justify-content-center">
  <div class="info">
    <h1>سوالات پرتکرار ...</h1>
    <h5>لطفاً برای کسب نتیجه بهتر، دسته بندی مورد نظر را انتخاب نمایید. </h5>
    <svg width="66" height="66" viewBox="0 0 66 66" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path
        d="M35.6685 56.8448C34.7834 56.954 33.8925 57.0092 33.0007 57.0102C19.7404 57.0102 8.99072 46.2606 8.99072 33.0002C8.99072 19.7399 19.7404 8.99023 33.0007 8.99023C46.2611 8.99023 57.0107 19.7399 57.0107 33.0002C57.0097 33.892 56.9545 34.7829 56.8453 35.668"
        stroke="#30357C" stroke-width="3.9375" stroke-linecap="round" stroke-linejoin="round" />
      <path d="M54.343 46.3391L47.6735 53.0086L43.6719 49.0069" stroke="#30357C" stroke-width="3.9375"
        stroke-linecap="round" stroke-linejoin="round" />
      <path
        d="M32.9991 42.6711C32.8151 42.6718 32.6664 42.8214 32.6669 43.0054C32.6674 43.1894 32.8168 43.3383 33.0008 43.338C33.1848 43.3378 33.3339 43.1886 33.3339 43.0045C33.3344 42.9157 33.2992 42.8304 33.2363 42.7677C33.1733 42.705 33.0879 42.6702 32.9991 42.6711"
        stroke="#30357C" stroke-width="3.9375" stroke-linecap="round" stroke-linejoin="round" />
      <path
        d="M26.8267 26.8262C27.4587 24.102 29.9399 22.2132 32.734 22.3292C35.8611 22.156 38.5419 24.5389 38.7365 27.6647C38.7365 31.6768 33.0008 33.0003 33.0008 35.6681"
        stroke="#30357C" stroke-width="3.9375" stroke-linecap="round" stroke-linejoin="round" />
    </svg>

  </div>
</div>

<div class="faq-parent container mb-5">
  <div class="row">
    <div class="col-12 col-lg-12 col-xl-12 pb-5">
      <div class="nav faq-nav justify-content-center nav-pills faq-side me-3" id="v-pills-tab" role="tablist"
        aria-orientation="vertical">
        <a class="nav-link ml-5 active" id="users-faq-tab" data-bs-toggle="pill" href="#users-faq" role="tab"
          aria-controls="v-pills-users" aria-selected="true">
          <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M24.7707 7.00048C26.1288 8.30935 26.896 10.1143 26.896 12.0004C26.896 13.8866 26.1288 15.6915 24.7707 17.0004C22.0093 19.7618 17.5322 19.7618 14.7708 17.0004C12.0094 14.239 12.0094 9.76188 14.7708 7.00048C17.5707 4.33383 21.9707 4.33383 24.7707 7.00048"
              stroke="#C3C6E8" stroke-width="2.49894" stroke-linecap="round" stroke-linejoin="round" />
            <path
              d="M6.43805 31.6666V31.6666C6.42979 29.6749 7.21728 27.7624 8.62561 26.3541C10.0339 24.9458 11.9464 24.1583 13.9381 24.1666H18.2714"
              stroke="#C3C6E8" stroke-width="2.49894" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M36.6668 21.6519H34.5701L32.8934 30.0368H24.5085L23.3335 24.1668H34.0668" stroke="#C3C6E8"
              stroke-width="2.49894" stroke-linecap="round" stroke-linejoin="round" />
            <path
              d="M25.2876 34.1719C25.3031 34.1559 25.3245 34.1469 25.3468 34.1469C25.369 34.1469 25.3904 34.1559 25.4059 34.1719C25.4386 34.2045 25.4386 34.2575 25.4059 34.2902C25.3732 34.3229 25.3203 34.3229 25.2876 34.2902C25.2716 34.2747 25.2626 34.2533 25.2626 34.231C25.2626 34.2087 25.2716 34.1874 25.2876 34.1719"
              stroke="#C3C6E8" stroke-width="2.49894" stroke-linecap="round" stroke-linejoin="round" />
            <path
              d="M31.9951 34.1719C32.0106 34.1559 32.032 34.1469 32.0543 34.1469C32.0766 34.1469 32.0979 34.1559 32.1134 34.1719C32.1461 34.2045 32.1461 34.2575 32.1134 34.2902C32.0808 34.3229 32.0278 34.3229 31.9951 34.2902C31.9791 34.2747 31.9701 34.2533 31.9701 34.231C31.9701 34.2087 31.9791 34.1874 31.9951 34.1719"
              stroke="#C3C6E8" stroke-width="2.49894" stroke-linecap="round" stroke-linejoin="round" />
          </svg>

          عمومی
        </a>
        <a class="nav-link" id="organ-faq-tab" data-bs-toggle="pill" href="#organ-faq" role="tab"
          aria-controls="v-pills-organ" aria-selected="true">
          <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle opacity="0.2" cx="14.6362" cy="15.5454" r="5" fill="#C3C6E8" stroke="#C3C6E8"
              stroke-width="0.909091" />
            <circle opacity="0.2" cx="29.1817" cy="16.4546" r="4.09091" fill="#C3C6E8" stroke="#C3C6E8"
              stroke-width="0.909091" />
            <circle cx="14.3303" cy="15.1814" r="5.81909" stroke="#C3C6E8" stroke-width="2.5" stroke-linecap="round"
              stroke-linejoin="round" />
            <circle cx="29.3364" cy="16.832" r="4.1684" stroke="#C3C6E8" stroke-width="2.5" stroke-linecap="round"
              stroke-linejoin="round" />
            <path
              d="M4.32617 34.3392V32.6435C4.32617 28.9753 7.29908 26.0024 10.9673 26.0024H17.6934C21.3616 26.0024 24.3345 28.9753 24.3345 32.6435V34.3392"
              stroke="#C3C6E8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M29.3369 26.0024H31.1743C34.8425 26.0024 37.8154 28.9753 37.8154 32.6435V34.3392" stroke="#C3C6E8"
              stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>

          سازمان
        </a>
        {{-- <a
          class="nav-link"
          id="shop-faq-tab"
          data-bs-toggle="pill"
          href="#shop-faq"
          role="tab"
          aria-controls="v-pills-shop"
          aria-selected="true"
        >
        <img src="/images/icons/shop-faq.svg" alt="#" title="#">
          فروشگاه فیزیکی       
        </a>
        <a
          class="nav-link"
          id="eshop-faq-tab"
          data-bs-toggle="pill"
          href="#eshop-faq"
          role="tab"
          aria-controls="v-pills-eshop"
          aria-selected="true"
        >
        <img src="/images/icons/eshop-faq.svg" alt="#" title="#">
          فروشگاه آنلاین  
        </a> --}}
      </div>
    </div>

    <div class="col-12 col-lg-12 col-xl-12 pr-lg-12 mt-4">
      <div class="tab-content" id="v-pills-tabContent">
        <!-- USERS FAQ -->
        <div class="tab-pane fade show active" id="users-faq" role="tabpanel" aria-labelledby="users-faq-tab">
          @foreach($Faq as $faq)
          <div class="qa-card">
            <h5 class="special-font m-0 cursor-pointer" data-bs-toggle="collapse"
              data-bs-target="#qa-o-{{$loop->index}}" {{-- id="qa-o-{{$loop->index}}" --}} aria-expanded="false"
              aria-controls="collapseExample" @click="toggle">
              {{$faq['q']}}

              <div class="info d-flex align-items-center">
                {{-- <img src="/images/icons/faq-copy.svg" class="ml-4" alt="#" title="#"> --}}
                <i class="far fa-chevron-down opa-3"></i>
              </div>

            </h5>

            <p class=" m-0 mt-2 collapse" id="qa-o-{{$loop->index}}">
              {!! str_replace("\n",'<br>',$faq['a']) !!}
            </p>
          </div>
          @endforeach
        </div>

        <!-- ORGAN FAQ -->
        <div class="tab-pane fade" id="organ-faq" role="tabpanel" aria-labelledby="organ-faq-tab">
          <!-- QA -->
          <div class="qa-card">


            <h5 class="special-font" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="false"
              aria-controls="faq1" @click="toggle">
              قصور کارمندان در بازپرداخت اقساط، چه تبعاتی برای شرکت دارد؟
              <div class="info d-flex align-items-center">
                {{-- <img src="/images/icons/faq-copy.svg" class="ml-4" alt="#" title="#"> --}}
                <i class="far fa-chevron-down opa-3"></i>
              </div>
            </h5>



            <p class="collapse  m-0" id="faq1">
              در صورت همکاری سازمان با قسطا در قالب طرح عادی سازمانی، مسولیتی متوجه سازمان نخواهد بود.در صورت همکاری در
              قالب طرح ویژه سازمانی، سازمان موظف است پس از درخواست کتبی قسطا، به پشتوانه گواهی کسر اقسطا معوق صادر شده،
              نسبت به کسر مبالغ اقساط از دریافتی ماهانه کارمند و پرداخت آن به قسطا اقدام نماید. در صورت افزایش معوقات
              کارمندان یک سازمان از حدودی که قسطا تعیین مینماید، ارائه خدمات به کارمندان تا زمان پرداخت معوقات متوقف
              میگردد.

            </p>




          </div>

          <!-- QA -->
          <div class="qa-card">

            <h5 class="special-font" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false"
              aria-controls="faq2" @click="toggle">
              فعالسازی پنل سازمان چه مدت طول میکشد؟
              <div class="info d-flex align-items-center">
                {{-- <img src="/images/icons/faq-copy.svg" class="ml-4" alt="#" title="#"> --}}
                <i class="far fa-chevron-down  opa-3"></i>
              </div>
            </h5>

            <p class="collapse  m-0" id="faq2">
              به محض ثبت نام سازمان و معرفی نماینده توسط سازمان، در کمتر از یک روز کاری، مدارک و اطلاعات توسط کارشناسان
              قسطا بررسی شده و پنل و کد خرید سازمانی در اختیار رابط سازمان قرار میگیرد.
            </p>

          </div>

          <!-- QA -->
          <div class="qa-card">


            <h5 class="special-font" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false"
              aria-controls="faq3" @click="toggle">
              برای ارتقا پنل سازمانی عادی به پنل سازمانی ویژه، چه مراحلی باید سپری شود؟
              <div class="info d-flex align-items-center">
                {{-- <img src="/images/icons/faq-copy.svg" class="ml-4" alt="#" title="#"> --}}
                <i class="far fa-chevron-down  opa-3"></i>
              </div>
            </h5>



            <p class="collapse  m-0" id="faq3">
              بسته به شهرت و اعتبار و اندازه سازمان، مدارک موردنیاز برای ارتقا پنل عادی به پنل ویژه متفاوت خواهد بود.
              مدارک مورد نیاز برای ارتقا پنل، در حساب سازمانی قابل رویت است. برای اطلاعات بیشتر، با واحد راهکارهای
              سازمانی قسطا در تماس باشید

            </p>





          </div>

          <!-- QA -->
          <div class="qa-card">


            <h5 class="special-font" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false"
              aria-controls="faq4" @click="toggle">
              آیا کارمندان باید از طریق پنل سازمانی اقدام به ثبت‌نام و ثبت سفارش نمایند؟
              <div class="info d-flex align-items-center">
                {{-- <img src="/images/icons/faq-copy.svg" class="ml-4" alt="#" title="#"> --}}
                <i class="far fa-chevron-down opa-3"></i>
              </div>
            </h5>



            <p class="collapse  m-0" id="faq4">
              خیر. پنل سازمان فقط در اختیار رابط سازمان و برای تایید خرید کارمندان قرار میگیر. کارمندان هر سازمان،
              بایستی در ابتدا کد خرید سازمانی شان را از رابط سازمان دریافت نمایند. سپس به صورت انفرادی در قسطا ثبتنام
              نموده و از طریق حساب کاربری شان در قسطا، ثبت سفارش کنند. در مرحله ثبت سفارش، با وارد کردن کد سازمانی،
              میتوانند از مزایای خرید سازمانی بهره مند شوند.

            </p>




          </div>

          <!-- QA -->
          <div class="qa-card">

            <h5 class="special-font" data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false"
              aria-controls="faq5" @click="toggle">
              آیا بستگان کارمندان سازمان ها امکان استفاده از مزایای خرید سازمانی قسطا را دارند؟
              <div class="info d-flex align-items-center">
                {{-- <img src="/images/icons/faq-copy.svg" class="ml-4" alt="#" title="#"> --}}
                <i class="far fa-chevron-down opa-3"></i>
              </div>
            </h5>



            <p class="collapse  m-0" id="faq5">
              در حال حاضر، تنها کارمندان سازمان ها میتوانند از مزایای خرید سازمانی قسطا بهره مند شوند. بستگان و آشنایان
              این افراد میتوانند به عنوان مشتریان غیر سازمانی در قسطا ثبت نام و ثبت سفارش نموده و از خدمات قسطا بهره مند
              شون

            </p>





          </div>

          <!-- QA -->
          <div class="qa-card">


            <h5 class="special-font" data-bs-toggle="collapse" data-bs-target="#faq6" aria-expanded="false"
              aria-controls="faq6" @click="toggle">
              اگر که به عنوان کارمند، دسته چک نداشته باشم، میتوانم از چک دوستان و بستگان برای ثبت سفارش استفاده کنم؟
              <div class="info d-flex align-items-center">
                {{-- <img src="/images/icons/faq-copy.svg" class="ml-4" alt="#" title="#"> --}}
                <i class="far fa-chevron-down opa-3"></i>
              </div>
            </h5>



            <p class="collapse  m-0" id="faq6">
              در حال حاضر، لازمه تایید سفارش و ارائه خدمات، تنها با چک متقاضی امکانپذیر میباشد
            </p>




          </div>

          <!-- QA -->
          <div class="qa-card">

            <h5 class="special-font" data-bs-toggle="collapse" data-bs-target="#faq7" aria-expanded="false"
              aria-controls="faq7" @click="toggle">
              آیا میتوانم برای بازپرداخت اقساط، اقساط را تجمیع کنم و تعداد چک های کمتری صادر کنم؟
              <div class="info d-flex align-items-center">
                {{-- <img src="/images/icons/faq-copy.svg" class="ml-4" alt="#" title="#"> --}}
                <i class="far fa-chevron-down opa-3"></i>
              </div>
            </h5>



            <p class="collapse  m-0" id="faq7">
              امکان تجمیع اقساط و ارائه چک های کمتر فراهم است. البته به شرطی که مبلغ هر قسط ، از میزان توان بازپرداخت
              ماهانه شخص که در اعتبار سنجی قسطا تعیین گردیده است بیشتر نباشد.
            </p>



          </div>

        </div>




        <!-- shop FAQ -->
        <div class="tab-pane fade" id="shop-faq" role="tabpanel" aria-labelledby="shop-faq-tab">
          <!-- QA -->
          <div class="qa-card">

            <div data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="false" aria-controls="faq1">
              <h5 class="special-font">
                قصور کارمندان در بازپرداخت اقساط، چه تبعاتی برای شرکت دارد؟
              </h5>
            </div>



            <div class="collapse" id="faq1">
              <p class=" m-0">
                در صورت همکاری سازمان با قسطا در قالب طرح عادی سازمانی، مسولیتی متوجه سازمان نخواهد بود.در صورت همکاری
                در قالب طرح ویژه سازمانی، سازمان موظف است پس از درخواست کتبی قسطا، به پشتوانه گواهی کسر اقسطا معوق صادر
                شده، نسبت به کسر مبالغ اقساط از دریافتی ماهانه کارمند و پرداخت آن به قسطا اقدام نماید. در صورت افزایش
                معوقات کارمندان یک سازمان از حدودی که قسطا تعیین مینماید، ارائه خدمات به کارمندان تا زمان پرداخت معوقات
                متوقف میگردد.
              </p>
            </div>




          </div>

          <!-- QA -->
          <div class="qa-card">


            <div data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
              <h5 class="special-font">
                فعالسازی پنل سازمان چه مدت طول میکشد؟
              </h5>
            </div>

            <div class="collapse" id="faq2">
              <p class=" m-0">
                به محض ثبت نام سازمان و معرفی نماینده توسط سازمان، در کمتر از یک روز کاری، مدارک و اطلاعات توسط
                کارشناسان قسطا بررسی شده و پنل و کد خرید سازمانی در اختیار رابط سازمان قرار میگیرد.
              </p>
            </div>



          </div>

          <!-- QA -->
          <div class="qa-card">

            <div data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
              <h5 class="special-font">
                برای ارتقا پنل سازمانی عادی به پنل سازمانی ویژه، چه مراحلی باید سپری شود؟
              </h5>
            </div>

            <div class="collapse" id="faq3">
              <p class=" m-0">
                بسته به شهرت و اعتبار و اندازه سازمان، مدارک موردنیاز برای ارتقا پنل عادی به پنل ویژه متفاوت خواهد بود.
                مدارک مورد نیاز برای ارتقا پنل، در حساب سازمانی قابل رویت است. برای اطلاعات بیشتر، با واحد راهکارهای
                سازمانی قسطا در تماس باشید
              </p>
            </div>




          </div>

          <!-- QA -->
          <div class="qa-card">

            <div data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">
              <h5 class="special-font">
                آیا کارمندان باید از طریق پنل سازمانی اقدام به ثبت‌نام و ثبت سفارش نمایند؟
              </h5>
            </div>

            <div class="collapse" id="faq4">
              <p class=" m-0">
                خیر. پنل سازمان فقط در اختیار رابط سازمان و برای تایید خرید کارمندان قرار میگیر. کارمندان هر سازمان،
                بایستی در ابتدا کد خرید سازمانی شان را از رابط سازمان دریافت نمایند. سپس به صورت انفرادی در قسطا ثبتنام
                نموده و از طریق حساب کاربری شان در قسطا، ثبت سفارش کنند. در مرحله ثبت سفارش، با وارد کردن کد سازمانی،
                میتوانند از مزایای خرید سازمانی بهره مند شوند.
              </p>
            </div>



          </div>

          <!-- QA -->
          <div class="qa-card ">
            <div data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false" aria-controls="faq5">
              <h5 class="special-font">
                آیا بستگان کارمندان سازمان ها امکان استفاده از مزایای خرید سازمانی قسطا را دارند؟
              </h5>
            </div>

            <div class="collapse" id="faq5">
              <p class=" m-0">
                در حال حاضر، تنها کارمندان سازمان ها میتوانند از مزایای خرید سازمانی قسطا بهره مند شوند. بستگان و
                آشنایان این افراد میتوانند به عنوان مشتریان غیر سازمانی در قسطا ثبت نام و ثبت سفارش نموده و از خدمات
                قسطا بهره مند شون
              </p>
            </div>




          </div>

          <!-- QA -->
          <div class="qa-card">

            <div data-bs-toggle="collapse" data-bs-target="#faq6" aria-expanded="false" aria-controls="faq6">
              <h5 class="special-font">
                اگر که به عنوان کارمند، دسته چک نداشته باشم، میتوانم از چک دوستان و بستگان برای ثبت سفارش استفاده کنم؟
              </h5>
            </div>

            <div class="collapse" id="faq6">
              <p class=" m-0">
                در حال حاضر، لازمه تایید سفارش و ارائه خدمات، تنها با چک متقاضی امکانپذیر میباشد
              </p>
            </div>



          </div>

          <!-- QA -->
          <div class="qa-card">
            <div data-bs-toggle="collapse" data-bs-target="#faq7" aria-expanded="false" aria-controls="faq7">
              <h5 class="special-font">
                آیا میتوانم برای بازپرداخت اقساط، اقساط را تجمیع کنم و تعداد چک های کمتری صادر کنم؟
              </h5>
            </div>

            <div class="collapse" id="faq7">
              <p class=" m-0">
                امکان تجمیع اقساط و ارائه چک های کمتر فراهم است. البته به شرطی که مبلغ هر قسط ، از میزان توان بازپرداخت
                ماهانه شخص که در اعتبار سنجی قسطا تعیین گردیده است بیشتر نباشد.
              </p>
            </div>


          </div>

        </div>


        <!-- eshop FAQ -->
        <div class="tab-pane fade" id="eshop-faq" role="tabpanel" aria-labelledby="eshop-faq-tab">
          <!-- QA -->
          <div class="qa-card">

            <div data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="false" aria-controls="faq1">
              <h5 class="special-font">
                قصور کارمندان در بازپرداخت اقساط، چه تبعاتی برای شرکت دارد؟
              </h5>
            </div>



            <div class="collapse" id="faq1">
              <p class=" m-0">
                در صورت همکاری سازمان با قسطا در قالب طرح عادی سازمانی، مسولیتی متوجه سازمان نخواهد بود.در صورت همکاری
                در قالب طرح ویژه سازمانی، سازمان موظف است پس از درخواست کتبی قسطا، به پشتوانه گواهی کسر اقسطا معوق صادر
                شده، نسبت به کسر مبالغ اقساط از دریافتی ماهانه کارمند و پرداخت آن به قسطا اقدام نماید. در صورت افزایش
                معوقات کارمندان یک سازمان از حدودی که قسطا تعیین مینماید، ارائه خدمات به کارمندان تا زمان پرداخت معوقات
                متوقف میگردد.
              </p>
            </div>




          </div>

          <!-- QA -->
          <div class="qa-card">


            <div data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
              <h5 class="special-font">
                فعالسازی پنل سازمان چه مدت طول میکشد؟
              </h5>
            </div>

            <div class="collapse" id="faq2">
              <p class=" m-0">
                به محض ثبت نام سازمان و معرفی نماینده توسط سازمان، در کمتر از یک روز کاری، مدارک و اطلاعات توسط
                کارشناسان قسطا بررسی شده و پنل و کد خرید سازمانی در اختیار رابط سازمان قرار میگیرد.
              </p>
            </div>



          </div>

          <!-- QA -->
          <div class="qa-card">

            <div data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
              <h5 class="special-font">
                برای ارتقا پنل سازمانی عادی به پنل سازمانی ویژه، چه مراحلی باید سپری شود؟
              </h5>
            </div>

            <div class="collapse" id="faq3">
              <p class=" m-0">
                بسته به شهرت و اعتبار و اندازه سازمان، مدارک موردنیاز برای ارتقا پنل عادی به پنل ویژه متفاوت خواهد بود.
                مدارک مورد نیاز برای ارتقا پنل، در حساب سازمانی قابل رویت است. برای اطلاعات بیشتر، با واحد راهکارهای
                سازمانی قسطا در تماس باشید
              </p>
            </div>




          </div>

          <!-- QA -->
          <div class="qa-card">

            <div data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">
              <h5 class="special-font">
                آیا کارمندان باید از طریق پنل سازمانی اقدام به ثبت‌نام و ثبت سفارش نمایند؟
              </h5>
            </div>

            <div class="collapse" id="faq4">
              <p class=" m-0">
                خیر. پنل سازمان فقط در اختیار رابط سازمان و برای تایید خرید کارمندان قرار میگیر. کارمندان هر سازمان،
                بایستی در ابتدا کد خرید سازمانی شان را از رابط سازمان دریافت نمایند. سپس به صورت انفرادی در قسطا ثبتنام
                نموده و از طریق حساب کاربری شان در قسطا، ثبت سفارش کنند. در مرحله ثبت سفارش، با وارد کردن کد سازمانی،
                میتوانند از مزایای خرید سازمانی بهره مند شوند.
              </p>
            </div>



          </div>

          <!-- QA -->
          <div class="qa-card">
            <div data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false" aria-controls="faq5">
              <h5 class="special-font">
                آیا بستگان کارمندان سازمان ها امکان استفاده از مزایای خرید سازمانی قسطا را دارند؟
              </h5>
            </div>

            <div class="collapse" id="faq5">
              <p class=" m-0">
                در حال حاضر، تنها کارمندان سازمان ها میتوانند از مزایای خرید سازمانی قسطا بهره مند شوند. بستگان و
                آشنایان این افراد میتوانند به عنوان مشتریان غیر سازمانی در قسطا ثبت نام و ثبت سفارش نموده و از خدمات
                قسطا بهره مند شون
              </p>
            </div>




          </div>

          <!-- QA -->
          <div class="qa-card">

            <div data-bs-toggle="collapse" data-bs-target="#faq6" aria-expanded="false" aria-controls="faq6">
              <h5 class="special-font">
                اگر که به عنوان کارمند، دسته چک نداشته باشم، میتوانم از چک دوستان و بستگان برای ثبت سفارش استفاده کنم؟
              </h5>
            </div>

            <div class="collapse" id="faq6">
              <p class=" m-0">
                در حال حاضر، لازمه تایید سفارش و ارائه خدمات، تنها با چک متقاضی امکانپذیر میباشد
              </p>
            </div>



          </div>

          <!-- QA -->
          <div class="qa-card">
            <div data-bs-toggle="collapse" data-bs-target="#faq7" aria-expanded="false" aria-controls="faq7">
              <h5 class="special-font">
                آیا میتوانم برای بازپرداخت اقساط، اقساط را تجمیع کنم و تعداد چک های کمتری صادر کنم؟
              </h5>
            </div>

            <div class="collapse" id="faq7">
              <p class=" m-0">
                امکان تجمیع اقساط و ارائه چک های کمتر فراهم است. البته به شرطی که مبلغ هر قسط ، از میزان توان بازپرداخت
                ماهانه شخص که در اعتبار سنجی قسطا تعیین گردیده است بیشتر نباشد.
              </p>
            </div>


          </div>

        </div>





      </div>
    </div>
  </div>
</div>
@endsection

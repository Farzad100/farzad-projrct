@php(config('app.env') == 'production' ? $dev=0 : $dev=1)
<!doctype html>
<html lang="fa" dir="rtl">

<head>
  @if($dev == 0)
  @if(isset($tag_alt) && $tag_alt)
  @include('addons.googletag_script_landing')
  @else
  @include('addons.googletag_script')
  @endif
  @endif
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="theme-color" content="#00579F">
  <meta name="app_version" content="{{$version}}">
  <link rel="icon" type="image/png" href="/images/favicon.ico">
  <meta name="robots" content="index, follow">


  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:image" content="https://ghesta.ir/images/logo.svg">
  <meta property="og:locale:alternate" content="fa_IR" />
  <meta property="og:site_name" content="ghesta" />
  <meta property="og:video" content="https://www.aparat.com/video/video/embed/videohash/MPsu8/vt/frame" />

  <!-- Twitter -->
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:image" content="https://ghesta.ir/images/logo.svg">
  <meta name="twitter:site" content="@ghesta_ir">
  <meta name="twitter:creator" content="@ghesta_ir">


  <!-- geo meta tag -->
  <meta name="geo.region" content="IR-07" />
  <meta name="geo.placename" content="Tehran" />
  <meta name="geo.position" content="35.700748;51.31925" />
  <meta name="ICBM" content="35.700748, 51.31925" />

  <!-- apple-touch-icon -->
  <link rel="apple-touch-icon" href="touch-icon-iphone.png">
  <link rel="apple-touch-icon" sizes="152x152" href="touch-icon-ipad.png">
  <link rel="apple-touch-icon" sizes="180x180" href="touch-icon-iphone-retina.png">
  <link rel="apple-touch-icon" sizes="167x167" href="touch-icon-ipad-retina.png">
  <meta name="apple-mobile-web-app-capable" content="yes">



  @stack('header')

  <!-- CSS only -->
  @stack('style')
  <link rel="stylesheet" href="/libs/bootstrap.min.css">
  <link rel="stylesheet" href="/css/style.css?ver={{$version}}">
</head>

<body>
  @if($dev == 0)
  @if(isset($tag_alt) && $tag_alt)
  @include('addons.googletag_noscript_landing')
  @else
  @include('addons.googletag_noscript')
  @endif
  @endif
  <div id="app">

    <div class="firstcont"></div>


    <!-- <button type="button" class="top-header w-100 border-0 p-0 bg-warning" data-bs-toggle="modal"
      data-bs-target="#staticBackdrop2">اطلاعیه مهم در خصوص تماس با قسطا</button> -->
    <!-- Modal -->
    {{-- <div class="modal fade" id="staticBackdrop2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
      aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-address modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <img src="/images/address-icon.png" alt="address" title="address">
          </div>
          <div class="modal-body px-3 py-0">
            <h5 class="mb-2 opa-5">آدرس جدید ما:</h5>
            <h5>میدان آزادی، اتوبان لشگری، جنب مترو بیمه، کارخانه نوآوری آزادی</h5>
            <a href="/map" title="#" target="_blank">
              <img class="MAddress w-100" src="/images/map-address.jpg" alt="address" title="address">
            </a>

            <a class=" map-route d-flex justify-content-between align-items-center" href="/map" title="#"
              target="_blank">
              <div class="info d-flex justify-content-start align-items-center">
                <img class="ml-3" src="/images/Label-arrow.png" alt="#" title="#">
                <h5 class="mb-0">مشاهده جزییات روی نقشه</h5>
              </div>
              <img src="/images/add-arrow.png" alt="#" title="#">
            </a>
          </div>
        </div>
      </div>
    </div> --}}
    <!-- Modal -->

    <!-- <div class="modal fade" id="staticBackdrop2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
      aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-address modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <img src="/images/icons/warning-notify.PNG" alt="address" title="address">
          </div>
          <div class="modal-body px-3 py-0">
            <h5 class="mb-2 opa-5">موضوع اطلاعیه:</h5>
            <h5 class="pb-4">مشتری گرامی، متاسفانه به دلیل عدم سرویس دهی شرکت تامین کننده تلفن اینترنتی
              شرکت، تماس های ورودی قسطا با اختلال همراه است. خواهشمند است
              تا رفع این مشکل برای ارتباط با پشتیبانی از شماره تماس <span>09054642646</span> استفاده نمایید.
              همچنین چت سایت و اکانت پشتیبانی قسطا نیز در پیام رسان تلگرام با آیدی <span>@ghesta_support</span>
              آماده پاسخگویی به مشتریان می باشند.</h5>
          </div>
        </div>
      </div>
    </div> -->


    <!-- Navigation -->
    <nav :class="[nav.isScrolled ? 'scrolled' : null, nav.isOpen ? 'scrolled' : null]">
      <div class="container">
        <div class="controller">
          <a href="/" class="logo">
            <img src="/images/logo-w.svg" alt=" قسطا پلتفرم آنلاين خريد اقساطی">
            <img src="/images/logo.svg" alt=" قسطا پلتفرم آنلاين خريد اقساطی">
          </a>


          <div class=" info d-flex align-items-center justify-content-between">
            <a :href=" '/dashboard' + '/' + this.$store.state.dashboard.role">
              <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M27.7563 12.5559C29.8307 14.6303 29.8307 17.9937 27.7563 20.0682C25.6818 22.1426 22.3184 22.1426 20.244 20.0682C18.1695 17.9937 18.1695 14.6303 20.244 12.5559C22.3184 10.4814 25.6818 10.4814 27.7563 12.5559Z"
                  fill="white" />
                <path
                  d="M27.7563 12.5559C29.8307 14.6303 29.8307 17.9937 27.7563 20.0682C25.6818 22.1426 22.3184 22.1426 20.244 20.0682C18.1695 17.9937 18.1695 14.6303 20.244 12.5559C22.3184 10.4814 25.6818 10.4814 27.7563 12.5559"
                  stroke="#00569F" stroke-width="1.71429" stroke-linecap="round" stroke-linejoin="round" />
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M24 25.6768C30.072 25.6768 36 28.2994 36 32.3328V33.6661C36 34.4021 35.4027 34.9994 34.6667 34.9994H13.3333C12.5973 34.9994 12 34.4021 12 33.6661V32.3328C12 28.2981 17.928 25.6768 24 25.6768Z"
                  fill="white" stroke="#00569F" stroke-width="1.71429" stroke-linecap="round" stroke-linejoin="round" />
              </svg>

            </a>
            <button class="toggle" @click="toggleNav()" :class="nav.isOpen ? 'active' : null"></button>
          </div>

        </div>

        <ul class="links" :class="nav.isOpen ? 'active' : null">

          {{-- <li><a href="/help">راهنما</a></li> --}}
          <li class="toggleUL d-flex align-items-center" @click="toggleUL">
            <a>همکاری با قسطا</a>
            <i id="mobileICO" class="far fa-chevron-down"></i>
            <ul id="mobileUL">
              {{-- <li>
                <a href="/eshop">فروشگاه‌های آنلاین</a>
              </li> --}}
              <li>
                <a href="/shop">فروشگاه‌های فیزیکی</a>
              </li>
              <li>
                <a href="/organ">سازمان‌ها</a>
              </li>
            </ul>
          </li>
          <li><a href="/faq">سوالات متداول</a></li>
          <li><a href="https://ghesta.ir/blog/" target="_blank">بلاگ</a></li>

          <li class="loginBTN mr-2 px-4" v-if="!isAuth"><a href="/login">
              <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M17.2866 4.86137C19.1018 6.67653 19.1018 9.61948 17.2866 11.4346C15.4715 13.2498 12.5285 13.2498 10.7134 11.4346C8.8982 9.61948 8.8982 6.67653 10.7134 4.86137C12.5285 3.04621 15.4715 3.04621 17.2866 4.86137"
                  stroke="#00569F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M14 16.3418C19.313 16.3418 24.5 18.6366 24.5 22.1658V23.3325C24.5 23.9765 23.9773 24.4991 23.3333 24.4991H4.66667C4.02267 24.4991 3.5 23.9765 3.5 23.3325V22.1658C3.5 18.6355 8.687 16.3418 14 16.3418Z"
                  stroke="#00569F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
              </svg>

              ورود / ثبت‌نام
            </a></li>
          <li class="loginBTN mr-2 px-4" v-if="isAuth">
            <a :href=" '/dashboard' + '/' + this.$store.state.dashboard.role">
              <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M17.2866 4.86137C19.1018 6.67653 19.1018 9.61948 17.2866 11.4346C15.4715 13.2498 12.5285 13.2498 10.7134 11.4346C8.8982 9.61948 8.8982 6.67653 10.7134 4.86137C12.5285 3.04621 15.4715 3.04621 17.2866 4.86137"
                  stroke="#00569F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M14 16.3418C19.313 16.3418 24.5 18.6366 24.5 22.1658V23.3325C24.5 23.9765 23.9773 24.4991 23.3333 24.4991H4.66667C4.02267 24.4991 3.5 23.9765 3.5 23.3325V22.1658C3.5 18.6355 8.687 16.3418 14 16.3418Z"
                  stroke="#00569F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
              </svg>

              پنل کاربری
            </a>
          </li>

          {{-- <li v-if="!isAuth"><a href="/register">ثبت‌نام</a></li> --}}


        </ul>
      </div>
    </nav>



    <!-- Content -->
    @yield('content')

    <!-- Footer -->
    <footer class="footer">
      <div class="above">
        <div class="container footer-conatin">
          <div class="row pb-3 firsRow">
            <div class="col-12">
              <div class="social">
                <a href="https://t.me/ghesta_ir" target="_blank">
                  <svg enable-background="new 0 0 24 24" class="ml-0" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" fill="#039be5" r="12" />
                    <path
                      d="m5.491 11.74 11.57-4.461c.537-.194 1.006.131.832.943l.001-.001-1.97 9.281c-.146.658-.537.818-1.084.508l-3-2.211-1.447 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.121l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.136-.953z"
                      fill="#fff" /></svg>
                </a>
                <a href="https://www.instagram.com/ghesta_ir/" target="_blank">
                  <svg enable-background="new 0 0 24 24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink">
                    <linearGradient id="SVGID_1_" gradientTransform="matrix(0 -1.982 -1.844 0 -132.522 -51.077)"
                      gradientUnits="userSpaceOnUse" x1="-37.106" x2="-26.555" y1="-72.705" y2="-84.047">
                      <stop offset="0" stop-color="#fd5" />
                      <stop offset=".5" stop-color="#ff543e" />
                      <stop offset="1" stop-color="#c837ab" />
                    </linearGradient>
                    <path
                      d="m1.5 1.633c-1.886 1.959-1.5 4.04-1.5 10.362 0 5.25-.916 10.513 3.878 11.752 1.497.385 14.761.385 16.256-.002 1.996-.515 3.62-2.134 3.842-4.957.031-.394.031-13.185-.001-13.587-.236-3.007-2.087-4.74-4.526-5.091-.559-.081-.671-.105-3.539-.11-10.173.005-12.403-.448-14.41 1.633z"
                      fill="url(#SVGID_1_)" />
                    <path
                      d="m11.998 3.139c-3.631 0-7.079-.323-8.396 3.057-.544 1.396-.465 3.209-.465 5.805 0 2.278-.073 4.419.465 5.804 1.314 3.382 4.79 3.058 8.394 3.058 3.477 0 7.062.362 8.395-3.058.545-1.41.465-3.196.465-5.804 0-3.462.191-5.697-1.488-7.375-1.7-1.7-3.999-1.487-7.374-1.487zm-.794 1.597c7.574-.012 8.538-.854 8.006 10.843-.189 4.137-3.339 3.683-7.211 3.683-7.06 0-7.263-.202-7.263-7.265 0-7.145.56-7.257 6.468-7.263zm5.524 1.471c-.587 0-1.063.476-1.063 1.063s.476 1.063 1.063 1.063 1.063-.476 1.063-1.063-.476-1.063-1.063-1.063zm-4.73 1.243c-2.513 0-4.55 2.038-4.55 4.551s2.037 4.55 4.55 4.55 4.549-2.037 4.549-4.55-2.036-4.551-4.549-4.551zm0 1.597c3.905 0 3.91 5.908 0 5.908-3.904 0-3.91-5.908 0-5.908z"
                      fill="#fff" />
                  </svg>
                </a>
                {{-- <a href="https://twitter.com/ghesta_ir" target="_blank">
                  <svg enable-background="new 0 0 24 24" class="ml-0" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="m21.552 7.749c0-.217-.008-.428-.018-.636.976-.693 1.797-1.558 2.466-2.554v-.001c-.893.391-1.843.651-2.835.777 1.02-.609 1.799-1.566 2.165-2.719-.951.567-2.001.967-3.12 1.191-.903-.962-2.19-1.557-3.594-1.557-2.724 0-4.917 2.211-4.917 4.921 0 .39.033.765.114 1.122-4.09-.2-7.71-2.16-10.142-5.147-.424.737-.674 1.58-.674 2.487 0 1.704.877 3.214 2.186 4.089-.791-.015-1.566-.245-2.223-.606v.054c0 2.391 1.705 4.377 3.942 4.835-.752.206-1.678.198-2.221.078.637 1.948 2.447 3.381 4.597 3.428-1.674 1.309-3.8 2.098-6.101 2.098-.403 0-.79-.018-1.177-.067 2.18 1.405 4.762 2.208 7.548 2.208 9.054 0 14.004-7.5 14.004-14.001z" fill="#55acee" /></svg>
                </a> --}}
                <a href="https://www.linkedin.com/company/ghesta/" target="_blank">
                  <svg enable-background="new 0 0 24 24" class="ml-0" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <g fill="#0077b5">
                      <path
                        d="m23.994 24v-.001h.006v-8.802c0-4.306-.927-7.623-5.961-7.623-2.42 0-4.044 1.328-4.707 2.587h-.07v-2.185h-4.773v16.023h4.97v-7.934c0-2.089.396-4.109 2.983-4.109 2.549 0 2.587 2.384 2.587 4.243v7.801z" />
                      <path d="m.396 7.977h4.976v16.023h-4.976z" />
                      <path
                        d="m2.882 0c-1.591 0-2.882 1.291-2.882 2.882s1.291 2.909 2.882 2.909 2.882-1.318 2.882-2.909c-.001-1.591-1.292-2.882-2.882-2.882z" />
                    </g>
                  </svg>
                </a>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-md-8 col-lg-6 d-flex parent-links">
              <div class="links ml-5">
                <a href="/estefta">شرعی بودن قسطا</a>
                <a href="/faq">سوالات متداول</a>
                <a href="https://t.me/ghesta_support" target="_blank">پشتیبانی آنلاین(تلگرام)</a>
                {{-- <a href="/help">راهنمای استفاده</a> --}}

              </div>
              <div class="links">
                <a href="/shop">همکاری با فروشگاه‌ها</a>
                <a href="/organ">همکاری با سازمان‌ها</a>

              </div>
            </div>
            <div class="col-12  col-md-4 col-lg-6 justify-content-end d-flex footerPic">
              @if($dev==1)
              <img src="/images/enamad.png" alt="نماد الکترونیکی قسطا">
              <img class="mr-3" src="/images/samandehi.png" alt="نشانه ملی ثبت">
              @else
              {{-- <a target="_blank" href="https://trustseal.enamad.ir/?id=103251&amp;Code=FsjSSj0UD7aYfyD0avBj">
                <img width="150" src="/images/enamad.png" style="cursor:pointer" id="FsjSSj0UD7aYfyD0avBj">
              </a> --}}
              <img id="jxlzfukzjxlzoeukesgtjxlz" style="cursor:pointer" alt="logo-samandehi"
                onclick="window.open('https://logo.samandehi.ir/Verify.aspx?id=161801&p=rfthgvkarfthmcsiobpdrfth', 'Popup','toolbar=no, scrollbars=no, location=no, statusbar=no, menubar=no, resizable=0, width=450, height=630, top=30')"
                src="/images/samandehi.png">
              @endif
            </div>
          </div>
          <div class="row address">
            <div class="col-12 d-flex justify-content-between border-top py-4 mt-4 px-0">
              <li>
                <a href="/map">
                  <svg width="48" height="50" viewBox="0 0 48 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="48" height="48" rx="20" fill="white" />
                    <g filter="url(#filter0_d)">
                      <path
                        d="M31 28L25 35L24 36.5L19.5 31L15 24V19.5L16.5 15L21 12.5H26L29.5 13.5L32.5 18L33 23L31 28Z"
                        fill="#00569F" />
                      <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M24 25.334C21.7907 25.334 20 23.5433 20 21.334C20 19.1247 21.7907 17.334 24 17.334C26.2093 17.334 28 19.1247 28 21.334C28 23.5433 26.2093 25.334 24 25.334Z"
                        fill="white" stroke="#00569F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                      <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M23.9998 36C23.9998 36 14.6665 28.3333 14.6665 21.3333C14.6665 16.1787 18.8452 12 23.9998 12C29.1545 12 33.3332 16.1787 33.3332 21.3333C33.3332 28.3333 23.9998 36 23.9998 36Z"
                        stroke="#00569F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </g>
                    <defs>
                      <filter id="filter0_d" x="3.6665" y="10" width="34.6667" height="40" filterUnits="userSpaceOnUse"
                        color-interpolation-filters="sRGB">
                        <feFlood flood-opacity="0" result="BackgroundImageFix" />
                        <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                          result="hardAlpha" />
                        <feOffset dx="-3" dy="6" />
                        <feGaussianBlur stdDeviation="3.5" />
                        <feColorMatrix type="matrix"
                          values="0 0 0 0 0 0 0 0 0 0.337255 0 0 0 0 0.623529 0 0 0 0.22 0" />
                        <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow" />
                        <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape" />
                      </filter>
                    </defs>
                  </svg>

                </a>

                تهران، میدان آزادی، اتوبان لشگری، جنب متروی بیمه، کارخانه نوآوری آزادی
              </li>
              <li>
                <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path opacity="0.16" fill-rule="evenodd" clip-rule="evenodd"
                    d="M25.7411 17.0805C27.3916 17.4525 29.0228 17.7623 30.4099 18.7422C31.8201 19.7384 32.792 21.1371 33.6337 22.656C34.6822 24.548 36.507 26.5188 35.8672 28.5897C35.228 30.6589 32.4594 30.9394 30.6955 32.1663C28.9993 33.3462 27.7445 35.1596 25.7411 35.6332C23.41 36.1843 20.8246 36.2226 18.7648 34.9867C16.6193 33.6993 15.4961 31.2804 14.5738 28.9325C13.5933 26.4365 12.4068 23.6861 13.3375 21.1705C14.2588 18.6804 16.7775 17.0547 19.2727 16.2319C21.392 15.5331 23.5656 16.5901 25.7411 17.0805Z"
                    fill="#3D3D3D" />
                  <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M22.4745 25.5255C20.9145 23.9655 19.7385 22.2188 18.9572 20.4468C18.7919 20.0722 18.8892 19.6335 19.1785 19.3442L20.2705 18.2535C21.1652 17.3588 21.1652 16.0935 20.3839 15.3122L18.8185 13.7468C17.7772 12.7055 16.0892 12.7055 15.0479 13.7468L14.1785 14.6162C13.1905 15.6042 12.7785 17.0295 13.0452 18.4428C13.7039 21.9268 15.7279 25.7415 18.9932 29.0068C22.2585 32.2722 26.0732 34.2962 29.5572 34.9548C30.9705 35.2215 32.3959 34.8095 33.3839 33.8215L34.2519 32.9535C35.2932 31.9122 35.2932 30.2242 34.2519 29.1828L32.6879 27.6188C31.9065 26.8375 30.6399 26.8375 29.8599 27.6188L28.6559 28.8242C28.3665 29.1135 27.9279 29.2108 27.5532 29.0455C25.7812 28.2628 24.0345 27.0855 22.4745 25.5255Z"
                    stroke="#3D3D3D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>

                <a href="tel:02191070092" class="ltr special-font">
                  021 - 910 700 92
                </a>
              </li>
              <li>
                <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path opacity="0.16" fill-rule="evenodd" clip-rule="evenodd"
                    d="M24.7411 15.0805C26.3916 15.4525 28.0228 15.7623 29.4099 16.7422C30.8201 17.7384 31.792 19.1371 32.6337 20.656C33.6822 22.548 35.507 24.5188 34.8672 26.5897C34.228 28.6589 31.4594 28.9394 29.6955 30.1663C27.9993 31.3462 26.7445 33.1596 24.7411 33.6332C22.41 34.1843 19.8246 34.2226 17.7648 32.9867C15.6193 31.6993 14.4961 29.2804 13.5738 26.9325C12.5933 24.4365 11.4068 21.6861 12.3375 19.1705C13.2588 16.6804 15.7775 15.0547 18.2727 14.2319C20.392 13.5331 22.5656 14.5901 24.7411 15.0805Z"
                    fill="#3D3D3D" />
                  <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M22.6004 13.5H25.4004C26.1736 13.5 26.8004 14.1268 26.8004 14.9V20.4999L24.0004 19.1L21.2004 20.4999V14.9C21.2004 14.5287 21.3479 14.1726 21.6105 13.91C21.873 13.6475 22.2291 13.5 22.6004 13.5V13.5Z"
                    stroke="#323232" stroke-width="1.74926" stroke-linecap="round" stroke-linejoin="round" />
                  <rect x="18.167" y="26.333" width="5.83328" height="3.49997" stroke="#323232" stroke-width="1.74926"
                    stroke-linecap="round" stroke-linejoin="round" />
                  <path
                    d="M26.8006 14.667H32.1672C33.4559 14.667 34.5006 15.7117 34.5006 17.0003V32.1668C34.5006 33.4555 33.4559 34.5002 32.1672 34.5002H15.834C14.5454 34.5002 13.5007 33.4555 13.5007 32.1668V17.0003C13.5007 15.7117 14.5454 14.667 15.834 14.667H21.2007"
                    stroke="#323232" stroke-width="1.74926" stroke-linecap="round" stroke-linejoin="round" />
                </svg>

                کد پستی
                ―
                1391955412
              </li>

              {{-- <a href="/map" target="_blank">
                <small>مشاهده آدرس روی نقشه</small>
              </a> --}}
            </div>
          </div>


        </div>
      </div>

      <div class="container">
        <div class="row">
          <div class="col-12 text-center">
            <span class="copy-right py-4 d-block">
              کلیه حقوق برای شرکت پیشگامان اعتبارآفرین شریف محفوظ است
            </span>
          </div>
        </div>
      </div>


      <div class="container">
        <div class="endLine text-center border-top mt-4  mb-3">
          <img @click="toggleFooter" src="/images/arrow.png" alt="icon">
        </div>
      </div>


      <div class=" pb-3">
        <div class="container">
          <div class="row">
            <div class="col-12 below">
              <div class="trans" id="trans"></div>
              <p class="long-description " id="myDIV">
                <strong>قسطا</strong>
                به عنوان
                <strong>اولین فروشگاه اینترنتی فروش اقساطی کالا و خدمات</strong>
                با تنوع بی نهایت، با شعار خرید اقساطی کالا و خدمات
                برای همه، شروع به فعالیت کرد و در مدت کوتاهی با اقبال عمومی مواجه شد. قسطا با درک شرایط فعلی جامعه و
                مشکلات
                معیشتی اقتصادی عموم افراد بر آن شد که بستری برای فروش قسطی منصفانه فراهم نماید. قسطا با شرایطی جدیدتر و
                گسترده
                تر نسبت به تمام سایت های این حوزه شما را قادر به خرید قسطی از هر فروشگاه اینترنتی یا فیزیکی خواهد نمود.
                خرید
                قسطی موبایل، لپ تاپ، لوازم منزل، کنسول بازی، بلیط هواپیما و هتل و … را از هر فروشگاهی که دوست دارید
                آنلاین

                <strong>
                  با قسطا هرچه در ذهن داشته باشید را می توانید
                  اقساطی بخرید.
                </strong>
                بنابراین به رویاهایتان تحقق ببخشید.
                <br>
                <br>
                اکنون
                <strong> خرید قسطی از کلیه فروشگاه های اینترنتی و فیزیکی پیش روی شماست</strong>
                . خرید اقساطی انواع کالاهای دیجیتال از دیجی
                کالا، خرید اقساطی تلویزیون، یخچال، کلیه لوازم منزل یا جهیزیه از فروشگاه ها و پاساژها ، خرید قسطی انواع
                بیمه
                از
                بیمیتو، هرنوع بلیط هواپیما یا رزرو هتل، انواع پوشاک از دیجی استایل و حتی خرید اقساطی انواع کالا از
                آمازون
                (با
                کمک سایت های ایرانی واسط مانند لک لک) فراهم شده است.
                <br>
                <br>
                قسطا در راستای افزایش قدرت خرید اقشار مختلف مردم خصوصا کارمندان، با ارائه قسطاکارت اقدام به راه اندازی
                راه
                حل
                سازمانی خود نموده است. در این راستا سازمان ها و شرکت های خصوصی یا دولتی کوچک و بزرگ می توانند با عقد
                قرارداد
                با
                قسطا، شرایط خرید اقساطی هر نوع کالا از هر فروشگاه اینترنتی یا فیزیکی را تنها با ارائه گواهی کسر از حقوق
                برای
                کارمندان خود فراهم کنند. بدون نیاز به پیش پرداخت یا چک، سفته و ضامن! همچنین سازمان ها و شرکت ها نیز می
                توانند
                تجهیزات مورد نیاز خود مانند لوازم اداری، اسکنر، پرینتر، دستگاه کپی، مبلمان اداری و… را به وسیله قسطاکارت
                ،
                به
                صورت قسطی خرید نمایند.
                <br>
                <br>
                <strong>
                  “الان بخر، بعدا بپرداز!”
                </strong>
                ، این شعار قسطاست. با قسطاکارت هرچه نیاز دارید، از انواع کالا و خدمات، از هر فروشگاه
                اینترنتی یا فیزیکی دلخواهتان، الان بخرید اما بعدا بپردازید!
              </p>
            </div>

          </div>
        </div>
      </div>
    </footer>

  </div>

  <script src="/libs/popper.min.js"></script>
  <script src="/libs/bootstrap.min.js"></script>
  <script src="/js/app.min.js?ver={{$version}}"></script>
  @stack('scripts')

  @if($dev == 0)
  @include('addons.append_body')
  @endif
</body>

</html>

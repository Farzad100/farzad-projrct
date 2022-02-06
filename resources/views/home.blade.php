@extends('layouts.app',['tag_alt'=>isset($tag_alt) ? $tag_alt : false])
@section('content')

@push('header')

@php($title='قسطا | خرید اقساطی کالا و خدمات')
@php($description=' سریع ترین روش برای خرید اقساطی از کلیه فروشگاه‌های آنلاین و فیزیکی - با قسطا در حداکثر 5 روز هر چیزی
رو قسطی تهیه کن.')
@php($url='https://ghesta.ir')

<!-- Primary Meta Tags -->
<title>{{$title}}</title>
<meta name="title" content="{{$title}}">
<meta name="description" content="{{$description}}">
<link rel="canonical" href="{{$url}}" />

<!-- Open Graph / Facebook -->
<meta property="og:url" content="{{$url}}">
<meta property="og:title" content="{{$title}}">
<meta property="og:description" content="{{$description}}">

<!-- Twitter -->
<meta property="twitter:url" content="{{$url}}">
<meta property="twitter:title" content="{{$title}}">
<meta property="twitter:description" content="{{$description}}">






@endpush

@push('style')
<link rel="stylesheet" href="/libs/swiper.min.css">
<link rel="stylesheet" href="/libs/owl.carousel.min.css">
@endpush





<!-- header -->
<header class="h-header">
  <div class="container">
    <div class="row flex-lg-row">
      <div class="col-12 col-md-6 col-lg-6 d-flex justify-content-start">
        <div class="text-right text-lg-right mt-3 mt-lg-0">
          <h1 class="special-font font-weight-bold my-4 big-title mb-5">
            قسطا کارت <span>(خریداقساطی)</span> </h1>
          <h5 class="special-font  small-title text2">
            با قسطا کارت هر کالا یا خدماتی رو از همه فروشگاه‌های آنلاین و فیزیکی سراسر کشور قسطی تهیه کنید
          </h5>

          <div class="d-flex">
            <a :href=" '/dashboard' + '/' + this.$store.state.dashboard.role" class="action-btn ml-4">
              <i>
                <svg width="39" height="39" viewBox="0 0 49 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M34.0146 37.0052V25.0002C34.0149 23.9388 33.5934 22.9208 32.8429 22.1703C32.0923 21.4198 31.0743 20.9982 30.0129 20.9985H10.0046C8.9432 20.9982 7.92519 21.4198 7.17467 22.1703C6.42415 22.9208 6.00264 23.9388 6.00293 25.0002V37.0052C6.00264 38.0666 6.42415 39.0846 7.17467 39.8351C7.92519 40.5857 8.9432 41.0072 10.0046 41.0069H30.0129C32.2229 41.0069 34.0146 39.2152 34.0146 37.0052Z"
                    stroke="#00569F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                  <path d="M6.00293 29.1601H34.0146" stroke="#00569F" stroke-width="3" stroke-linecap="round"
                    stroke-linejoin="round" />
                  <path
                    d="M12.4053 20.9985L14.8263 11.9547C15.101 10.9298 15.7722 10.0563 16.6919 9.52698C17.6116 8.99769 18.7041 8.85613 19.7283 9.13355L39.0564 14.3157C40.0813 14.5904 40.9548 15.2617 41.4841 16.1813C42.0134 17.101 42.155 18.1935 41.8775 19.2178L38.7763 30.8026C38.2244 32.8875 36.1143 34.1554 34.0143 33.6638"
                    stroke="#00569F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </i>
              دریافت قسطاکارت
            </a>
            <a href="#calcPosition" class="action-btn">
              محاسبه‌گر اقساط
            </a>
          </div>


        </div>
      </div>
      <div class="col-12 col-md-6 col-lg-6 text-center">
        <div class="c-cards">
          <img src="/images/ghesta-cart.png" alt="قسطا كارت يك كارت اعتياري براي خريد قسطی بدون ضامن از سراسر ايران" >
        </div>
      </div>
    </div>
  </div>

</header>

<!-- about -->
<div class="about mb-2 ">
  <div class="container d-flex ">
    <div class="row">
      <div class="col-12 col-md-6">
        <h2 class="special-font font-weight-600 mb-4 mt-4 ">
          <span class="highlighted-text">قسطاکارت</span> چیست؟
        </h2>
        <h5 class="m-0 special-font pb-5 pr-4">
          قسطاکارت یه کارت اعتباریه که به میزان مبلغ درخواستی شما شارژ میشه و <span class="highlighted-text">میتونین
            باهاش</span> از هر <span class="highlighted-text">فروشگاه اینترنتی</span> یا
          فیزیکی در سراسر کشور خریدتون رو انجام بدین و هزینه اش رو بعداً بصورت اقساطی بپردازین.
        </h5>
      </div>
      <div class="col-12 col-md-6 d-flex align-items-center  justify-content-center  text-center">

        <!-- Button trigger modal -->
        <button type="button" class="playvideo" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
          <img src="/images/VideoGhesta.jpg" alt="ویدیوی معرفی پلتفرم قسطاو معرفی شرایط قسطاکارت" >
        </button>

        <!-- Modal -->
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
          aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">معرفی قسطا</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <iframe class="moarefi-ghesta" src="https://www.aparat.com/video/video/embed/videohash/MPsu8/vt/frame"
                  id="video1" title="معرفی قسطا" allowFullScreen></iframe>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- features -->
@include('components.features')

<!-- how to get card -->

<div class="sec-title text-center ">
  <span class="text-c-l mb-3 d-block ">
    چگونه
  </span>
  <h2 class="special-font mb-3 ">
    <span class="highlighted-text">مراحل دریافت قسطا کارت</span>
  </h2>

</div>

<div class="steps">
  <div class="container d-flex flex-column flex-lg-row align-items-center justify-content-center">
    <div class="step d-flex align-items-start text-center">
      <svg width="136" height="92" viewBox="0 0 136 92" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g filter="url(#filter0_d)">
          <rect width="78.382" height="78.382" rx="20"
            transform="matrix(0.866044 -0.499967 0.866044 0.499967 0 42.1885)" fill="#00569F" />
        </g>
        <rect width="78.382" height="78.382" rx="20" transform="matrix(0.866044 -0.499967 0.866044 0.499967 0 39.1885)"
          fill="#EEEEF8" />
        <path
          d="M84.1255 45.229L80.6545 47.2328L79.8426 47.7015L79.0307 47.2328L74.6464 44.7017C67.3121 40.4676 60.1063 37.2452 53.0291 35.0344L52.0142 34.7297L52.5622 34.1555L54.8559 31.6596L55.4039 31.062L56.4188 31.3901C64.132 33.7806 71.9129 37.2413 79.7614 41.7722L84.1255 44.2916L84.9374 44.7603L84.1255 45.229Z"
          fill="#30357C" />
        <defs>
          <filter id="filter0_d" x="0.146484" y="4.85742" width="135.472" height="86.6619" filterUnits="userSpaceOnUse"
            color-interpolation-filters="sRGB">
            <feFlood flood-opacity="0" result="BackgroundImageFix" />
            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" />
            <feOffset dy="6" />
            <feGaussianBlur stdDeviation="5" />
            <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.337255 0 0 0 0 0.623529 0 0 0 0.24 0" />
            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow" />
            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape" />
          </filter>
        </defs>
      </svg>

      <div class="t">
        <h4 class="special-font">
          ثبت سفارش
        </h4>
        <p>
          سفارش رو با تعیین مبلغ مورد نیاز، تعداد اقساط و میزان پیش‌پرداخت ثبت کنین.
        </p>
      </div>
    </div>
    <div class="step d-flex align-items-end text-center">
      <svg width="136" height="92" viewBox="0 0 136 92" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g filter="url(#filter0_d)">
          <rect width="78.382" height="78.382" rx="20"
            transform="matrix(0.866044 -0.499967 0.866044 0.499967 0 42.1885)" fill="#00569F" />
        </g>
        <rect width="78.382" height="78.382" rx="20" transform="matrix(0.866044 -0.499967 0.866044 0.499967 0 39.1885)"
          fill="#EEEEF8" />
        <path
          d="M65.086 35.3156C66.3445 34.5891 66.852 33.929 66.6084 33.3353C66.3919 32.8509 65.4649 32.1205 63.8276 31.144C63.5569 30.9878 63.0969 30.7222 62.4473 30.3472L60.519 29.234L59.7071 28.7653L60.519 28.2965L63.8682 26.3631L64.6801 25.8943L65.492 26.3631L67.3391 27.4294C68.7194 28.2262 69.8696 28.9684 70.7898 29.6558C71.7099 30.3277 72.4745 31.0424 73.0834 31.8002C73.6788 32.5502 73.9833 33.2728 73.9968 33.968C74.0239 34.6555 73.733 35.3781 73.124 36.1359C72.5286 36.8858 71.5814 37.6358 70.2823 38.3857C69.4298 38.8779 68.3675 39.3193 67.0955 39.7099C69.5719 40.8738 72.3865 42.3659 75.5395 44.1861L79.9238 46.7172L80.7357 47.1859L79.9238 47.6546L76.4529 49.6584L75.641 50.1271L74.829 49.6584L70.4447 47.1273C63.151 42.9167 55.952 39.6903 48.8477 37.4483L47.8328 37.1436L48.3809 36.5694L50.6948 34.0618L51.2835 33.4173L52.3187 33.804L60.5596 36.8272C62.3458 36.5304 63.8546 36.0265 65.086 35.3156Z"
          fill="#30357C" />
        <defs>
          <filter id="filter0_d" x="0.146484" y="4.85742" width="135.472" height="86.6619" filterUnits="userSpaceOnUse"
            color-interpolation-filters="sRGB">
            <feFlood flood-opacity="0" result="BackgroundImageFix" />
            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" />
            <feOffset dy="6" />
            <feGaussianBlur stdDeviation="5" />
            <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.337255 0 0 0 0 0.623529 0 0 0 0.24 0" />
            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow" />
            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape" />
          </filter>
        </defs>
      </svg>

      <div class="t">
        <h4 class="special-font">
          بارگذاری مدارک
        </h4>
        <p>
          مدارک لازم شامل یک برگ چک صیادی (خالی)، گزارش حساب و کارت ملی رو بارگذاری کنین.
        </p>
      </div>
    </div>
    <div class="step d-flex align-items-start text-center border-none ">
      <svg width="136" height="92" viewBox="0 0 136 92" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g filter="url(#filter0_d)">
          <rect width="78.382" height="78.382" rx="20"
            transform="matrix(0.866044 -0.499967 0.866044 0.499967 0 42.1885)" fill="#00569F" />
        </g>
        <rect width="78.382" height="78.382" rx="20" transform="matrix(0.866044 -0.499967 0.866044 0.499967 0 39.1885)"
          fill="#EEEEF8" />
        <path
          d="M60.9859 37.6826C61.5001 37.3858 61.6489 36.9718 61.4324 36.4405C61.2294 35.9015 60.5731 35.3117 59.4635 34.6711L56.1347 32.7259L55.3228 32.2572L56.1347 31.7885L59.4432 29.8785L60.2551 29.4097L61.0671 29.8785L64.3959 31.8236C65.1672 32.2689 65.8506 32.6009 66.446 32.8197C67.0549 33.0306 67.6165 33.1361 68.1307 33.1361C68.6449 33.1204 69.1118 32.9915 69.5313 32.7494C69.7884 32.6009 69.9711 32.4642 70.0793 32.3392C70.1876 32.2142 70.2417 32.0424 70.2417 31.8236C70.2417 31.5893 70.0387 31.3002 69.6328 30.9565C69.2268 30.5972 68.6247 30.187 67.8263 29.7261L63.787 27.3943L62.9751 26.9255L63.787 26.4568L67.0752 24.5585L67.8872 24.0898L68.6991 24.5585L72.7384 26.8904C75.4583 28.4606 77.028 30.023 77.4475 31.5776C77.8534 33.1243 77.0348 34.4875 74.9914 35.6671C73.2187 36.6905 71.216 37.1671 68.9832 37.0967C69.105 38.3857 68.3337 39.5106 66.6693 40.4715C65.9656 40.8777 65.0928 41.2098 64.0509 41.4676C66.5001 42.6159 69.3148 44.108 72.4948 45.9438L76.8791 48.4749L77.6911 48.9436L76.8791 49.4123L73.4082 51.4161L72.5963 51.8848L71.7844 51.4161L67.4 48.885C60.1063 44.6744 52.9073 41.448 45.803 39.206L44.7881 38.9013L45.3362 38.3271L47.6501 35.8195L48.2388 35.175L49.274 35.5617L57.5961 38.6084C58.3674 38.5381 58.9967 38.4404 59.4838 38.3154C59.9845 38.1826 60.4852 37.9717 60.9859 37.6826Z"
          fill="#30357C" />
        <defs>
          <filter id="filter0_d" x="0.146484" y="4.85742" width="135.472" height="86.6619" filterUnits="userSpaceOnUse"
            color-interpolation-filters="sRGB">
            <feFlood flood-opacity="0" result="BackgroundImageFix" />
            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" />
            <feOffset dy="6" />
            <feGaussianBlur stdDeviation="5" />
            <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.337255 0 0 0 0 0.623529 0 0 0 0.24 0" />
            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow" />
            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape" />
          </filter>
        </defs>
      </svg>

      <div class="t">
        <h4 class="special-font">
          بررسی مدارک
        </h4>
        <p>
          مدارک شما ظرف 1 روز کاری برای اعتبارسنجی بررسی می‌شه.
        </p>
      </div>
    </div>
    <div class="step d-flex align-items-end text-center border-none">
      <svg width="136" height="92" viewBox="0 0 136 92" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g filter="url(#filter0_d)">
          <rect width="78.382" height="78.382" rx="20"
            transform="matrix(0.866044 -0.499967 0.866044 0.499967 0 42.1885)" fill="#00569F" />
        </g>
        <rect width="78.382" height="78.382" rx="20" transform="matrix(0.866044 -0.499967 0.866044 0.499967 0 39.1885)"
          fill="#EEEEF8" />
        <path
          d="M66.5881 35.8312C67.9142 35.3625 69.1795 34.7805 70.3838 34.0852C71.737 33.304 72.8399 32.3158 73.6924 31.1206L74.3216 30.2183L75.5395 30.8979L79.0916 32.8783L79.6397 33.1947L79.3758 33.6048C78.415 34.9719 76.9942 36.1984 75.1132 37.2842C72.8534 38.5888 69.9914 39.6317 66.5272 40.4129C69.2742 41.7488 71.9738 43.1823 74.6261 44.7134L79.0104 47.2445L79.8223 47.7132L79.0104 48.182L75.5395 50.1857L74.7276 50.6545L73.9156 50.1857L69.5313 47.6546C62.2646 43.4596 55.0656 40.2333 47.9343 37.9756L46.9397 37.6592L47.4675 37.0967L49.7611 34.6008L50.3498 33.9563L51.385 34.343L60.3972 37.6241C60.3972 37.6241 60.4311 37.6201 60.4987 37.6123C60.5799 37.5967 60.6543 37.585 60.722 37.5772C60.7897 37.5537 60.8303 37.5459 60.8438 37.5537C58.4622 35.9757 57.1631 34.4211 56.9466 32.89C56.7301 31.3432 57.7111 29.941 59.8898 28.6832C61.6219 27.6833 63.6652 27.0271 66.0198 26.7146L66.7099 26.6209L67.1767 26.9372L69.8561 28.8356L70.9521 29.6089L69.2877 29.8902C67.6909 30.1714 66.3378 30.6323 65.2281 31.2729C64.1456 31.8979 63.672 32.5775 63.8073 33.3118C63.9291 34.0383 64.8222 34.882 66.4866 35.8429L66.5069 35.8546C66.534 35.8546 66.561 35.8468 66.5881 35.8312Z"
          fill="#30357C" />
        <defs>
          <filter id="filter0_d" x="0.146484" y="4.85742" width="135.472" height="86.6619" filterUnits="userSpaceOnUse"
            color-interpolation-filters="sRGB">
            <feFlood flood-opacity="0" result="BackgroundImageFix" />
            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" />
            <feOffset dy="6" />
            <feGaussianBlur stdDeviation="5" />
            <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.337255 0 0 0 0 0.623529 0 0 0 0.24 0" />
            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow" />
            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape" />
          </filter>
        </defs>
      </svg>

      <div class="t">
        <h4 class="special-font">
          تحویل چک‌ها و دریافت قسطاکارت
        </h4>
        <p>
          به ازای اقساط چک صادر می‌کنین و به صورت حضوری یا با پست به ما تحویل می‌دین. ما هم قسطاکارت رو برای شما ارسال
          می‌کنیم.
        </p>
      </div>

    </div>
    <div class="step d-flex align-items-start text-center border-none">
      <svg width="136" height="92" viewBox="0 0 136 92" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g filter="url(#filter0_d)">
          <rect width="78.382" height="78.382" rx="20"
            transform="matrix(0.866044 -0.499967 0.866044 0.499967 0 42.1885)" fill="#00569F" />
        </g>
        <rect width="78.382" height="78.382" rx="20" transform="matrix(0.866044 -0.499967 0.866044 0.499967 0 39.1885)"
          fill="#EEEEF8" />
        <path
          d="M63.6449 36.3351C63.8343 37.3663 63.9764 38.1279 64.0712 38.6201C64.1659 39.0966 64.2944 39.702 64.4568 40.4364C64.6057 41.1629 64.741 41.7019 64.8628 42.0535C64.9981 42.3972 65.174 42.8112 65.3905 43.2956C65.5935 43.7721 65.8168 44.151 66.0603 44.4322C66.3039 44.6978 66.5881 44.979 66.9129 45.2759C67.2512 45.5649 67.6301 45.8305 68.0495 46.0727C68.875 46.5493 69.5381 46.8149 70.0387 46.8695C70.5394 46.9086 71.0604 46.7719 71.6017 46.4594C71.967 46.2485 72.2377 46.0766 72.4136 45.9438C72.5895 45.7954 72.7384 45.6235 72.8601 45.4282C72.9684 45.2251 72.9549 45.022 72.8196 44.8189C72.6978 44.608 72.4745 44.3541 72.1497 44.0572C71.825 43.7604 71.3581 43.4205 70.7492 43.0378L70.0184 42.569L70.7898 42.1238L73.6112 40.495L74.3825 40.0497L75.1944 40.4715C75.9928 40.9012 76.6762 41.2176 77.2445 41.4207C77.8264 41.616 78.3474 41.7136 78.8074 41.7136C79.254 41.7058 79.6329 41.6511 79.9441 41.5496C80.2554 41.4324 80.6342 41.2449 81.0808 40.9871C81.6627 40.6512 81.9198 40.3465 81.8521 40.0731C81.798 39.7919 81.3514 39.4091 80.5125 38.9247C80.1606 38.7216 79.7953 38.5341 79.4164 38.3623C79.0375 38.1904 78.5977 38.0381 78.097 37.9053C77.5963 37.7725 77.1565 37.6592 76.7776 37.5655C76.3852 37.4639 75.8372 37.3663 75.1335 37.2725C74.4299 37.1788 73.8615 37.1085 73.4285 37.0616C72.9819 37.0069 72.2918 36.9444 71.3581 36.8741C70.4109 36.796 69.6734 36.7374 69.1456 36.6983C68.6179 36.6593 67.7451 36.6007 66.5272 36.5226C65.3093 36.4444 64.3486 36.3819 63.6449 36.3351ZM79.1931 45.3345C79.7344 46.9125 78.9834 48.2913 76.94 49.4709C74.8155 50.6974 72.5557 51.2521 70.1605 51.1349C67.7654 51.0021 65.4379 50.2834 63.178 48.9788C62.6774 48.6897 62.2105 48.3734 61.7775 48.0296C61.3445 47.6859 60.9588 47.3617 60.6205 47.057C60.2957 46.7446 59.9845 46.3696 59.6868 45.9321C59.3891 45.479 59.1455 45.104 58.9561 44.8072C58.7666 44.4947 58.5704 44.0611 58.3674 43.5065C58.1509 42.944 57.9885 42.5144 57.8803 42.2175C57.772 41.9206 57.6435 41.448 57.4946 40.7996C57.3458 40.1356 57.2375 39.6591 57.1699 39.37C57.1157 39.0732 57.0142 38.5459 56.8654 37.7881C56.7165 37.0147 56.6151 36.4796 56.5609 36.1827L55.4242 36.1593L54.389 36.1476L54.3282 35.55L53.9831 32.5384L53.9019 31.8119L55.1401 31.8236C58.1712 31.8549 60.8641 31.9174 63.2186 32.0111C65.5867 32.0971 67.9278 32.2377 70.2417 32.433C72.5692 32.6205 74.6193 32.8744 76.392 33.1947C78.1782 33.5071 79.8494 33.9095 81.4056 34.4016C82.9753 34.8859 84.3285 35.4562 85.4652 36.1124C87.7385 37.4248 88.9767 38.7724 89.1797 40.1551C89.3962 41.53 88.4354 42.8346 86.2974 44.0689C84.2676 45.2407 81.8995 45.6626 79.1931 45.3345Z"
          fill="#30357C" />
        <defs>
          <filter id="filter0_d" x="0.146484" y="4.85742" width="135.472" height="86.6619" filterUnits="userSpaceOnUse"
            color-interpolation-filters="sRGB">
            <feFlood flood-opacity="0" result="BackgroundImageFix" />
            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" />
            <feOffset dy="6" />
            <feGaussianBlur stdDeviation="5" />
            <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0.337255 0 0 0 0 0.623529 0 0 0 0.24 0" />
            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow" />
            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape" />
          </filter>
        </defs>
      </svg>

      <div class="t">
        <h4 class="special-font">
          پیش پرداخت و شارژ قسطاکارت
        </h4>
        <p>
          پیش‌پرداخت رو واریز می‌کنید و قسطاکارت شما به میزان مبلغ درخواستی‌تون شارژ
          می‌شه.
        </p>
      </div>
    </div>
  </div>
</div>



<div class="container d-flex d-none justify-content-center">
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

<!-- CALCULATOR -->
@include('components.calculator')


<!-- CTA -->
@include('components.cta')


<!-- TESTIMONIALS -->
{{-- @include('components.testimonials') --}}


<div class="modal" id="newSite" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h4 class="special-font text-danger text-center">
          وبسایت درحال بروزرسانی است و ممکن است شما تا یک هفته آینده دچار اختلالاتی در فرایند‌های سفارش خود شوید
        </h4>
        <div class="border-top text-center pt-3">
          در صورت بروز مشکل با پشتیبانی تماس حاصل فرمایید
        </div>
      </div>
    </div>
  </div>
</div>

@include('addons.sorry_phone')

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

  window.vm.order.type = "individual";
  window.vm.getGlobalsData();

</script>

<script src="/libs/jquery.min.js"></script>
<script src="/libs/owl.carousel.min.js"></script>


<script>
  jQuery(document).ready(function ($) {
    "use strict";
    //  TESTIMONIALS CAROUSEL HOOK
    $('#customers-testimonials').owlCarousel({
      loop: true,
      center: true,
      items: 5,
      margin: 0,
      autoplay: true,
      dots: true,
      autoplayTimeout: 8500,
      smartSpeed: 450,
      infinite: true,
      responsive: {
        0: {
          items: 1
        },
        768: {
          items: 3
        },
        1024: {
          items: 5
        }
      }
    });
  });

</script>


@endpush

@endsection

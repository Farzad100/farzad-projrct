@extends('layouts.landing')
@section('content') 

<!-- Navigation -->
<nav>
  <div class="container">
    <a href="" class="logo">
      <img src="/images/logo.svg" alt="قسطا">
    </a>
    <a href="#" class="btn btn-primary btn-lg ml-md-2 rounded-pill">
      ثبت‌ سفارش
    </a>
  </div>
</nav>

<!-- Header (Product preview) -->
<header class="h-torob-product" style="
  background-image:url('/images/bg-2.jpg');
  background-repeat:no-repeat;
  background-size:cover;
  ">
  <div class="container">
    <div class="row flex-column-reverse flex-xl-row">
      <div class="col-12 col-xl-5 d-flex align-items-center">
        <div class="text-center text-xl-right">
          <h4 class="special-font">
            شما در حال مشاهده ارزانترین محصول لیست شده در
            <span class="rounded-pill ml-1 pr-1 bg-white text-danger">
              <img height="30" src="https://torob.com/static/images/logo_original.png" alt="">
              ترب
            </span>
            هستید و می‌توانید از طریق قسطا این محصول را به صورت قسطی خریداری کنید.
          </h4>
        </div>
      </div>
      <div class="col-12 col-xl-7 position-relative mb-5">
        <div class="_product-image">
          <img class="_get-this-color" src="https://storage.torob.com/backend-api/base/images/8_/u5/8_u5j0xBxbMAshxz.jpg" crossorigin="anonymous" alt="">
        </div>
        <div class="_buy-box">
          <div class="d-flex justify-content-between px-2">
            <span>
              شوبین
            </span>
            <span class="opa-5">
              تهران
            </span>
          </div>
          <h5 class="mt-3">
            لپ تاپ 15 اینچ ایسوس X543MA
          </h5>
          <h5 class="mt-3 price-style estedad-font">
            8,200,000
            <small>
              تومان
            </small>
          </h5>
          <a href="" class="btn btn-success w-100 mt-4 rounded-pill">
            خرید قسطی
          </a>
          <button class="_more-options">
            فروشنده‌های بیشتر
          </button>
        </div>
      </div>
    </div>
  </div>
  {{-- <div class="gra">
    <div class="gra-1"></div>
    <div class="gra-2"></div>
    <div class="gra-3"></div>
  </div> --}}
</header>
@endsection



@push('header')
  <!-- Primary Meta Tags -->
  <title>Torob landing</title>
  <meta name="title" content="...">
  <meta name="description" content="...">

  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="...">
  <meta property="og:title" content="...">
  <meta property="og:description" content="...">
  <meta property="og:image" content="...">

  <!-- Twitter -->
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="...">
  <meta property="twitter:title" content="...">
  <meta property="twitter:description" content="...">
  <meta property="twitter:image" content="...">

  <meta name="robots" content="noindex, nofollow">
  <link rel="stylesheet" href="/css/landing.css">
@endpush



@push('scripts')
<script>
  window.vm.getGlobalsData();
</script>
@endpush
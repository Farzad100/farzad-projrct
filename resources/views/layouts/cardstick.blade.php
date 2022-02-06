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

  @stack('header')

  <!-- CSS only -->
  @stack('style')
  <link rel="stylesheet" href="/libs/bootstrap.min.css">
  <link rel="stylesheet" href="/css/style.css?ver={{$version}}">

  <style>
    body {
      width: 100%;   
      height: 100%;
      margin: 0;
      padding: 0;
    }
    .page {
      background: white;
      display: block;
      margin: 0 auto;
      margin-bottom: 0.5cm;
      width: 210mm;
      min-height: 297mm;
      max-height: 297mm;
      /* border: 1px solid red; */
      overflow: hidden;
      position: relative;
    }
    .page > div {
      width: 100%;
      height: calc(270mm/3);
      /* border-bottom: 1px solid red; */
      display: flex;
      align-items: center;
    }
    .page > div > span {
      width: calc(105mm - 15mm);
      height: calc(99mm - 25mm);
      /* border: 1px solid blue; */
      margin: 0 calc(15mm/2);
      display: block;
    }
    .page > div:first-child span {
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
    }
    .page > div:last-child {
      justify-content: flex-end;
    }
    .page > div:last-child span {
      height: 60mm;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
    }
    @page {
      size: A4;
      margin: 0;
    }
    @media print {
      html, body {
        width: 210mm;
        height: 297mm;   
        overflow: hidden;     
      }
      .page {
        margin: 0;
        overflow: hidden;
        border: initial;
        border-radius: initial;
        width: initial;
        min-height: initial;
        max-height: 297mm;
        box-shadow: initial;
        background: initial;
        page-break-after: always;
      }
    }
    </style>
</head>

<body> 
  <div id="app">
      <div class="page">
        <div>
          <span class="p-4">

            <span class="d-block mb-2">
              <h6>آدرس گیرنده:</h6>
              <strong>
                آرمان شجاعی
              </strong>
              <small class="opa-8">
                •
                کد ملی: 1850349741
              </small>
            </span>
            <small class="d-block mb-2 text-justify">
              آدرس: اسکندری جنوبی، کوچه بوستان ش نمتبلمسینتل  شسنبشسی تن  نخسشحبنسشیخ س یسلنمیبتل کمشسنبمکسینکمن شکمسینبکمسینبکمسین کمنسشبمکسینکملنس کمینبسیکم کمینبسی مکن یسم شبنسشخحنب  نسخشبکمسشین رقی، کوچه راشدی، کوچه ولی‌اله خاموشی، پلاک ۹۲ 
            </small>
            <div class="d-flex">
              <small class="pl-4 estedad-font d-flex aic">
                <span class="opa-7">کدپستی:</span>
                ۱۲۴۶۲۶۸۷۳۲۵
              </small>
              <small class="estedad-font d-flex aic">
                <span class="opa-7">موبایل:</span>
                09385586943
              </small>
            </div>
          </span>
        </div>
        <div class="pt-5">
          <span class="pt-5">
            رمز کارت: 6224
          </span>
        </div>
      </div> 
  </div>

  <script src="/libs/popper.min.js"></script>
  <script src="/libs/bootstrap.min.js"></script>
  <script src="/js/app.min.js?ver={{$version}}"></script>
  <script>
    
  </script>
  @stack('scripts')

  @if($dev == 0)
  @include('addons.append_body')
  @endif
</body>

</html>
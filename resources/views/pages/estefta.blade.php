@extends('layouts.app')

@push('header')

@php($title='قسطا | استفتاء از مراجع تقلید')
@php($description='سریع ترین روش برای خرید اقساطی از کلیه فروشگاه‌های آنلاین و فیزیکی - با قسطا در حداکثر 4 روز هر چیزی رو قسطی تهیه کن')
@php($url='https://ghesta.ir/estefta')


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

@section('content')


<header class="estefta">
  <div class="container">
    <div class="row d-flex align-items-center justify-content-center flex-column">
      <div class="col-10 Stext">
        <h1 class="mb-3">استفتاء</h1>
        <h2>از مراجع تقلید</h2>
      </div>
      <div class="col-10 col-lg-5 col-md-7  text-center">
        <img  src="/images/estefta-pic.png" alt="استفتاء،از مراجع تقلید" >
      </div>
      
    </div>
    
  </div>
  
</header>


<div class="container rah">
  <div class="row d-flex align-items-center justify-content-center flex-column">
    <div class="col-10 col-lg-5 col-md-7 text-right mb-3">
      <h6 class="mb-4">استفتاء از مقام معظم رهبری (دامت برکاته)</h6>
      <h4 class="mb-3">
        شرکتی با شرایط زیر اقدام به فروش اقساطی کالا و خدمات به مشتریان می‌کند. حکم معامله به شرح زیر چیست؟
      </h4>
      <p>
        مشتری با مراجعه به وب‌سایت شرکت اعلام می‌کند که قصد خرید کالا یا خدماتی با مبلغ مشخص را دارد. طی قراردادی که مشتری با شرکت می‌بندد، یک کارت خرید بانکی به نام وی صادر شده و به میزان مبلغ خریدِ اعلامی از سوی مشتری، شارژ می‌شود. مشتری با این کارت به وکالت از شرکت، کالا یا خدمات موردنظر خود را نقداَ از هر فروشگاه دلخواه خریداری کرده و سپس شرکت آن را به صورت اقساطی به خود او می‌فروشد.
        <br>
        شرایط فروش اقساطی در هنگام عقد قرارداد و تحویل کارت خرید به صورت دقیق به اطلاع مشتری می‌رسد (اعم از میزان سود، پیش‌پرداخت و زمان و مبالغ اقساط). لازم به ذکر است:
        <br>
        هر دو طرف از شیوه معامله و نقش خود آگاهی دارند.
        <br>
        با کارت‌ خرید بانکی صادر شده تنها می‌توان از درگاه‌های پرداخت اینترنتی و پایانه‌های فروشگاهی خرید کرد و قابلیت نقدشوندگی ندارد.
        <br>
        باتشکر و احترام
      </p>
    </div>
  </div>
</div>
<div class="container responseRah">
  <div class="row d-flex align-items-center justify-content-center flex-column">
    <div class="col-10 col-lg-5 col-md-7 text-right my-4 px-4">
      <h6 class="mt-3">جوابیه</h6>
      <h6>موضوع: معاملات و شغلها</h6>
      <h6>شماره استفتاء: w572y7k</h6>
      <h5 class="mt-5">بسم الله الرحمن الرحیم</h5>
      <h5>سلام علیکم و رحمة الله و برکاته، در فرض سؤال اشکال ندارد.</h5>
      <h5 class="mb-4">موفق و مؤید باشید</h5>
      <div class="d-flex align-items-center justify-content-end mt-5">
        <img  src="/images/rah-pic.png" alt=" (ره) مهر تایید سیدعلی خامنه ای" >
      </div>
    </div>
  </div>
</div>





@endsection

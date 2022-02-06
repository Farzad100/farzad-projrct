@extends('layouts.app')

@push('header')
@php($title='قسطا | راهنمای استفاده')
@php($description='سریع ترین روش برای خرید اقساطی از کلیه فروشگاه‌های آنلاین و فیزیکی - با قسطا در حداکثر 4 روز هر چیزی رو قسطی تهیه کن')

<!-- Primary Meta Tags -->
<title>{{$title}}</title>
<meta name="title" content="{{$title}}">
<meta name="description" content="{{$description}}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="https://ghesta.ir/help">
<meta property="og:title" content="{{$title}}">
<meta property="og:description" content="{{$description}}">
<meta property="og:image" content="">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="https://ghesta.ir/help">
<meta property="twitter:title" content="{{$title}}">
<meta property="twitter:description" content="{{$description}}">
<meta property="twitter:image" content="">

@endpush

@section('content')

<div class="page-header d-flex align-items-end" style="
  background-image:url('/images/h-header.jpg');
  background-repeat:no-repeat;
  background-size:cover;
  ">
  <div class="container pb-5">
    <h1 class="special-font">
     آموزش ویدئویی
    </h1>
  </div>
</div>

<div class="my-5 container faq">

  <h4 class="special-font mb-5">
    اگر موقع ثبت نام یا ثبت سفارش در قسطا مشکلی هست، اصلاً نگران نباشید! ما واستون ویدیوهای کوتاهی آماده کردیم که در اونها قدم به قدم مراحل ثبت نام تا ثبت سفارش جدید رو بطور کامل طی می کنیم. چت آنلاین و پشتیبانی تلگرامی هم پاسخگو هستیم :)
  </h4>

  <div class="row">
    <div class="col-12 col-lg-6">
      <div id="6442007100"><script type="text/JavaScript" src="https://www.aparat.com/embed/rXlVq?data[rnddiv]=6442007100&data[responsive]=yes&&recom=none"></script></div>
    </div>
    <div class="col-12 col-lg-6">
      <div id="7385153545"><script type="text/JavaScript" src="https://www.aparat.com/embed/8Pt42?data[rnddiv]=7385153545&data[responsive]=yes&&recom=none"></script></div>
    </div>
  </div>

</div>


@endsection

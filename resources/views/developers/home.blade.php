@extends('layouts.developers')
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

@push('style')
<link rel="stylesheet" href="/libs/swiper.min.css">
<link rel="stylesheet" href="/css/shop.css">
@endpush

<div class="container px-4" id="featured-3">
  <h2 class="special-font pb-2 mb-4 border-bottom">
    شروع
  </h2>
  <p>
    لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه
    روزنامه و مجله در ستون و سطرآنچنان که لازم است و برای شرایط فعلی تکنولوژی مورد نیاز و کاربردهای متنوع با هدف بهبود
    ابزارهای کاربردی می باشد. کتابهای زیادی در شصت و سه درصد گذشته، حال و آینده شناخت فراوان جامعه و متخصصان را می طلبد
    تا با نرم افزارها شناخت بیشتری را برای طراحان رایانه ای علی الخصوص طراحان خلاقی و فرهنگ پیشرو در زبان فارسی ایجاد
    کرد. در این صورت می توان امید داشت که تمام و دشواری موجود در ارائه راهکارها و شرایط سخت تایپ به پایان رسد وزمان مورد
    نیاز شامل حروفچینی دستاوردهای اصلی و جوابگوی سوالات پیوسته اهل دنیای موجود طراحی اساسا مورد استفاده قرار گیرد.
  </p>

  <h4 class="special-font">
    نحوه نصب
  </h4>
  <pre><code class="bash"> 
    $ npm install  
  </code></pre>

  <h4 class="special-font">
    نحوه اجرا
  </h4>
  <pre><code class="bash"> 
    $ npm run serve  
  </code></pre>

  <h4 class="special-font">
    نحوه استفاده
  </h4>
  
  <pre><code class="javascript"> 
    const ghesta = new Ghesta();
    ghesta.start();
  </code></pre>
</div>

@endsection

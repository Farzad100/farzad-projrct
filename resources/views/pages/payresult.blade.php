@extends('layouts.app')

@push('header')

@php($title='قسطا | نتیج پرداخت')
@php($description='سریع ترین روش برای خرید اقساطی از کلیه فروشگاه‌های آنلاین و فیزیکی - با قسطا در حداکثر 4 روز هر چیزی رو قسطی تهیه کن')

<!-- Primary Meta Tags -->
<title>{{$title}}</title>
<meta name="title" content="{{$title}}">
<meta name="description" content="{{$description}}">
<meta name="robots" content="noindex, nofollow">

@endpush

@section('content')
<div class="page-header d-flex align-items-end" style="
  background-image:url('/images/h-header2.jpg');
  background-repeat:no-repeat;
  background-size:cover;
  ">
  <div class="container pb-5 d-flex flex-column flex-lg-row align-items-center justify-content-center justify-content-lg-between">
    <h1 class="special-font">
      نتیجه پرداخت تست
    </h1>
  </div>
</div>

<div class="container mb-5">
  <div class="row">
    <div class="col-12 pt-5">
      <form action="{{$x['url']}}" method="post" class="ltr">
        <div class="p-2">
        POST to {{$x['url']}}
        </div>
        @if(isset($x['success']))
        <div class="p-2"> 
          code: {{$x['code']}}
          <br>
          clientrefid: {{$x['clientrefid']}}
          <br>
          payping_refid: {{$x['payping_refid']}}
          <br>
          refid from tx: {{$x['refid']}}
        </div>
        <div class="p-2">
          refid (= payping_refid) :
          <input type="text" name="refid" value="{{$x['payping_refid']}}">
        </div> 
        @else
        <div class="p-2">
          payping_refid: {{$x['payping_refid']}}
        </div>
        <div class="p-2">
          refid: 
          <input type="text" name="refid" value="{{$x['refid']}}">
        </div> 
        <div class="p-2">
          code: 
          <input type="text" name="code" value="{{$x['code']}}">
        </div>
        <div class="p-2">
          clientrefid: 
          <input type="text" name="clientrefid" value="{{$x['clientrefid']}}">
        </div> 
        @endif
        <div class="p-2">
          <button type="submit">TEST</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
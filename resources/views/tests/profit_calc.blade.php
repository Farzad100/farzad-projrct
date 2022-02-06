@php(config('app.env') == 'production' ? $dev=0 : $dev=1)
<!DOCTYPE html>
<html dir="rtl" lang="fa">

<head>
  @if($dev == 0)
  @include('addons.googletag_script')
  @endif
  <meta charset="utf-8">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="_token" content="{{ csrf_token() }}"> 
  <meta name="robots" content="noindex, nofollow">
  <meta name="theme-color" content="#00579F">

  <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon">
  <link rel="icon" href="/images/favicon.ico" type="image/x-icon">

  <title>قسطا</title>

  <link rel="stylesheet" href="/libs/bootstrap.min.css"> 
<style>
  td, th {
    border: 1px solid black;
    width: 110px;
    text-align: right;
    padding: 12px 24px;
  }
</style>
</head>

<body direction="ltr">
  @foreach($res as $item) 
  <div class="mt-5" style="max-width: 400px; direction: ltr; margin: 0 auto;"> 
    <div>oid: {{$item['oid']}}</div>
    <div>amount: {{number_format($item['amount'])}}</div>
    <div>prepayment: {{number_format($item['prepayment'])}}</div>
    <div>ghest: {{number_format($item['ghest'])}}</div> 
    <div>months: {{$item['months']}}</div>
    <div>cheques: {{$item['cheques']}}</div> 
    <div>scount: {{$item['gain']}}</div>
    <table style="direction: ltr" class="table table-striped"> 

      <thead>
        <th>ردیف</th>
        <th>باقیمانده</th>
        <th>سود</th>
        <th>اصل</th>
      </thead>
      
      @foreach($item['details'] as $key=>$row) 
      <tr>
        <td style="width: 32px">{{$key}}</td>
        <td>{{number_format($row['remaining'])}}</td>
        <td>{{number_format($row['income'])}}</td>
        <td>{{number_format($row['origin'])}}</td>
      </tr>
      @endforeach

      <tr style="font-weight: bold">
        <td style="width: 32px">sum</td>
        <td>0</td>
        <td>{{number_format($item['i'])}}</td>
        <td>{{number_format($item['o'])}}</td>
      </tr>

      <tr>
        <td style="width: 32px">{{$item['cheques']}}</td>
        <td>{{number_format($item['x']['remaining'])}}</td>
        <td>{{number_format($item['x']['income'])}}</td>
        <td>{{number_format($item['x']['origin'])}}</td>
      </tr>

    </table>
    <div>total should be: <strong>{{number_format($item['total'])}}</strong></div>
  </div>
  @endforeach
</body>

</html>
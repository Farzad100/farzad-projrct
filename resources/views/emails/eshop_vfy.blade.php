<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Ghesta</title> 

  <style>
    body {
      background: #e5eef5;
      font-family: sans-serif !important;
    }

    .tac {
      text-align: center;
    } 

    td.td-x-padd {
      padding-right: 50px;
      padding-left: 50px;
    }

    td.td-t-padd {
      padding-top: 64px;
    }

    td.td-b-padd {
      padding-bottom: 56px;
    }

    img {
      border-radius: 4px;
    }

    .text-brand {
      color: #0058a0;
    }

    .text-center {
      text-align: center;
    }

    .box-otp {
      background-color: #f7f7f7;
      border: 3px dashed #e0e0e0;
      padding: 16px 36px;
      border-radius: 8px;
      font-size: 32px;
      font-weight: bold;
      font-family: sans-serif;
    }

    .button {
      background: #003365;
      border-radius: 6px;
      padding: 10px 25px;
      color: #fff;
      text-decoration: none;
      display: inline-block;
    }

    .button:hover {
      background: #001C37;
    }

    .social {
      text-align: center;
    }

    .social a {
      font-size: 14px;
      margin-left: 10px;
      border-radius: 6px;
      background: #c9dff5;
      display: inline-flex;
      align-items: center;
      padding: 6px;
    }

    .social a svg {
      height: 24px;
    }
  </style>
</head>

<body style="direction: rtl;">

  <img style="
    height: 50px;
    display: block;
    margin: 42px auto;
  " src="https://ghesta.ir/images/logo-h50.png" alt="">

  <table style="
    background: #fff;
    box-shadow: 0 5px 15px #d9e2ec;
    width: 100%;
    max-width: 600px;
    margin: 0 auto 30px auto;
    border-radius: 6px;
  ">
    <thead>
      <tr>
        <td> 
        </td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style="padding-bottom: 48px; padding-right: 20px; padding-left: 20px;">
          <h2 class="text-brand" style="margin-top: 32px; text-align: center;">
            کد تأیید ایمیل
          </h2>
          <p style="margin-top: 32px; direction: rtl; text-align: justify; font-size: 1rem">
            فروشنده گرامی، درخواستی مبنی بر احراز ایمیل فروشگاه اینترنتی شما در قسطا ثبت شده. از کد زیر برای تأیید ایمیل استفاده کنید:
          </p>
          <br>
          <div style="text-align: center; margin-top: 8px;">
            <span class="box-otp">
              {{$otp}}
            </span>
          </div>
          <br>
          <p style="margin-top: 32px; direction: rtl; text-align: justify; font-size: 1rem">
            چنانچه این درخواست از سمت شما نیست، لطفاً آن را نادیده بگیرید و کد را در اختیار دیگران قرار ندهید (حتی تیم قسطا!).
          </p>
        </td>
      </tr>
    </tbody>
  </table>

  <table style="
    width: 100%;
    max-width: 600px;
    margin: 0 auto 50px auto;
    height: 100px;
  ">
    <tbody>
      <tr>
        <td>
          <div class="social">
            <a href="https://t.me/ghesta_ir" target="_blank">
              <img src="https://ghesta.ir/images/telegram-h48.png" alt="telegram" height="24">
            </a>
            <a href="https://www.instagram.com/ghesta_ir/" target="_blank" class="dana-font" style="text-decoration: none; color: darkblue">
              <img src="https://ghesta.ir/images/instagram-h48.png" alt="instagram" height="24">
              <span style="padding: 8px">اینستاگرام</span>
            </a>
            <a href="https://www.linkedin.com/company/ghesta/" target="_blank">
              <img src="https://ghesta.ir/images/linkedin-h48.png" alt="linkedin" height="24">
            </a>
          </div>
        </td>
      </tr>
      <tr class="tac">
        <h3 class="dana-font text-brand tac" style="margin-bottom: 5px">
          قسطا، الان بخر بعداً بپرداز!
        </h3>

        <span class="tac dana-font text-brand" style="display: block; margin-bottom: 35px; direction: ltr; font-weight: bold">
          ۰۲۱-۹۱۰ ۷۰۰ ۹۲
        </span>
      </tr>
      <tr>
        <td style="text-align: center; font-size: 12px">
          <small>
            مایل به دریافت این ایمیل نیستید؟
            <a href="">
              لغو اشتراک
            </a>
          </small>
        </td>
      </tr>
    </tbody>
  </table>

</body>

</html>
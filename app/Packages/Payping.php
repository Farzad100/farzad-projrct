<?php

namespace App\Packages;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class Payping
{
    public const base_oauth = 'https://oauth.payping.ir/v1/';

    public static function token()
    {
        return config('globals.payment.payping_token');
    }

    public static function user_exists($email)
    {
        $res = Http::withToken(self::token())->get(self::base_oauth . 'client/EmailExist?Email=' . $email);
        if (isset($res['exist'])) return $res['exist'];
        return $res;
    }

    public static function user_register($infoArray)
    {
        $res = Http::withToken(self::token())->post(self::base_oauth . 'client/ClientRegisterInit', $infoArray);
        $res = $res->json();
        return $res;

        /* "UserName": "string",
        "Email": "string",   //اجباری
        "FirstName": "string",
        "LastName": "string",
        "PhoneNumber": "string",
        "NationalCode": "string",
        "BirthDay": "2000-02-02",
        "Sheba": "string",
        "ReturnUrl": "[AuthorizationUrl]",     //اجباری
        "Address": "string", 
        "PostalCode": "2315468970",  
        "LocalPhone": "05138420884",  
        "TaxCode": "9876543210",
        "IsOfficialSubmited": true,  //اگر کسب و کار به صورت رسمی ثبت شده است,
        "BNationalCode": "18564852364"  //(در صورت رسمی ثبت شدن اجباری) شناسه ملی شرکت,
        "SabtNumber": "668751"  //شماره ثبت,
        "EconomicCode": "57654"  //کد اقتصادی, */
    }
}

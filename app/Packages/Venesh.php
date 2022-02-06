<?php

namespace App\Packages;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class Venesh
{
    public const base = 'https://venesh.ir/sandbox/';
    public const client_id = 27;

    public static function token()
    {
        $token = DB::table('tkn')->where(['name' => 'venesh'])->where('expired_at', '>', Carbon::now()->addHour())->first();
        if ($token) return $token->token;

        $res = Http::post(self::base . 'oauth/token', [
            'username' => config('globals.venesh.username'),
            'password' => config('globals.venesh.password'),
            'client_secret' => config('globals.venesh.secret'),
            'grant_type' => "password",
            'client_id' => 27,
        ]);
        $res = $res->json();

        DB::table('tkn')->updateOrInsert([
            'name' => 'venesh'
        ], [
            'token' => $res['access_token'],
            'rtoken' => $res['refresh_token'],
            'expired_at' => Carbon::now()->addHours(23)
        ]);

        return $res['access_token'];
    }

    public static function chequeReader($image, $encoded = true)
    {
        try {
            $res = Http::withToken(self::token())->timeout(20)
                ->post(self::base . 'api/Auth/v2/Validation/ChequeReader', [
                    'Image' => $encoded ? $image : (substr($image, 0, 4) == 'http' ? base64_encode(file_get_contents($image)) : base64_encode($image)),
                ]);
            $res = $res->json();
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return [
                'Message' => 'Timeout'
            ];
        }


        return $res;
    }
}

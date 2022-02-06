<?php

namespace App\Models;

use App\Models\Edate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Finnotech extends Model
{
    protected $guarded = ['id'];

    public static $API = 'https://apibeta.finnotech.ir';
    public static $REDIRECT_URI = 'https://ghesta.ir/ft';
    // public static $DEPOSIT_ID = '0100837938004';
    public static $DEPOSIT_ID = '0203394585007';
    public static $NID = '0013332511';
    public static $SCOPE_SMS_NID = 'facility:sms-nid-verification:get';

    public static function client($param)
    {
        return config('globals.finnotech.' . $param);
    }

    public static function TOKEN_BASIC()
    {
        return base64_encode(self::client('username') . ':' . self::client('password'));
    }

    public static function TOKEN_AC()
    {
        $x = Setting::select('val')->where('prop', 'ft-token')->first();
        return $x->val;
    }

    public static function TOKEN_CC()
    {
        $x = Setting::select('val')->where('prop', 'token-cc')->first();
        return $x->val;
    }

    //Functions
    public static function backCheques($nationalId, $trackId = null)
    {
        //NationalId Format: 10 numbers without - (as a string)

        $url = self::$API . "/credit/v2/clients/" . self::client('username') . "/users/$nationalId/backCheques?trackId=$trackId";
        return self::curlThis($url, 'CC');
    }

    public static function bonCharge($cardNumber, $amount, $track_id)
    {
        //Card Format: 16 numbers without -

        $url = self::$API . "/oak/v2/clients/" . self::client('username') . "/deposits/" . self::$DEPOSIT_ID . "/chargeCard?trackId=boncharge-" . $track_id;
        $postFields = ['card' => $cardNumber, 'amount' => $amount];
        return self::post($url, 'AC', $postFields);
    }

    public static function curlThis($url, $authType = 'AC', $method = 'GET', $postFields = null, $decode = 0, $proxy = 1)
    {
        if ($authType == '') $authType = 'AC';
        if ($proxy == 1) {
            $result = self::reqProxy($url, $authType, $method, $postFields);
            $decode == 1 ? $res = json_decode($result, true) : $res = $result;
            return $result;
        }
        $header = ["Content-Type: application/json"];
        switch ($authType) {
            case 'AC':
                $auth = self::TOKEN_AC();
                $authType = 'Bearer';
                break;
            case 'CC':
                $auth = self::TOKEN_CC();
                $authType = 'Bearer';
                break;
            case 'Basic':
                $auth = self::TOKEN_BASIC();
                break;
            default:
                $header = [];
                $auth = $authType;
                $authType = 'Bearer';
                break;
        }
        $authorization = "Authorization: $authType " . $auth;
        array_push($header, $authorization);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($method != 'GET') curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postFields));
        $result = curl_exec($ch);
        curl_close($ch);
        $decode == 1 ? $res = json_decode($result, true) : $res = $result;
        return $res;
    }

    public static function post($url, $authType = 'AC', $fields = null, $decode = 0, $proxy = 1)
    {
        if ($authType == '') $authType = 'AC';
        if ($proxy == 1) {
            $result = self::reqProxy($url, $authType, 'POST', $fields);
            $decode == 1 ? $res = json_decode($result, true) : $res = $result;
            return $result;
        }
        switch ($authType) {
            case 'AC':
                $token = self::TOKEN_AC();
                $authType = 'Bearer';
                break;
            case 'Basic':
                $token = self::TOKEN_BASIC();
                $authType = 'Basic';
                break;
            default:
                $token = $authType;
                $authType = 'Bearer';
                break;
        }
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => 2,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($fields),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: $authType $token"
            ],
        ]);
        $result = curl_exec($ch);
        curl_close($ch);
        $decode == 1 ? $res = json_decode($result, true) : $res = $result;
        return $res;
    }

    public static function reqProxy($uri, $auth, $method, $fields = [], $decode = 0)
    { 
        $token = self::client('proxy_token');
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => self::client('proxy_url'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_HTTP_VERSION => 2,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($fields),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer " . $token,
                "DEST_URI: " . $uri,
                "DEST_AUTH: " . $auth,
                "DEST_METHOD: " . $method
            ],
        ]);
        $result = curl_exec($ch);
        curl_close($ch);
        $decode == 1 ? $res = json_decode($result, true) : $res = $result;
        return $res;
    }

    public function oAuth_SMS($mobile, $scopes = null, $state = null)
    {
        //Mobile Format: 11 numbers (as a string)
        //Scopes Format: comma seperated 
        $redirectUri = self::$REDIRECT_URI;
        $url = self::$API . "/dev/v2/oauth2/authorize?client_id=" . self::client('username') . "&response_type=code&redirect_uri=$redirectUri&scope=$scopes&mobile=$mobile&state=$state&auth_type=SMS";
        return self::curlThis($url, 'Basic');
    }

    public function oAuthRequest($nationalId, $mobile, $trackId, $otp)
    {
        //NationalId Format: 10 numbers without - (as a string)
        //Mobile Format: 11 numbers (as a string)
        $url = self::$API . "/dev/v2/oauth2/verify/sms";
        $postFields = ['mobile' => "$mobile", 'otp' => "$otp", 'nid' => "$nationalId", 'trackId' => "$trackId"];
        return self::curlThis($url, 'Basic', 'POST', $postFields);
    }

    public function oAuthToken($code)
    {
        $url = self::$API . "/dev/v2/oauth2/token";
        $redirectUri = self::$REDIRECT_URI;
        $postFields = ['grant_type' => "authorization_code", 'code' => $code, 'auth_type' => 'SMS', 'redirect_uri' => $redirectUri];
        return self::curlThis($url, 'Basic', 'POST', $postFields);
    }

    public static function revokeToken()
    {

        $url = self::$API . "/dev/v2/clients/" . self::client('username') . "/token";
        $postFields = ['token' => self::TOKEN_AC(), 'token_type' => 'CODE', 'bank' => '062'];
        return self::curlThis($url, 'AC', 'DELETE', $postFields);
    }

    public static function refreshToken($type)
    {
        $url = self::$API . "/dev/v2/oauth2/token";
        if ($type == 'ac') {
            $expireTS = (int)Setting::select('comment')->where('prop', 'ft-token')->first()->comment;
            $rtoken = Setting::select('val')->where('prop', 'ft-token-refresh')->first()->val;
            if ($expireTS < time()) {
                $fields = ['grant_type' => "refresh_token", 'token_type' => 'CODE', 'bank' => '062', 'refresh_token' => $rtoken];
                $res = self::post($url, 'Basic', $fields, 1);
                if ($res['status'] == 'DONE') {
                    $token = $res['result']['value'];
                    $refreshToken = $res['result']['refreshToken'];
                    if (!empty($token) && Setting::where('prop', 'ft-token')->update(['val' => $token, 'comment' => time() + (8 * 86400)])) {
                        Setting::where('prop', 'ft-token-refresh')->update([
                            'val' => $refreshToken,
                            'comment' => Edate::edate('Y-m-d H:i', time() + (8 * 86400))
                        ]);
                    }
                }
            }
        } else if ($type == 'cc') {
            $expireTS = (int)Setting::select('comment')->where('prop', 'token-cc')->first()->comment;
            $rtoken = Setting::select('val')->where('prop', 'token-cc-refresh')->first()->val;
            if ($expireTS < time()) {
                $fields = [
                    'grant_type' => "refresh_token",
                    'token_type' => "CLIENT-CREDENTIAL",
                    'refresh_token' => $rtoken
                ];
                $res = self::post($url, 'Basic', $fields, 1);
                if ($res['status'] == 'DONE') {
                    $token = $res['result']['value'];
                    $refreshToken = $res['result']['refreshToken'];
                    if (!empty($token) && Setting::where('prop', 'token-cc')->update(['val' => $token, 'comment' => time() + (8 * 86400)])) {
                        Setting::where('prop', 'token-cc-refresh')->update([
                            'val' => $refreshToken,
                            'comment' => Edate::edate('Y-m-d H:i', time() + (8 * 86400))
                        ]);
                    }
                }
            }
        }

        return $res;
    }

    public static function reliabilityAvailableServices($nationalId, $token, $trackId = null)
    {
        //NationalId Format: 10 numbers without - (as a string)

        $url = self::$API . "/credit/v2/clients/" . self::client('username') . "/users/$nationalId/cc/available/reliability?trackId=$trackId";
        return self::curlThis($url, $token);
    }

    public static function reliabilityScore_SMS($nationalId, $token, $trackId = null)
    {
        //NationalId Format: 10 numbers without - (as a string)

        $url = self::$API . "/credit/v2/clients/" . self::client('username') . "/users/$nationalId/sms/score/reliability?trackId=$trackId";
        return self::curlThis($url, $token);
    }

    public static function reliabilityEmpty_SMS($nationalId, $token, $trackId = null)
    {
        //NationalId Format: 10 numbers without - (as a string)

        $url = self::$API . "/credit/v2/clients/" . self::client('username') . "/users/$nationalId/sms/empty/reliability?trackId=$trackId";
        return self::curlThis($url, $token);
    }

    public static function reliabilityBasic_SMS($nationalId, $token, $trackId = null)
    {
        //NationalId Format: 10 numbers without - (as a string)

        $url = self::$API . "/credit/v2/clients/" . self::client('username') . "/users/$nationalId/sms/basic/reliability?trackId=$trackId";
        return self::curlThis($url, $token);
    }

    public static function reliabilityStandard_SMS($nationalId, $token, $trackId = null)
    {
        //NationalId Format: 10 numbers without - (as a string)

        $url = self::$API . "/credit/v2/clients/" . self::client('username') . "/users/$nationalId/sms/standard/reliability?trackId=$trackId";
        return self::curlThis($url, $token);
    }

    public static function reliabilityAdvanced_SMS($nationalId, $token, $trackId = null)
    {
        //NationalId Format: 10 numbers without - (as a string)

        $url = self::$API . "/credit/v2/clients/" . self::client('username') . "/users/$nationalId/sms/advanced/reliability?trackId=$trackId";
        return self::curlThis($url, $token);
    }

    public static function reliabilityHistory_SMS($nationalId, $token, $trackId = null)
    {
        //NationalId Format: 10 numbers without - (as a string)

        $url = self::$API . "/credit/v2/clients/" . self::client('username') . "/users/$nationalId/sms/historical/reliability?trackId=$trackId";
        return self::curlThis($url, $token);
    }

    public static function bonBalance($cardNumber = null, $trackId = null)
    {
        //Card Format: 16 numbers without -

        $url = self::$API . "/oak/v2/clients/" . self::client('username') . "/card/balance?trackId=bon-balance-$trackId";
        $postFields = ['card' => $cardNumber];
        return self::curlThis($url, 'CC', 'POST', $postFields);
    }

    public static function bonStatement($cardNumber = null, $fromDate = null, $toDate = null, $trackId = null)
    {
        //Date Format: YYMMDD
        //Card Format: 16 numbers without -
        
        $url = self::$API . "/oak/v2/clients/" . self::client('username') . "/card/statement?trackId=bonstate-$trackId";
        $postFields = ['card' => $cardNumber];
        if ($fromDate) $postFields['fullFromDate'] = $fromDate;
        if ($toDate) $postFields['fullToDate'] = $toDate;
        return self::post($url, 'CC', $postFields);
    }

    public static function bonChargeInquiry($ref_id, $track_id = null)
    {
        //Card Format: 16 numbers without -

        $url = self::$API . "/oak/v2/clients/" . self::client('username') . "/inquiryChargeCard?trackId=inq-charge-" . $track_id;
        $postFields = ['trackId' => $ref_id];
        return self::post($url, 'AC', $postFields);
    }

    public static function bonValid($cardNumber, $track_id = null)
    {
        //Card Format: 16 numbers without -

        $url = self::$API . "/oak/v2/clients/" . self::client('username') . "/getValidCard?trackId=bonvalid-" . $track_id;
        $postFields = ['card' => $cardNumber];
        return self::curlThis($url, 'AC', 'POST', $postFields);
    }

    public static function cardInfo($cardNumber = null, $trackId = null)
    {
        //Card Format: 16 numbers without -

        $url = self::$API . "/mpg/v2/clients/" . self::client('username') . "/cards/$cardNumber?trackId=$trackId";
        return self::curlThis($url);
    }

    public static function cardToDeposit($cardNumber, $nationalId, $trackId = null)
    {
        //Card Format: 16 numbers without -
        //NationalId Format: 10 numbers without - (as a string)

        $url = self::$API . "/oak/v2/clients/" . self::client('username') . "/cardToDeposit?trackId=$trackId&nid=$nationalId";
        $postFields = ['card' => $cardNumber];
        return self::curlThis($url, 'Bearer', 'POST', $postFields);
    }

    public static function depositToIban($bankCode, $depsoitAddress, $trackId = null)
    {
        //Iban Format: 26 Chars, IR with 24 numbers (as a string)

        $url = self::$API . "/oak/v2/clients/" . self::client('username') . "/iban?trackId=$trackId&bank=$bankCode&deposit=$depsoitAddress";
        return self::curlThis($url);
    }

    public static function facilities($nationalId, $trackId = null)
    {
        //NationalId Format: 10 numbers without - (as a string)

        $url = self::$API . "/credit/v2/clients/" . self::client('username') . "/users/$nationalId/facilityInquiry?trackId=$trackId";
        return self::curlThis($url, 'CC');
    }

    public function getTokens($nationalId, $bank = null)
    {
        //NationalId Format: 10 numbers without - (as a string)

        $url = self::$API . "/dev/v2/clients/" . self::client('username') . "/tokens?nid=$nationalId&bank=$bank";
        return self::curlThis($url);
    }

    public static function ibanInfo($iban, $trackId = null)
    {
        //Iban Format: 26 Chars, IR with 24 numbers (as a string)

        $url = self::$API . "/oak/v2/clients/" . self::client('username') . "/ibanInquiry?trackId=$trackId&iban=$iban";
        return self::curlThis($url);
    }

    public static function nidInfo($nationalId, $birthDate, $trackId = null)
    {
        //NationalId Format: 10 numbers without - (as a string)
        //BirthDate Format : yyyy/mm/dd

        $url = self::$API . "/facility/v2/clients/" . self::client('username') . "/users/$nationalId/cc/nidInquiry?trackId=$trackId&birthDate=$birthDate";
        return self::curlThis($url);
    }

    public static function nidInfo_SMS($nationalId, $birthDate, $trackId = null)
    {
        //NationalId Format: 10 numbers without - (as a string)
        //BirthDate Format : yyyy/mm/dd
        $url = self::$API . "/facility/v2/clients/" . self::client('username') . "/users/$nationalId/sms/nidInquiry?trackId=$trackId&birthDate=$birthDate";
        return self::curlThis($url);
    }

    public static function nidValidation($token, $nationalId, $birthDate, $fname = null, $lname = null, $fatherName = null, $trackId = null)
    {
        //NationalId Format: 10 numbers without - (as a string)
        //BirthDate Format : yyyy/mm/dd
        $url = self::$API . "/oak/v2/clients/" . self::client('username') . "/nidVerification?trackId=$trackId&nationalCode=$nationalId&birthDate=$birthDate&firstName=$fname&lastName=$lname&fatherName=$fatherName";
        return self::curlThis($url, $token);
    }

    public static function nidValidation2($nationalId, $birthDate, $fname = null, $lname = null, $fatherName = null, $trackId = null)
    {
        //NationalId Format: 10 numbers without - (as a string)
        //BirthDate Format : yyyy/mm/dd
        $url = self::$API . "/facility/v2/clients/" . self::client('username') . "/users/$nationalId/cc/nidVerification?trackId=$trackId&birthDate=$birthDate&firstName=$fname&lastName=$lname&fatherName=$fatherName";
        return self::curlThis($url);
    }

    public function nidValidation_SMS($token, $nationalId, $birthDate, $fname = null, $lname = null, $fatherName = null, $trackId = null)
    {
        //NationalId Format: 10 numbers without - (as a string)
        //BirthDate Format : yyyy/mm/dd
        $fname = urlencode($fname);
        $lname = urlencode($lname);
        $fatherName = urlencode($fatherName);
        $url = self::$API . "/facility/v2/clients/" . self::client('username') . "/users/$nationalId/sms/nidVerification?trackId=$trackId&birthDate=$birthDate&firstName=$fname&lastName=$lname&fatherName=$fatherName";
        return self::curlThis($url, $token);
    }

    public static function nidShahkar($nationalId, $mobile, $trackId = null)
    { 
        $url = self::$API . "/mpg/v2/clients/" . self::client('username') . "/shahkar/verify?trackId=$trackId&mobile=$mobile&nationalCode=$nationalId";
        return self::curlThis($url);
    }
}

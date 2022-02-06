<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use SoapClient;
use SoapFault;

class Sms extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected static $gateway = 'faraz';

    protected static $number_mode = '';

    protected static $method = 'REST'; //REST or else

    protected $casts = ['meta' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pattern()
    {
        return $this->belongsTo(User::class, 'pattern_id');
    }

    public static function apikey()
    {
        return config('globals.sms.' . self::$gateway . '_apikey');
    }

    public static function number()
    {
        return config('globals.sms.' . self::$gateway . '_number' . self::$number_mode);
    }

    public static function is_flooding($number, $pattern_id, $ip = null)
    {
        $mobile = '98' . substr($number, -10);
        if (in_array($mobile, ['989039295338', '989109270876', '989385586943', '989153229665'])) return false;
        if (Sms::where(['mobile' => $mobile, 'pattern_id' => $pattern_id])->where('sent_at', '>', Carbon::now()->subMinutes(2))->exists())
            return 'لطفاً دقایقی دیگر مجدداً امتحان کنید';

        if (Sms::where(['mobile' => $mobile, 'pattern_id' => $pattern_id])->where('sent_at', '>', Carbon::now()->subMinutes(60))->count() > 4) {
            // if ($mobile != '9109270876') Sms::send(config('globals.cto.mobile'), 'مشکل مشکوک پیامکی ' . $mobile);
            return 'لطفاً ساعاتی دیگر مجدداً تلاش کنید';
        }

        if (Sms::where(['mobile' => $mobile])->where('sent_at', '>', Carbon::now()->subMinutes(60))->count() > 10) {
            if ($mobile != '9109270876') Sms::send(config('globals.cto.mobile'), '2مشکل مشکوک پیامکی ' . $mobile);
            return 'لطفاً ساعاتی دیگر مجدداً تلاش کنید';
        }

        return false;
    }

    public static function send($numbers, $text, $params = [], $trusted = 0)
    {
        $message = $text;

        if (config('app.env') == 'local' && $text != 1) return mt_rand(10000, 9999999);

        if (is_numeric($text)) {
            $message = Pattern::select('message')->whereId($text)->first()->message;
            if ($trusted == 0 && !is_array($numbers) && self::is_flooding($numbers, $text)) return false;
        }

        foreach ($params as $key => $value) $message = str_replace('%' . $key . '%', $value, $message);

        if (!is_array($numbers)) $numbers = array("$numbers");
        foreach ($numbers as $key => $value) $numbers[$key] = '98' . substr($value, -10);

        $meta = null;
        if (self::$method == 'REST') {
            $client = new \IPPanel\Client(self::apikey());
            $bulk_id = $client->send(self::number(), $numbers, "قسطا\n" . $message);
            if (is_array($bulk_id) || is_object($bulk_id)) {
                $meta = $bulk_id;
                $bulk_id = null;
            }
        } else {
            ini_set("soap.wsdl_cache_enabled", "0");
            try {
                $client = new SoapClient("https://ippanel.com/class/sms/wsdlservice/server.php?wsdl");
                $user = config('globals.sms.faraz_username');
                $pass = config('globals.sms.faraz_password');
                $fromNum = self::number();
                $toNum = $numbers;
                $op = "send";
                $time = '';
                $result = $client->SendSMS($fromNum, $toNum, "قسطا\n" . $message, $user, $pass, $time, $op);
                $bulk_id = $result;
            } catch (SoapFault $ex) {
                $meta = $ex;
                $bulk_id = null;
            }
        }

        Sms::create([
            'mobile' => $numbers[0],
            'bulk_id' => $bulk_id,
            'pattern_id' => is_numeric($text) ? $text : null,
            'message' => $message,
            'sent_at' => Carbon::now(),
            'number' => self::number(),
            'meta' => $meta,
        ]);

        return $bulk_id;
    }

    public static function getMessage($bulk_id)
    {
        $client = new \IPPanel\Client(self::apikey());
        return $client->getMessage($bulk_id); //status,cost,paybackCost, ... recipientsCount,createdAt,sentAt,number,message
    }
}

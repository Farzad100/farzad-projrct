<?php

namespace App\Models;

use Carbon\Carbon;
use SoapClient;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ICBS extends Model
{
    use SoftDeletes;
    
    protected $table = 'icbs';

    protected $guarded = ['id'];

    protected $casts = ['result' => 'array'];

    public const expiration_days = 25;
    public const price_bank = 8000;
    public const price_reg = 2000;
    public const price_sim = 1000;

    public static $method_MobileAndNationalCodeMatching = 'MobileAndNationalCodeMatching';
    public static $method_BankCs = 'BankCs';
    public static $method_IntegratedCs = 'IntegratedCs';
    public static $method_PostInfo = 'PostInfo';
    public static $method_RegInfo = 'RegInfo';
    public static $method_GetServiceStatus = 'GetServiceStatus';

    public static function accountModel()
    {
        return [
            'UserName' => config('globals.icbs.username'),
            'PassWord' => config('globals.icbs.password')
        ];
    }

    public static function MobileAndNationalCodeMatching($nid, $mobile, $mode = 0, $meta = null)
    {
        $method = self::$method_MobileAndNationalCodeMatching;
        if ($mode == 'restrict' || $mode == 'restrict-shop') {
            $ip = Useful::ip();
            $records = ICBS::select('id', 'nid', 'ip', 'extra', 'created_at', 'status')
                ->where(['method' => $method, 'ip' => $ip])
                ->orWhere(function ($query) use ($method, $mobile) {
                    $query->where(['method' => $method, 'extra' => $mobile]);
                })
                ->orWhere(function ($query) use ($method, $nid) {
                    $query->where(['method' => $method, 'nid' => $nid]);
                })
                ->where('created_at', '>', Carbon::now()->subDays(self::expiration_days))
                ->orderBy('id', 'desc')->get();

            /* $ip_month = $records->where('ip', $ip)->count();
            $ip_day = $records->where('ip', $ip)->where('created_at', '>', Carbon::now()->subDay())->count();
            $ip_hour = $records->where('ip', $ip)->where('created_at', '>', Carbon::now()->subHour())->count();

            if ($ip_month > ($mode == 'restrict' ? 300 : 5000))
                return ['name' => 'failed', 'message' => 'دسترسی شما به پنل محدود شده است. لطفاً بعداً تلاش کنید یا با پشتیبانی تماس بگیرید.'];

            if ($ip_day > ($mode == 'restrict' ? 20 : 200))
                return ['name' => 'failed', 'message' => 'دسترسی شما به پنل محدود شده است. لطفاً بعداً تلاش کنید یا با پشتیبانی تماس بگیرید.'];

            if ($ip_hour > ($mode == 'restrict' ? 10 : 30))
                return ['name' => 'failed', 'message' => 'دسترسی شما به پنل محدود شده است. لطفاً بعداً تلاش کنید یا با پشتیبانی تماس بگیرید.']; */

            //MOBILE
            $mobile_month = $records->where('extra', $mobile)->count();
            $mobile_day = $records->where('extra', $mobile)->where('created_at', '>', Carbon::now()->subDay())->count();
            $mobile_hour = $records->where('extra', $mobile)->where('created_at', '>', Carbon::now()->subHour())->count();

            if ($mobile_month > ($mode == 'restrict' ? 30 : 10))
                return ['name' => 'failed', 'message' => 'دسترسی شما به پنل محدود شده است. لطفاً بعداً تلاش کنید یا با پشتیبانی تماس بگیرید.'];

            if ($mobile_day > ($mode == 'restrict' ? 15 : 5))
                return ['name' => 'failed', 'message' => 'دسترسی شما به پنل محدود شده است. لطفاً بعداً تلاش کنید یا با پشتیبانی تماس بگیرید.'];

            if ($mobile_hour > ($mode == 'restrict' ? 13 : 3))
                return ['name' => 'failed', 'message' => 'دسترسی شما به پنل محدود شده است. لطفاً بعداً تلاش کنید یا با پشتیبانی تماس بگیرید.'];

            $mode = 0;
        }

        return self::req($method, [
            'nationalCode' => $nid,
            'mobileNumber' => $mobile,
        ], $mode, ['user_id' => $meta]);
    }

    public static function Shahkar($nid, $mobile, $mode = 0, $meta = null)
    {
        if (strlen($nid) < 10 || strlen($mobile) < 10) return false;
        $x = self::MobileAndNationalCodeMatching($nid, Useful::enum($mobile, 'mobile'), $mode, $meta);
        if (isset($x->id)) return $x;
        return self::MobileAndNationalCodeMatching($nid, Useful::enum($mobile, 'mobile'), $mode, $meta);
    }

    public static function BankCs($nid, $mode = 0)
    {
        return self::req(self::$method_BankCs, [
            'nationalCode' => $nid,
        ], $mode);
    }

    public static function IntegratedCs($nid, $mode = 0)
    {
        return self::req(self::$method_IntegratedCs, [
            'nationalCode' => $nid,
        ], $mode);
    }

    public static function PostInfo($postalCode, $mode = 0)
    {
        return self::req(self::$method_PostInfo, [
            'postCode' => $postalCode,
        ], $mode);
    }

    public static function RegInfo($nid, $birth = '', $mode = 0)
    {
        $b = explode('-', $birth);
        return self::req(self::$method_RegInfo, [
            'nationalCode' => $nid,
            'birthDate' => [
                'Day' => $b[2],
                'Month' => $b[1],
                'Year' => $b[0],
            ],
        ], $mode);
    }

    public static function req($method, $inputs, $mode = 0, $meta = null)
    {
        if (in_array($mode, [0, 'cache'])) {
            $where['method'] = $method;
            $where['status'] = 1;
            if (isset($inputs['nationalCode']))
                $where['nid'] = $inputs['nationalCode'];
            if (isset($inputs['birthDate']))
                $where['extra'] = $inputs['birthDate']['Year'] . '-' . $inputs['birthDate']['Month'] . '-' . $inputs['birthDate']['Day'];
            else if (isset($inputs['mobileNumber']))
                $where['extra'] = $inputs['mobileNumber'];
            else if (isset($inputs['postCode']))
                $where['extra'] = $inputs['postCode'];
            if (count($where) == 1) return false;

            $record = ICBS::select('id', 'result', 'created_at', 'status')->where($where)->orderBy('id', 'desc')->first();
            if ($record) {
                if (
                    in_array($method, [self::$method_IntegratedCs, self::$method_BankCs]) &&
                    Carbon::now()->subDays(self::expiration_days)->greaterThan($record->created_at)
                ) { 
                    if ($mode === 'cache') return $record;
                } else { 
                    return $record;
                } 
            }
        }

        $client = new SoapClient("https://cr24.icbs.ir/Cr24PrService.svc?wsdl", [
            'trace' => true,
            'cache_wsdl' => WSDL_CACHE_NONE
        ]);

        $accountModel = [
            'accountModel' => [
                'UserName' => config('globals.icbs.username'),
                'PassWord' => config('globals.icbs.password')
            ]
        ];

        $params = array_merge($accountModel, $inputs);

        $result = $client->$method($params);
        $result = json_decode(json_encode($result), true);

        if (!in_array($method, [ICBS::$method_IntegratedCs]) && $result[$method . 'Result']['ServiceStatus'] != 'Success') {
            Useful::report("⭕️" . " " . "خطای استعلام", [
                'method' => $method,
                'nid' => $where['nid'] ?? 'x',
                'extra' => $where['extra'],
            ]);
            $success = 0;
        }

        if (isset($inputs['birthDate']))
            $birth = $inputs['birthDate']['Year'] . '-' . $inputs['birthDate']['Month'] . '-' . $inputs['birthDate']['Day'];

        $res = ICBS::create([
            'user_id' => $meta['user_id'] ?? null,
            'nid' => $inputs['nationalCode'] ?? null,
            'extra' => $birth ?? ($inputs['mobileNumber'] ?? ($inputs['postCode'] ?? null)),
            'method' => $method,
            'status' => $success ?? 1,
            'result' => $result,
            'ip' => Useful::ip(),
            'admin_id' => Auth::check() ? (Auth::user()->is_admin > 10 ? Auth::user()->id : null) : null
        ]);

        return $res;
    }

    public static function last_record($where, $days = null)
    {
        if ($days)
            $res = ICBS::select('id', 'result', 'created_at', 'status')
                ->where($where)
                ->where('status', 1)
                ->where('created_at', '>', Carbon::now()->subDays($days))
                ->orderBy('id', 'desc')->first();

        $res = ICBS::select('id', 'result', 'created_at', 'status')
            ->where($where)->where('status', 1)->orderBy('id', 'desc')->first();

        if ($res && Carbon::now()->subDays(self::expiration_days)->greaterThan($res->created_at)) $res->is_expired = true;
        else if ($res) $res->is_expired = false;
        return $res;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Finnotech;
use App\Models\Edate;
use App\Models\Useful;
use App\Models\Backcheque;
use App\Models\Facility;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinnotechController extends Controller
{
    public function index(Request $request)
    {
        $nid = $request->nid;
        $type = $request->type;
        $search = $request->search;
        if ($search == 1) {
            if (strlen($nid) == 10 && !Inquiry::where('nid', $nid)->searchType($type)->exists()) {
                if (strlen($type) > 3) {
                    Inquiry::create(['nid' => $nid, 'type' => $type, 'manual' => true]);
                }
            }
            $query = Inquiry::searchNid($nid)->searchType($type);
        } else {
            $query = Inquiry::where('id', '>', 0);
        }
        return $query->orderBy('updated_at', 'DESC')->paginate(40);
    }

    public function nidValidationFinalStep($token, $mobile, $nid, $birth, $fname, $lname)
    {
        $FT = new Finnotech();
        $user = $FT->nidValidation_SMS($token, $nid, $birth, $fname, $lname);
        $finalResult = json_decode($user, true);
        if (isset($finalResult['status']) && $finalResult['status'] == 'DONE') {
            $fnameS = (int)$finalResult['result']['firstNameSimilarity'];
            $lnameS = (int)$finalResult['result']['lastNameSimilarity'];
            if ($fnameS + $lnameS == 200) {
                User::where('username', $mobile)->update([
                    'nid' => $nid,
                    'birth' => $birth,
                    'fname' => $fname,
                    'lname' => $lname,
                    'verified' => 1,
                    'verified_at' => Carbon::now()
                ]);
                return Useful::ok('verified');
            }
            return Useful::nok(['code' => 'conflict', 'fnameS' => $fnameS, 'lnameS' => $lnameS]);
        }
        return Useful::nok('failed');
    }

    public function nidValidation(Request $request)
    {

        $FT = new Finnotech();
        $wtd = $request->wtd;
        $nid = $request->nid;
        $mobile = User::where('nid', $nid)->select('username')->first()['username'];
        if (User::where('username', $mobile)->first()['verified'] == 1) {
            return Useful::nok(['description' => 'این کاربر قبلاً احراز هویت شده است.']);
        }
        $trackId = $request->trackId;
        $otp = $request->otp;
        $birth = $request->birth;
        $fname = $request->fname;
        $lname = $request->lname;
        $count = Finnotech::where([
            'nid' => $nid,
            'mobile' => $mobile,
            'scopes' => Finnotech::$SCOPE_SMS_NID,
        ])->whereNotNull('token')->count();
        $Hour = (int)Edate::edate('H', time());
        if ($Hour < 8 || $Hour > 19) return Useful::nok(['description' => 'در ساعات 8 الی 19 تلاش کنید']);
        if ($count > 0) {
            $token = Finnotech::where([
                'nid' => $nid,
                'mobile' => $mobile,
                'scopes' => Finnotech::$SCOPE_SMS_NID,
            ])->whereNotNull('token')->select('token')->first()['token'];
            return $this->nidValidationFinalStep($token, $mobile, $nid, $birth, $fname, $lname);
        }
        switch ($wtd) {
            case 'send':
                $this->validate($request, [
                    'nid' => 'required|string|min:10|max:10',
                    'mobile' => 'required|string|min:11|max:11'
                ]);
                $sms = $FT->oAuth_SMS($mobile, Finnotech::$SCOPE_SMS_NID);
                $result = json_decode($sms, true);
                if (isset($result['status']) && $result['status'] == 'DONE') {
                    $trackId = $result['result']['trackId'];
                    //Save in DB
                    Finnotech::updateOrCreate([
                        'nid' => $nid,
                        'mobile' => $mobile,
                        'scopes' => Finnotech::$SCOPE_SMS_NID
                    ], ['mobile' => $mobile]);
                    return Useful::ok(['trackId' => $trackId]);
                }
                return Useful::nok('smsFailed');
                break;
            case 'vcode':
                $req = $FT->oAuthRequest($nid, $mobile, $trackId, $otp);
                $verify = json_decode($req, true);
                if (isset($verify['status']) && $verify['status'] == 'DONE') {
                    $code = $verify['result']['code'];
                    $result = $FT->oAuthToken($code);
                    $oAuth = json_decode($result, true);
                    if (isset($oAuth['status']) && $oAuth['status'] == 'DONE') {
                        $token = $oAuth['result']['value'];
                        //Save in DB
                        Finnotech::where([
                            'nid' => $nid,
                            'mobile' => $mobile,
                            'scopes' => Finnotech::$SCOPE_SMS_NID
                        ])->update([
                            'token' => $token,
                            'token_refresh' => $oAuth['result']['refreshToken'],
                            'extra' => "$result"
                        ]);
                        //Validate User With Received Information
                        return $this->nidValidationFinalStep($token, $mobile, $nid, $birth, $fname, $lname);
                    }
                    return Useful::nok('tokenNotReceived');
                }
                return Useful::nok('invalidCode');
                break;
        }
    }

    public function nidValidationManual(Request $request)
    {
        $FT = new Finnotech();
        $wtd = $request->wtd;
        $nid = $request->nid;
        $mobile = $request->mobile;
        $trackId = $request->trackId;
        $otp = $request->otp;
        $birth = $request->birth;
        $fname = $request->fname;
        $lname = $request->lname;
        $count = Finnotech::where([
            'nid' => $nid,
            'mobile' => $mobile,
            'scopes' => Finnotech::$SCOPE_SMS_NID,
        ])->whereNotNull('token')->count();
        $Hour = (int)Edate::edate('H', time());
        if ($Hour < 8 || $Hour > 19) return Useful::nok(['description' => 'در ساعات 8 الی 19 تلاش کنید']);
        if ($count > 0) {
            $token = Finnotech::where([
                'nid' => $nid,
                'mobile' => $mobile,
                'scopes' => Finnotech::$SCOPE_SMS_NID,
            ])->whereNotNull('token')->select('token')->first()['token'];
            return $this->nidValidationFinalStep($token, $mobile, $nid, $birth, $fname, $lname);
        }
        switch ($wtd) {
            case 'send':
                $sec = $request->sec;
                if ($sec != 'pashmak') return Useful::nok('wrongSecurityCode');
                $this->validate($request, [
                    'nid' => 'required|string|min:10|max:10',
                    'mobile' => 'required|string|min:11|max:11'
                ]);
                $sms = $FT->oAuth_SMS($mobile, Finnotech::$SCOPE_SMS_NID);
                $result = json_decode($sms, true);
                if (isset($result['status']) && $result['status'] == 'DONE') {
                    $trackId = $result['result']['trackId'];
                    //Save in DB
                    Finnotech::updateOrCreate([
                        'nid' => $nid,
                        'mobile' => $mobile,
                        'scopes' => Finnotech::$SCOPE_SMS_NID
                    ], ['mobile' => $mobile]);
                    return Useful::ok(['trackId' => $trackId]);
                }
                return Useful::nok('smsFailed');
                break;
            case 'vcode':
                $req = $FT->oAuthRequest($nid, $mobile, $trackId, $otp);
                $verify = json_decode($req, true);
                if (isset($verify['status']) && $verify['status'] == 'DONE') {
                    $code = $verify['result']['code'];
                    $result = $FT->oAuthToken($code);
                    $oAuth = json_decode($result, true);
                    if (isset($oAuth['status']) && $oAuth['status'] == 'DONE') {
                        $token = $oAuth['result']['value'];
                        //Save in DB
                        Finnotech::where([
                            'nid' => $nid,
                            'mobile' => $mobile,
                            'scopes' => Finnotech::$SCOPE_SMS_NID
                        ])->update([
                            'token' => $token,
                            'token_refresh' => $oAuth['result']['refreshToken'],
                            'extra' => "$result"
                        ]);
                        //Validate User With Received Information
                        return $this->nidValidationFinalStep($token, $mobile, $nid, $birth, $fname, $lname);
                    }
                    return Useful::nok('tokenNotReceived');
                }
                return Useful::nok($req);
                break;
        }
    }

    public static function pastTime($date, $mode = 'day')
    {
        $ts = Carbon::parse($date)->timestamp;
        switch ($mode) {
            case 'day':
                $pastDays = floor((time() - $ts) / 86400);
                $pastHours = floor(((time() - $ts) % 86400) / 3600);
                $past = ['days' => $pastDays, 'hours' => $pastHours];
                break;
            default:
                $past = 0;
                break;
        }
        return $past;
    }

    //ADMIN
    public function back_cheques($nid, $force = 0)
    {
        if (Backcheque::where('nid', $nid)->whereDate('created_at', '>', Carbon::now()->subDays(7))->doesntExist()) $force = 1;
        
        if ($force == 1) {
            $req = json_decode(Finnotech::backCheques($nid), true);
            if (isset($req['status']) && $req['status'] == 'DONE') {
                if (empty($req['result']['nid'])) $error = 'نتیجه جدید قابل دریافت نبود.';
                else Backcheque::create(['nid' => $nid, 'result' => $req['result']]);
            }
        }

        $inqs = Backcheque::where('nid', $nid)->orderBy('id', 'desc')->get();
        $collection = [];
        $n = 0;
        foreach ($inqs as $inq) {
            $collection[$n]['date'] = Edate::edateFromCarbon('fluent', $inq->created_at);
            if (isset($inq['result']['chequeList'])) {
                $sum_amount = 0;
                foreach ($inq['result']['chequeList'] as $ch) {
                    $collection[$n]['cheques'][] = [
                        'account_number' => (int)$ch['accountNumber'],
                        'amount' => $ch['amount'],
                        'back_date' => substr($ch['backDate'], 0, 4) . '-' . substr($ch['backDate'], 4, 2) . '-' . substr($ch['backDate'], -2),
                        'bank_id' => $ch['bankCode'],
                        'branch' => $ch['branchCode'],
                        'bank_name' => $ch['branchDescription'],
                        'cheque_number' => (int)$ch['number'],
                    ];
                    $sum_amount += $ch['amount'];
                }
                $collection[$n]['sum'] = [
                    'count' => count($inq['result']['chequeList']),
                    'amount' => $sum_amount
                ];
            } else {
                $collection[$n]['cheques'] = [];
            }
            ++$n;
        }

        return Useful::ok($collection);
    }

    //ADMIN
    public function facilities($nid, $force = 0)
    {
        if (Facility::where('nid', $nid)->whereDate('created_at', '>', Carbon::now()->subDays(7))->doesntExist()) $force = 1;

        if ($force == 1) {
            $req = json_decode(Finnotech::facilities($nid), true); 
            if (isset($req['status']) && $req['status'] == 'DONE') {
                if (empty($req['result']['user'])) $error = 'نتیجه جدید قابل دریافت نبود.';
                else Facility::create(['nid' => $nid, 'result' => $req['result']]);
            }
        }

        $inqs = Facility::where('nid', $nid)->orderBy('id', 'desc')->get();
        $collection = [];
        $n = 0;
        foreach ($inqs as $inq) {
            $collection[$n]['date'] = Edate::edateFromCarbon('fluent', $inq->created_at);
            if (isset($inq['result']['facilityList'])) {
                foreach ($inq['result']['facilityList'] as $ch) {
                    $collection[$n]['facilities'][] = [
                        'amount' => $ch['amountOrginal'],
                        'benefit' => $ch['benefitAmount'],
                        'sum' => $ch['amountOrginal'] + $ch['benefitAmount'],
                        'debt' => $ch['debtorTotalAmount'],
                        'expired' => $ch['pastExpiredAmount'],
                        'deferred' => $ch['deferredAmount'],
                        'suspicious' => $ch['suspiciousAmount'],
                        'bank_name' => $ch['branchDescription'],
                        'branch' => $ch['branchCode'],
                        'bank_id' => $ch['bankCode'],
                    ];
                }
                $collection[$n]['total_amount'] = $inq['result']['facilityTotalAmount'];
                $collection[$n]['total_debt'] = $inq['result']['facilityDebtTotalAmount'];
                $collection[$n]['total_expired'] = $inq['result']['facilityPastExpiredTotalAmount'];
                $collection[$n]['total_deferred'] = $inq['result']['facilityDeferredTotalAmount'];
                $collection[$n]['total_suspicious'] = $inq['result']['facilitySuspiciousTotalAmount'];
            } else {
                $collection[$n]['facilities'] = [];
            }
            ++$n;
        }

        return Useful::ok($collection);
    }

    //ADMIN
    public function inquiry(Request $request)
    {
        $id = $request->id; 
        $user = User::select('nid')->whereId($id)->first();
        $nid = $user->nid;
        if(!$nid) return Useful::nok(['name'=>'nid_invalid','message'=>'کدملی یافت نشد.']);
        $type = $request->type;
        $force = $request->force;
        switch ($type) {
            case 'back-cheques':
                return $this->back_cheques($nid, $force); 
            case 'facilities':
                return $this->facilities($nid, $force);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Edate;
use App\Models\Useful;
use App\Models\Discount;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    private static $GHESTA_API_TOKEN = 'Bearer NJVD849rfdmfkm*#RngkMGLdkslfpds09irfM:GBIGOREgkMFmBFOG_DIRGMGNKlkdsg@org(*)Crjgkfl<(@@fj1859xgrUOLMWwcWn845139bmtoPGKRMGOU$KMGFGDLaxxXyF#mdfJfe8uFNDJ&432vvH';

    public static function bilakh()
    {
        return json_encode(['status' => false, 'data' => 'No Pain, No Gain!']);
    }

    public function checkToken($token)
    {
        return $token == self::$GHESTA_API_TOKEN;
    }

    public static function getTimespan($weekId = null, $jy = null, $jm = 0, $jd = 0)
    {
        $base_timestamp = Edate::jmktime(1, 0, 30, 6, 15, 1398);
        if (!empty($weekId)) {
            $start = $base_timestamp + (($weekId - 1) * 7 * 86400);
            $end = $start + (6 * 86400) + (86400 - (2 * 1815.5));
        } else {
            if (is_null($jy)) $jy = Edate::edate('Y');
            if ($jm == 0) {
                if ($jy <= 1398) $start = $base_timestamp;
                else $start = Edate::jmktime(0, 0, 0, 1, 1, $jy);
                $end = Edate::jmktime(22, 0, 0, 12, 28, $jy);
            } else {
                $start = Edate::jmktime(0, 0, 0, $jm, 1, $jy);
                $lastDay = Edate::edate('t', $start);
                $end = Edate::jmktime(22, 0, 0, $jm, $lastDay, $jy);
            }
        }
        $completed = 1;
        if ($end > time()) {
            $end = time();
            $completed = 0;
        }
        return [
            'start' => Carbon::createFromTimestamp($start),
            'end' => Carbon::createFromTimestamp($end),
            'startj' => Edate::edate('Y-m-d', $start),
            'endj' => Edate::edate('Y-m-d', $end),
            'passed' => $completed
        ];
    }

    public static function saleReport()
    {
        $form_week = Report::select('week_id')->orderBy('id', 'DESC')->first()->week_id - 8;
        for ($weekId = $form_week; $weekId <= $form_week + 10; $weekId++) {
            $timespan = self::getTimespan($weekId);
            if ($timespan['passed'] == 1) {
                $amount = Order::chargedIn($timespan['start'], $timespan['end'])->sum('amount');
                Report::updateOrCreate(
                    ['week_id' => $weekId],
                    [
                        'sale_week' => $amount,
                        'start' => $timespan['start'],
                        'end' => $timespan['end'],
                        'startj' => $timespan['startj'],
                        'endj' => $timespan['endj'],
                        'year' => substr($timespan['startj'], 0, 4),
                        'passed' => 1,
                    ]
                );
                $total = Report::where('week_id', '<=', $weekId)->sum('sale_week');
                Report::where('week_id', $weekId)->update(['sale_total' => $total]);
            } else break;
        }
        return 'yes';
    }

    public function ordersByYear(Request $request)
    {
        if (!$this->checkToken($request->header('Authorization'))) return self::bilakh();
        $jy = (int)$request->y;
        if ($jy < 1398) return Useful::nok('Access denied');
        $timespan = self::getTimespan($jy);
        $start = $timespan[0];
        $end = $timespan[1];
        $orders = Order::whereNotNull('finished_at')->whereDate('finished_at', '>=', $start)->whereDate('finished_at', '<', $end)->select('amount', 'finished_at')->get();
        $ordersTotalAmount = Order::whereNotNull('finished_at')->whereDate('finished_at', '>', $start)->whereDate('finished_at', '<', $end)->sum('amount');
        $count = $orders->count();
        return Useful::ok(['count' => $count, 'total' => $ordersTotalAmount, 'details' => $orders]);
    }

    public function ordersByWeek(Request $request)
    {
        //        if (!$this->checkToken($request->header('Authorization'))) return self::bilakh();
        $weekId = (int)$request->week_id;
        if ($weekId == 'all') {
            $reps = Report::where('passed', 1)->get();
            foreach ($reps as $rep) {
                $details[$rep->week_id] = [
                    'weekID' => $rep->week_id,
                    'sale' => $rep->sale_week * 10,
                    'cumulative' => $rep->sale_total * 10,
                    'unit' => 'IRR',
                    'startWeek' => $rep->startj,
                    'endWeek' => $rep->endj,
                ];
            }
        } else if (is_numeric($weekId)) {
            $rep = Report::where('week_id', $weekId)->first();
            if (!$rep) return Useful::nok('The week did not pass yet!');
            $orders = Order::select('user_id', 'created_at', 'oid', 'amount')->with(array('user' => function ($query) {
                $query->select('fname', 'lname', 'username', 'id');
            }))->chargedIn($rep->start, $rep->end)->get();
            $details = [
                'weekID' => $rep->week_id,
                'sale' => $rep->sale_week * 10,
                /*'cumulative' => $rep->sale_total * 10,
                'unit' => 'IRR',
                'startWeek' => $rep->startj,
                'endWeek' => $rep->endj,*/
                'orders' => $orders
            ];
        } else {
            return Useful::nok('WeekID not found!');
        }
        return Useful::ok($details);
    }

    public function ordersWeeklyDigest(Request $request)
    {
        if (!$this->checkToken($request->header('Authorization'))) return self::bilakh();
        $weekId = (int)$request->week_id;
        $request->sort ? $sort = $request->sort : $sort = 'ASC';
        if ($weekId == 'all') {
            $reps = Report::where('passed', 1)->orderBy('week_id', $sort)->get();
            foreach ($reps as $rep) {
                $details[] = [
                    'weekID' => $rep->week_id,
                    'startWeek' => $rep->startj,
                    'endWeek' => $rep->endj,
                    'sale' => $rep->sale_week * 10,
                    'cumulative' => $rep->sale_total * 10,
                    'unit' => 'IRR',
                ];
            }
        } else if ($weekId == 'last') {
            $rep = Report::where('passed', 1)->orderBy('week_id', 'desc')->first();
            $details = [
                'weekID' => $rep->week_id,
                'startWeek' => $rep->startj,
                'endWeek' => $rep->endj,
                'sale' => $rep->sale_week * 10,
                'cumulative' => $rep->sale_total * 10,
                'unit' => 'IRR',
            ];
        } else if (is_numeric($weekId)) {
            $rep = Report::where('week_id', $weekId)->first();
            if (!$rep) return Useful::nok('The week did not pass yet!');
            $details = [
                'weekID' => $rep->week_id,
                'startWeek' => $rep->startj,
                'endWeek' => $rep->endj,
                'sale' => $rep->sale_week * 10,
                'cumulative' => $rep->sale_total * 10,
                'unit' => 'IRR',
            ];
        } else {
            return Useful::nok('WeekID not found!');
        }
        return Useful::ok($details);
    }

    public function ordersWeeklyDigestx(Request $request)
    {
        $weekId = (int)$request->week_id;
        $request->sort ? $sort = $request->sort : $sort = 'ASC';
        if ($weekId == 'all') {
            $reps = Report::where('passed', 1)->orderBy('week_id', $sort)->get();
            foreach ($reps as $rep) {
                $details[] = [
                    'weekID' => $rep->week_id,
                    'sale' => $rep->sale_week * 10,
                    'cumulative' => $rep->sale_total * 10,
                    'unit' => 'IRR',
                    'startWeek' => $rep->startj,
                    'endWeek' => $rep->endj,
                ];
            }
        } else if ($weekId == 'last') {
            $rep = Report::where('passed', 1)->orderBy('week_id', 'desc')->first();
            $details = [
                'weekID' => $rep->week_id,
                'sale' => $rep->sale_week * 10,
                'cumulative' => $rep->sale_total * 10,
                'unit' => 'IRR',
                'startWeek' => $rep->startj,
                'endWeek' => $rep->endj,
            ];
        } else if (is_numeric($weekId)) {
            $rep = Report::where('week_id', $weekId)->first();
            if (!$rep) return Useful::nok('The week did not pass yet!');
            $details = [
                'weekID' => $rep->week_id,
                'sale' => $rep->sale_week * 10,
                'cumulative' => $rep->sale_total * 10,
                'unit' => 'IRR',
                'startWeek' => $rep->startj,
                'endWeek' => $rep->endj,
            ];
        } else {
            return Useful::nok('WeekID not found!');
        }
        return Useful::ok($details);
    }

    public function chatr(Request $request)
    {
        $t = 'Bearer mtgfbEgkMFmBFOG_DIRGMGxvcjgkfjyty46gfvb3559xgrUOLhtgfhghfghgf$KMGFGDLaxxXyF#mdfJfe8ucvncuHF32546vfkdj';
        //        if ($request->header('Authorization') != $t) return self::bilakh();
        if ($request->passwd != 'mcnvifg8nt4-$x') return self::bilakh();
        $discounts = Discount::where('code', 'like', '%chatr%')->where('active', 1)->get();
        foreach ($discounts as $discount) {
            $discount->used = Payment::where('discount_id', $discount['id'])->where('status', 100)->count();
            if (Carbon::parse($discount->expired_at)->timestamp < time()) $status = 'expired';
            else if ($discount->limit <= $discount->used) $status = 'limitExceeded';
            else $status = 'active';
            $details[] = [
                'name' => $discount->code,
                'status' => $status,
                'percent' => $discount->percent,
                'maxAmount' => $discount->amount,
                'unit' => 'Toman',
                'quantity' => $discount->limit,
                'used' => $discount->used,
                'remaining' => $discount->limit - $discount->used,
                'expiredAt' => Edate::edateFromCarbon('Y-m-d', $discount->expired_at),
            ];
        }
        return Useful::ok($details);
    }

    public function emtiaz(Request $request)
    {
        $t = 'Bearer XmtgfbEgkMFmBFOG_DIRtr45GMGxvcjgvcbtrkfjyty46gfvb3559xgrhftghU@OLhtgfhghfghgf$KMGFGDLaxxXyF#mdfJfe8ucvncuHF32546vcvpllkdsmgkfdhghfUI5uhdfjisdr8934ujdfhjfdhfJDGF834uu98tgdjkgu4389uevjdkfvuUNC*#ryvuerhnby3498ntvdHHDHF';
        if ($request->header('Authorization') != $t) return self::bilakh();

        $users = User::select('id', 'username', 'click_id')->where('utm_source', 'emtiaz')->whereHas('payments', function ($q) {
            $q->select('user_id')->where(['type' => 'prepayment', 'status' => 100]);
        })->with(['payments' => function ($q) {
            $q->select('user_id', 'paid_at')->where(['type' => 'prepayment', 'status' => 100]);
        }])->get();
        $registers = User::select('id', 'username', 'created_at', 'click_id')->where('utm_source', 'emtiaz')->get();
        $array = [];
        foreach ($users as $user) {
            $array['payments'][] = [
                'mobile' => $user->username,
                'click_id' => $user->click_id,
                'date' => $user->payments[0]->paid_at,
                'jDate' => Edate::edateFromCarbon('Y-m-d H:i:s', $user->payments[0]->paid_at),
            ];
        }
        foreach ($registers as $register) {
            $array['registeredUsers'][] = [
                'mobile' => $register->username,
                'click_id' => $register->click_id,
                'date' => $register->created_at,
                'jDate' => Edate::edateFromCarbon('Y-m-d H:i:s', $register->created_at),
            ];
        }
        $result = (object)$array;

        return Useful::ok($result);
    }

    public static function zyx()
    {
        $users = User::select('id')->where('id', '>', 2500)->get();
        foreach ($users as $user) {
            User::generateUserInviteCode($user->id);
        }
    }
}

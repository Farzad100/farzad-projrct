<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ExportController;
use App\Models\Address;
use App\Models\Client;
use App\Models\Edate;
use App\Models\ICBS;
use App\Models\Order;
use App\Models\Useful;
use App\Models\User;
use App\Models\Usermeta;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MalexController extends Controller
{
    public function cs($request = null)
    {
        // $format = $request->format;
        $type = 'all';
        $from_date = '1399-12-13';

        $from_ts = null;
        if ($from_date) {
            $x = explode('-', $from_date);
            $from_ts = Edate::jmktime(0, 0, 0, $x[1], $x[2], $x[0]);
        }

        if ($type == 'all') {
            $users_collection = User::select('id', 'fname', 'lname', 'username', 'nid', 'birth', 'created_at')
                ->whereHas('orders', function ($q) use ($from_ts) {
                    $q->successful()->where(function ($q) use ($from_ts) {
                        if ($from_ts)
                            $q->where('charged_at', '>=', Carbon::createFromTimestamp($from_ts));
                        else
                            $q->whereNotNull('charged_at');
                    });
                })->whereHas('usermeta', function ($q) {
                    $q->whereNotNull('gender');
                })->orderBy('id', 'asc')->get();

            $orders = self::orders($from_ts);
            $users = self::users($from_ts, $users_collection);
            $fcl = self::fcl($from_ts, $users_collection);
            $bch = self::bch($from_ts, $users_collection);

            $url = ExportController::excel([
                'orders' => $orders,
                'users' => $users,
                'fcl' => $fcl,
                'bch' => $bch,
            ], 'cs_malex', 'sheets');
        } else {
            $data = self::$type($from_ts);
            $url = ExportController::excel($data, $type . '_malex');
        }

        return Useful::yes(['url' => Client::BASE_URL . $url]);
    }

    //users (have successful orders)
    public static function users($from_ts = null, $users = null)
    {
        if (!$users) {
            $users = User::select('id', 'fname', 'lname', 'username', 'nid')
                ->whereHas('orders', function ($q) use ($from_ts) {
                    $q->successful()->where(function ($q) use ($from_ts) {
                        if ($from_ts)
                            $q->where('charged_at', '>=', Carbon::createFromTimestamp($from_ts));
                        else
                            $q->whereNotNull('charged_at');
                    });
                })->whereHas('usermeta', function ($q) {
                    $q->whereNotNull('gender');
                })->orderBy('id', 'asc')->get();
        }

        $data[0] = [
            'User_ID',
            'Name',
            'Family',
            'Customer Registration Day',
            'Customer Registration Month',
            'Customer Registration Year',
            'Mobile Number',
            'NID',
            'Birth Day',
            'Birth Month',
            'Birth Year',
            'Home_Province',
            'Home_City',
            'Home_Postal Code',
            'Home_Tel',
            'Work_Province',
            'Work_City',
            'Work_Postal_Code',
            'Work_Tel',
            'Gender',
            'Marital_Status',
            'Children_Number',
            'Homeownership',
            'Dependants',
            'Time from Last Home Transfer',
            'Work_Exprience_Years',
            'Main Occupation',
            'Main Occupation Category',
            'Main Occupation Contract Type',
            'Income or Salary',
            'Income Spouse',
            'Other Incomes',
            'Mnthy_Expncs_E',
            'Mnthy_Expncs_NE',
            'Car',
            'Car_Brand',
            'Cheque_Years',
            'Stock_Edalat',
            'Ins_Life',
            'Ins_Supp',
            'Subcidy',
            'Bourse_Code',
            'ICBS_Date',
            'ICBS_BankCsDecisionStatus',
            'ICBS_BankCsScore',
            'ICBS_BCH_Count',
            'ICBS_BCH_Sum',
            'ICBS_BCH_Score',
            'ICBS_FCL_TotalAmTashilat',
            'ICBS_FCL_TotalAmOriginal',
            'ICBS_FCL_TotalAmBedehi',
            'ICBS_FCL_TotalAmSarResid',
            'ICBS_FCL_TotalAmMoavagh',
            'ICBS_FCL_TotalAmMashkuk',
            'ICBS_FCL_TotalAmTahodST',
            'ICBS_FCL_Score',
            'ICBS_CreditRecord_Score',
        ];

        foreach ($users as $user) {
            $regDate = explode('-', Edate::edateFromCarbon('Y-m-d', $user->created_at));
            $birthDate = explode('-', $user->birth);
            $address_home = $user->addresses->where('type', 'home')->last();
            $address_work = $user->addresses->where('type', 'work')->last();
            if (!$user->usermeta) $user->usermeta = new Usermeta();

            $Icbs = ICBS::IntegratedCs($user->nid, 'cache');
            $icbs = $Icbs->result['IntegratedCsResult'];

            $data[] = [
                $user->id,
                $user->fname,
                $user->lname,
                $regDate[2] ?? '',
                $regDate[1] ?? '',
                $regDate[0] ?? '',
                $user->username,
                $user->nid,
                $birthDate[2] ?? '',
                $birthDate[1] ?? '',
                $birthDate[0] ?? '',
                $address_home ? Address::state_name($address_home->state) : null,
                $address_home ? $address_home->city : null,
                $address_home ? $address_home->postal_code : null,
                $address_home ? $address_home->phone : null,
                $address_work ? Address::state_name($address_work->state) : null,
                $address_work ? $address_work->city : null,
                $address_work ? $address_work->postal_code : null,
                $address_work ? $address_work->phone : null,
                $user->usermeta->gender,
                $user->usermeta->marital,
                $user->usermeta->children,
                $user->usermeta->home_ownership,
                $user->usermeta->dependants,
                $user->usermeta->residence_years,
                $user->usermeta->job_exprience,
                $user->usermeta->job_section,
                $user->usermeta->job_category,
                $user->usermeta->job_contract_type,
                $user->usermeta->fin_salary,
                $user->usermeta->fin_salary_spouse,
                $user->usermeta->fin_salary_etc,
                $user->usermeta->fin_costs,
                $user->usermeta->fin_costs_etc,
                $user->usermeta->assets_car,
                $user->usermeta->assets_car_brand,
                $user->usermeta->fin_cheque_years,
                $user->usermeta->assets_haves && in_array('stock_edalat', $user->usermeta->assets_haves) ? 1 : 0,
                $user->usermeta->assets_haves && in_array('ins_life', $user->usermeta->assets_haves) ? 1 : 0,
                $user->usermeta->assets_haves && in_array('ins_supp', $user->usermeta->assets_haves) ? 1 : 0,
                $user->usermeta->assets_haves && in_array('subcidy', $user->usermeta->assets_haves) ? 1 : 0,
                $user->usermeta->assets_haves && in_array('bourse_code', $user->usermeta->assets_haves) ? 1 : 0,
                Edate::edateFromCarbon('Y-m-d', $Icbs->created_at),
                $icbs['BankCsDecisionStatus'] ? 1 : 0,
                $icbs['BankCsScore'],
                $icbs['BankCsCheckInfo']['CheckCount'],
                $icbs['BankCsCheckInfo']['CheckAmountSum'],
                Useful::str_numbers($icbs['BankCsCreditReportList']['CsReportFull'][0]['ScoreSum'])[0],
                $icbs['BankCsFacilityInfo']['TotalAmTashilat'],
                $icbs['BankCsFacilityInfo']['TotalAmOriginal'],
                $icbs['BankCsFacilityInfo']['TotalAmBedehi'],
                $icbs['BankCsFacilityInfo']['TotalAmSarResid'],
                $icbs['BankCsFacilityInfo']['TotalAmMoavagh'],
                $icbs['BankCsFacilityInfo']['TotalAmMashkuk'],
                $icbs['BankCsFacilityInfo']['TotalAmTahodST'],
                Useful::str_numbers($icbs['BankCsCreditReportList']['CsReportFull'][1]['ScoreSum'])[0],
                Useful::str_numbers($icbs['BankCsCreditReportList']['CsReportFull'][2]['ScoreSum'])[0],
            ];
        }

        return $data;
    }

    //orders (successful)
    public static function orders($from_ts = null)
    {
        $orders = Order::successful()
            ->where(function ($q) use ($from_ts) {
                if ($from_ts)
                    $q->where('charged_at', '>=', Carbon::createFromTimestamp($from_ts));
                else
                    $q->whereNotNull('charged_at');
            })->with(['ghests' => function ($q) {
                $q->select('id', 'order_id')->where('ghest_date', '<', Carbon::now());
            }, 'user' => function ($q) {
                $q->select('id', 'fname', 'lname');
            }])->orderBy('created_at', 'asc')->get();

        $data[0] = [
            'User_ID',
            'Order_ID',
            'Order Number',
            'User_Name',
            'Submit Day',
            'Submit Month',
            'Submit Year',
            'Ghesta Growth Phase',
            'Charge Day',
            'Charge Month',
            'Charge Year',
            'Charge Amount',
            'Customer Share',
            'Ghesta Share',
            'Installment Amount',
            'Repayment Installments',
            'Due Installments',
            'Number of bit-size Deliquencies ( 0 -7 Days )',
            'Number of short period deliquencies ( 7 - 30 Days )',
            'Number of short period deliquencies ( + 31 Days )',
            'Number of Defaults',
        ];

        foreach ($orders as $order) {
            $crDate = explode('-', Edate::edateFromCarbon('Y-m-d', $order->created_at));
            $chargeDate = explode('-', Edate::edateFromCarbon('Y-m-d', $order->charged_at));

            if (substr($order->oid, 0, 3) == 'GHS' || substr($order->oid, 0, 3) == 'GHD') $gp = 0;
            elseif (Carbon::parse($order->created_at)->timestamp > Edate::jmktime(0, 0, 0, 9, 15, 1399)) $gp = 2;
            else $gp = 1;

            $data[] = [
                $order->user_id,
                $order->id,
                $order->oid,
                $order->user->fullname,
                (int)$crDate[2],
                (int)$crDate[1],
                (int)$crDate[0],
                $gp,
                (int)$chargeDate[2],
                (int)$chargeDate[1],
                (int)$chargeDate[0],
                $order->amount,
                $order->prepayment,
                $order->amount - $order->prepayment,
                $order->ghest,
                $order->cheques,
                $order->ghests->count(),
            ];
        }

        return $data;
    }

    //facilities (successful)
    public static function fcl($from_ts = null, $users = null)
    {
        if (!$users) {
            $users = User::select('id', 'fname', 'lname', 'username', 'nid')
                ->whereHas('orders', function ($q) use ($from_ts) {
                    $q->successful()->where(function ($q) use ($from_ts) {
                        if ($from_ts)
                            $q->where('charged_at', '>=', Carbon::createFromTimestamp($from_ts));
                        else
                            $q->whereNotNull('charged_at');
                    });
                })->whereHas('usermeta', function ($q) {
                    $q->whereNotNull('gender');
                })->orderBy('id', 'asc')->get();
        }

        $data[0] = [
            'User_ID',
            'User_Name',
            'NID',
            'AmOriginal',
            'AmBenefit',
            'AmBedehi',
            'AmMoavagh',
            'AmMashkuk',
            'GhrType',
            'Bank_Code',
            'Bank_Name',
        ];

        foreach ($users as $user) {
            $Icbs = ICBS::IntegratedCs($user->nid, 'cache');
            $icbs = $Icbs->result['IntegratedCsResult'];
            if (!isset($icbs['BankCsFacilityInfo']['FacilityInformationItems']['FFacilityInformationItem'])) continue;
            $fcls = $icbs['BankCsFacilityInfo']['FacilityInformationItems']['FFacilityInformationItem'];
            if (isset($fcls['AmBedehiKol'])) $fcls = [$fcls];
            foreach ($fcls as $fcl) {
                $data[] = [
                    $user->id,
                    $user->fullname,
                    $user->nid,
                    $fcl['AmOriginal'],
                    $fcl['AmBenefit'],
                    $fcl['AmBedehiKol'],
                    $fcl['AmMoavagh'],
                    $fcl['AmMashkuk'],
                    $fcl['GhrType'],
                    $fcl['BnkCD'],
                    config('banks.' . Useful::zerofill($fcl['BnkCD'], 3) . '.fa'),
                ];
            }
        }

        return $data;
    }

    //back cheques (successful)
    public static function bch($from_ts = null, $users = null)
    {
        if (!$users) {
            $users = User::select('id', 'fname', 'lname', 'username', 'nid')
                ->whereHas('orders', function ($q) use ($from_ts) {
                    $q->successful()->where(function ($q) use ($from_ts) {
                        if ($from_ts)
                            $q->where('charged_at', '>=', Carbon::createFromTimestamp($from_ts));
                        else
                            $q->whereNotNull('charged_at');
                    });
                })->whereHas('usermeta', function ($q) {
                    $q->whereNotNull('gender');
                })->orderBy('id', 'asc')->get();
        }

        $data[0] = [
            'User_ID',
            'User_Name',
            'NID',
            'Amount',
            'CheckDate',
            'BackDate',
            'Bank_Code',
            'Bank_Name',
        ];

        foreach ($users as $user) {
            $Icbs = ICBS::IntegratedCs($user->nid, 'cache');
            $icbs = $Icbs->result['IntegratedCsResult'];
            if (!isset($icbs['BankCsCheckInfo']['CheckInformationItems']['FCheckInformationItem'])) continue;
            $bchs = $icbs['BankCsCheckInfo']['CheckInformationItems']['FCheckInformationItem'];
            if (isset($bchs['ACCNTNO'])) $bchs = [$bchs];
            foreach ($bchs as $bch) {
                $data[] = [
                    $user->id,
                    $user->fullname,
                    $user->nid,
                    $bch['AMCHQ'],
                    Edate::edateFromCarbon('Y-m-d', $bch['CheckDate']),
                    Edate::edateFromCarbon('Y-m-d', $bch['ReturnedDate']),
                    $bch['CDBNK'],
                    config('banks.' . Useful::zerofill($bch['CDBNK'], 3) . '.fa'),
                ];
            }
        }

        return $data;
    }
}

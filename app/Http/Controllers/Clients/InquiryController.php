<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Credit;
use App\Models\Order;
use App\Models\Pattern;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Shop;
use App\Models\Sms;
use App\Models\Useful;
use App\Models\User;
use App\Models\Verification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InquiryController extends Controller
{
    private static $message = [
        'mobile_nid_conflict' => 'کد ملی یا موبایل وارد شده نادرست است؛ ممکن است با شماره موبایل دیگری در سایت قسطا ثبت نام کرده باشید.',
        'user_doesnt_exist' => 'در حال حاضر موجودی اعتبار شما در قسطا 0 تومان است. برای ثبت سفارش و افزایش اعتبار خود از طریق لینک وارد حساب خود در سایت قسطا شوید:
        <a href="https://ghesta.ir/dashboard?utm_source=payping&utm_medium=online-shop&utm_content=credit0" target="_blank">ورود به قسطا</a>',
    ];

    public function shop(Request $request)
    {
        //Shop Inquiry
        if (strlen($request->domain) < 3 || strlen($request->domain) > 100)
            return Useful::no(['code' => 'domain_invalid', 'message' => 'دامنه نامعتبر است'], 400, $request->log_id);
        $domain = Useful::domain_extractor($request->domain);
        $shop = Shop::select('id', 'status')->where('domain', $domain)->where('status', 'active')->first();
        if (!$shop) return Useful::no(['code' => 'shop_doesnt_exist', 'message' => 'فروشگاه وجود ندارد'], 400, $request->log_id);

        return Useful::yes(null, 200, $request->log_id);
    }

    public function user(Request $request)
    {
        //Shop Inquiry
        if (strlen($request->domain) < 3 || strlen($request->domain) > 100)
            return Useful::no(['code' => 'domain_invalid', 'message' => 'دامنه نامعتبر است'], 400, $request->log_id);
        $domain = Useful::domain_extractor($request->domain);
        $shop = Shop::select('id', 'status')->where('domain', $domain)->where('status', 'active')->first();
        if (!$shop) return Useful::no(['code' => 'shop_doesnt_exist', 'message' => 'فروشگاه وجود ندارد'], 400, $request->log_id);

        $domain = strtolower($request->domain);

        $res = Shop::select('id', 'status')->where('domain', $domain)->where('status', 'active')->first();
        if (!$res)
            return Useful::no(['code' => 'shop_doesnt_exist', 'message' => 'فروشگاه وجود ندارد'], 400, $request->log_id);

        //User Inquiry
        $username = Useful::enum($request->mobile, 'username');
        if (strlen($username) != 10)
            return Useful::no(['code' => 'mobile_invalid', 'message' => 'موبایل نامعتبر است'], 400, $request->log_id);

        $nid = User::check_nid($request->nid);
        if (!$nid)
            return Useful::no(['code' => 'nid_invalid', 'message' => 'کدملی نامعتبر است'], 400, $request->log_id);

        $user = User::where('username', $username)->first();

        if ($user) {
            if ($user->verified_at) {
                $otp = mt_rand(100000, 999999);

                //TEST
                if ($user->id == 21961) {
                    $otp = 123456;
                    $sent = true;
                } else {
                    $sent = Sms::send($username, Pattern::otp, ['otp' => $otp], 1);
                }

                $client_id = Client::select('id')->where('name', $request->client)->first()->id;
                $track_id = 'C' . Useful::randomString(12) . '_' . $client_id;
                if (!$sent) return Useful::no(['code' => 'sms_failed', 'message' => 'پیامک ارسال نشد'], 400, $request->log_id);
                if (Verification::create([
                    'prop' => 'mobile',
                    'val' => $username,
                    'type' => 'clients',
                    'is_verified' => 0,
                    'track_id' => $track_id,
                    'otp' => $otp,
                    'otp_sent_at' => Carbon::now(),
                ])) {
                    return Useful::yes([
                        'mobile' => '0' . $username,
                        'otp_sent_at' => Carbon::now()->timestamp,
                        'track_id' => $track_id
                    ], 200, $request->log_id);
                }
            } else {
                $code = 'user_doesnt_exist';
            }
        } else {
            $user = User::where('nid', $nid)->first();
            $code = $user ? ($user->verified_at ? 'mobile_nid_conflict' : 'user_doesnt_exist') : 'user_doesnt_exist';
        }
        return Useful::no([
            'code' => $code,
            'message' => self::$message[$code]
        ], 400, $request->log_id);
    }

    public function otp_validation(Request $request)
    {
        //Shop Inquiry
        if (strlen($request->domain) < 3 || strlen($request->domain) > 100)
            return Useful::no(['code' => 'domain_invalid', 'message' => 'دامنه نامعتبر است'], 400, $request->log_id);
        $domain = Useful::domain_extractor($request->domain);
        $shop = Shop::select('id', 'status')->where('domain', $domain)->where('status', 'active')->first();
        if (!$shop) return Useful::no(['code' => 'shop_doesnt_exist', 'message' => 'فروشگاه وجود ندارد'], 400, $request->log_id);

        $client_id = Client::select('id')->where('name', $request->client)->first()->id;
        $username = Useful::enum($request->mobile, 'username');
        $otp = Useful::enum($request->otp);
        $track_id = $request->track_id;
        $amount = Useful::enum($request->amount);
        if ($amount < 1000)
            return Useful::no(
                [
                    'code' => 'amount_very_low',
                    'message' => 'مبلغ باید بیشتر از 100,000 تومان باشد'
                ],
                400,
                $request->log_id
            );

        $case = Verification::where(['val' => $username, 'track_id' => $track_id, 'is_verified' => 0])->first();
        if (!$case)
            return Useful::no(
                [
                    'code' => 'verification_failed',
                    'message' => 'احراز هویت ناموفق بود. لطفاً درخواست کد تأیید جدید بدهید.'
                ],
                400,
                $request->log_id
            );

        if ($case->otp == $otp) {
            $case->update([
                'is_verified' => 1,
                'verified_at' => Carbon::now(),
                'track_id' => 'revoked' . time()
            ]);
            $user = User::where('username', $username)->first();
            if ($user) {
                $invoice = $this->invoice($user, $amount);
                if ($invoice->balance == 0)
                    return Useful::no([
                        'code' => 'credit_0',
                        'message' => self::$message['user_doesnt_exist']
                    ], 400, $request->log_id);

                $invoice_create = Credit::create([
                    'user_id' => $user->id,
                    'order_id' => $invoice->order_id,
                    'shop_id' => $shop->id,
                    'client_id' => $client_id,
                    'amount' => $invoice->amount,
                    'prepayment' => $invoice->prepayment,
                    'credit_required' => $invoice->credit_required,
                    'credit' => $invoice->credit,
                    'cash' => $invoice->cash,
                    'payable' => $invoice->payable,
                    'meta' => ['shop_return_url' => $request->shop_return_url],
                ]);
                if ($invoice_create) {
                    $id = $invoice_create->id;
                    // $track_id = 'credit-' . Useful::randomString(14) . substr(time(), -6) . '_' . $client_id;
                    $track_id = $request->payping_refid;
                    $response = Http::withToken(config('globals.payment.payping_token'))
                        ->withHeaders([
                            "Accept: application/json",
                            "Content-Type: application/json",
                        ])
                        ->post('https://api.payping.ir/v2/pay', [
                            'payerName' => $user->fname . ' ' . $user->lname,
                            'amount' => $invoice->payable,
                            'payerIdentity' => '0' . $user->username,
                            'returnUrl' => "https://ghesta.ir/payments/result/ghesta_payping",
                            'clientRefId' => $track_id,
                        ]);
                    $code = $response->json()['code'];
                    $payment = Payment::create([
                        'credit_id' => $id,
                        'authority' => $code,
                        'type' => 'credit',
                        'amount' => $invoice->payable,
                        'gateway' => 'ghesta_payping',
                        'track_id' => $track_id,
                        'status' => 0,
                        'meta' => ['shop_return_url' => $request->shop_return_url],
                    ]);
                    $invoice_create->update(['payment_id' => $payment->id, 'track_id' => $track_id]);

                    return Useful::yes([
                        'user' => [
                            'fname' => $user->fname,
                            'lname' => $user->lname,
                            'mobile' => '0' . $user->username,
                        ],
                        'invoice' => [
                            'code' => $code,
                            'amount' => $invoice->amount,
                            'prepayment' => $invoice->prepayment,
                            'credit_required' => $invoice->credit_required,
                            'credit' => $invoice->credit,
                            'cash' => $invoice->cash,
                            'payable' => $invoice->payable,
                            'description' => $invoice->description,
                        ],
                        'track_id' => $track_id,
                    ], 200, $request->log_id);
                }
                return Useful::no(['code' => 'db_510', 'message' => 'خطا در ثبت اطلاعات. لطفاً مجدداً تلاش کنید'], 400, $request->log_id);
            }
            return Useful::no(['code' => 'mobile_invalid', 'message' => 'موبایل نامعتبر است'], 400, $request->log_id);
        }

        $case->update(['try_times' => $case->try_times + 1]);
        if ($case->try_times > 2) {
            $case->update(['is_verified' => -1]);
            return Useful::no(['code' => 'otp_expired', 'message' => 'کد منقضی شده است'], 400, $request->log_id);
        }
        return Useful::no(['code' => 'otp_wrong', 'message' => 'کد وارد شده اشتباه است'], 400, $request->log_id);
    }

    public function invoice($user, $amount)
    {
        $deposit = Order::select('id', 'amount')->where(['user_id' => $user->id, 'is_credit' => 1, 'status' => 'cycle'])->orderBy('id', 'desc')->first();

        //TEST
        if ($user->id == 490 || $user->id == 21961 || $user->id == 25047) {
            $deposit = (object)[
                'id' => 267,
                'amount' => 7000000
            ];
        }

        if (!$deposit) return (object)['balance' => 0];

        $withdraws = Credit::where('order_id', $deposit->id)->done()->sum('credit');
        $deposits = $deposit->amount;
        $balance = $deposits - $withdraws;
        $rpa = Setting::select('val')->where('prop', 'rpa')->first()->val;

        if ($balance >= $amount) {
            $prepayment = ($rpa / 100) * $amount;
            $credit_required = $amount - $prepayment;
        } else {
            $prepayment = ($rpa / 100) * $balance;
            $credit_required = $amount - $prepayment;
        }

        $cash =  $credit_required - $balance;
        if ($cash < 0) {
            $cash = 0;
            $credit = $credit_required;
        } else
            $credit = $balance;

        return (object)[
            'amount' => $amount,
            'credit_required' => $credit_required,
            'balance' => $balance,
            'credit' => $credit,
            'prepayment' => $prepayment,
            'cash' => $cash,
            'payable' => $cash + $prepayment,
            'order_id' => $deposit->id,
            'description' => 'پیش پرداخت: ' . '<strong>' . number_format($prepayment) . '</strong>' . ' تومان <br/>'
                . 'باقیمانده نقدی: ' . '<strong>' . number_format($cash) . '</strong>' . ' تومان',
        ];
    }

    public function credit_recheck(Request $request)
    {
        $username = Useful::enum($request->mobile, 'username');
        if (strlen($username) != 10)
            return Useful::no(['code' => 'mobile_invalid', 'message' => 'موبایل نامعتبر است'], 400, $request->log_id);

        $user = User::where('username', $username)->first();

        $track_id = $request->payping_refid ?? ($request->refid_payping ?? $request->track_id);
        $credit = Credit::where(['user_id' => $user->id, 'track_id' => $track_id])->first();
        if (!$credit)
            return Useful::no(
                [
                    'code' => 'record_not_found',
                    'message' => 'ترکیب شناسه رهگیری/موبایل نامعتبر است'
                ],
                400,
                $request->log_id
            );

        if ($credit->done_at)
            return Useful::no(
                [
                    'code' => 'already_done',
                    'message' => 'این درخواست قبلاً موفقیت آمیز بوده و از اعتبار کاربر کسر شده است.'
                ],
                400,
                $request->log_id
            );

        if (!$credit->paid_at)
            return Useful::no(
                [
                    'code' => 'not_paid',
                    'message' => 'مبلغ نقدی قابل پرداخت این سفارش هنوز پرداخت نشده است.'
                ],
                400,
                $request->log_id
            );

        if ($credit->amount != $request->amount || ($credit->credit + $credit->payable != $request->amount))
            return Useful::no(
                [
                    'code' => 'amount_not_matched',
                    'message' => 'مبلغ پرداخت شده با اعداد ما همخوانی ندارد.'
                ],
                400,
                $request->log_id
            );

        $invoice = $this->invoice($user, $credit->amount);
        if ($invoice->payable != $credit->payable)
            return Useful::no(
                [
                    'code' => 'credit_conflict',
                    'message' => 'تناقضی در میزان اعتبار وجود دارد'
                ],
                400,
                $request->log_id
            );

        if ($credit->update(['done_at' => Carbon::now()]))
            return Useful::yes([
                'done' => true,
                'done_at' => Carbon::now()->timestamp,
                'track_id' => $track_id
            ], 200, $request->log_id);
    }
}

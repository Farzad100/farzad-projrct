<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Credit;
use App\Models\Useful;
use App\Models\Ghest;
use App\Models\Discount;
use App\Models\Doc;
use App\Models\ICBS;
use App\Models\Order;
use App\Models\Pattern;
use App\Models\Payment;
use App\Models\Shop;
use App\Models\Sms;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Stevebauman\Location\Location;

class PaymentController extends Controller
{
    protected static $zarinpal_status = 1;

    public function index(Request $request)
    {
        $uri = Route::current()->uri;
        $role = explode('/', $uri)[1];

        Auth::user()->username == 9109270876 ? $debugger = 1 : $debugger = null;

        switch ($role) {
            case 'admin':
                $query = Payment::query();
                break;
            case 'shop':
                $shop_id = Shop::select('id')->where('user_id', Auth::user()->id)->first()->id;
                $query = Payment::shop($shop_id);
                break;
            case 'organ':
                $organ_id = Shop::select('id')->where('user_id', Auth::user()->id)->first()->id;
                $query = Payment::organ($organ_id);
                break;
            default:
                return Useful::nok(['scope_not_found', 'دسترسی غیرمجاز']);
                break;
        }

        $payments = $query->search($request, $role)->with([
            'order' => function ($q) {
                $q->select('id', 'oid', 'user_id', 'shop_id', 'organ_id');
            }, 'order.user' => function ($q) {
                $q->select('id', 'fname', 'lname', 'username');
            }, 'order.shop' => function ($q) {
                $q->select('id', 'name', 'type');
            }, 'order.organ' => function ($q) {
                $q->select('id', 'name', 'fame');
            }
        ])->paginate(20);
        $new = [];
        $n = 0;
        foreach ($payments as $payment) {
            if (!$payment->order) continue;
            $new[$n]['id'] = $payment->id;
            $col['date'] = [
                'title' => 'تاریخ',
                // 'value' => Edate::edateFromCarbon('j F Y', $ghest->ghest_date),
                'value' => $payment->paid_at,
                'addition' => $debugger ? 'date' : 'date',
            ];
            $col['amount'] = [
                'title' => 'مبلغ',
                'value' => $payment->amount,
                'type' => 'toman'
            ];
            $col['status'] = [
                'title' => 'وضعیت',
                'value' => $ghest_status->farsi,
                'type' => 'status',
                'css_classes' => 'badge-' . $ghest_status->class,
                'addition' => strlen($delay) > 1 ? $delay . ' تأخیر' : ''
            ];
            $col['order'] = [
                'title' => 'سفارش',
                'value' => $ghest->order->oid,
                'addition' => $debugger ? $ghest->order_id : '',
                'css_classes' => 'ltr nova-font text-right',
            ];
            $col['title'] = [
                'title' => 'کاربر',
                'value' => $ghest->order->user->fname . ' ' . $ghest->order->user->lname,
                'addition' => '0' . $ghest->order->user->username,
                'css_classes' => 'text-right'
            ];
            if ($role == 'admin') {
                $tags = [];
                if ($ghest->order->shop_id) {
                    $tags[] = [
                        'name' => 'shop',
                        'title' => 'فروشگاهی',
                        'tooltip' => $ghest->order->shop->name,
                    ];
                }
                if ($ghest->order->organ_id) {
                    $tags[] = [
                        'name' => 'organ',
                        'title' => 'سازمانی',
                        'tooltip' => $ghest->order->organ->fame ? $ghest->order->organ->fame : $ghest->order->organ->name,
                    ];
                }
                if ($ghest->order->payback_type == 'epay') {
                    $tags[] = [
                        'name' => 'epay',
                        'title' => 'بازپرداخت اینترنتی',
                    ];
                }
                $text = '';
                foreach ($tags as $tag) $text .= $tag['title'] . ' - ';
                $col['tags'] = [
                    'title' => 'برچسب ها',
                    'value' => mb_substr($text, 0, -2, 'UTF-8'),
                    'addition' => isset($tags[0]['tooltip']) ? $tags[0]['tooltip'] : '',
                ];

                $col['action'] = [
                    'title' => '',
                    'value' => '',
                ];

                if ($ghest->order->payback_type == 'epay') {
                    if ($ghest->passed_at) {
                        $col['action'] = [];
                    } else {
                        $col['action'] = [
                            'type' => 'button',
                            'buttons' => [
                                [
                                    'value' => 'پرداخت',
                                    'icon' => 'credit-card',
                                    'btn_color' => 'success',
                                    'type' => 'straight',
                                    'method' => 'post',
                                    'endpoint' => '/orders/' . $ghest->order->oid . '/payment/ghest',
                                ],
                                [
                                    'value' => 'غیر اینترنتی',
                                    'icon' => 'coins',
                                    'btn_color' => 'secondary',
                                    'type' => 'confirm',
                                    'method' => 'post',
                                    'endpoint' => '/admin/orders/' . $ghest->order->oid . '/payment/ghest',
                                ],
                            ],
                        ];
                    }
                } else if ($ghest->order->payback_type == 'cheque') {
                    if (!$ghest->passed_at && !$ghest->backed_at) {
                        $col['action'] = [
                            'type' => 'button',
                            'buttons' => [
                                [
                                    'value' => 'پاس شد',
                                    'icon' => 'calendar-check',
                                    'css_classes' => 'text-success border-success',
                                    'type' => 'confirm',
                                    'method' => 'post',
                                    'endpoint' => '/admin/ghests/' . $ghest->id . '/change/pass',
                                ],
                                [
                                    'value' => 'برگشت',
                                    'icon' => 'angry',
                                    'css_classes' => 'text-danger border-danger',
                                    'type' => 'confirm',
                                    'method' => 'post',
                                    'endpoint' => '/admin/ghests/' . $ghest->id . '/change/back',
                                ]
                            ],
                        ];
                    } else if ($ghest->passed_at) {
                        $col['action'] = [];
                    } else {
                        $col['action'] = [
                            'type' => 'button',
                            'buttons' => [
                                [
                                    'value' => 'وصول شد؟',
                                    'btn_color' => 'warning',
                                    'type' => 'confirm',
                                    'method' => 'post',
                                    'endpoint' => '/admin/ghests/' . $ghest->id . '/change/payback',
                                ]
                            ],
                        ];
                    }
                }
            }

            if ($role == 'organ') {
                if (!$ghest->passed_at && $ghest->order->payback_type == 'epay') {
                    $col['action'] = [
                        'type' => 'button',
                        'buttons' => [
                            [
                                'value' => 'پرداخت',
                                'icon' => 'credit-card',
                                'btn_color' => 'success',
                                'type' => 'straight',
                                'method' => 'post',
                                'endpoint' => '/orders/' . $ghest->order->oid . '/payment/ghest',
                            ],
                        ],
                    ];
                } else {
                    $col['action'] = [
                        'title' => '',
                        'value' => '',
                    ];
                }
            }
            $new[$n]['td'] = $col;
            $col = null;
            ++$n;
        }
        return Useful::recollect($ghests, $new);
    }

    public static function getAuthority($info, $callback = null)
    {
        $gateway = $info['gateway'] ?? Payment::$gateway;
        $callback = "https://ghesta.ir/payments/result/";
        if ($gateway == 'zarinpal') {
            $result = self::post('https://api.zarinpal.com/pg/v4/payment/request.json', [
                'merchant_id' => config('globals.payment.zarinpal_token'),
                'amount' => $info['amount'] * 10,
                'mobile' => (string)$info['mobile'],
                'description' => (string)$info['description'],
                'callback_url' => $callback,
            ]);
            if (isset($result['data']) && count($result['data']) > 0 && isset($result['data']['authority']))
                return $result['data']['authority'];
            else if (isset($result['errors']) && count($result['errors']) > 0)
                return $result['errors'];
            return 0;
        } else if ($gateway == 'payping' || $gateway == 'ghesta_payping') {
            $result = Http::withToken(config('globals.payment.payping_token'))
                ->withHeaders([
                    "Accept: application/json",
                    "Content-Type: application/json",
                ])
                ->post('https://api.payping.ir/v2/pay', [
                    'amount' => $info['amount'],
                    'clientRefId' => $info['track_id'],
                    'payerIdentity' => (string)$info['mobile'],
                    'description' => (string)$info['description'],
                    'returnUrl' => $callback . $gateway,
                ]);
            $res = $result->json();
            if (array_key_exists('code', $res)) return $res['code'];
            else return $res;
        }
    }

    public static function getPayLink($code, $gw = null)
    {
        $gateway = $gw ?? Payment::$gateway;
        if ($gateway == 'zarinpal') {
            $payLink = 'https://www.zarinpal.com/pg/StartPay/' . $code . '/ZarinGate';
        } else if ($gateway == 'payping' || $gateway == 'ghesta_payping') {
            $payLink = 'https://api.payping.ir/v2/pay/gotoipg/' . $code;
        }
        return $payLink;
    }

    public static function checkIranIP($ip)
    {
        return true;
        $loc = new Location();
        $location = $loc->get($ip);
        return $location->countryName == 'Iran';
    }

    public static function calcDiscountCode($discount_code = null, $oid)
    {
        if (empty($discount_code)) return Useful::nok(['invalid', 'کد تخفیف نامعتبر است']);
        $user = Auth::user();
        $mobile = $user->mobile;
        $user_id = $user->id;
        $discount = Discount::where(['code' => $discount_code, 'is_active' => 1])->first();
        $amount = Order::select('prepayment')->where('oid', $oid)->first()->prepayment;

        //Check Existence of Code
        if (!$discount) return Useful::nok(['invalid', 'کد تخفیف نامعتبر است']);

        //Check Expiration Date
        if (Carbon::parse($discount->expired_at)->timestamp < time()) return Useful::nok(['expired', 'کد تخفیف منقضی شده است']);

        //Check Private mobile
        if (!empty($discount->mobile) && $discount->mobile != $mobile) return Useful::nok(['invalid', 'کد تخفیف نامعتبر است']);

        //Check Global Limit
        $used = Payment::where('discount_id', $discount->id)->where('status', 100)->count();
        if ($used >= $discount['limit']) return Useful::nok(['limit_count', 'ظرفیت این کد به اتمام رسیده است']);

        //Check Limit Per User
        $usedByUser = Payment::where('discount_id', $discount->id)->user($user_id)->where('status', 100)->count();
        if ($usedByUser >= $discount->limit_per_user) return Useful::nok(['used', 'کد تخفیف قبلاً استفاده شده است']);

        //Check Tags
        $tags = explode('-', $discount->tags);
        if (in_array('first', $tags)) {
            if (Order::where('user_id', $user_id)->whereIn('status', ['prepaid', 'cycle', 'completed'])->exists()) {
                return Useful::nok(['first', 'کد تخفیف نامعتبر است']);
            }
        }
        if (in_array('submit4d', $tags)) {
            $sent_message_ts = Carbon::parse($user->sent_message_at)->timestamp;
            $order_submit_ts = Carbon::parse(Order::where('id', $oid)->first()->created_at)->timestamp;
            if ($order_submit_ts - $sent_message_ts > (4 * 86400)) return Useful::nok('expired');
        }
        /* if (in_array('docs4d', $tags)) {
            $cta_upload_ts = Carbon::parse($user->cta_upload_at)->timestamp;
            $docs_upload_ts = Carbon::parse(Order::where('oid', $oid)->first()->bank->upload_at)->timestamp;
            if ($docs_upload_ts - $cta_upload_ts > (4 * 86400)) return Useful::nok('expired');
        } */

        //Calculation
        $percent = $discount->percent;
        $maxAmount = $discount->amount;
        $offAmount = ($percent / 100) * $amount;
        if ($offAmount > $maxAmount) $offAmount = $maxAmount;
        if ($offAmount > $amount) $offAmount = $amount;
        return Useful::ok(['new_amount' => $amount - $offAmount, 'discount' => $offAmount]);
    }

    public function check_coupon(Request $request)
    {
        $oid = $request->oid;
        $discount_code = strtolower($request->code);
        return self::calcDiscountCode($discount_code, $oid);
    }

    public function inquiry(Request $request)
    {
        $oid = $request->oid;
        $discount_code = $request->code;
        $order = Order::where('oid', $oid)->with('user')->first();

        if (self::$zarinpal_status == 0 && $order->user_id != 490) {
            return Useful::nok([
                'name' => 'gateway_error',
                'message' => 'امکان اتصال به درگاه بانکی موقتاً وجود ندارد. لطفاً بعداً تلاش کنید.'
            ]);
        }

        $invoice = OrderController::invoice_inquiry_costs($order->user);
        if ($invoice['total'] == 0) {
            $this->inquiry_paid($order, Carbon::now());
            return Useful::ok(null, [
                'custom_message' => [
                    'msg_id' => mt_rand(1, 99999),
                    'msg' => 'صورتحساب استعلام قبلاً پرداخت شده است.',
                    'type' => 'success',
                    'style' => 'float',
                ]
            ]);
        }

        $off = self::calcDiscountCode($discount_code, $order->oid);
        if ($off['status']) {
            $new_amount = $off['result']['new_amount'];
            $discount_id = Discount::select('id')->where('code', $discount_code)->first()->id;
        } else {
            $new_amount = $invoice['total'];
            $discount_id = null;
        }

        $Payment = Payment::where(['order_id' => $order->id, 'type' => 'inquiry', 'status' => 0])
            ->orderBy('id', 'desc')->with('order')->first();

        if ($Payment && $Payment->amount > 0 && empty($Payment->authority)) {
            $Payment->forceDelete();
            $resultStatus = 0;
        } else {
            $resultStatus = $Payment ? $this->check($Payment)['status'] : 0;
        }

        $track_id = Useful::randomString(4) . time();
        if ($resultStatus == 100 && $Payment->order_id && $Payment->paid_at) {
            $this->inquiry_paid($order, $Payment->paid_at);
            return Useful::nok(['paid_before', 'این صورتحساب قبلاً پرداخت شده است'], ['force_update']);
        }
        $payment = Payment::create([
            'order_id' => $order->id,
            'type' => 'inquiry',
            'amount' => $new_amount,
            'discount_id' => $discount_id,
            'ip' => Useful::ip(),
            'gateway' => Payment::$gateway,
            'track_id' => $track_id,
        ]);

        if ($payment->amount == 0) {
            $date = Carbon::now();
            $payment->update(['paid_at' => $date, 'status' => 100, 'ref_id' => 1]);
            $this->inquiry_paid($order, $date);
            return Useful::ok(null, [
                'custom_message' => [
                    'msg_id' => mt_rand(1, 99999),
                    'msg' => 'سفارش شما در صف استعلام قرار گرفت و نتیجه از طریق پیامک به اطلاعتان خواهد رسید.',
                    'type' => 'success',
                    'style' => 'float',
                ]
            ]);
        }

        $info = [
            'amount' => $payment->amount,
            'description' => 'هزینه استعلام برای سفارش ' . $order->oid,
            'email' => $order->user->email,
            'mobile' => $order->user->mobile,
            'gateway' => Payment::$gateway,
            'track_id' => $track_id,
        ];

        $Authority = $this->getAuthority($info);

        if (is_array($Authority)) {
            $payment->update(['status' => -1, 'meta' => $Authority]);
            Useful::report("" . " آتوریتی استعلام", [
                'شناسه سفارش' => $order->id,
                'شماره سفارش' => $order->oid,
                'شناسه پرداخت' => $payment->id,
            ]);
        } else if ($Authority === 0) {
            Useful::report("" . " آتوریتی استعلام 0 شد", [
                'شناسه سفارش' => $order->id,
                'شماره سفارش' => $order->oid,
                'شناسه پرداخت' => $payment->id,
            ]);
        } else {
            $payment->update(['status' => 0, 'authority' => $Authority]);
            return Useful::ok(['url' => self::getPayLink($Authority)]);
        }

        return Useful::nok(['gateway', 'اختلال در درگاه پرداخت؛ لطفاً بعد از دقایقی مجدداً تلاش کنید']);
    }

    public function prepayment(Request $request)
    {
        $oid = $request->oid;
        $discount_code = $request->code;
        $order = Order::oid($oid);
        if (self::$zarinpal_status == 0 && $order->user_id != 490) {
            return Useful::nok([
                'name' => 'gateway_error',
                'message' => 'امکان اتصال به درگاه بانکی موقتاً وجود ندارد. لطفاً بعداً تلاش کنید.'
            ]);
        }
        $off = self::calcDiscountCode($discount_code, $order->oid);
        if ($off['status']) {
            $new_amount = $off['result']['new_amount'];
            $discount_id = Discount::select('id')->where('code', $discount_code)->first()->id;
        } else {
            $new_amount = $order->prepayment;
            $discount_id = null;
        }

        $Payment = Payment::where(['order_id' => $order->id, 'type' => 'prepayment', 'status' => 0])
            ->orderBy('id', 'desc')->with('order')->first();

        if ($Payment && $Payment->amount > 0 && empty($Payment->authority)) {
            $Payment->forceDelete();
            $resultStatus = 0;
        } else {
            $resultStatus = $Payment ? $this->check($Payment)['status'] : 0;
        }

        $invoice = self::final_invoice($order);

        $new_amount = $invoice['total'] - Useful::list_search('prepayment', $invoice['details'])['price'] + $new_amount;

        $track_id = Useful::randomString(4) . time();
        if ($resultStatus == 100 && $Payment->order_id && $Payment->paid_at && $Payment->order->status == 'prepayment') {
            Order::whereId($Payment->order_id)->update(['status' => 'prepaid', 'prepaid_at' => $Payment->paid_at]);
            return Useful::nok(['paid_before', 'این صورتحساب قبلاً پرداخت شده است'], ['force_update']);
        } else if ($payment = Payment::create([
            'order_id' => $order->id,
            'type' => 'prepayment',
            'amount' => $new_amount,
            'discount_id' => $discount_id,
            'ip' => Useful::ip(),
            'gateway' => Payment::$gateway,
            'track_id' => $track_id,
        ])) {
            $order = Order::oid($oid);
            $user = User::info($order->user_id);
            if ($payment->amount == 0) {
                $payment->update(['paid_at' => Carbon::now(), 'status' => 100, 'ref_id' => 1]);
                $order->update(['status' => 'prepaid', 'prepaid_at' => Carbon::now()]);
                return Useful::nok(['amount0', 'سفارش شما در صف شارژ قسطاکارت قرار گرفت']);
            }

            $info = [
                'amount' => $payment->amount,
                'description' => 'پیش پرداخت برای قسطاکارت - سفارش ' . $order->oid,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'gateway' => Payment::$gateway,
                'track_id' => $track_id,
            ];

            $Authority = $this->getAuthority($info);

            if (is_array($Authority)) {
                $payment->update(['status' => -1, 'meta' => $Authority]);
                Sms::send(config('globals.cto.mobile'), 'آتوریتی پیش پرداخت' . $order->id);
            } else if ($Authority === 0) {
                Sms::send(config('globals.cto.mobile'), '0آتوریتی پیش پرداخت' . $order->id);
            } else {
                $payment->update(['status' => 0, 'authority' => $Authority]);
                return Useful::ok(['url' => self::getPayLink($Authority)]);
            }
            return Useful::nok(['gateway', 'اختلال در درگاه پرداخت؛ لطفاً بعد از دقایقی مجدداً تلاش کنید']);
        }
        return Useful::nok(['dbp', 'خطای سرور']);
    }

    public function ghest(Request $request)
    {
        if (self::$zarinpal_status == 0) {
            return Useful::nok([
                'name' => 'gateway_error',
                'message' => 'امکان اتصال به درگاه بانکی موقتاً وجود ندارد. لطفاً بعداً تلاش کنید.'
            ]);
        }
        $oid = $request->oid;
        $order = Order::oid($oid);
        $user = User::info($order->user_id);

        $Payment = Payment::where(['order_id' => $order->id, 'type' => 'ghest', 'status' => 0])->orderBy('id', 'desc')->with('order')->first();
        if ($Payment) {
            if (empty($Payment->authority)) $Payment->forceDelete();
            else $result = $this->check($Payment);
        }

        $ghest = Ghest::select('amount', 'ghest_date')->where('order_id', $order->id)->notPassed()->orderBy('ghest_date', 'asc')->first();
        if ($ghest) {
            $gateway = Payment::$gateway;
            $track_id = Useful::randomString(4) . time();
            if ($payment = Payment::create([
                'order_id' => $order->id,
                'type' => 'ghest',
                'amount' => $ghest->amount,
                'ip' => Useful::ip(),
                'gateway' => $gateway,
                'track_id' => $track_id,
            ])) {
                $oum = Useful::oum(Ghest::where(['order_id' => $order->id])->passed()->count() + 1);

                $info = [
                    'amount' => $ghest->amount,
                    'description' => "قسط $oum سفارش " . $order->oid,
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                    'gateway' => $gateway,
                    'track_id' => $track_id,
                ];

                $Authority = $this->getAuthority($info);

                if (is_array($Authority)) {
                    $payment->update(['status' => -1, 'meta' => $Authority]);
                    Sms::send(config('globals.cto.mobile'), 'قسط ' . $order->id);
                } else if ($Authority === 0) {
                    Sms::send(config('globals.cto.mobile'), '0قسط ' . $order->id);
                } else {
                    $payment->update(['status' => 0, 'authority' => $Authority]);
                    return Useful::ok(['url' => self::getPayLink($Authority)]);
                }
                return Useful::nok(['gateway', 'اختلال در درگاه پرداخت؛ لطفاً بعد از دقایقی مجدداً تلاش کنید']);
            }
            return Useful::nok(['dbpgh', 'خطای سرور']);
        }
        return Useful::nok(['not_found', 'همه اقساط این سفارش پرداخت شده است.']);
    }

    public function extra(Request $request)
    {
        if (self::$zarinpal_status == 0) {
            return Useful::nok([
                'name' => 'gateway_error',
                'message' => 'امکان اتصال به درگاه بانکی موقتاً وجود ندارد. لطفاً بعداً تلاش کنید.'
            ]);
        }

        $amount = $request->amount;
        if ($amount < 1000) {
            return Useful::nok([
                'name' => 'min_amount',
                'message' => 'حداقل مبلغ قابل شارژ 100 هزار تومان است'
            ]);
        } else if ($amount > 5000000) {
            return Useful::nok([
                'name' => 'max_amount',
                'message' => 'حداکثر مبلغ شارژ 5 میلیون تومان است.'
            ]);
        }

        $oid = $request->oid;
        $order = Order::oid($oid);
        $user = User::info($order->user_id);
        $gateway = Payment::$gateway;
        $track_id = Useful::randomString(4) . time();
        if ($payment = Payment::create([
            'order_id' => $order->id,
            'type' => 'extra',
            'amount' => $amount,
            'ip' => Useful::ip(),
            'gateway' => $gateway,
            'track_id' => $track_id,
        ])) {
            $info = [
                'amount' => $payment->amount,
                'description' => "شارژ اضافی سفارش " . $order->oid,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'gateway' => $gateway,
                'track_id' => $track_id,
            ];

            $Authority = $this->getAuthority($info);

            if (is_array($Authority)) {
                $payment->update(['status' => -1, 'meta' => $Authority]);
                Sms::send(config('globals.cto.mobile'), 'شارژ اضافی ' . $order->id);
            } else if ($Authority === 0) {
                Sms::send(config('globals.cto.mobile'), '0شارژ اضافی ' . $order->id);
            } else {
                $payment->update(['status' => 0, 'authority' => $Authority]);
                return Useful::ok(['url' => self::getPayLink($Authority)]);
            }
            return Useful::nok(['gateway', 'اختلال در درگاه پرداخت؛ لطفاً بعد از دقایقی مجدداً تلاش کنید']);
        }
        return Useful::nok(['dbpe', 'خطای سرور']);
    }

    public function result(Request $request)
    {
        $authority = $request->authority;
        if ($authority) {
            $request = Payment::where('authority', $authority)
                ->where('created_at', '>', Carbon::now()->subDays(3))->first();
            return $this->check($request);
        }

        if ($request->gateway == 'ghesta_payping') {
            if ($request->header('origin') != "https://api.payping.ir") abort(403);
            if ($request->header('referer') != "https://api.payping.ir/") abort(403);
        }

        return $this->check((object)[
            'gateway' => $request->gateway,
            'code' => $request->code,
            'refid' => $request->refid,
            'clientrefid' => $request->clientrefid,
            'cardnumber' => $request->cardnumber,
            'cardhashpan' => $request->cardhashpan,
        ]);
    }

    public function check_again(Request $request)
    {
        $id = $request->id;
        $payment = Payment::whereId($id)->first();
        $res = $this->check($payment);
        if ($res) return Useful::ok();
        return Useful::nok();
    }

    public function check($request)
    {
        if (property_exists($request, 'gateway')) $gateway = $request->gateway;
        else $gateway = 'local';

        switch ($gateway) {
            case 'zarinpal':
                $authority = $request->authority;
                if (empty($authority)) {
                    return Useful::nok(['authority_not_found', 'چنین تراکنشی وجود ندارد']);
                }
                $payment = Payment::where(['authority' => $authority])->first();
                break;
            case 'payping':
            case 'ghesta_payping':
                $refId = $request->refid;
                $clientrefid = $request->clientrefid;
                $track_id = $clientrefid ?? $request->track_id;
                $payment = Payment::where(['authority' => $request->code, 'gateway' => $gateway])->first();
                break;
            case 'local':
                $payment = Payment::where(['authority' => $request])->first();
                break;
        }

        if (!$payment)
            return Useful::nok(['tx_not_found', 'چنین تراکنشی وجود ندارد']);

        $gateway = $payment->gateway;

        if ($payment->status == 100) {
            $payment->order_id ? $order = Order::find($payment->order_id) : $order = null;
            return Useful::ok([
                'amount' => $payment->amount,
                'ref_id' => $payment->ref_id,
                'paid_at' => $payment->paid_at,
                'oid' => $order ? $order->oid : null,
                'type' => $payment->type,
                'type_farsi' => Payment::$types[$payment->type],
            ]);
        } else if ($payment->ref_id == '1' && in_array($gateway, ['zarinpal', 'local'])) {
            $card_hash = '';
            $card_mask = '';
            $status = 100;
        } else {
            switch ($gateway) {
                case 'zarinpal':
                    $res = self::post('https://api.zarinpal.com/pg/v4/payment/verify.json', [
                        'merchant_id' => config('globals.payment.zarinpal_token'),
                        'authority' => $authority,
                        'amount' => ($payment->amount) * 10,
                    ]);
                    if (isset($res['data']) && count($res['data']) > 0) {
                        $status = $res['data']['code'];
                        $refId = $res['data']['ref_id'];
                        $card_mask = $res['data']['card_pan'];
                        $card_hash = $res['data']['card_hash'];
                        if (!$card_hash) {
                            $card_mask = 'zarinpal';
                            $card_hash = 'zarinpal';
                        }
                    } else {
                        $status = $res['errors']['code'];
                    }
                    break;
                case 'payping':
                case 'ghesta_payping':
                    $result = Http::withToken(config('globals.payment.payping_token'))
                        ->withHeaders([
                            "Accept: application/json",
                            "Content-Type: application/json",
                        ])
                        ->post('https://api.payping.ir/v2/pay/verify', [
                            'refId' => $refId,
                            'amount' => $payment->amount,
                        ]);
                    $res = $result->json();
                    if ($result->successful()) {
                        $status = 100;
                        $card_mask = $res['cardNumber'];
                        $card_hash = $res['cardHashPan'];
                    } else {
                        $status = -51;
                    }
                    break;
            }
        }
        switch ($status) {
            case '-9':
            case '-10':
            case '-11':
            case '-31':
            case '-52':
                $msg = "خطای $status لطفا با پشتیبانی تماس بگیرید";
                break;
            case '-50':
                $msg = 'تناقض در مبلغ پرداختی با مبلغ قرارداد';
                break;
            case '101':
            case '100':
                $ref_id = $payment->ref_id ?? $refId;
                $paid_at = $payment->paid_at ?? Carbon::now();
                if ($payment->update([
                    'paid_at' => $paid_at,
                    'status' => 100,
                    'ref_id' => $ref_id,
                    'card_mask' => $card_mask,
                    'card_hash' => $card_hash
                ])) {
                    if ($gateway == 'ghesta_payping') {
                        if (Credit::where('track_id', $track_id)->update(['paid_at' => $paid_at])) {
                            return view('pages.payresult', ['x' => [
                                'success' => true,
                                'payping_refid' => $track_id,
                                'refid' => $ref_id,
                                'clientrefid' => $clientrefid,
                                'url' => "https://api.payping.ir/v1/thanks/ghesta",
                                'code' => $payment->authority,
                                'cardnumber' => '',
                                'cardhashpan' => '',
                            ]]);
                        }
                    }
                    $order = Order::whereId($payment->order_id)->with('user')->first();
                    $user = User::info($order->user_id);
                    if ($payment->type == 'prepayment' && $paid_at) {
                        $order->update(['status' => 'prepaid', 'prepaid_at' => $paid_at]);
                        if ($order->shop_id) {
                            Sms::send(
                                Shop::mobile($order->shop_id),
                                Pattern::user_order_prepaid_by_shop,
                                [
                                    'name' => $user->full_name,
                                    'oid' => $order->oid,
                                    'amount' => number_format($payment->amount),
                                    'refid' => $ref_id == 1 ? '(غیر اینترنتی)' : $ref_id,
                                ]
                            );
                        } else {
                            Sms::send(
                                $user->mobile,
                                Pattern::user_order_prepaid,
                                [
                                    'name' => $user->fname,
                                    'oid' => $order->oid,
                                    'amount' => number_format($payment->amount),
                                    'refid' => $ref_id == 1 ? '(غیر اینترنتی)' : $ref_id,
                                ]
                            );
                        }
                    } elseif ($payment->type == 'ghest') {
                        $oum = Payment::where(['order_id' => $order->id, 'type' => 'ghest', 'status' => 100])->count();
                        $order->update(['passed' => $oum]);

                        Ghest::where('order_id', $order->id)->notPassed()->orderBy('ghest_date', 'asc')->limit(1)
                            ->update(['passed_at' => Carbon::now(), 'payment_id' => $payment->id]);

                        if (Ghest::where('order_id', $order->id)->passed()->count() != $oum) {
                            $conflict = 1;
                            Sms::send(
                                config('globals.cto.mobile'),
                                "تناقض در اقساط سفارش " . $order->id
                            );
                        } else $conflict = 0;

                        //completed
                        if ($order->cheques == $oum && $conflict == 0) {
                            $order->update(['status' => 'completed']);
                            Sms::send(
                                $user->mobile,
                                Pattern::user_order_completed,
                                [
                                    'name' => $user->fname,
                                    'oid' => $order->oid,
                                ]
                            );
                        }

                        Sms::send(
                            $user->mobile,
                            Pattern::user_order_ghest_paid,
                            [
                                'name' => $user->fname,
                                'oid' => $order->oid,
                                'oum' => Useful::oum($oum),
                                'amount' => number_format($payment->amount),
                                'refid' => $ref_id == 1 ? '(غیر اینترنتی)' : $ref_id,
                            ]
                        );

                        /* Sms::send(
                            config('globals.cfo.mobile'),
                            Pattern::admin_order_ghest_paid,
                            [
                                'name' => $user->full_name,
                                'oid' => $order->oid,
                                'oum' => Useful::oum($oum),
                                'amount' => number_format($payment->amount),
                                'mobile' => $user->mobile,
                            ]
                        ); */
                    } else if ($payment->type == 'extra') {
                        $order->update(['status' => 'extra_charge', 'reason' => $payment->id]);

                        Sms::send(
                            $user->mobile,
                            Pattern::user_order_extra_charge_paid,
                            [
                                'name' => $user->fname,
                                'amount' => number_format($payment->amount),
                                'refid' => $ref_id,
                            ]
                        );

                        Sms::send(
                            config('globals.support.mobile'),
                            Pattern::admin_order_extra_charge_paid,
                            [
                                'name' => $user->fname,
                                'amount' => number_format($payment->amount),
                                'oid' => $order->oid,
                            ]
                        );
                    } else if ($payment->type == 'inquiry') {
                        $this->inquiry_paid($order, $paid_at);
                    }

                    if ($gateway == 'payping')
                        return Redirect::to('/payments/result?Authority=' . $payment->authority);

                    return Useful::ok([
                        'amount' => $payment->amount,
                        'ref_id' => $ref_id,
                        'oid' => $order->oid,
                        'type' => $payment->type,
                        'type_farsi' => Payment::$types[$payment->type],
                        'paid_at' => $payment->paid_at,
                    ]);
                }
                $msg = 'پرداخت شما موفق بوده، اما به دلایلی در سیستم ثبت نمی شود. لطفاً با واحد فنی قسطا تماس بگیرید: '
                    . config('globals.phone');
                break;
            default:
                $msg = 'تراکنش ناموفق بود';
                break;
        }

        if ($gateway == 'ghesta_payping') {
            $x = [
                'url' => $payment->meta['shop_return_url'],
                'code' => $payment->authority,
                'refid' => $refId,
                'clientrefid' => $request->clientrefid,
                'payping_refid' => $track_id,
                'cardnumber' => '',
                'cardhashpan' => '',
            ];
            return view('pages.payresult', ['x' => $x]);
            return self::post_fl($payment->meta['shop_return_url'], [
                'refid' => $payment->ref_id,
                'code' => $payment->authority,
                'clientrefid' => $payment->track_id,
                'cardnumber' => '',
                'cardhashpan' => '',
            ]);
            return Http::asForm()->timeout(10)->post($payment->meta['shop_return_url'], [
                'refid' => $payment->ref_id,
                'code' => $payment->authority,
                'clientrefid' => $payment->track_id,
                'cardnumber' => '',
                'cardhashpan' => '',
            ]);
            return Useful::no(['code' => 'tx_failed', 'message' => 'تراکنش ناموفق']);
        }

        if ($status != $payment->status) $payment->update(['status' => $status]);
        $order = $payment->order_id ? Order::find($payment->order_id) : null;
        return Useful::nok(['tx_failed', $msg, 'code' => $status, 'oid' => $order ? $order->oid : null]);
    }

    public function inquiry_paid($order, $date = null)
    {
        if (is_null($date)) $date = Carbon::now();

        $order->update(['inquiry_paid_at' => $date]);

        ICBS::where('user_id', $order->user_id)->whereNull('paid_at')->update(['paid_at' => $date]);

        return $order;
    }

    //ADMIN
    public function ghest_offline(Request $request)
    {
        $oid = $request->oid;
        $order = Order::oid($oid);
        $user = User::find($order->user_id);
        $ghest = Ghest::select('amount', 'ghest_date')->where('order_id', $order->id)->notPassed()->orderBy('ghest_date', 'asc')->first();
        if ($ghest) {
            $authority_fake = 'manual-' . Useful::randomString(6) . $order->id;
            if (Payment::create([
                'order_id' => $order->id,
                'type' => 'ghest',
                'amount' => $order->ghest,
                'paid_at' => Carbon::now(),
                'ref_id' => 1,
                'authority' => $authority_fake,
                'gateway' => 'local'
            ])) {
                $this->check($authority_fake);
                return Useful::ok();
            }
        }
        return Useful::nok(['not_found', 'هیچ قسط پرداخت نشده ای یافت نشد']);
    }

    //ADMIN
    public function prepayment_offline(Request $request)
    {
        $oid = $request->oid;
        $order = Order::oid($oid);
        $authority_fake = 'manual-' . Useful::randomString(4) . $order->id;
        if (Payment::create([
            'order_id' => $order->id,
            'type' => 'prepayment',
            'amount' => $order->prepayment,
            'paid_at' => Carbon::now(),
            'ref_id' => 1,
            'authority' => $authority_fake,
            'gateway' => 'local',
        ])) {
            $this->check($authority_fake);
            return Useful::ok();
        }
        return Useful::nok();
    }

    public static function post($url, $body)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        $result = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($result, true);
        return $res;
    }

    public static function post_fl($url, $body)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
        $res = json_decode($result, true);
        return $res;
    }

    public static function final_invoice($order)
    {
        $rep = Doc::select('format')->where('order_id', $order->id)->where('type', 'report')->orderBy('id', 'desc')->first();
        $bkp = Doc::select('format')->where('order_id', $order->id)->where('type', 'backup')->orderBy('id', 'desc')->first();

        $price_validation = 0;
        if ($order->payback_type === 'cheque') {
            if (in_array($rep->format, ['xls', 'xlsx']) && (in_array($bkp->format, ['xls', 'xlsx']) || is_null($bkp->format)))
                $price_validation = Order::inquiry_rabbit_price;
            else
                $price_validation = Order::inquiry_turtle_price;
        }

        $card = Card::where(['user_id' => $order->user->id])->where('delivered_at', '>', Carbon::now()->subWeek())->orderBy('id', 'desc')->first();

        if (!$card) $price_card = 0;
        if ($card) $price_card = 25000;

        $invoice['details'] = [
            [
                'title' => 'پیش‌پرداخت',
                'value' => 'prepayment',
                'price' => $order->prepayment
            ],
            [
                'title' => 'اعتبارسنجی',
                'value' => 'scoring',
                'price' => $price_validation
            ],
            [
                'title' => 'صدور و ارسال کارت',
                'value' => 'card',
                'price' => $price_card,
            ]
        ];

        $invoice['total'] = $order->prepayment + $price_validation + $price_card;

        return $invoice;
    }
}

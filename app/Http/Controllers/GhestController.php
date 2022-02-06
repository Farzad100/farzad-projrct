<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Cheque;
use App\Models\Edate;
use App\Models\Useful;
use App\Models\Ghest;
use App\Models\Order;
use App\Models\Organ;
use App\Models\Pattern;
use App\Models\Shop;
use App\Models\Sms;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class GhestController extends Controller
{
    public function index(Request $request)
    {
        $uri = explode('/', Route::current()->uri);
        $role = $uri[1];
        $export = end($uri) == 'export' ? 1 : null;

        Auth::user()->username == 9109270876 ? $debugger = 1 : $debugger = null;

        $query = Ghest::query();

        switch ($role) {
            case 'admin':
                break;
            case 'shop':
                $shop_id = Shop::select('id')->where('user_id', Auth::user()->id)->first()->id;
                $query = $query->shop($shop_id);
                break;
            case 'organ':
                $organ_id = Organ::select('id')->where('user_id', Auth::user()->id)->first()->id;
                $query = $query->organ($organ_id);
                break;
            default:
                return Useful::nok(['scope_not_found', 'دسترسی غیرمجاز']);
                break;
        }

        $ghests = $query->search($request, $role)->with([
            'order' => function ($q) {
                $q->select('id', 'oid', 'user_id', 'shop_id', 'organ_id', 'payback_type', 'charged_at');
            }, 'order.user' => function ($q) {
                $q->select('id', 'fname', 'lname', 'username', 'nid');
            }, 'order.shop' => function ($q) {
                $q->select('id', 'name', 'type');
            }, 'order.organ' => function ($q) {
                $q->select('id', 'name', 'fame');
            }, 'account' => function ($q) {
                $q->select('id', 'bank_id');
            }
        ]);

        $ghests = $export ? $ghests->get() : $ghests->paginate(20);

        foreach ($ghests as $ghest) {
            if ($ghest->account) {
                $acc = $ghest->account;
                $cheque_number = Useful::cheque_number_helper($acc->bank_id, $ghest->prepend, $ghest->series, $ghest->append);
                $bank_name = Account::bank_name($acc->bank_id)['fa'];
            } else {
                $cheque_number = null;
                $bank_name = null;
            }
            $ghest->cheque_number = $cheque_number;
            $ghest->status = Ghest::status($ghest);
            $ghest->delay = $ghest->passed_at ? Edate::date_diff_carbon($ghest->ghest_date, $ghest->passed_at, 'day') : null;
        }

        if ($export) return $this->export($ghests);

        $new = [];
        $n = 0;
        foreach ($ghests as $ghest) {
            $delay = $ghest->passed_at ? Edate::date_diff_carbon($ghest->ghest_date, $ghest->passed_at) : null;

            $new[$n]['id'] = $ghest->id;
            $col['date'] = [
                'title' => 'تاریخ',
                'value' => Edate::edateFromCarbon('j F Y', $ghest->ghest_date),
                'addition' => $debugger ? $ghest->id : '',
            ];
            $col['amount'] = [
                'title' => 'مبلغ',
                'value' => $ghest->amount,
                'type' => 'toman',
            ];
            $col['status'] = [
                'title' => 'وضعیت',
                'value' => $ghest->status->farsi,
                'type' => 'status',
                'css_classes' => 'badge-' . $ghest->status->class,
                'addition' => $delay ? $delay . ' تأخیر' : ''
            ];
            $col['order'] = [
                'title' => 'سفارش',
                'value' => $ghest->order->oid,
                'addition' => $debugger ? $ghest->order_id : '',
                'css_classes' => 'ltr nova-font text-right',
            ];
            $col['cheque'] = [
                'nosort' => true,
                'title' => 'شماره چک',
                'value' => $ghest->cheque_number,
                'addition' => $bank_name,
            ];
            $col['user'] = [
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
                                    'value' => 'برگشت',
                                    'icon' => 'angry',
                                    'css_classes' => 'text-danger border-danger',
                                    'type' => 'modal',
                                    'modal_name' => 'backedModal',
                                    'method' => 'post',
                                    'endpoint' => '/admin/ghests/' . $ghest->id . '/change/back',
                                ],
                                [
                                    'value' => 'پاس شد',
                                    'icon' => 'calendar-check',
                                    'css_classes' => 'text-success border-success',
                                    'type' => 'modal',
                                    'modal_name' => 'passedModal',
                                    'method' => 'post',
                                    'endpoint' => '/admin/ghests/' . $ghest->id . '/change/pass',
                                ]
                            ],
                        ];
                    } else if ($ghest->passed_at) {
                        $col['action'] = [
                            'type' => 'button',
                            'buttons' => [
                                [
                                    'value' => 'بازنشانی وضعیت',
                                    'css_classes' => 'text-secondary',
                                    'type' => 'confirm',
                                    'method' => 'post',
                                    'endpoint' => '/admin/ghests/' . $ghest->id . '/change/reset',
                                ]
                            ],
                        ];
                    } else {
                        $col['action'] = [
                            'type' => 'button',
                            'buttons' => [
                                [
                                    'value' => 'وصول شد؟',
                                    'btn_color' => 'warning',
                                    'type' => 'modal',
                                    'modal_name' => 'passedModal',
                                    'method' => 'post',
                                    'endpoint' => '/admin/ghests/' . $ghest->id . '/change/payback',
                                ]
                            ],
                        ];
                    }

                    if (Order::has_old_cheques($ghest->order->id)) {
                        $col['action']['buttons'][] = [
                            'value' => 'دانلود تصویر چک ها',
                            'icon' => 'download',
                            'btn_color' => 'outline-primary',
                            'type' => 'straight',
                            'method' => 'get',
                            'endpoint' => '/admin/orders/' . $ghest->order->oid . '/cheques-download',
                        ];
                    } else {
                        $cheque = DB::table('cheques')->select('id')
                            ->where(['order_id' => $ghest->order->id, 'series' => $ghest->series])->first();

                            if(!$cheque) return $ghest;

                        $col['action']['buttons'][] = [
                            'value' => 'دانلود تصویر چک',
                            'icon' => 'download',
                            'btn_color' => 'outline-primary',
                            'type' => 'straight',
                            'method' => 'get',
                            'endpoint' => '/admin/cheques/' . $cheque_id . '/download',
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

    public function export($ghests)
    {
        $data[0] = [
            'ردیف',
            'تاریخ سررسید',
            'مبلغ',
            'اصل',
            'سود',
            'وضعیت',
            'تاریخ وصول',
            'تأخیر (روز)',
            'تاریخ شارژ سفارش',
            'شماره سفارش',
            'نوع بازپرداخت',
            'شماره چک',
            'نام مشتری',
            'کدملی مشتری',
            'موبایل مشتری',
        ];

        $n = 1;
        foreach ($ghests as $ghest) {
            $data[$n] = [
                $n,
                Edate::edateFromCarbon('Y-m-d', $ghest->ghest_date),
                $ghest->amount,
                $ghest->origin,
                $ghest->income,
                $ghest->status->farsi,
                $ghest->passed_at ? Edate::edateFromCarbon('Y-m-d', $ghest->passed_at) : null,
                $ghest->delay ?? '',
                Edate::edateFromCarbon('Y-m-d', $ghest->order->charged_at),
                $ghest->order->oid,
                $ghest->type == 'epay' ? 'اینترنتی' : 'چکی',
                $ghest->series,
                $ghest->order->user->full_name,
                $ghest->order->user->nid,
                $ghest->order->user->mobile,
            ];
            ++$n;
        }

        $url = ExportController::excel($data, 'ghests');
        return Useful::ok(['url' => $url], ['action_type' => 'open_url']);
    }

    public function single(Request $request)
    {
        $oid = $request->oid;

        if ($oid == 'recent') {
            $user_id = Auth::user()->id;
            $futures = Ghest::user($user_id)->whereDate('ghest_date', '>=', Carbon::now())
                ->orWhere(function ($query) use ($user_id) {
                    $query->user($user_id)->notPassed();
                });
            $pasts = Ghest::user($user_id)->whereDate('ghest_date', '<', Carbon::now())->passed()->limit(10);
        } else {
            $order = Order::select('id', 'user_id', 'shop_id', 'organ_id')->where('oid', $oid)->first();
            $this->guard($order);
            $order_id = $order->id;
            $futures = Ghest::where('order_id', $order_id)->whereDate('ghest_date', '>=', Carbon::now())
                ->orWhere(function ($query) use ($order_id) {
                    $query->where('order_id', $order_id)->notPassed();
                });
            $pasts = Ghest::where('order_id', $order->id)->whereDate('ghest_date', '<', Carbon::now())->passed();
        }

        $futurex = $futures->with(['order' => function ($q) {
            $q->select('id', 'oid', 'payback_type');
        }])->orderBy('ghest_date', 'asc')->get();
        $pastx = $pasts->with(['order' => function ($q) {
            $q->select('id', 'oid', 'payback_type');
        }])->orderBy('ghest_date', 'desc')->get();
        $ghests = $futurex->merge($pastx);

        $new = [];
        $n = 0;
        foreach ($ghests as $ghest) {
            $ghest_status = Ghest::status($ghest);
            $new[$n]['id'] = $ghest->id;
            $col['amount'] = [
                'title' => 'مبلغ',
                'value' => number_format($ghest->amount) . ' تومان',
                'addition' => 'سفارش ' . $ghest->order->oid,
            ];
            $col['date'] = [
                'title' => 'سررسید',
                'value' => Edate::edateFromCarbon('j F Y', $ghest->ghest_date)
            ];
            $col['status'] = [
                'title' => 'وضعیت',
                'value' => $ghest_status->farsi,
                'type' => 'status',
                'css_classes' => 'badge-' . $ghest_status->class,
                'status_ps' => $ghest_status->name == 'expired' ? Edate::date_diff_carbon($ghest->ghest_date) . ' تأخیر' : ''
            ];
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
                        ]
                    ],
                ];
            } else {
                $col['action'] = [
                    'title' => null,
                    'value' => null,
                ];
            }
            $new[$n]['td'] = $col;
            $col = null;
            ++$n;
        }
        return Useful::ok($new);
    }

    public function status_counts(Request $request)
    {
        $uri = Route::current()->uri;
        $role = explode('/', $uri)[1];

        switch ($role) {
            case 'admin':
                $query = Ghest::query();
                break;
            case 'shop':
                $shop_id = Shop::select('id')->where('user_id', Auth::user()->id)->first()->id;
                $query = Ghest::shop($shop_id);
                break;
            case 'organ':
                $organ_id = Organ::select('id')->where('user_id', Auth::user()->id)->first()->id;
                $query = Ghest::organ($organ_id);
                break;
            default:
                return Useful::nok(['scope_not_found', 'دسترسی غیرمجاز']);
                break;
        }

        $query = $query->orderBy('ghest_date', 'asc');

        // $query = Ghest::orderBy('ghest_date', 'asc');

        $q1 = clone $query;
        $q2 = clone $query;
        $q3 = clone $query;
        $q4 = clone $query;
        $q5 = clone $query;
        $q6 = clone $query;

        $counts['all'] = $q1->count();
        $counts['passed'] = $q2->passed()->count();
        $counts['expired'] = $q3->expired()->count();
        $counts['near'] = $q4->near()->count();
        $counts['today'] = $q5->today()->count();
        $counts['future'] = $q6->future()->count();

        return Useful::ok($counts);
    }

    //ADMIN
    public function change_status(Request $request)
    {
        $id = $request->id;
        $action = $request->action;
        $action_date = $request->action_date;
        if (!$action_date) $action_date = 'today';
        switch ($action_date) {
            case 'today':
                $jdate = Edate::edateFromCarbon('Y-m-d', Carbon::now());
                $done_at = Edate::j2carbon($jdate);
                break;
            case 'yesterday':
                $jdate = Edate::edateFromCarbon('Y-m-d', Carbon::now()->subDay());
                $done_at = Edate::j2carbon($jdate);
                break;
            case 'expiration':
                $jdate = 'expiration';
                break;
            default:
                $jdate = Useful::enum($action_date, 'jdate');
                $done_at = Edate::j2carbon($jdate);
                break;
        }

        $ghest = Ghest::find($id);
        if ($jdate == 'expiration') $done_at = $ghest->ghest_date;
        switch ($action) {
            case 'back':
                $order_id = $ghest->order_id;
                if ($ghest->update(['backed_at' => $done_at, 'passed_at' => null])) {
                    $passed_count = Ghest::where('order_id', $order_id)->passed()->count();
                    Order::whereId($order_id)->update(['passed' => $passed_count]);
                    return Useful::ok();
                }
                break;
            case 'pass':
            case 'payback':
                if ($ghest->update(['passed_at' => $done_at])) {
                    $order_id = $ghest->order_id;
                    $passed_count = Ghest::where('order_id', $order_id)->passed()->count();
                    Order::whereId($order_id)->update(['passed' => $passed_count]);
                    $order = Order::whereId($order_id)->whereColumn('cheques', 'passed')->first();
                    if ($order) {
                        $user = User::info($order->user_id);
                        $order->update(['status' => 'completed', 'closed_at' => Carbon::now()]);
                        Sms::send(
                            $user->username,
                            Pattern::user_order_completed,
                            [
                                'name' => $user->fname,
                                'oid' => $order->oid,
                            ]
                        );
                    }
                    return Useful::ok();
                }
                break;
            case 'reset':
                $order_id = $ghest->order_id;
                if ($ghest->update(['backed_at' => null, 'passed_at' => null])) {
                    $passed_count = Ghest::where('order_id', $order_id)->passed()->count();
                    Order::whereId($order_id)->update(['passed' => $passed_count]);
                    return Useful::ok();
                }
                break;
            default:
                return Useful::nok(['invalid_status']);
        }
        return Useful::nok(['dbgh-p']);
    }

    public function guard($order)
    {
        if (User::is_admin()) return true;
        $user = Auth::user();
        if ($user->id == $order->user_id) return true;
        if ($order->shop_id) {
            $shop_owner_id = Shop::select('user_id')->where('id', $order->shop_id)->first()->user_id;
            if ($user->id == $shop_owner_id) return true;
        }
        if ($order->organ_id) {
            $organ_owner_id = Organ::select('user_id')->where('id', $order->organ_id)->first()->user_id;
            if ($user->id == $organ_owner_id) return true;
        }
        return abort(403, 'This is not your business!');
    }

    public static function cheques_receive_recheck()
    {
        $orders = Order::select('id', 'docs_received_at')->whereNotNull('docs_received_at')->whereBetween('id', [20000, 22000])->get();
        foreach ($orders as $order)
            Ghest::where('order_id', $order->id)->update(['received_at' => $order->docs_received_at]);
        return 1;
    }

    public static function auto_red_badge_1month_delay()
    {
        $ghests = Ghest::where('type', 'cheque')->expired(30)
            ->whereBetween('ghest_date', [Carbon::now()->subDays(61), Carbon::now()->subDays(31)])
            ->whereHas('order.user', function ($q) {
                $q->where('badge', '!=', 'red');
            })->with(['order' => function ($q) {
                $q->select('id', 'user_id');
            }])->get();

        $users = [];
        foreach ($ghests as $ghest) $users[] = $ghest->order->user_id;

        User::whereIn('id', $users)->update(['badge' => 'red']);

        return true;
    }
}

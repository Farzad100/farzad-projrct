<?php

namespace App\Http\Controllers;

use App\Imports\cardsToImport;
use App\Models\Bontx;
use App\Models\Card;
use App\Models\Charge;
use App\Models\Doc;
use App\Models\Finnotech;
use App\Models\Useful;
use App\Models\Edate;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\PdfToText\Pdf;

class CardController extends Controller
{
    public function card_received(Request $request)
    {
        $user_id = Auth::user()->id;
        $card_number = Useful::enum($request->card_number);
        if (strlen($card_number) != 4)
            return Useful::nok(['cards', 'لطفاً چهار رقم آخر شماره قسطاکارت خود را وارد نمایید']);

        $card = Card::where('card_number', 'like', '%' . $card_number)->whereNull('delivered_at')->first();
        if ($card && ($card->user_id == $user_id || User::is_admin())) {
            $card->update(['delivered_at' => Carbon::now()]);
            return Useful::ok();
        }
        return Useful::nok(['cards', 'شماره واردشده اشتباه است']);
    }

    //ADMIN
    public function index(Request $request)
    {
        $cards = Card::search($request)->with('user', function ($q) {
            $q->select('id', 'fname', 'lname', 'nid', 'birth', 'username')->withTrashed();
        })->paginate(20);
        $new = [];
        $n = 0;
        foreach ($cards as $card) {
            $user = $card->user;
            $new[$n]['id'] = $card->id;

            $col['name'] = [
                'title' => 'نام کاربر',
                'value' => $user->fname . ' ' . $user->lname,
                'addition' => '0' . $user->username
            ];

            $col['other'] = [
                'title' => 'کدملی',
                'value' => $user->nid,
                'addition' => $user->birth
            ];

            $col['number'] = [
                'title' => 'شماره کارت',
                'value' => $card->card_number,
                'addition' => 'سری: ' . $card->series
            ];

            $col['created_at'] = [
                'title' => 'تاریخ صدور',
                'value' => Edate::edateFromCarbon('j F Y', $card->created_at),
                // 'addition' => 'ساعت ' . Edate::edateFromCarbon('H', $user->created_at)
            ];

            $col['sent_at'] = [
                'title' => 'تاریخ ارسال',
                'value' => Edate::edateFromCarbon('j F Y', $card->sent_at),
                // 'addition' => 'ساعت ' . Edate::edateFromCarbon('H', $user->created_at)
            ];

            $col['delivered_at'] = [
                'title' => 'تاریخ تحویل',
                'value' => Edate::edateFromCarbon('j F Y', $card->delivered_at),
                // 'addition' => 'ساعت ' . Edate::edateFromCarbon('H', $user->created_at)
            ];

            if ($card->delivered_at) {
                $btn0 = [
                    'value' => 'شارژ',
                    'icon' => 'bolt',
                    'btn_color' => 'success',
                    'type' => 'modal',
                    'modal_name' => 'chargeModal',
                    'method' => 'get',
                    'endpoint' => '/salar/cards/' . $card->id,
                ];
            } else if ($card->sent_at) {
                $btn0 = [
                    'value' => 'تحویل داده شد؟',
                    'btn_color' => 'info',
                    'type' => 'confirm',
                    'method' => 'post',
                    'endpoint' => '/admin/cards/' . $card->id . '/delivered',
                ];
            } else {
                $btn0 = [
                    'value' => 'ارسال شد؟',
                    'btn_color' => 'warning',
                    'type' => 'confirm',
                    'method' => 'post',
                    'endpoint' => '/admin/cards/' . $card->id . '/sent',
                ];
            }
            $col['action'] = [
                'type' => 'button',
                'buttons' => [
                    $btn0,
                    [
                        'value' => 'ده شارژ اخیر',
                        'icon' => '',
                        'type' => 'modal',
                        'modal_name' => 'txModal',
                        'method' => 'get',
                        'endpoint' => '/admin/cards/' . $card->id . '/charge-history',
                    ],
                    [
                        'value' => 'گردش بن کارت',
                        'icon' => '',
                        'type' => 'modal',
                        'modal_name' => 'statementModal',
                        'method' => 'get',
                        'endpoint' => '/admin/cards/' . $card->id . '/statement',
                    ],
                    [
                        'value' => 'ویرایش',
                        'icon' => 'edit',
                        'type' => 'modal',
                        'modal_name' => 'editModal',
                        'method' => 'post',
                        'endpoint' => '/admin/cards/' . $card->id . '/edit',
                    ],
                    [
                        'value' => 'حذف',
                        'icon' => 'trash',
                        'css_classes' => 'text-danger',
                        'type' => 'confirm',
                        'method' => 'post',
                        'endpoint' => '/admin/cards/' . $card->id . '/delete',
                    ],
                ],
            ];
            $new[$n]['td'] = $col;
            $col = null;
            ++$n;
        }
        return Useful::recollect($cards, $new);
    }

    //ADMIN
    public function single(Request $request)
    {
        $id = $request->id;
        $card = Card::select('user_id', 'card_number')->whereId($id)->first();
        $user = User::info($card->user_id);
        $res = [
            'name' => $user->full_name,
            'mobile' => $user->mobile,
            'card_number' => $card->card_number,
        ];
        return Useful::ok($res);
    }

    //ADMIN
    public function edit(Request $request)
    {
        $id = $request->id;

        foreach (['card_number', 'series'] as $item) {
            $x[$item] = $request->$item;
        }

        if (!empty($request->sent_at)) $x['sent_at'] = Edate::j2carbon($request->sent_at);
        else $x['sent_at'] = null;

        if (!empty($request->delivered_at)) $x['delivered_at'] = Edate::j2carbon($request->delivered_at);
        else $x['delivered_at'] = null;

        if (Card::find($id)->update($x)) return Useful::ok();
        return Useful::nok();
    }

    //ADMIN
    public function edit_fields(Request $request)
    {
        $card = Card::where('id', $request->id)->first();

        if (!empty($card->sent_at)) {
            $sent_at = Edate::edateFromCarbon('Y-m-d', $card->sent_at);
            $sent_at_x = explode('-', $sent_at);
        } else $sent_at = null;

        if (!empty($card->delivered_at)) {
            $delivered_at = Edate::edateFromCarbon('Y-m-d', $card->delivered_at);
            $delivered_at_x = explode('-', $delivered_at);
        } else $delivered_at = null;

        $res = [
            [
                'section' => '',
                'size' => 'lg',
                'fields' => [
                    [
                        'label' => 'شماره کارت',
                        'v_model' => 'card_number',
                        'value' => $card->card_number,
                        'type' => 'text',
                    ],
                    [
                        'label' => 'سری کارت',
                        'v_model' => 'series',
                        'value' => $card->series,
                        'type' => 'text',
                    ],
                    [
                        'label' => 'تاریخ ارسال',
                        'v_model' => 'sent_at',
                        'value' => $sent_at,
                        'type' => 'date',
                        'values' => [
                            'y' => $sent_at ? $sent_at_x[0] : null,
                            'm' => $sent_at ? $sent_at_x[1] : null,
                            'd' => $sent_at ? $sent_at_x[2] : null,
                        ],
                    ],
                    [
                        'label' => 'تاریخ تحویل',
                        'v_model' => 'delivered_at',
                        'value' => $delivered_at,
                        'type' => 'date',
                        'values' => [
                            'y' => $delivered_at ? $delivered_at_x[0] : null,
                            'm' => $delivered_at ? $delivered_at_x[1] : null,
                            'd' => $delivered_at ? $delivered_at_x[2] : null,
                        ],
                    ],
                ],
            ],
        ];

        return Useful::ok($res);
    }

    //ADMIN
    public function charge_history(Request $request)
    {
        $charges = Charge::select(['id', 'amount', 'charged_at', 'status', 'type'])
            ->where('card_id', $request->id)->orderBy('id', 'desc')->limit(10)->get();
        if ($charges) return Useful::ok($charges);
        return Useful::ok([]);
    }

    //ADMIN //TODO
    public function statement(Request $request)
    {
        $base = Edate::edate('ymd');
        $txes = Bontx::where('card_id', $request->id)->orderBy('id', 'desc')->limit(30)->get();
        if ($txes) return Useful::ok($txes);
        return Useful::ok([]);
    }

    //ADMIN
    public function create(Request $request)
    {
        $card = Card::where('card_number', $request->card_number)->first();
        if ($card) return Useful::nok(['card_exists', 'این شماره کارت قبلاً ثبت شده است']);

        $user = User::where('username', substr($request->mobile, -10))->first();
        if ($user && Card::create([
            'card_number' => str_replace('-', '', $request->card_number),
            'user_id' => $user->id,
            'series' => $request->series,
        ])) return Useful::ok();
        else if (!$user) return Useful::nok(['user_doesnt_exists', 'کاربری با این شماره موبایل یافت نشد']);
        return Useful::nok(['dbc-c', 'خطا در ثبت قسطاکارت']);
    }

    //ADMIN
    public function delete(Request $request)
    {
        if (Card::whereId($request->id)->delete()) return Useful::ok();
        return Useful::nok();
    }

    //ADMIN
    public function delivered(Request $request)
    {
        if (Card::whereId($request->id)->update(['delivered_at' => Carbon::now()])) return Useful::ok();
        return Useful::nok();
    }

    //ADMIN
    public function sent(Request $request)
    {
        if (Card::whereId($request->id)->update(['sent_at' => Carbon::now()])) return Useful::ok();
        return Useful::nok();
    }

    public function cards_to_print_export(Request $request)
    {
        $series = $request->series;
        Order::whereIn('status', ['upload_secondary', 'check_secondary', 'wait_for_cheques', 'prepayment'])
            ->whereNull('series_card')->whereNull('shop_id')->update(['series_card' => $series]);
        Shop::where('status', 'active')->whereNull('series_card')->update(['series_card' => $series]);

        $orders = Order::select('id', 'user_id')->where('series_card', $series)->with([
            'user' => function ($q) {
                $q->select('id', 'fname', 'lname', 'nid', 'username', 'birth');
            }
        ])->get();

        $data[] = [
            'کد ملی',
            'نام',
            'نام خانوادگی',
            'شماره تلفن',
            'تاریخ تولد',
        ];

        foreach ($orders as $order) {
            $data[] = [
                $order->user->nid,
                $order->user->fname,
                $order->user->lname,
                '0' . $order->user->username,
                Useful::enum($order->user->birth, 'birthx'),
            ];
        }

        $shops = Shop::select('id', 'user_id')->where('series_card', $series)->with([
            'user' => function ($q) {
                $q->select('id', 'fname', 'lname', 'nid', 'username', 'birth');
            }
        ])->get();

        foreach ($shops as $shop) {
            $data[] = [
                $shop->user->nid,
                $shop->user->fname,
                $shop->user->lname,
                '0' . $shop->user->username,
                Useful::enum($shop->user->birth, 'birthx'),
            ];
        }

        $url = ExportController::excel($data, 'cards_' . $series);
        return Useful::ok(['url' => $url], ['action_type' => 'open_url']);
    }

    public function cards_to_submit_import(Request $request)
    {
        $series = $request->series;
        $file = $request->file;
        $now = Carbon::now();
        $uploaded = Storage::put(Doc::tmp_folder('cards'), $file);

        $rows = Excel::toArray(new cardsToImport, $uploaded)[0];
        $cards = [];
        foreach ($rows as $row) {
            $cards[] = [
                'card_number' => $row[3],
                'nid' => $row[5],
                'series' => $series,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        array_shift($cards);

        foreach ($cards as $card) $nid_list[] = $card['nid'];

        $users = User::select('id', 'nid')->whereIn('nid', $nid_list)->get();

        foreach ($cards as $key => $card) {
            $cards[$key]['user_id'] = $users->where('nid', $card['nid'])->first()->id;
            unset($cards[$key]['nid']);
        }

        DB::table('cards')->insert($cards);

        $shops = Shop::select('id', 'user_id', 'card_id')->whereStatus('active')->whereNull('card_id')->get();
        foreach ($shops as $shop) {
            $card = Card::select('id')->where('user_id', $shop->user_id)->orderBy('id', 'desc')->first();
            if ($card)
                $shop->update(['card_id' => $card->id]);
            $card = null;
        }

        return Useful::ok();
    }

    //SALAR
    public function extra_charge(Request $request)
    {
        $id = $request->id;
        $card_number = $request->card_number;
        $amount = $request->amount;
        // $amount = Edate::tr_num(str_replace(',', '', $amount));
        $card = Card::where(['card_number' => str_replace('-', '', $card_number), 'id' => $id])->first();
        if ($chargeRecord = Charge::create([
            'created_by' => Auth::user()->id,
            'card_id' => $card->id,
            'amount' => $amount,
            'type' => 'extra',
            'deposit' => Finnotech::$DEPOSIT_ID
        ])) {
            $res = Finnotech::bonCharge($card_number, $amount * 10, $chargeRecord->id);
            $result = json_decode($res, true);
            if ($result['status'] && $result['status'] == 'DONE') {
                $chargeRecord->update([
                    'tracker' => $result['result']['tracker'],
                    'track_id' => $result['trackId'],
                    'charged_at' => Carbon::now(),
                    'status' => 1,
                ]);

                //Resolve charges
                $extras = Charge::where('type', 'extra')->whereNotNull('charged_at')->whereNull('order_id')
                    ->where('amount', '>', 10000)->with(['card.user'])->get();

                foreach ($extras as $extra) {
                    $orders = Order::where('user_id', $extra->card->user_id)->orderBy('id', 'desc')->get();
                    if (!$orders) continue;
                    foreach ($orders as $order) {
                        $payment = Payment::where(['type' => 'extra', 'order_id' => $order->id, 'amount' => $extra->amount])
                            ->successful()->first();

                        if ($payment) {
                            $extra->update([
                                'order_id' => $order->id,
                                'payment_id' => $payment->id,
                            ]);
                            continue;
                        }
                    }
                }

                return Useful::ok();
            }
            $chargeRecord->update(['comment' => $res]);
            return Useful::nok($res);
        }
        return Useful::nok(['dbcharge', 'خطا در ارتباط با']);
    }
}

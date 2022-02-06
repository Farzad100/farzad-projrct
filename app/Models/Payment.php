<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = ['meta' => 'array'];

    public static $gateway = 'zarinpal'; //zarinpal | payping

    public static $types = [
        'prepayment' => 'پیش پرداخت',
        'ghest' => 'پرداخت قسط',
        'extra' => 'شارژ اضافی',
        'credit' => 'خرید آنلاین',
        'inquiry' => 'هزینه استعلام',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }

    public function scopeUserId($query, $user_id)
    {
        return $query->whereHas('order', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        });
    }

    public function scopeOrgan($query, $organ_id)
    {
        return $query->whereHas('order', function ($query) use ($organ_id) {
            $query->where('organ_id', $organ_id);
        });
    }

    public function scopeShop($query, $shop_id)
    {
        return $query->whereHas('order', function ($query) use ($shop_id) {
            $query->where('shop_id', $shop_id);
        });
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 100);
    }

    public function scopeGhest($query)
    {
        return $query->where('type', 'ghest');
    }

    public function scopePrepayment($query)
    {
        return $query->where('type', 'prepayment');
    }

    public function scopeCredit($query)
    {
        return $query->where('type', 'credit');
    }

    public function scopeWhereCase($query, $col, $value)
    {
        return $query->whereRaw("BINARY `$col` like ?", $value);
    }

    public static function sync_extra_charges()
    {
        $payments = Payment::select('id', 'order_id', 'type', 'amount', 'paid_at', 'charged_at')
            ->where('type', 'extra')->successful()->with([
                'order' => function ($q) {
                    $q->select('id', 'user_id', 'oid', 'amount', 'shop_id');
                }
            ])->get();

        foreach ($payments as $payment) {
            $charge = Charge::where('amount', $payment->amount)->whereNotNull('charged_at')
                ->user($payment->order->user_id)->first();
            if ($charge) {
                $charge->update(['type' => 'extra', 'payment_id' => $payment->id, 'order_id' => $payment->order_id]);
            }
        }

        return 1;
    }

    public static function sync_orders_charges()
    {
        $orders = Order::select('id', 'user_id', 'oid', 'amount', 'shop_id', 'charged_at')
            ->whereNotNull('charged_at')
            ->whereDoesntHave('charges')
            ->whereDate('charged_at', '>', Carbon::now()->subMonths(7))
            ->with([
                'shop' => function ($q) {
                    $q->select('id', 'user_id', 'name');
                },
                'charges' => function ($q) {
                    $q->select('id', 'card_id', 'order_id', 'amount');
                },
            ])->orderBy('charged_at', 'asc')->get();
        return $orders;
        foreach ($orders as $order) {
            $charges = Charge::select('id', 'amount', 'charged_at')->where('amount', $order->amount)
                ->whereNotNull('charged_at')
                ->whereNotNull('charged_at')
                ->whereDate('charged_at', Carbon::parse($order->charged_at))
                ->userId($order->shop_id ? $order->shop->user_id : $order->user_id)
                ->orderBy('id', 'asc')->get();

            if (count($charges) == 0) continue;
            else if (count($charges) == 1) {
                $charges[0]->update(['type' => 'charge', 'order_id' => $order->id]);
            } else {
                $charges[0]->update(['type' => 'charge', 'order_id' => $order->id]);
                $x[] = $order->id . ' x ' . count($charges) . ' x ' . $charges[0]->id . ' x ' . $charges[1]->id;
            }
        }

        return $x;
    }

    public static function sync_orders_charges_2()
    {
        $charges = Charge::select('id', 'amount', 'charged_at', 'card_id')
            ->whereNotNull('charged_at')
            ->whereNull('type')
            ->where('amount', '>', 10000)
            ->whereNull('order_id')
            ->with([
                'card' => function ($q) {
                    $q->select('id', 'user_id');
                }, 'card.user' => function ($q) {
                    $q->select('id', 'fname', 'lname', 'username', 'nid');
                },
            ])->orderBy('id', 'asc')->get();

        foreach ($charges as $charge) {
            $orders = Order::select('id', 'user_id', 'shop_id', 'charged_at')
                ->whereNotNull('charged_at')
                ->whereDate('charged_at', $charge->charged_at)
                ->where('amount', $charge->amount)
                ->orderBy('id', 'asc')->get();

            if ($orders->count() == 1) {
                $charge->orders = $orders;
            }
        }

        return $charges;
    }

    public static function sync_orders_extracharges()
    {
        $charges = Charge::where('type', '!=', 'charge')->whereBetween('amount', [10000, 10000000])->with([
            'card' => function ($q) {
                $q->withTrashed();
            },
            'card.user' => function ($q) {
                $q->withTrashed();
            }
        ])->get();

        $x = [];
        foreach ($charges as $charge) {
            $payment = Payment::userId($charge->card->user->id)
                ->where(['type' => 'extra', 'amount' => $charge->amount])
                ->whereBetween('paid_at', [Carbon::parse($charge->charged_at)->subDay(5), Carbon::parse($charge->charged_at)->addHours(6)])
                ->first();

            if ($payment) {
                $charge->update(['type' => 'extra', 'order_id' => $payment->order_id, 'payment_id' => $payment->id]);
                $x[] = $charge->id;
            }

            $payment = null;
        }

        return count($x) . ' x ' . Payment::where(['type' => 'extra'])->whereNotNull('paid_at')->count();
    }

    public static function sync_orders_charges_3()
    {
        $charges = Charge::select('id', 'amount', 'card_id', 'charged_at')
            ->where('type', '!=', 'extra')
            ->whereNotNull('charged_at')
            ->whereNull('order_id')
            ->with([
                'card' => function ($q) {
                    $q->select('id', 'user_id', 'card_number')->withTrashed();
                }
            ])->orderBy('charged_at', 'asc')->get();

        $ch0 = [];
        $ch1 = [];
        $ch2 = [];
        $ch3 = [];
        $ch4 = [];
        foreach ($charges as $charge) {
            $userx_id = $charge->card->user_id;
            $orders = Order::select('id', 'oid','user_id', 'shop_id', 'charged_at')
                ->whereNull('charge_id')
                ->where('amount', $charge->amount)
                // ->where('user_id', $userx_id)
                ->whereBetween('charged_at', [Carbon::parse($charge->charged_at), Carbon::parse($charge->charged_at)->addHours(12)])
                ->whereNotNull('shop_id')
                ->with([
                    'shop' => function ($q) use ($userx_id) {
                        $q->select('id', 'user_id','name')->where('user_id', $userx_id)->withTrashed();
                    },
                ])
                ->orderBy('charged_at', 'asc')->get();

            if (count($orders) > 0) {
                if (count($orders) == 1) {
                    $orders[0]->update(['charge_id' => $charge->id]);
                    $charge->update(['type' => 'charge', 'order_id' => $orders[0]->id]);
                }
                $charge->order_user_id = $orders[0]->user_id;
                $charge->order_charged_at = $orders[0]->charged_at;
                if ($orders[0]->shop_id) {
                    if (!$orders[0]->shop) continue;
                    $charge->order_shopcard_id = Card::select('id')->where('user_id', $orders[0]->shop->user_id)->withTrashed()->first()->id;
                    $charge->order_shopuser_id = $orders[0]->shop->user_id;
                    $charge->order_shop_id = $orders[0]->shop_id;
                    $charge->oid = $orders[0]->oid;
                    $charge->shopname = $orders[0]->shop->name;
                }
            }

            if (count($orders) == 0) $ch0[] = $charge;
            else if (count($orders) == 1) {
                $ch1[] = $charge;
            } else if (count($orders) == 2) {
                $ch2[] = $charge;
            } else if (count($orders) == 3) {
                $ch3[] = $charge;
            } else {
                $ch4[] = $charge;
            }
            $orders = [];
        }

        return [
            'ch1-' . count($ch1) => $ch1,
            'ch2-' . count($ch2) => $ch2,
            'ch3-' . count($ch3) => $ch3,
            'ch4-' . count($ch4) => $ch4,
            'ch0-' . count($ch0) => $ch0,
        ];


        $orders = Order::select('id', 'user_id', 'oid', 'amount', 'shop_id', 'charge_id', 'charged_at')
            ->whereNull('charge_id')
            ->searchLike('oid', 'GHC')
            ->whereNotNull('charged_at')
            ->where('charged_at', '>=', Carbon::now()->subMonths(4))
            ->with([
                'shop' => function ($q) {
                    $q->select('id', 'user_id', 'name');
                }
            ])->orderBy('charged_at', 'asc')->get();

        /* foreach ($orders as $order) {
            $charges = Charge::select('id', 'amount', 'charged_at', 'card_id')
                ->where('amount', $order->amount)
                ->where('order_id', $order->id)->orderBy('charged_at', 'asc')
                ->with([
                    'card' => function ($q) {
                        $q->select('id', 'user_id', 'card_number');
                    },
                    'card.user' => function ($q) {
                        $q->select('id', 'fname', 'lname');
                    }
                ])
                ->get();
            if (count($charges) > 0) {
                $order->charge_time = $charges[0]->charged_at;
                $order->charge_code = $charges[0]->id;
                $order->card_number = $charges[0]->card;
                $uc = Card::select('id','user_id', 'card_number', 'delivered_at')->where('user_id', $order->user_id)
                    ->with([
                        'user' => function ($q) {
                            $q->select('id', 'fname', 'lname');
                        }
                    ])->get();
                $order->user_card = $uc;
                $x[] = $order;
            }
        }
        return $x; */

        $ch0 = [];
        $ch1 = [];
        $ch2 = [];
        $ch3 = [];
        $ch4 = [];
        foreach ($orders as $order) {
            $charges = Charge::select('id', 'amount', 'charged_at')
                ->where('amount', $order->amount)
                ->whereBetween('charged_at', [Carbon::parse($order->charged_at)->subHours(5), Carbon::parse($order->charged_at)])
                ->userId($order->shop_id ? $order->shop->user_id : $order->user_id)
                ->orderBy('charged_at', 'asc')->get();

            if (count($charges) == 0) $ch0[] = $order->id;
            else if (count($charges) == 1) {
                // $charges[0]->update(['type' => 'charge', 'order_id' => $order->id]);
                $order->update(['charge_id' => $charges[0]->id]);
                $ch1[] = $order->id;
            } else if (count($charges) == 2) {
                $ch2[] = $order->id;
            } else if (count($charges) == 3) {
                $ch3[] = $order->id;
            } else {
                $ch4[] = $order;
            }
            $charges = [];
        }

        return [
            'ch0-' . count($ch0) => $ch0,
            'ch1-' . count($ch1) => $ch1,
            'ch2-' . count($ch2) => $ch2,
            'ch3-' . count($ch3) => $ch3,
            'ch4-' . count($ch4) => $ch4,
        ];
    }
}

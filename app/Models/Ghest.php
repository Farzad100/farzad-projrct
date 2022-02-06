<?php

namespace App\Models;

use App\Models\Edate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ghest extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public static $status = [
        'passed' => 'پاس شده',
        'expired' => 'پاس نشده',
        'today' => 'سررسید امروز',
        'near' => 'نزدیک به سررسید',
        'future' => 'آتی',
        'backed' => 'برگشت خورد',
        'extended' => 'مهلت مجدد',
    ];

    public static function status($ghest)
    {
        $now = time();
        $gts = Carbon::parse($ghest->ghest_date)->timestamp;
        if ($ghest->passed_at) {
            $status = 'passed';
            $class = 'success';
        } else if ($ghest->backed_at) {
            $status = 'backed';
            $class = 'danger';
        } else if ($gts < $now && $now - $gts < 40000) {
            $status = 'today';
            $class = 'info';
        } else if ($gts < $now) {
            $status = 'expired';
            $class = 'danger';
        } else if ($gts > $now && $gts - $now < (3 * 86400)) {
            $status = 'near';
            $class = 'warning';
        } else {
            $status = 'future';
            $class = 'secondary';
        }
        return (object)[
            'name' => $status,
            'farsi' => self::$status[$status],
            'class' => $class,
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function cheque()
    {
        return $this->belongsTo(Cheque::class, 'cheque_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function scopeUser($query, $user_id)
    {
        return $query->whereHas('order', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        });
    }

    public function scopeOrgan($query, $organ_id)
    {
        if (is_array($organ_id)) {
            return $query->whereHas('order', function ($query) use ($organ_id) {
                $query->whereIn('organ_id', $organ_id);
            });
        }
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

    public function scopePassed($query)
    {
        return $query->whereNotNull('passed_at');
    }

    public function scopeNotPassed($query)
    {
        return $query->whereNull('passed_at');
    }

    public function scopeBacked($query, $days = null)
    {
        if ($days) return $query->whereNotNull('backed_at')->whereDate('backed_at', '<', Carbon::now()->subDays($days));
        return $query->whereNotNull('backed_at');
    }

    public function scopeExpired($query, $days = 0)
    {
        return $query->whereNull('passed_at')->whereDate('ghest_date', '<', Carbon::now()->subDays($days));
        return $query->whereNull('passed_at')->searchDate('ghest_date', null, Edate::edateFromCarbon('Y-m-d', Carbon::now()->subDays($days)), 'shamsi'); //FIXME:
    }

    public function scopeFuture($query)
    {
        return $query->where('ghest_date', '>', Carbon::now());
    }

    public function scopeNear($query)
    {
        return $query->searchDate('ghest_date', Edate::edate('Y-m-d', time()), Edate::edate('Y-m-d', time() + 3 * 86400), 'shamsi');
    }

    public function scopeToday($query)
    {
        return $query->searchDate('ghest_date', Edate::edate('Y-m-d', time()), Edate::edate('Y-m-d', time()), 'shamsi');
    }

    public function scopeTomorrow($query)
    {
        return $query->searchDate('ghest_date', Edate::edate('Y-m-d'), Edate::edate('Y-m-d', time() + 86400), 'shamsi');
    }

    public function scopeSoClose($query)
    {
        return $query->whereDate('ghest_date', Carbon::now()->addDays(3));
    }

    public function scopeSearch($query, $request, $role = null)
    {
        if (empty($request->sort)) {
            $sort_by = 'ghest_date';
            $sort_order = 'desc';
        } else {
            $sort = explode('-', $request->sort);
            $sort_by = $sort[0];
            $sort_order = $sort[1];
        }
        $x = $query
            ->searchDate('ghest_date', $request->from_date, $request->to_date, 'shamsi')
            ->searchStatus($request->status)
            ->searchStatus($request->fast_status)
            ->searchExact('type', $request->payback_type)
            ->searchAmount($request->min, $request->max)
            ->searchLike('oid', $request->oid, 'order')
            ->searchLike('username', substr($request->mobile, -10), 'order.user')
            ->searchLike('fname', $request->fname, 'order.user')
            ->searchLike('lname', $request->lname, 'order.user')
            ->searchLike('nid', $request->nid, 'order.user')
            ->searchExact('series', $request->series)
            ->orderBy($sort_by, $sort_order);
        if ($role == 'admin') {
            return $x
                ->searchOrgan($request->organ)
                ->searchLike('name', $request->shop, 'order.shop');
        }
        return $x;
    }

    public function scopeSearchStatus($query, $status)
    {
        if (empty($status)) return $query;
        switch ($status) {
            case 'passed':
                return $query->passed();

            case 'expired':
                return $query->expired();

            case 'near':
                return $query->near();

            case 'today':
                return $query->today();

            case 'future':
                return $query->future();

            default:
                break;
        }
        return $query;
    }

    public function scopeSearchAmount($query, $min = null, $max = null)
    {
        if (empty($min) && empty($max)) {
            return $query;
        } else if (empty($min)) {
            return $query->where('amount', '<', (int)$max);
        } else if (empty($max)) {
            return $query->where('amount', '>', (int)$min);
        } else {
            return $query->whereBetween('amount', [(int)$min, (int)$max]);
        }
    }

    public function scopeSearchDate($query, $type, $fromDate = null, $toDate = null, $format = 'j')
    {
        $f = ($fromDate && strlen($fromDate) > 6);
        $t = ($toDate && strlen($toDate) > 6);

        if ($f) {
            $fromPart = explode('-', $fromDate);
            $fromTS = Edate::jmktime(0, 0, 1, $fromPart[1], $fromPart[2], $fromPart[0]);
            $from = Carbon::createFromTimestamp($fromTS);
        }
        if ($t) {
            $toPart = explode('-', $toDate);
            $toTS = Edate::jmktime(23, 59, 59, $toPart[1], $toPart[2], $toPart[0]);
            $to = Carbon::createFromTimestamp($toTS);
        }

        if (!$f && !$t) {
            return $query;
        } else if (!$f) {
            if ($format == 'shamsi')
                return $query->where('shamsi', '<=', Useful::enum($toDate, 'jdatex'));
            return $query->where($type, '<=', $to);
        } else if (!$t) {
            if ($format == 'shamsi')
                return $query->where('shamsi', '>=', Useful::enum($fromDate, 'jdatex'));
            return $query->where($type, '>=', $from);
        } else {
            if ($format == 'shamsi')
                return $query->whereBetween('shamsi', [Useful::enum($fromDate, 'jdatex'), Useful::enum($toDate, 'jdatex')]);
            return $query->whereBetween($type, [$from, $to]);
        }
    }

    public function scopeSearchOrgan($query, $organ = null)
    {
        if (empty($organ)) return $query;
        if (is_numeric($organ)) {
            return $query->whereHas('order.organ', function ($query) use ($organ) {
                $query->where('code', $organ);
            });
        }
        return $query->whereHas('order.organ', function ($query) use ($organ) {
            $query->where('name', 'like', '%' . $organ . '%')->orWhere('fame', 'like', '%' . $organ . '%');
        });
        return false;
    }

    public function scopeSearchExact($query, $type, $str, $rel = null)
    {
        if (empty($str)) return $query;
        if ($rel) {
            return $query->whereHas($rel, function ($query) use ($str, $type) {
                $query->where($type,  $str);
            });
        }
        return $query->where($type, $str);
    }

    public function scopeSearchLike($query, $type, $str, $rel = null)
    {
        if (empty($str)) return $query;
        if ($rel) {
            return $query->whereHas($rel, function ($query) use ($str, $type) {
                $query->where($type, 'like', '%' . $str . '%');
            });
        }
        return $query->where($type, 'like', '%' . $str . '%');
    }

    public static function recheck_orders()
    {
        $orders = Order::successful()->where('payback_type', 'epay')->whereDoesnthave('ghests')->orderBy('secondary_uploaded_at', 'desc')->get();

        foreach ($orders as $order) {
            $chs = Order::cheques_info($order)['cheques'];
            $arr = [];
            foreach ($chs as $ch) {
                $arr[] = [
                    'order_id' => $order->id,
                    'amount' => $ch['amount'],
                    'ghest_date' => Edate::j2carbon($ch['date']),
                    'shamsi' => str_replace('-', '', $ch['date']),
                    'type' => $order->payback_type,
                ];
            }
            Ghest::insert($arr);
        }

        return true;
    }

    public static function remove_residual_ghests()
    {
        $orders = Order::select('id')->worthless()->whereHas('ghests')->get();
        $arr = [];
        foreach ($orders as $order) {
            $arr[] = $order->id;
        }
        if (count($arr) > 0) Ghest::whereIn('order_id', $arr)->delete();

        return count($arr);
    }
}

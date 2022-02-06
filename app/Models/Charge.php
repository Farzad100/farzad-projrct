<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function scopeUserId($query, $user_id)
    {
        return $query->whereHas('card', function ($query) use ($user_id) {
            $query->where('user_id',  $user_id);
        });
    }

    public function scopeShopId($query, $shop_id)
    {
        return $query->whereHas('order', function ($query) use ($shop_id) {
            $query->where('shop_id',  $shop_id);
        });
    }

    public function scopeSearch($query, $request, $role = null)
    {
        if (empty($request->sort)) {
            $sort_by = 'id';
            $sort_order = 'desc';
        } else {
            $sort = explode('-', $request->sort);
            $sort_by = $sort[0];
            $sort_order = $sort[1];
        }
        if ($request->fast_status) {
            $x = $query->searchStatus($request->fast_status);
        } else {
            $x = $query
                ->searchDate($request->date_type, $request->from_date, $request->to_date)
                ->searchStatus($request->status)
                ->searchTrait($request->trait)
                ->searchAmount($request->min, $request->max)
                ->searchLike('oid', $request->oid)
                ->searchExact('series', $request->series)
                ->searchExact('series_card', $request->series_card)
                ->searchExact('amount', $request->amount)
                ->searchLike('product', $request->name)
                ->searchLike('username', substr($request->mobile, -10), 'user')
                ->searchLike('fname', $request->fname, 'user')
                ->searchLike('lname', $request->lname, 'user')
                ->searchLike('nid', $request->nid, 'user');
            if ($role == 'admin') {
                $x
                    ->searchExact('series', $request->series)
                    ->searchOrgan($request->organ)
                    ->searchLike('name', $request->shop, 'shop');
            }
        }
        return $x->orderBy($sort_by, $sort_order);
    }

    public function scopeSearchAmount($query, $min = null, $max = null)
    {
        if (empty($min) && empty($max)) {
            return $query;
        } else if (empty($min)) {
            return $query->where('amount', '<=', (int)$max);
        } else if (empty($max)) {
            return $query->where('amount', '>=', (int)$min);
        } else {
            return $query->whereBetween('amount', [(int)$min, (int)$max]);
        }
    }

    public function scopeSearchOrgan($query, $organ = null)
    {
        if (empty($organ)) return $query;
        if (is_numeric($organ)) {
            return $query->whereHas('organ', function ($query) use ($organ) {
                $query->where('code', $organ);
            });
        }
        return $query->whereHas('organ', function ($query) use ($organ) {
            $query->where('name', 'like', '%' . $organ . '%')->orWhere('fame', 'like', '%' . $organ . '%');
        });
        return false;
    }

    public function scopeSearchDate($query, $type, $fromDate = null, $toDate = null, $format = 'j')
    {
        $fromDate && strlen($fromDate) > 6 ? $f = true : $f = false;
        $toDate && strlen($toDate) > 6 ? $t = true : $t = false;

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
            return $query->where($type, '<=', $to);
        } else if (!$t) {
            return $query->where($type, '>=', $from);
        } else {
            return $query->whereBetween($type, [$from, $to]);
        }
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
}

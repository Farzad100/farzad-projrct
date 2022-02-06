<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use SoftDeletes;



    protected $guarded = ['id'];

    protected $casts = ['meta' => 'array'];

    public function payments()
    {
        return $this->hasMany(Payment::class, 'discount_id');
    }

    public static function status_fixer($discount, $used = 0)
    {
        if ($discount->limit == $used && $discount->mobile) {
            if ($discount->is_active == 1) $discount->update(['is_active' => 0]);
            $status = 'used';
        } else if ($discount->limit <= $used) {
            if ($discount->is_active == 1) $discount->update(['is_active' => 0]);
            $status = 'limit_exceeded';
        } else if (Carbon::parse($discount->expired_at)->timestamp < time()) {
            if ($discount->is_active == 1) $discount->update(['is_active' => 0]);
            $status = 'expired';
        } else if ($discount->is_active == 0) $status = 'inactive';
        else $status = 'active';
        return $status;
    }

    public static $status = [
        '' => [
            'farsi' => '',
            'class' => ''
        ],
        'used' => [
            'farsi' => 'استفاده شد',
            'class' => 'secondary'
        ],
        'inactive' => [
            'farsi' => 'غیرفعال',
            'class' => 'secondary'
        ],
        'limit_exceeded' => [
            'farsi' => 'اتمام ظرفیت',
            'class' => 'danger'
        ],
        'expired' => [
            'farsi' => 'منقضی',
            'class' => 'warning'
        ],
        'active' => [
            'farsi' => 'فعال',
            'class' => 'success'
        ],
    ];

    public function scopeSearch($query, $request, $role = null)
    {
        $x = $query
            ->searchDate('expired_at', $request->from_date, $request->to_date)
            ->searchAmount($request->min, $request->max)
            ->searchLike('code', $request->fname)
            ->searchStatus($request->status)
            ->searchStatus($request->fast_status);
        return $x;
    }

    public function scopeSearchStatus($query, $status = null)
    {
        if (empty($status) || $status == 'all') return $query;
        switch ($status) {
            case 'inactive':
                return $query->where('is_active', 0)->orWhere('expired_at', '<', Carbon::now());

            case 'active':
                return $query->where('is_active', 1);

            case 'used':
                return $query->whereNotNull('mobile')->where('is_active', 0);

            case 'expired':
                return $query->where('expired_at', '>', Carbon::now());

            case 'limit':
                return $query->whereHas('payments'); //TODO:

            default:
                return $query->where('status', $status);
        }
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

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organ extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = ['meta' => 'array'];

    protected $appends = ['state_name'];

    public function getStateNameAttribute()
    {
        return Address::state_name("{$this->state}");
    }

    public static function title($id)
    {
        $x = Organ::select('name', 'fame')->where('id', $id)->first();
        if (!$x) return '';
        if ($x->fame) return $x->fame;
        else if ($x->name) return $x->name;
        else return '';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function docs()
    {
        return $this->hasMany(Doc::class, 'organ_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'organ_id');
    }

    public function orders_inprogress()
    {
        return $this->hasMany(Order::class, 'organ_id')->inprogress();
    }

    public function orders_worthy()
    {
        return $this->hasMany(Order::class, 'organ_id')->worthy();
    }

    public function orders_worthless()
    {
        return $this->hasMany(Order::class, 'organ_id')->worthless();
    }

    public function orders_successful()
    {
        return $this->hasMany(Order::class, 'organ_id')->successful();
    }

    public static $types = [
        'i' => 'معرفی',
        'g' => 'ضمانت',
    ];

    public static $levels = [
        '1' => 'عادی',
        '2' => 'ویژه',
    ];

    public static $payback_types = [
        'cheque' => 'چک قسط معمولی',
        'gcheck' => 'چک ضمانت',
        'sefte' => 'سفته ضمانت',
        'special' => 'خاص',
    ];

    public static $employees = [
        '20' => 'کمتر از 20 نفر',
        '100' => 'بین 20 تا 100 نفر',
        '500' => 'بین 100 تا 500 نفر',
        '2500' => 'بین 500 تا 2500 نفر',
        '10000' => 'بیشتر از 2500 نفر',
    ];

    public static $age = [
        '3' => 'کمتر از 3 سال',
        '10' => 'بین 3 تا 10 سال',
        '100' => 'بیشتر از 10 سال',
    ];

    public static $positions = [
        'cao' => 'رئیس هیئت مدیره',
        'ceo' => 'مدیرعامل',
        'cfo' => 'مدیر/معاون امور مالی',
        'chro' => 'مدیر/معاون منابع انسانی',
        'hr' => 'کارشناس منابع انسانی/امور مالی',
    ];

    public static $status = [
        '' => [
            'farsi' => '',
            'class' => ''
        ],
        'agreement' => [
            'farsi' => 'در انتظار امضاء قرارداد',
            'class' => 'warning'
        ],
        'uploading' => [
            'farsi' => 'در انتظار بارگذاری مدارک',
            'class' => 'warning'
        ],
        'docs_uploaded' => [
            'farsi' => 'در حال بررسی مدارک',
            'class' => 'info'
        ],
        'pending' => [
            'farsi' => 'در انتظار بررسی اولیه',
            'class' => 'info'
        ],
        'final' => [
            'farsi' => 'در حال بررسی نهایی',
            'class' => 'info'
        ],
        'active' => [
            'farsi' => 'فعال',
            'class' => 'success'
        ],
        'inactive' => [
            'farsi' => 'غیرفعال',
            'class' => 'secondary'
        ],
        'blocked' => [
            'farsi' => 'غیرمجاز',
            'class' => 'danger'
        ],
        'temp' => [
            'farsi' => 'موقت',
            'class' => 'secondary'
        ]
    ];

    public static function mobile($organ_id)
    {
        $organ = Organ::select('id', 'user_id')->whereId($organ_id)->with(['user' => function ($q) {
            $q->select('id', 'username');
        }])->first();
        return '0' . $organ->user->username;
    }

    public function scopeSearchStatus($query, $str = null)
    {
        if (empty($str) || $str == 'all') return $query;
        return $query->where('status', $str);
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
        $x = $query
            ->searchDate($request->date_type, $request->from_date, $request->to_date)
            ->searchStatus($request->status)
            ->searchStatus($request->fast_status)
            ->searchExact('type', $request->type)
            ->searchLike('name', $request->name)
            ->searchLike('username', substr($request->mobile, -10), 'user')
            ->searchLike('fname', $request->fname, 'user')
            ->searchLike('lname', $request->lname, 'user')
            ->searchLike('nid', $request->nid, 'user')
            ->orderBy($sort_by, $sort_order);
        return $x;
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

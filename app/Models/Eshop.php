<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Eshop extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $appends = ['state_name'];

    public function getStateNameAttribute()
    {
        return Address::state_name("{$this->state}");
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function card()
    {
        return $this->belongsTo(Card::class, 'user_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function docs()
    {
        return $this->hasMany(Doc::class, 'eshop_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'eshop_id');
    }

    public function orders_inprogress()
    {
        return $this->hasMany(Order::class, 'eshop_id')->inprogress();
    }

    public function orders_worthy()
    {
        return $this->hasMany(Order::class, 'eshop_id')->worthy();
    }

    public function orders_worthless()
    {
        return $this->hasMany(Order::class, 'eshop_id')->worthless();
    }

    public function orders_successful()
    {
        return $this->hasMany(Order::class, 'shop_id')->successful();
    }

    public static $types = [
        'online' => 'اینترنتی',
        'offline' => 'فیزیکی',
    ];

    public static $categories = [
        'digital' => 'کالای دیجیتال',
        'home' => 'خانه و آشپزخانه',
        'cloth' => 'مد و پوشاک',
        'travel' => 'گردشگری',
        'jewel' => 'سکه، طلا و جواهرات',
        'beauty' => 'سلامت و زیبایی',
        'service' => 'ابزار و خدمات',
        'other' => 'سایر موارد',
    ];

    public static function list_categories()
    {
        $arr = [];
        $cats = self::$categories;
        foreach ($cats as $index => $label) {
            array_push($arr, ['label' => $label, 'value' => $index]);
        }
        return $arr;
    }

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
        'verification' => [
            'farsi' => 'احراز اولیه',
            'class' => 'warning'
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
        ]
    ];

    public static function mobile($eshop_id)
    {
        $shop = Eshop::select('id', 'user_id')->whereId($eshop_id)->with(['user' => function ($q) {
            $q->select('id', 'username');
        }])->first();
        return '0' . $shop->user->username;
    }

    public static function title($id)
    {
        $x = Eshop::select('name')->where('id', $id)->first();
        if (!$x) return '';
        else if ($x->name) return $x->name;
        else return '';
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

        if (!$request->date_type) {
            if ($request->status) {
                switch ($request->status) {
                    case 'active':
                        $request->date_type = 'start_at';
                        break;
                    case 'uploading':
                        $request->date_type = 'agreed_at';
                        break;
                    case 'docs_uploaded':
                        $request->date_type = 'docs_uploaded_at';
                        break;
                    case 'final':
                        $request->date_type = 'docs_accepted_at';
                        break;
                    default:
                        $request->date_type = 'created_at';
                        break;
                }
            } else {
                $request->date_type = 'created_at';
            }
        }

        $x = $query
            ->searchDate($request->date_type, $request->from_date, $request->to_date)
            ->searchStatus($request->status)
            ->searchStatus($request->fast_status)
            ->searchExact('type', $request->type)
            ->searchLike('domain', $request->domain)
            ->searchExact('category', $request->category)
            ->searchLike('name', $request->name)
            ->searchLike('username', substr($request->mobile, -10), 'user')
            ->searchLike('fname', $request->fname, 'user')
            ->searchLike('lname', $request->lname, 'user')
            ->searchLike('nid', $request->nid, 'user')
            ->searchLike('utm_source', $request->utm_source, 'user')
            ->searchLike('utm_medium', $request->utm_medium, 'user')
            ->searchLike('utm_campaign', $request->utm_campaign, 'user')
            ->searchLike('utm_content', $request->utm_content, 'user')
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

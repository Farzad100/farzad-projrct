<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Card extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function charges()
    {
        return $this->hasMany(Charge::class, 'card_id', 'id');
    }

    public static function ghestacard($user_id)
    {
        $ghestacard = Card::where('user_id', $user_id)->orderBy('id', 'desc')->first();
        if (!$ghestacard) {
            $card['status'] = 'printing';
            $card['status_farsi'] = 'در انتظار صدور';
            $card['what_to_do'] = 'قسطاکارت شما در صف صدور توسط بانک است و بمحض دریافت توسط کارشناسان قسطا برای شما ارسال خواهدشد.';
        } else {
            if ($ghestacard->delivered_at) {
                $card['status'] = 'delivered';
                $card['status_farsi'] = 'تحویل داده شده';
                $card['delivered_at'] = $ghestacard->delivered_at;
                $card['card_number'] = $ghestacard->card_number;
            } else if ($ghestacard->sent_at) {
                $card['status'] = 'sent';
                $card['status_farsi'] = 'ارسال شده';
                $card['created_at'] = $ghestacard->created_at;
                $card['sent_at'] = $ghestacard->sent_at;
                $card['card_number'] = substr($ghestacard->card_number, 0, -4) . 'xxxx';
                $card['what_to_do'] = '';
            } else {
                $card['status'] = 'printed';
                $card['status_farsi'] = 'صادر شده';
                $card['created_at'] = $ghestacard->created_at;
                $card['sent_at'] = $ghestacard->sent_at;
                $card['printed_at'] = $ghestacard->created_at;
                $card['card_number'] = substr($ghestacard->card_number, 0, -4) . 'xxxx';
                $address = Address::select('state')->where('user_id', $user_id)->where('type', 'home')->orderBy('id', 'desc')->first();
                if ($address && $address->state != 7)
                    $card['what_to_do'] = 'شما می توانید با هماهنگی قبلی قسطاکارت خود را حضوری یا با پیک دریافت نمایید.';
                else
                    $card['what_to_do'] = 'قسطاکارت در اولین فرصت، از طریق پست به آدرس منزل شما ارسال خواهدشد.';
            }
            $card['series'] = $ghestacard->series;
        }
        return $card;
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
            ->searchLike('username', substr($request->mobile, -10), 'user')
            ->searchLike('fname', $request->fname, 'user')
            ->searchLike('lname', $request->lname, 'user')
            ->searchLike('nid', $request->nid, 'user')
            ->searchExact('series', $request->series)
            ->searchLike('card_number', str_replace('-', '', $request->card_number))
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

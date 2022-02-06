<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Credit extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = ['meta' => 'array'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function scopeDone($query)
    {
        return $query->whereNotNull('done_at');
    }

    public function scopePaid($query)
    {
        return $query->whereNotNull('paid_at');
    }

    public function scopeWhereCase($query, $col, $value)
    {
        return $query->whereRaw("BINARY `$col` like ?", $value);
    }
}

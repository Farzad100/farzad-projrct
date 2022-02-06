<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cheque extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = ['meta' => 'array', 'reason' => 'array'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}

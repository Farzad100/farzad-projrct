<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = ['meta' => 'array'];

    public const types = [
        'inquiry' => 'استعلام‌ها',
        'inquiry_bank' => 'استعلام بانکی',
        'inquiry_sim' => 'استعلام سیمکارت',
        'inquiry_reg' => 'استعلام هویتی',
        'prepayment' => 'پیش‌پرداخت',
        'extra' => 'شارژ اضافی',
        'ghest' => 'قسط',
        'scoring' => 'اعتبارسنجی'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    } 
}

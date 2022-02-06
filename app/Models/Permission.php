<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    public static $scopes = [
        'users' =>'کاربران',
        'scp-chart_users' =>'چارت های کاربران',
        'docs' => 'مدارک',
        'shops' => 'فروشگاه ها',
        'organs' => 'سازمان ها',
        'orders' =>'سفارش ها',
        'scp-chart_orders' => 'چارت های سفارش',
        'scp-chart_sales' => 'چارت های فروش',
        'ghests' => 'اقساط',
        'notes' => 'یادداشت ها',
        'inbox' => 'پیام ها',
        'cards' => 'قسطاکارت ها',
        'discounts' => 'تخفیف ها',
        'links' => 'لینک های کوتاه',
        'settings' => 'تنظیمات',
        'accounting' => 'حسابداری',
        'commissions' => 'کارمزدها',
        'full_access' => 'دسترسی تام',
        'full_read' => 'دسترسی کامل خواندن',
    ];
}

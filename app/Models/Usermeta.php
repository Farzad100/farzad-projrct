<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Usermeta extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = ['assets_haves' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static $martial_status = [
        'single_family' => 'مجرد هستم و همراه پدر و مادر زندگی می کنم',
        'single' => 'مجرد هستم و مستقل از پدر و مادر زندگی میکنم',
        'married' => 'متاهل هستم',
        'divorced' => 'از همسر جدا شده ام',
        'widow' => 'همسرم فوت شده است',
        'none' => 'ترجیح میدهم که پاسخ ندهم',
    ];

    public static $home_ownership = [
        'land_lord' => 'مالک خانه',
        'apartment_lord' => 'مالک آپارتمان',
        'land_tenant' => 'مستاجر خانه',
        'apartment_tenant' => 'مستاجر آپارتمان',
        'mate' => 'همراه فرد دیگری یا خانواده زندگی میکنم.',
    ];

    public static $degrees = [
        'doctorate' => 'دکترای تخصصی یا حرفه ای یا معادل',
        'master' => 'کارشناسی ارشد یا معادل آن',
        'bachelor' => 'کارشناسی یا معادل آن',
        'associate' => 'کاردانی یا معادل آن',
        'diploma' => 'دیپلم یا معادل آن',
        'highschool' => 'زیر دیپلم (دبیرستان)',
        'none' => 'بدون سواد'
    ];

    public static $job_section = [
        'public' => 'کارمند یا بازنشسته سازمان دولتی',
        'private' => 'کارمند یا بازنشسته شرکت خصوصی',
        'student' => 'دانشجو',
        'freelance' => 'شغل آزاد',
        'other' => 'سایر',
    ];

    public static $job_contract_type = [
        'self' => 'مالک کسب و کار یا خویش فرما',
        'contract' => 'قرارداد پروژه ای',
        'part' => 'قرارداد پاره وقت',
        'full' => 'قرارداد تمام وقت',
        'other' => 'سایر',
    ];

    public static $job_category = [
        'education' => 'فرهنگی آموزشی',
        'military' => 'نظامی و سیاسی',
        'finance' => 'بانکی و سرمایه گذاری',
        'trade' => 'تجارت و بازرگانی',
        'transport' => 'تاکسی و حمل و نقل',
        'retail' => 'خرده فروشی و فروشگاهی',
        'pro' => 'شغلهای تخصصی سازمانی (حسابداری، وکالت،...)',
        'skill' => 'شغل های مهارتی و حرفه ای غیرسازمانی ( نجاری، تاسیسات و ...)',
    ];

    public static $assets_haves = [
        'ins_life' => 'بیمه عمر',
        'ins_supp' => 'بیمه تکمیلی درمان',
        'stock_edalat' => 'سهام عدالت',
        'subcidy' => 'یارانه',
        'bourse_code' => 'کد بورسی',
    ];

    public static $assets_car = [
        'none' => 'خودرو ندارم',
        'leasing' => 'مالک خودرو هستم با همکاری لیزینگ',
        'bank' => 'مالک خودرو هستم با همکاری بانک',
    ];
}

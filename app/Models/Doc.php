<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doc extends Model
{
  use SoftDeletes;

  protected $guarded = ['id'];

  protected $casts = ['meta' => 'array'];

  public static function chunks_folder($path = '')
  {
    return 'chunks/' . $path;
  }

  public static function app_path($path = '')
  {
    return storage_path('app/') . $path;
  }

  public static function chunks_path($path = '')
  {
    return storage_path('app/') . self::chunks_folder($path);
  }

  public static function docs_folder($path = '')
  {
    return 'dox/' . $path;
  }

  public static function tmp_folder($path = '')
  {
    return 'tmpx/' . $path;
  }

  public static function tmp_path($path = '')
  {
    return storage_path('app/') . self::tmp_folder($path);
  }

  public static function docs_path($path = '')
  {
    return storage_path('app/') . self::docs_folder($path);
  }

  public static function temps_folder($path = '')
  {
    return 't3mpDox/' . $path;
  }

  public static function temps_path($path = '')
  {
    return public_path(self::temps_folder($path));
  }

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  public function admin()
  {
    return $this->belongsTo(User::class, 'decided_by');
  }

  public function order()
  {
    return $this->belongsTo(Order::class, 'order_id');
  }

  public function account()
  {
    return $this->belongsTo(Account::class, 'account_id');
  }

  public function organ()
  {
    return $this->belongsTo(Organ::class, 'organ_id');
  }

  public function shop()
  {
    return $this->belongsTo(Shop::class, 'shop_id');
  }

  public function company()
  {
    return $this->belongsTo(Company::class, 'company_id');
  }

  public static $shop_all = ['bl', 'sd', 'sp', 'ec', 'logo']; //business license, shop document, shop photo, establishment certification

  public static $organ_all = ['recom'];

  public static $user_all = ['ncf', 'ncb', 'bc']; //nc: national card (front or back) , ic: identity certificate

  public static $order_all = ['ncf', 'ncb', 'bc', 'report', 'backup', 'cheque', 'extra', 'wage', 'fish', 'factor', 'sefte', 'gcheck'];

  public static $registration = [
    'shop' => [
      'offline' => ['ncf', 'ncb', 'bl', 'sd', 'sp'],
      'online' => ['ncf', 'logo', 'ec'], //ec: establishment certification
    ],
    'organ' => [
      1 => ['ncf', 'ncb', 'recom'],
      2 => ['ncf', 'ncb', 'recom'],
    ]
  ];

  public static $order = [
    'primary' => [
      'user' => ['ncf', 'cheque', 'report', 'backup', 'extra'],
      'shop_offline' => ['ncf', 'cheque', 'report', 'backup', 'extra'],
      'shop_online' => ['ncf', 'cheque', 'report', 'backup', 'extra'],
      'organ_1_cheque' => ['ncf', 'cheque', 'report', 'backup', 'wage', 'extra'],
      'organ_2_cheque' => ['ncf', 'cheque', 'report', 'backup', 'wage', 'extra'],
      'organ_1_gcheck' => ['ncf', 'cheque', 'report', 'backup', 'wage', 'extra'],
      'organ_2_gcheck' => ['ncf', 'cheque', 'report', 'backup', 'wage', 'extra'],
      'organ_1_sefte' => ['ncf', 'wage', 'extra'],
      'organ_2_sefte' => ['ncf', 'wage', 'extra'],
      'organ_1_special' => ['ncf'],
      'organ_2_special' => ['ncf'],
    ],
    'secondary' => [
      'user' => [],
      'shop_offline' => [],
      'shop_online' => [],
      'organ_1_cheque' => [],
      'organ_2_cheque' => [],
      'organ_1_gcheck' => ['gcheck'],
      'organ_2_gcheck' => ['gcheck'],
      'organ_1_sefte' => ['sefte'],
      'organ_2_sefte' => ['sefte'],
      'organ_1_special' => ['sefte'],
      'organ_2_special' => ['sefte'],
    ],
    'delivery' => [
      'user' => ['cheques'],
      'shop_offline' => ['cheques', 'factor'],
      'shop_online' => ['cheques'],
      'organ_1_cheque' => ['cheques', 'wage'],
      'organ_2_cheque' => ['cheques', 'wage'],
      'organ_1_gcheck' => ['gcheck', 'wage'],
      'organ_2_gcheck' => ['gcheck', 'wage'],
      'organ_1_sefte' => ['sefte', 'wage'],
      'organ_2_sefte' => ['sefte', 'wage'],
      'organ_1_special' => ['sefte'],
      'organ_2_special' => ['sefte'],
    ]
  ];

  public static $order_optional = ['extra'];

  public static $types = [
    'ncf' => [
      'item' => 'کارت ملی',
      'title' => 'تصویر کارت ملی',
      'description' => 'با زدن روی دکمه انتخاب فایل، تصویر کارت ملی خود را بارگذاری کنید',
      'maxFiles' => 1,
      'scope' => 'user',
      'acceptedFiles' => 'image/*',
      'formats' => ['jpeg', 'jpg']
    ],
    'ncb' => [
      'item' => 'پشت کارت ملی',
      'title' => 'تصویر پشت کارت ملی',
      'description' => 'با زدن روی دکمه انتخاب فایل، تصویر پشت کارت ملی خود را بارگذاری کنید',
      'maxFiles' => 1,
      'scope' => 'user',
      'acceptedFiles' => 'image/*',
      'formats' => ['jpeg', 'jpg']
    ],
    'bc' => [
      'item' => 'شناسنامه',
      'title' => 'تصویر شناسنامه',
      'description' => 'با زدن روی دکمه انتخاب فایل، تصویر صفحه اول شناسنامه خود را بارگذاری کنید',
      'maxFiles' => 1,
      'scope' => 'user',
      'acceptedFiles' => 'image/*',
      'formats' => ['jpeg', 'jpg']
    ],
    'bcnc' => [
      'item' => 'کارت ملی و شناسنامه',
      'title' => 'تصویر کارت ملی و شناسنامه',
      'description' => 'کارت ملی و صفحه اول شناسنامه خود را در کنار یکدیگر گذاشته و تصویر آن را بارگذاری کنید',
      'maxFiles' => 1,
      'scope' => 'user',
      'acceptedFiles' => 'image/*',
      'formats' => ['jpeg', 'jpg']
    ],
    'cheque' => [
      'item' => 'چک صیادی',
      'title' => 'تصویر چک صیادی',
      'description' => 'از یک برگ چک خالی عکس بگیرید و آن را بارگذاری کنید. بطوری که اطلاعات آن مخصوصاً شناسه صیادی واضح باشد. <a href="/images/cheque-sample.jpg" target="_blank">نمونه تصویری</a>',
      'maxFiles' => 1,
      'scope' => 'order',
      'acceptedFiles' => 'image/*',
      'formats' => ['jpeg', 'jpg']
    ],
    'report' => [
      'item' => 'پرینت حساب',
      'title' => 'پرینت حساب',
      'description' => 'x',
      'maxFiles' => 250,
      'scope' => 'order',
      'acceptedFiles' => '.xlsx,.xls,.pdf,.zip,.rar,image/*',
      'formats' => ['xlsx', 'xls', 'pdf', 'zip', 'rar', 'jpg', 'jpeg', 'png']
    ],
    'backup' => [
      'item' => 'پرینت حساب پشتیبان',
      'title' => 'پرینت حساب پشتیبان',
      'description' => 'حساب پشتیبان معمولاً همان حسابی است که حقوق شما به آن واریز می‌شود، از رسوب و معدل بالاتری نسبت به حساب های دیگر شما برخوردار است و در نتیجه باعث بالارفتن نمره اعتبارسنجی شما می‌شود. اگر پرینت حساب خود را با فرمت اکسل بارگذاری کرده اید، برای استفاده از تعرفه اعتبارسنجی هوشمند می‌بایست این حساب را هم با فرمت اکسل بارگذاری نمایید.',
      'maxFiles' => 250,
      'scope' => 'order',
      'acceptedFiles' => '.xlsx,.xls,.pdf,.zip,.rar,image/*',
      'formats' => ['xlsx', 'xls', 'pdf', 'zip', 'rar', 'jpg', 'jpeg', 'png']
    ],
    'extra' => [
      'item' => 'مدارک اضافی',
      'title' => 'مدارک اضافی',
      'description' => 'در این بخش می توانید با هماهنگی و بارگذاری مدارک بیشتر، نمره اعتبارسنجی خود را بهبود ببخشید.',
      'maxFiles' => 10,
      'scope' => 'order',
      'acceptedFiles' => 'image/*,.pdf,.zip,.rar',
      'formats' => ['jpeg', 'jpg', 'pdf', 'zip', 'rar']
    ],
    'cheques' => [
      'item' => 'چک های نوشته شده',
      'title' => 'تصویر چک های نوشته شده',
      'description' => 'پس از تکمیل چک ها از آن ها بطور جداگانه عکس واضح بگیرید و همه را در این بخش بارگذاری کنید',
      'maxFiles' => 12,
      'scope' => 'order',
      'acceptedFiles' => 'image/*',
      'formats' => ['jpeg', 'jpg']
    ],
    'contract' => [
      'item' => 'قرارداد',
      'title' => 'تصویر قرارداد',
      'description' => 'پس از چاپ و امضای قرارداد، از آن عکس گرفته و در این بخش بارگذاری کنید',
      'maxFiles' => 1,
      'scope' => 'order',
      'acceptedFiles' => 'image/*',
      'formats' => ['jpeg', 'jpg']
    ],
    'sefte' => [
      'item' => 'سفته',
      'title' => 'تصویر سفته',
      'description' => 'x',
      'maxFiles' => 1,
      'scope' => 'order',
      'acceptedFiles' => 'image/*',
      'formats' => ['jpeg', 'jpg']
    ],
    'gcheck' => [
      'item' => 'چک ضمانت',
      'title' => 'تصویر چک ضمانت',
      'description' => 'x',
      'maxFiles' => 1,
      'scope' => 'order',
      'acceptedFiles' => 'image/*',
      'formats' => ['jpeg', 'jpg']
    ],
    'wage' => [
      'item' => 'گواهی کسر از حقوق',
      'title' => 'تصویر گواهی کسر از حقوق',
      'description' => 'گواهی کسر از حقوق را از سازمان خود دریافت کنید، از آن عکس گرفته و در این بخش بارگذاری نمایید',
      'maxFiles' => 1,
      'scope' => 'order',
      'acceptedFiles' => 'image/*',
      'formats' => ['jpeg', 'jpg']
    ],
    'fish' => [
      'item' => 'فیش حقوقی مهرشده',
      'title' => 'تصویر فیش حقوقی مهرشده',
      'description' => 'فیش حقوقی مهرشده را از سازمان خود دریافت کنید، از آن عکس گرفته و در این بخش بارگذاری نمایید',
      'maxFiles' => 1,
      'scope' => 'order',
      'acceptedFiles' => 'image/*',
      'formats' => ['jpeg', 'jpg']
    ],
    'recom' => [
      'item' => 'معرفی نامه سازمان',
      'title' => 'معرفی نامه سازمان',
      'description' => 'معرفی نامه سازمان خود را مطابق نمونه آماده کرده و تصویر (یا فایل pdf) آن را بارگذاری نمایید. <a href="/recom.docx" target="_blank">دانلود فایل نمونه</a>',
      'maxFiles' => 1,
      'scope' => 'organ',
      'acceptedFiles' => 'image/*',
      'formats' => ['jpeg', 'jpg']
    ],
    'factor' => [
      'item' => 'صورتحساب/رسید کالا',
      'title' => 'تصویر صورتحساب/رسید کالا',
      'description' => 'از صورتحساب یا رسید کالا/خدماتی که به مشتری فروخته اید عکس گرفته و در این بخش بارگذاری کنید (با مهر و امضا)',
      'maxFiles' => 1,
      'scope' => 'order',
      'acceptedFiles' => 'image/*',
      'formats' => ['jpeg', 'jpg']
    ],
    'bl' => [
      'item' => 'جواز کسب',
      'title' => 'تصویر جواز کسب',
      'description' => 'از جواز یا پروانه کسب خود عکس بگیرید و در این قسمت بارگذاری کنید',
      'maxFiles' => 3,
      'scope' => 'shop',
      'acceptedFiles' => 'image/*',
      'formats' => ['jpeg', 'jpg']
    ],
    'sd' => [
      'item' => 'سند مکانی',
      'title' => 'تصویر سند مکانی',
      'description' => 'از سند مالکیت محل کسب و کار خود یا اجاره نامه عکس بگیرید و آنها را در این بخش بارگذاری کنید',
      'maxFiles' => 5,
      'scope' => 'shop',
      'acceptedFiles' => 'image/*',
      'formats' => ['jpeg', 'jpg']
    ],
    'sp' => [
      'title' => 'عکس از فروشگاه',
      'description' => 'یک تصویر از فروشگاه بیندازید، بطوریکه سردر آن هم در تصویر مشخص باشد',
      'maxFiles' => 1,
      'scope' => 'shop',
      'acceptedFiles' => 'image/*',
      'formats' => ['jpeg', 'jpg']
    ],
    'ec' => [
      'item' => 'گواهی تأسیس',
      'title' => 'تصویر گواهی تأسیس',
      'description' => 'تصویری از گواهی ثبتی',
      'maxFiles' => 1,
      'scope' => 'shop',
      'acceptedFiles' => 'image/*',
      'formats' => ['jpeg', 'jpg']
    ],
    'logo' => [
      'item' => 'لوگوی فروشگاه',
      'title' => 'لوگوی فروشگاه',
      'description' => 'فایل لوگوی فروشگاه را در سایز 500x500 و در فرمت png بارگذاری نمایید',
      'maxFiles' => 1,
      'scope' => 'shop',
      'acceptedFiles' => 'image/png',
      'formats' => ['png']
    ],
    'chf' => [
      'item' => 'تصویر چک',
      'title' => 'تصویر چک',
      'description' => '',
      'maxFiles' => 1,
      'scope' => 'order',
      'acceptedFiles' => 'image/*',
      'formats' => ['jpeg', 'jpg']
    ],
    'chb' => [
      'item' => 'تصویر پشت چک',
      'title' => 'تصویر پشت چک',
      'description' => '',
      'maxFiles' => 1,
      'scope' => 'order',
      'acceptedFiles' => 'image/*',
      'formats' => ['jpeg', 'jpg']
    ],
  ];

  public const reject_options = [
    [
      'text' => 'چک اشتباه بارگذاری شده/نیاز به بارگذاری مجدد',
      'value' => 'cheque_wrong',
      'options' => [
        'reject'
      ]
    ],
    [
      'text' => 'در قسمت تاریخ خط خوردگی/اشتباه وجود دارد',
      'value' => 'date',
      'updates' => [
        'need_chb' => 1,
      ],
    ],
    [
      'text' => 'در قسمت مبلغ خط خوردگی/اشتباه وجود دارد',
      'value' => 'price',
      'updates' => [
        'need_chb' => 1,
      ],
    ],
    [
      'text' => 'در قسمت «در وجه» خط خوردگی/اشتباه وجود دارد',
      'value' => 'who',
      'updates' => [
        'need_chb' => 1,
      ],
    ],
    [
      'text' => 'شناسه ملی شرکت را اشتباه وارد کرده اید.',
      'value' => 'nic_wrong',
      'updates' => [
        'need_chb' => 1,
      ],
    ],
    [
      'text' => 'شناسه ملی شرکت را ننوشته‌اید یا ناقص است.',
      'value' => 'nic',
      'options' => [
        'reject'
      ]
    ],
    [
      'text' => 'چک در سامانه صیاد ثبت نشده',
      'value' => 'submit',
      'updates' => [
        'is_submitted' => 0,
      ]
    ],
    [
      'text' => 'مشخصات چک در سامانه صیاد اشتباه ثبت شده',
      'value' => 'submit_wrong',
      'updates' => [
        'is_submitted' => 0,
      ]
    ],
    [
      'text' => 'از رنگ‌های متفاوت برای نوشتن چک یا امضا استفاده شده.',
      'value' => 'color',
      'options' => [
        'reject'
      ]
    ],
  ];

  public static function validation_table()
  {
    return '<div class="validation_table mt-3 mb-5 border border-1 border-warning"><div class="mb-3 p-3 text-justify">
    چنانچه
    فایل پرینت حساب خود را با فرمت اکسل از اینترنت بانک دریافت و
    بارگذاری نمایید، اعتبارسنجی به این شیوه هوشمند، سریع‌تر و ارزان‌تر خواهد
    بود. هزینه اعتبارسنجی در انتهای فرآیند به همراه پیش‌پرداخت
    دریافت می‌گردد.
  </div>
  <div>
    <table class="table table-striped mb-0">
      <thead>
        <th class="pb-2" />
        <th class="pb-2">
          طرح هوشمند
        </th>
        <th class="pb-2">
          طرح معمولی
        </th>
      </thead>
      <tbody>
        <tr>
          <td class="font-weight-bold">فرمت قابل قبول:</td>
          <td>اکسل(xls,xlsx)</td>
          <td>همه فرمت‌ها</td>
        </tr>
        <tr>
          <td class="font-weight-bold">زمان بررسی:</td>
          <td>' . Order::inquiry_rabbit_time / 86400 . ' روز کاری</td>
          <td>' . Order::inquiry_turtle_time / 86400 . ' روز کاری</td>
        </tr>
        <tr>
          <td class="font-weight-bold">هزینه:</td>
          <td>' . number_format(Order::inquiry_rabbit_price) . ' تومان</td>
          <td>' . number_format(Order::inquiry_turtle_price) . ' تومان</td>
        </tr>
      </tbody>
    </table>
  </div></div>';
  }
}

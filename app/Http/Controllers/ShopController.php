<?php

namespace App\Http\Controllers;

use App\Mail\EshopVerification;
use App\Models\Useful;
use App\Models\Address;
use App\Models\Card;
use App\Models\Company;
use App\Models\Doc;
use App\Models\Edate;
use App\Models\Order;
use App\Models\Pattern;
use App\Models\Sms;
use App\Models\Shop;
use App\Models\User;
use App\Models\Verification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

class ShopController extends Controller
{
  //ADMIN
  public function index(Request $request)
  {
    $uri = explode('/', Route::current()->uri);
    $export = end($uri) == 'export' ? 1 : null;

    $query = Shop::search($request)->with(['user', 'company'])->withCount(['orders', 'orders_successful']);

    if ($export) {
      $shops = $query->get();
      return $this->export($shops);
    }

    $shops = $query->paginate(20);

    $new = [];
    $n = 0;
    foreach ($shops as $shop) {
      if (in_array($shop->status, ['uploading', 'docs_uploaded']))
        $shop->status = self::review_docs($shop)[0]['new_status'];
      $user = $shop->user;

      if (!$user) return $shop;

      $new[$n]['id'] = $shop->id;

      $col['name'] = [
        'title' => 'نام فروشگاه',
        'value' => $shop->name,
        'addition' => Shop::$categories[$shop->category]
      ];
      if ($shop->company) $col['name']['icon'] = 'user-tie';

      $col['type'] = [
        'title' => 'مدل همکاری',
        'value' => Shop::$types[$shop->type],
        'addition' => ''
      ];

      $col['status'] = [
        'title' => 'وضعیت',
        'value' => Shop::$status[$shop->status]['farsi'],
        'type' => 'status',
        'css_classes' => 'badge-' . Shop::$status[$shop->status]['class'],
        'addition' => $shop->status == 'active' ? Edate::date_diff_carbon($shop->start_at) : ''
      ];

      $col['orders'] = [
        'title' => 'سفارش موفق',
        'value' => $shop->orders_successful_count,
        'addition' => $shop->orders_count - $shop->orders_successful_count > 0 ? 'از ' . $shop->orders_count : '',
      ];

      $col['sale'] = [
        'title' => 'فروش موفق',
        'value' =>  Order::where('shop_id', $shop->id)->whereNotNull('charged_at')->sum('amount'),
        'type' => 'toman',
      ];

      $col['owner'] = [
        'title' => 'رابط فروشگاه',
        'value' => $user->fname . ' ' . $user->lname,
        'addition' => '0' . $user->username
      ];

      /* $col['utm'] = [
        'title' => 'UTM',
        'value' => $user->utm_source,
        'addition' => $user->utm_campaign ? 'camp: ' . $user->utm_campaign : '',
      ]; */

      $col['action'] = [
        'type' => 'button',
        'buttons' => [
          [
            'value' => 'اطلاعات بیشتر',
            'icon' => 'info',
            'btn_color' => 'info',
            'type' => 'route',
            'route' => 'dash-shops-single',
            'params' => ['id' => $shop->id],
          ],
          [
            'value' => 'ویرایش اطلاعات',
            'icon' => 'edit',
            'btn_color' => 'primary',
            'type' => 'modal',
            'modal_name' => 'editModal',
            'method' => 'post',
            'endpoint' => '/admin/shops/' . $shop->id . '/edit',
          ],
          [
            'value' => 'حذف فروشگاه',
            'icon' => 'trash',
            'css_classes' => 'text-danger',
            'type' => 'confirm',
            'method' => 'post',
            'endpoint' => '/admin/shops/' . $shop->id . '/delete',
          ],
        ],
      ];

      $new[$n]['td'] = $col;
      $col = null;
      ++$n;
    }
    return Useful::recollect($shops, $new);
  }

  public function export($shops)
  {
    $data[0] = [
      'ردیف',
      'نام',
      'دسته بندی',
      'نوع همکاری',
      'وضعیت',
      'تاریخ ثبت نام',
      'تاریخ امضای قرارداد',
      'تاریخ بارگذاری مدارک',
      'تاریخ تأیید مدارک',
      'تاریخ شروع همکاری',
      'تعداد سفارشها',
      'سفارشهای موفق',
      'میزان فروش',
      'مالکیت',
      'استان',
    ];
    $n = 1;
    foreach ($shops as $shop) {
      $data[$n] = [
        $n,
        $shop->name,
        Shop::$categories[$shop->category],
        $shop->type == 'offline' ? 'فیزیکی' : 'آنلاین',
        Shop::$status[$shop->status]['farsi'],
        $shop->created_at ? Edate::edateFromCarbon('Y-m-d', $shop->created_at) : '',
        $shop->agreed_at ? Edate::edateFromCarbon('Y-m-d', $shop->agreed_at) : '',
        $shop->docs_uploaded_at ? Edate::edateFromCarbon('Y-m-d', $shop->docs_uploaded_at) : '',
        $shop->docs_accepted_at ? Edate::edateFromCarbon('Y-m-d', $shop->docs_accepted_at) : '',
        $shop->start_at && $shop->status == 'active' ? Edate::edateFromCarbon('Y-m-d', $shop->start_at) : '',
        $shop->orders_count,
        $shop->orders_successful_count,
        Order::where('shop_id', $shop->id)->successful()->sum('amount'),
        $shop->company_id ? 'حقوقی' : 'حقیقی',
        Address::state_name($shop->state),
      ];
      ++$n;
    }

    $url = ExportController::excel($data, 'shops');
    return Useful::ok(['url' => $url], ['action_type' => 'open_url']);
  }

  //ADMIN
  public function single(Request $request)
  {
    $shop = Shop::where('id', $request->id)->with(['company', 'card'])->first();
    if ($shop->company_id) $shop->company->state_name = Address::state_name($shop->company->state);
    if ($shop->status == 'pending') {
      if ($shop->type == 'offline') {
        if ($shop->agreed_at) $shop->status = 'uploading';
        else $shop->status = 'agreement';
      }
    }
    $shop->docs = self::review_docs($shop);
    $shop->status = $shop->docs[0]['new_status'];
    $shop->status_farsi = Shop::$status[$shop->status]['farsi'];
    $shop->category = Shop::$categories[$shop->category];
    $shop->owner = User::info($shop->user_id);
    $orders = Order::select('id', 'user_id', 'amount', 'charged_at')->where('shop_id', $shop->id)->get();
    $shop->users_count = $orders->unique('user_id')->count();
    $shop->orders_count = $orders->count();
    $shop->orders_successful_count = $orders->whereNotNull('charged_at')->count();
    $shop->sales_total = $orders->whereNotNull('charged_at')->sum('amount');
    $shop->ghestacard = $shop->card_id ? Card::ghestacard($shop->user_id) : null;
    return Useful::ok($shop);
  }

  /* public function shop_metadata($shop){
    
  } */

  //ADMIN
  public function edit(Request $request)
  {
    $Shop = Shop::find($request->id);
    if (!empty($request->company_nic)) {
      $company = Company::where(['nic' => $request->company_nic])->update([
        'name' => Useful::std($request->company_name),
        'fame' => Useful::std($request->company_fame),
        'ec' => Useful::std($request->company_ec),
        'nic' => Useful::std($request->company_nic),
        'rn' => Useful::std($request->company_rn),
        'state' => Useful::std($request->company_state),
        'city' => Useful::std($request->company_city),
        'address' => Useful::std($request->company_address),
        'postal_code' => Useful::std($request->company_postal_code),
        'phone' => Useful::std($request->company_phone),
      ]);
      if (!$company) return Useful::nok(['dbc', 'متأسفانه مشکلی در ارتباط با سرور رخ داده است.']);
      // $company_id = $company->id;
    }
    if ($Shop->update([
      'name' => Useful::std($request->name),
      'category' => $request->category,
      'type' => $request->type,
      'tax_code' => Useful::std($request->tax_code),
      'phone' => Useful::std($request->phone),
      'phone_direct' => Useful::std($request->phone_direct),
      'state' => Useful::std($request->state),
      'city' => Useful::std($request->city),
      'address' => Useful::std($request->address),
      'postal_code' => Useful::enum($request->postal_code),
      'commission_default' => $request->commission_default,
      'commission_percent' => Useful::enum($request->commission_percent) ?? 0,
      'commission_amount' => Useful::enum($request->commission_amount) ?? 0,
    ])) {
      return Useful::ok('updated');
    }
    return Useful::nok(['dbsh-e', 'خطا در اتصال به دیتابیس']);
  }

  //ADMIN
  public function edit_fields(Request $request)
  {
    $shop = Shop::where('id', $request->id)->with('company')->first();

    $shop->owner = User::info($shop->user_id);

    $res = [
      [
        'section' => 'مشخصات فروشگاه',
        'size' => 'lg',
        'fields' => [
          [
            'label' => 'نام فروشگاه',
            'v_model' => 'name',
            'value' => $shop->name,
            'type' => 'text',
            'width' => '6',
          ],
          [
            'label' => 'دسته بندی',
            'v_model' => 'category',
            'value' => $shop->category,
            'type' => 'select',
            'options' => Shop::list_categories(),
            'width' => '6',
          ],
          [
            'label' => 'مدل همکاری',
            'v_model' => 'type',
            'value' => $shop->type,
            'type' => 'select',
            'options' => [
              [
                'label' => 'آنلاین',
                'value' => 'online',
              ],
              [
                'label' => 'آفلاین',
                'value' => 'offline',
              ],
            ],
            'width' => '6',
          ],
          [
            'label' => 'نوع مالکیت',
            'v_model' => 'authority',
            'value' => $shop->company_id ? 'حقوقی' : 'حقیقی',
            'type' => 'text',
            'width' => '6',
            'disabled' => true,
          ],
          [
            'label' => 'سایت',
            'v_model' => 'website',
            'value' => $shop->website,
            'type' => 'text',
            'width' => '6',
          ],
          [
            'label' => 'ایمیل',
            'v_model' => 'email',
            'value' => $shop->email,
            'type' => 'text',
            'width' => '6',
          ],
          [
            'label' => 'تلفن ثابت',
            'v_model' => 'phone',
            'value' => $shop->phone,
            'type' => 'text',
            'width' => '6',
          ],
          [
            'label' => 'داخلی',
            'v_model' => 'phone_direct',
            'value' => $shop->phone_direct,
            'type' => 'text',
            'width' => '6',
          ],
          [
            'label' => 'استان',
            'v_model' => 'state',
            'value' => $shop->state,
            'type' => 'select',
            'options' => Address::list_states(),
            'width' => '6',
          ],
          [
            'label' => 'شهر',
            'v_model' => 'city',
            'value' => $shop->city,
            'type' => 'text',
            'width' => '6',
          ],
          [
            'label' => 'کدپستی',
            'v_model' => 'postal_code',
            'value' => $shop->postal_code,
            'type' => 'text',
            'width' => '6',
          ],
          [
            'label' => 'آدرس',
            'v_model' => 'address',
            'value' => $shop->address,
            'type' => 'textarea',
          ],
          [
            'label' => 'درباره',
            'v_model' => 'about',
            'value' => $shop->about,
            'type' => 'textarea',
            'disabled' => true,
          ],
          [
            'label' => 'مدل کارمزد',
            'v_model' => 'commission_default',
            'value' => $shop->commission_default,
            'type' => 'select',
            'options' => [
              ['label' => 'پیشفرض', 'value' => 1],
              ['label' => 'اختصاصی', 'value' => 0]
            ],
            'width' => '6',
          ],
          [
            'label' => 'درصد کارمزد',
            'v_model' => 'commission_percent',
            'value' => $shop->commission_percent,
            'type' => 'text',
            'mode' => 'percent',
            'width' => '6',
          ],
          [
            'label' => 'سقف کارمزد',
            'v_model' => 'commission_amount',
            'value' => $shop->commission_amount,
            'type' => 'text',
            'mode' => 'price',
          ],
          [
            'label' => 'نام فروشنده',
            'v_model' => 'owner_name',
            'value' => $shop->owner->full_name,
            'type' => 'text',
            'width' => '6',
            'disabled' => true,
          ],
          [
            'label' => 'موبایل فروشنده',
            'v_model' => 'owner_mobile',
            'value' => $shop->owner->mobile,
            'type' => 'text',
            'width' => '6',
            'disabled' => true,
          ],
        ],
      ],
    ];

    if ($shop->company_id) {
      array_push($res, [
        'section' => 'مشخصات حقوقی',
        'size' => 'lg',
        'fields' => [
          [
            'label' => 'مدیرعامل',
            'v_model' => 'ceo_name',
            'value' => $shop->company->ceo_fname . ' ' . $shop->company->ceo_lname,
            'type' => 'text',
            'width' => '6',
            'disabled' => true,
          ],
          [
            'label' => 'کدملی مدیرعامل',
            'v_model' => 'ceo_nid',
            'value' => $shop->company->ceo_nid,
            'type' => 'text',
            'width' => '6',
            'disabled' => true,
          ],
          [
            'label' => 'نام شرکت',
            'v_model' => 'company_name',
            'value' => $shop->company->name,
            'type' => 'text',
            'width' => '6',
          ],
          [
            'label' => 'شماره ثبت',
            'v_model' => 'company_rn',
            'value' => $shop->company->rn,
            'type' => 'text',
            'width' => '6',
          ],
          [
            'label' => 'شناسه ملی',
            'v_model' => 'company_nic',
            'value' => $shop->company->nic,
            'type' => 'text',
            'width' => '6',
            'disabled' => true,
          ],
          [
            'label' => 'کد اقتصادی',
            'v_model' => 'company_ec',
            'value' => $shop->company->ec,
            'type' => 'text',
            'width' => '6',
          ],
          [
            'label' => 'تلفن ثابت',
            'v_model' => 'company_phone',
            'value' => $shop->company->phone,
            'type' => 'text',
            'width' => '6',
          ],
          [
            'label' => 'استان',
            'v_model' => 'company_state',
            'value' => $shop->company->state,
            'type' => 'select',
            'options' => Address::list_states(),
            'width' => '6',
          ],
          [
            'label' => 'شهر',
            'v_model' => 'company_city',
            'value' => $shop->company->city,
            'type' => 'text',
            'width' => '6',
          ],
          [
            'label' => 'کدپستی',
            'v_model' => 'company_postal_code',
            'value' => $shop->company->postal_code,
            'type' => 'text',
            'width' => '6',
          ],
          [
            'label' => 'آدرس',
            'v_model' => 'company_address',
            'value' => $shop->company->address,
            'type' => 'textarea',
          ],
        ],
      ]);
    }

    return Useful::ok($res);
  }

  //ADMIN
  public function delete(Request $request)
  {
    if (Shop::find($request->id)->delete()) return Useful::ok();
    return Useful::nok(['dbsh', 'خطا در اتصال به دیتابیس']);
  }

  //ADMIN
  public function change_status(Request $request)
  {
    $id = $request->id;
    $status = $request->status;

    switch ($status) {
      case 'active':
        $shop = Shop::find($id);
        $shop->update(['status' => 'active']);
        if (empty($shop->start_at)) {
          $shop->update(['start_at' => Carbon::now()]);
          Sms::send(
            Shop::mobile($shop->id),
            Pattern::shop_activated,
            ['shop' => $shop->name]
          );
        }
        break;
      case 'inactive':
        Shop::find($id)->update(['status' => 'inactive']);
        break;
      case 'pending':
        Shop::find($id)->update(['status' => 'pending']);
        break;
      case 'uploading':
        Shop::find($id)->update(['status' => 'uploading']);
        break;
      default:
        break;
    }

    return Useful::ok();
  }

  public function agree(Request $request)
  {
    $user_id = Auth::user()->id;
    $shop = Shop::where(['user_id' => $user_id])->whereNull('agreed_at')->first();
    if ($shop->update(['agreed_at' => Carbon::now(), 'status' => 'uploading'])) return Useful::ok();
    return Useful::nok(['dbsh', 'خطا در اتصال به دیتابیس']);
  }

  public static function review_docs($shop)
  {
    $shop_id = $shop->id;
    $user_id = $shop->user_id;
    $docs = [];
    $n = 0;
    $point = 0;
    $point_accepted = 0;
    foreach (Doc::$registration['shop'][$shop->type] as $type) {
      $docTypes = Doc::$types;
      if ($type == 'ec' && !$shop->company_id) continue;
      if ($docTypes[$type]['scope'] == 'user') {
        $doc = Doc::where('user_id', $user_id)->where('type', $type)->orderBy('id', 'desc')->first();
        $docs[$n]['url'] = '/api/upload/' . $type;
        $docs[$n]['concatUrl'] = '/concat/' . $type;
      } else {
        $doc = Doc::where('shop_id', $shop_id)->where('type', $type)->orderBy('id', 'desc')->first();
        $docs[$n]['url'] = '/api/shop/upload/' . $type;
        $docs[$n]['concatUrl'] = '/shop/concat/' . $type;
      }
      if ($doc) {
        switch ($doc->is_verified) {
          case 1:
            $docs[$n]['status'] = 'verified';
            $docs[$n]['point'] = 1;
            $docs[$n]['message'] = 'این مدرک تأیید شده';
            break;
          case 0:
            $docs[$n]['status'] = 'pending';
            $docs[$n]['point'] = 1;
            $docs[$n]['message'] = 'در انتظار بررسی توسط کارشناسان قسطا';
            break;
          case -1:
            $docs[$n]['status'] = 'rejected';
            $docs[$n]['point'] = 0;
            $docs[$n]['message'] = 'این مدرک به دلیل ' . $doc->reason . ' تأیید نشده. لطفاً مجدداً آن را بارگذاری کنید.';
            if (empty($doc->reason)) $docs[$n]['message'] = 'این مدرک توسط کارشناس قسطا تأیید نشده. لطفاً مجدداً آن را بارگذاری کنید.';
            break;
        }
        if (User::is_admin()) {
          $docs[$n]['link'] = '/admin/docs/' . $doc->id;
          $explode = explode('.', $doc->address);
          $docs[$n]['format'] = end($explode);
        }
      } else {
        $docs[$n]['status'] = 'void';
        $docs[$n]['point'] = 0;
        $docs[$n]['message'] = 'این مدرک هنوز بارگذاری نشده';
      }
      $docs[$n]['name'] = $type;
      $docs[$n]['title'] = $docTypes[$type]['title'];
      $docs[$n]['description'] = $docTypes[$type]['description'];
      if (isset($docTypes[$type]['acceptedFiles'])) $docs[$n]['acceptedFiles'] = $docTypes[$type]['acceptedFiles'];
      $docs[$n]['maxFiles'] = $docTypes[$type]['maxFiles'];
      $point = $point + $docs[$n]['point'];
      if ($docs[$n]['status'] == 'verified') ++$point_accepted;

      $docs[$n]['url'] .= '/' . $user_id;
      $docs[$n]['concatUrl'] .= '/' . $user_id;

      ++$n;
    }
    $required_point = count(Doc::$registration['shop'][$shop->type]);
    if ($type == 'ec' && !$shop->company_id) --$required_point;
    $new_status = $shop->status;
    if ($shop->status == 'docs_uploaded') {
      if ($point < $required_point) {
        $new_status = 'uploading';
        $shop->update(['status' => $new_status, 'docs_uploaded_at' => null]);
      } else if ($point_accepted == $required_point) {
        $new_status = 'final';
        $shop->update(['status' => $new_status, 'docs_accepted_at' => Carbon::now()]);
      }
    } else if ($shop->status == 'uploading') {
      if ($point_accepted == $required_point) {
        $new_status = 'final';
        $shop->update(['status' => $new_status, 'docs_accepted_at' => Carbon::now()]);
      } else if ($point == $required_point) {
        $new_status = 'docs_uploaded';
        $shop->update(['status' => $new_status, 'docs_uploaded_at' => Carbon::now()]);
        Sms::send(
          config('globals.crm2.mobile'),
          Pattern::admin_shop_docs_uploaded,
          [
            'shop' => $shop->name,
          ]
        );
      }
    }
    $docs[0]['new_status'] = $new_status;
    return $docs;
  }

  public function info(Request $request)
  {
    $user_id = Auth::user()->id;
    if ($request->just) {
      switch ($request->just) {
        case 'status':
          $shop = Shop::select('status')->where('user_id', $user_id)->first();
          if ($shop->status == 'active') return 'active';
          return 'inactive';
          break;
      }
    }
    $shop = Shop::where('user_id', $user_id)->with('company')->first();
    $shop_id = $shop->id;
    if ($shop->status == 'inactive') {
      return Useful::ok([
        'status' => 'inactive',
        'mode' => 'show_message',
        'color' => 'muted',
        'message' => 'فروشنده گرامی؛<br/>فروشگاه شما در حال حاضر غیرفعال است. ممکن است در بخش اطلاعیه ها، اطلاعات بیشتری برای این موضوع پیدا کنید.'
      ]);
    }
    if ($shop->company_id) $shop->company->state_name = Address::state_name($shop->company->state);
    if ($shop->status == 'pending') {
      if ($shop->agreed_at) $shop->status = 'uploading';
      else $shop->status = 'agreement';
    }
    if (in_array($shop->status, ['uploading', 'docs_uploaded'])) {
      $shop->docs = self::review_docs($shop);
      $shop->status = $shop->docs[0]['new_status'];
    } else $shop->docs = null;
    if ($shop->status == 'final') {
      return Useful::ok([
        'status' => 'final',
        'mode' => 'show_message',
        'message' => 'فروشنده گرامی؛<br/>
        تمامی مدارک شما توسط کارشناسان قسطا بررسی و تأیید شده است و در حال بررسی نهایی هستیم.<br/>
        پس از تأیید نهایی فروشگاه شما فعال خواهدشد و نتیجه از طریق پیامک به اطلاعتان خواهدرسید.'
      ]);
    }
    $shop->owner = User::info($shop->user_id);
    $shop->mode = null;
    $shop->status_farsi = Shop::$status[$shop->status]['farsi'];
    $shop->ghestacard = $shop->card_id ? Card::ghestacard($shop->user_id) : null;
    $shop->status_farsi = Shop::$status[$shop->status]['farsi'];

    if ($shop->status != 'active')
      return Useful::ok($shop);

    $orders = Order::select('id', 'user_id', 'amount', 'charged_at')->where('shop_id', $shop->id)->get();
    $shop->users_count = $orders->unique('user_id')->count();
    $shop->orders_count = $orders->count();
    $shop->orders_successful_count = $orders->whereNotNull('charged_at')->count();
    $shop->sales_total = $orders->whereNotNull('charged_at')->sum('amount');
    return Useful::ok($shop);
  }

  public function docs(Request $request)
  {
    $id = Auth::user()->id;
    $shop = Shop::where('user_id', $id)->first();
    if (in_array($shop->status, ['uploading', 'docs_uploaded'])) $docs = self::review_docs($shop);
    else $docs = null;
    return Useful::ok(['cta' => null, 'docs' => $docs]);
  }

  protected function create_shop(Request $request)
  {
    $email = $request->email;
    if ($request->type == 'online') {
      $domain = Useful::domain_extractor($request->website);
      if (Useful::str_has($email, '@') && explode('@', $email)[1] != $domain)
        return Useful::nok(['email', "لطفاً ایمیل معتبر سازمانی متصل به دامنه $domain وارد کنید"]);
      $email = explode('@', $email)[0] . '@' . $domain;
    }
    $email = strtolower($email);

    $user_id = Auth::user()->id;
    if (Shop::where(['user_id' => $user_id, 'type' => $request->type])->exists())
      return Useful::nok(['shopExists', 'شما قبلاً فروشگاه خود را ثبت کرده اید.']);

    $request->validate([
      'name' => ['required', 'string', 'max:100'],
      'category' => ['required', 'string', 'max:20'],
      'type' => ['required', 'string', 'max:10'],
      'state' => ['required', 'string', 'max:30'],
      'city' => ['required', 'string', 'max:30'],
      'address' => ['required', 'string', 'max:250'],
      'about' => ['required', 'string', 'max:250'],
      'postal_code' => ['required', 'max:10'],
      'phone' => ['required', 'max:11'],
    ]);

    if (!empty($request->company_nic)) {
      $request->validate([
        'company_name' => ['required', 'string', 'max:100'],
        'company_state' => ['required', 'string', 'max:30'],
        'ceo_fname' => ['required', 'string', 'max:30'],
        'ceo_lname' => ['required', 'string', 'max:30'],
        'ceo_nid' => ['required', 'string', 'size:10'],
        'company_nic' => ['required', 'string', 'size:11'],
        'company_rn' => ['required', 'string', 'min:1', 'max:7'],
        'company_postal_code' => ['required', 'string', 'max:10'],
        'company_phone' => ['required', 'string', 'max:11'],
      ]);
    }

    if ($request->company_nic) {
      $company = Company::select('id')->where('nic', Useful::std($request->company_nic))->first();
      if ($company) {
        if (Shop::where('company_id', $company->id)->exists())
          return Useful::nok(['company_exists', 'این شرکت قبلاً ثبت شده است']);
        $company->delete();
      }

      $company = Company::create([
        'ceo_fname' => Useful::std($request->ceo_fname),
        'ceo_lname' => Useful::std($request->ceo_lname),
        'ceo_nid' => Useful::std($request->ceo_nid),
        'name' => Useful::std($request->company_name),
        'fame' => Useful::std($request->company_fame),
        'ec' => Useful::std($request->company_ec),
        'nic' => Useful::std($request->company_nic),
        'rn' => Useful::std($request->company_rn),
        'state' => Address::state_code($request->company_state),
        'city' => Useful::std($request->company_city),
        'address' => Useful::std($request->company_address),
        'postal_code' => Useful::std($request->company_postal_code),
        'phone' => Useful::std($request->company_phone),
      ]);

      if (!$company)
        return Useful::nok(['dbc', 'متأسفانه مشکلی در ارتباط با سرور رخ داده است.']);

      $company_id = $company->id;
    }

    $shop = Shop::create([
      'user_id' => $user_id,
      'company_id' => $company_id ?? null,
      'name' => Useful::std($request->name),
      'category' => $request->category,
      'type' => $request->type,
      'tax_code' => Useful::std($request->tax_code),
      'domain' => Useful::domain_extractor($request->website),
      'website' => strtolower($request->website),
      'email' => $email,
      'state' => Address::state_code($request->state),
      'city' => Useful::std($request->city),
      'address' => Useful::std($request->address),
      'postal_code' => Useful::enum($request->postal_code),
      'phone' => Useful::std($request->phone),
      'phone_direct' => Useful::std($request->phone_direct),
      'about' => Useful::std($request->about),
      'status' => $request->type == 'offline' ? 'agreement' : 'pending',
    ]);

    if ($request->type == 'online') {
      $otp = mt_rand(100000, 999999);
      Mail::to($email)->send(new EshopVerification($otp));
      Verification::create([
        'prop' => 'email',
        'val' => $email,
        'type' => 'eshop',
        'verified_at' => null,
        'otp' => $otp,
        'otp_sent_at' => Carbon::now(),
        'ip' => Useful::ip(),
      ]);
    }

    if ($shop) return Useful::ok();
    return Useful::nok(['dbsh', 'متأسفانه مشکلی در ارتباط با سرور رخ داده است.']);
  }

  public function verify_domain(Request $request)
  {
    $user_id = Auth::user()->id;
    $shop = Shop::where('user_id', $user_id)->first();

    if ($shop->domain_verified_at)
      return Useful::ok('دامنه قبلاً اعتبارسنجی شده');

    $curl = Http::timeout(10)->get('https://' . $shop->domain . '/ghesta-' . ($shop->user_id + 1859) . '.txt');

    if ($curl->failed())
      return Useful::nok(['404', 'فایل موردنظر یافت نشد. لطفاً بعد از ایجاد فایل مجدداً تلاش کنید.']);

    if ($curl->successful()) {
      $shop->update(['domain_verified_at' => Carbon::now()]);
      return Useful::ok(null, [
        'custom_message' => [
          'msg_title' => '',
          'msg' => 'دامنه با موفقیت احراز شد.',
          'msg_id' => 0,
          'type' => 'success',
          'style' => 'float',
        ]
      ]);
    }
  }

  public function email_again(Request $request)
  {
    $user_id = Auth::user()->id;
    $shop = Shop::where('user_id', $user_id)->first();
    $email = $shop->email;

    $recent = Verification::where([
      'val' => $email,
      'type' => 'eshop',
    ])->where('otp_sent_at', '>', Carbon::now()->subMinutes(15))
      ->orderBy('id', 'desc')->first();

    if ($recent)
      return Useful::nok(['wait...', 'کد تأیید به تازگی برای شما ارسال شده، لطفاً صبر کنید تا ایمیل به دستتان برسد.']);

    if ($shop->email_verified_at)
      return Useful::ok('ایمیل قبلاً اعتبارسنجی شده');

    $verification = Verification::where([
      'val' => $email,
      'type' => 'eshop',
    ])->whereNull('verified_at')
      ->where('otp_sent_at', '>', Carbon::now()->subHour())
      ->orderBy('id', 'desc')->first();

    if ($verification && $verification->try_times < 3) {
      $otp = $verification->otp;
    } else {
      $otp = mt_rand(100000, 999999);
      Verification::create([
        'prop' => 'email',
        'val' => $email,
        'type' => 'eshop',
        'verified_at' => null,
        'otp' => $otp,
        'otp_sent_at' => Carbon::now(),
        'ip' => Useful::ip(),
      ]);
    }

    Mail::to($email)->send(new EshopVerification($otp));
    return Useful::ok(null, [
      'custom_message' => [
        'msg_title' => '',
        'msg' => 'ایمیل ارسال شد.',
        'msg_id' => 0,
        'type' => 'info',
        'style' => 'float',
      ]
    ]);
  }

  public function verify_email(Request $request)
  {
    $otp = $request->otp;
    if (!is_numeric($otp) || $otp < 100000 || $otp > 999999)
      return Useful::nok(['cheat', 'مقدار وارد شده نامعتبر است']);

    $user_id = Auth::user()->id;
    $shop = Shop::where('user_id', $user_id)->first();
    $email = $shop->email;

    $case = Verification::where([
      'val' => $email,
      'type' => 'eshop',
    ])->whereNull('verified_at')
      ->where('try_times', '<', 4)
      ->where('otp_sent_at', '>', Carbon::now()->subHours(12))
      ->orderBy('id', 'desc')->first();

    if ($case) {
      if ($case->otp == $otp) {
        $shop->update(['email_verified_at' => Carbon::now()]);
        $case->delete();
        return Useful::ok(null, [
          'custom_message' => [
            'msg_title' => '',
            'msg' => 'ایمیل احراز شد.',
            'msg_id' => 0,
            'type' => 'success',
            'style' => 'float',
          ]
        ]);
      }
      $case->update(['try_times' => $case->try_times + 1]);
      if ($case->try_times > 3) {
        $case->update(['track_id' => Useful::randomString(16)]);
      }
      return Useful::nok(['otp', 'کد وارد شده اشتباه است']);
    }
    return Useful::nok(['cheat', 'اطلاعات نامعتبر است']);
  }
}

<?php

namespace App\Http\Controllers;

use App\Models\Useful;
use App\Models\Address;
use App\Models\Company;
use App\Models\Doc;
use App\Models\Edate;
use App\Models\Ghest;
use App\Models\Order;
use App\Models\Organ;
use App\Models\Sms;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

class OrganController extends Controller
{
    //ADMIN
    public function index(Request $request)
    {
        $uri = explode('/', Route::current()->uri);
        $export = end($uri) == 'export' ? 1 : null;

        $query = Organ::search($request)->with(['user', 'company'])->withCount(['orders', 'orders_successful']);

        if ($export) {
            $organs = $query->get();
            /* $active_organs = $organs->select('id')->whereNotNull('start_at')->get();
            $aos = [];
            foreach ($active_organs as $ao) $aos[] = $ao->id;
            $organs_ghest_amount = Ghest::select('amount')->organ($aos)->get(); */
            foreach ($organs as $organ) {
                if ($organ->start_at) {
                    $organGhests = Ghest::select('id', 'amount', 'passed_at', 'ghest_date', 'shamsi', 'order_id')->organ($organ->id)->get();
                    $organ->totalGhests = $organGhests->sum('amount');
                    $organ->pastGhests = $organGhests->where('ghest_date', '<', Carbon::now())->sum('amount');
                    $organ->unpaidGhests = $organGhests->whereNull('passed_at')->sum('amount');
                    $organ->expiredGhests = $organGhests->where('ghest_date', '<', Carbon::now())->whereNull('passed_at')->sum('amount');
                }
            }
            return $this->export($organs);
        }

        $organs = $query->paginate(20);

        $new = [];
        $n = 0;
        foreach ($organs as $organ) {
            $organ->unpaidGhests = Ghest::organ($organ->id)->notPassed()->sum('amount');
            $organ->expiredGhests = Ghest::organ($organ->id)->expired()->sum('amount');
            if (in_array($organ->status, ['uploading', 'docs_uploaded'])) $organ->status = self::review_docs($organ)[0]['new_status'];
            $organ->remained_credit = $organ->credit - $organ->unpaidGhests;

            $user = $organ->user;

            $new[$n]['id'] = $organ->id;

            $col['name'] = [
                'title' => 'نام سازمان',
                'value' => $organ->fame,
                'addition' => $organ->name,
            ];

            $col['status'] = [
                'title' => 'وضعیت',
                'value' => Organ::$status[$organ->status]['farsi'],
                'type' => 'status',
                'css_classes' => 'badge-' . Organ::$status[$organ->status]['class'],
                'addition' => $organ->status == 'active' ? Edate::date_diff_carbon($organ->start_at) : '',
            ];

            $col['credit'] = [
                'title' => 'سقف ضمانت',
                'value' =>  $organ->credit,
                'type' =>  'toman',
                'addition' => $organ->credit > 0 ? 'مانده: ' . number_format($organ->remained_credit) : '',
            ];

            $col['orders'] = [
                'title' => 'سفارش موفق',
                'value' => $organ->orders_successful_count,
                'addition' => $organ->orders_count - $organ->orders_successful_count > 0 ? 'از ' . $organ->orders_count : '',
            ];

            $col['sale'] = [
                'title' => 'فروش موفق',
                'value' =>  number_format(Order::where('organ_id', $organ->id)->successful()->sum('amount')),
                'addition' => $organ->expiredGhests > 0 ? 'اقساط معوق: ' . number_format($organ->expiredGhests) : '',
            ];

            $col['owner'] = [
                'title' => 'رابط سازمان',
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
                        'route' => 'dash-organs-single',
                        'params' => ['id' => $organ->id],
                    ],
                    [
                        'value' => 'ویرایش اطلاعات',
                        'icon' => 'edit',
                        'btn_color' => 'primary',
                        'type' => 'modal',
                        'modal_name' => 'editModal',
                        'method' => 'post',
                        'endpoint' => '/admin/organs/' . $organ->id . '/edit',
                    ],
                    [
                        'value' => 'حذف سازمان',
                        'icon' => 'trash',
                        'css_classes' => 'text-danger',
                        'type' => 'confirm',
                        'method' => 'post',
                        'endpoint' => '/admin/organs/' . $organ->id . '/delete',
                    ],
                ],
            ];

            $new[$n]['td'] = $col;
            $col = null;
            ++$n;
        }
        return Useful::recollect($organs, $new);
    }

    public function export($organs)
    {
        $data[0] = [
            'ردیف',
            'نام یا شهرت',
            'نوع همکاری',
            'وضعیت',
            'تاریخ ثبت نام',
            'تاریخ شروع همکاری',
            'تعداد سفارشها',
            'سفارشهای کامل شده',
            'میزان فروش',
            'سقف ضمانت',
            'اعتبار باقیمانده',
            'کل اقساط',
            'اقساط سررسید شده',
            'معوقات',
        ];
        $n = 1;
        foreach ($organs as $organ) {
            $data[$n] = [
                $n,
                $organ->fame ?  $organ->fame :  $organ->name,
                $organ->level == 2 ? 'ویژه' : 'عادی',
                Organ::$status[$organ->status]['farsi'],
                Edate::edateFromCarbon('Y-m-d', $organ->created_at),
                $organ->start_at ? Edate::edateFromCarbon('Y-m-d', $organ->start_at) : '',
                $organ->orders_count,
                $organ->orders_successful_count,
                Order::where('organ_id', $organ->id)->successful()->sum('amount'),
                $organ->credit,
                $organ->credit - $organ->unpaidGhests,
                $organ->totalGhests ?? 0,
                $organ->pastGhests ?? 0,
                $organ->expiredGhests ?? 0,
            ];
            ++$n;
        }

        $url = ExportController::excel($data, 'organs');
        return Useful::ok(['url' => $url], ['action_type' => 'open_url']);
    }

    //ADMIN
    public function single(Request $request)
    {
        $organ = Organ::where('id', $request->id)->with(['company'])->first();
        $organ->docs = self::review_docs($organ);
        $organ->status = $organ->docs[0]['new_status'];
        $organ->status_farsi = Organ::$status[$organ->status]['farsi'];
        $organ->agent = User::info($organ->user_id);
        return Useful::ok($organ);
    }

    //ADMIN
    public function edit(Request $request)
    {
        $organ = Organ::find($request->id);
        if ($organ->update([
            'name' => $request->name,
            'fame' => $request->fame,
            'level' => $request->level,
            'payback_type' => $request->payback_type,
            'employees' => $request->employees,
            'age' => $request->age,
            'credit' => $request->credit,
            'negative_percent' => $request->negative_percent,
            'phone' => $request->phone,
            'phone_direct' => $request->phone_direct,
            'email' => $request->email,
            'website' => $request->website,
            'about' => $request->about,
            'agent_position' => $request->agent_position,
        ])) {
            Company::find($organ->company_id)->update([
                'state' => $request->state,
                'city' => $request->city,
                'address' => $request->address,
                'postal_code' => $request->postal_code,
            ]);
            return Useful::ok();
        }
        return Useful::nok(['dbo-e', 'خطا در اتصال به دیتابیس']);
    }

    //ADMIN 
    public function edit_fields(Request $request)
    {
        $organ = Organ::where('id', $request->id)->with('company')->first();

        $organ->agent = User::info($organ->user_id);

        $res = [
            [
                'section' => 'مشخصات سازمان',
                'fields' => [
                    [
                        'label' => 'نام سازمان',
                        'v_model' => 'name',
                        'value' => $organ->name,
                        'type' => 'text',
                    ],
                    [
                        'label' => 'شهرت',
                        'v_model' => 'fame',
                        'value' => $organ->fame,
                        'type' => 'text',
                        'width' => '6',
                    ],
                    [
                        'label' => 'سطح همکاری',
                        'v_model' => 'level',
                        'value' => $organ->level,
                        'type' => 'select',
                        'options' => Useful::list_maker(Organ::$levels),
                        'width' => '6',
                    ],
                    [
                        'label' => 'مدل ضمانت',
                        'v_model' => 'payback_type',
                        'value' => $organ->payback_type,
                        'type' => 'select',
                        'options' => Useful::list_maker(Organ::$payback_types),
                        // 'width' => '6',
                    ],
                    [
                        'label' => 'سایت',
                        'v_model' => 'website',
                        'value' => $organ->website,
                        'type' => 'text',
                        'width' => '6',
                    ],
                    [
                        'label' => 'ایمیل',
                        'v_model' => 'email',
                        'value' => $organ->email,
                        'type' => 'text',
                        'width' => '6',
                    ],
                    [
                        'label' => 'تعداد کارمندان',
                        'v_model' => 'employees',
                        'value' => $organ->employees,
                        'type' => 'select',
                        'options' => Useful::list_maker(Organ::$employees),
                        'width' => '6',
                    ],
                    [
                        'label' => 'سن سازمان',
                        'v_model' => 'age',
                        'value' => $organ->age,
                        'type' => 'select',
                        'options' => Useful::list_maker(Organ::$age),
                        'width' => '6',
                    ],
                    [
                        'label' => 'سقف ضمانت (تومان)',
                        'v_model' => 'credit',
                        'value' => $organ->credit,
                        'type' => 'text',
                        'mode' => 'price',
                        'width' => '6',
                    ],
                    [
                        'label' => 'درصد منفی مجاز',
                        'v_model' => 'negative_percent',
                        'value' => $organ->negative_percent,
                        'type' => 'text',
                        'mode' => 'percent',
                        'width' => '6',
                    ],
                    [
                        'label' => 'تلفن ثابت',
                        'v_model' => 'phone',
                        'value' => $organ->phone,
                        'type' => 'text',
                        'width' => '6',
                    ],
                    [
                        'label' => 'داخلی',
                        'v_model' => 'phone_direct',
                        'value' => $organ->phone_direct,
                        'type' => 'text',
                        'width' => '6',
                    ],
                    [
                        'label' => 'استان',
                        'v_model' => 'state',
                        'value' => $organ->company->state,
                        'type' => 'select',
                        'options' => Address::list_states(),
                        'width' => '6',
                    ],
                    [
                        'label' => 'شهر',
                        'v_model' => 'city',
                        'value' => $organ->company->city,
                        'type' => 'text',
                        'width' => '6',
                    ],
                    [
                        'label' => 'کدپستی',
                        'v_model' => 'postal_code',
                        'value' => $organ->company->postal_code,
                        'type' => 'text',
                        'width' => '6',
                    ],
                    [
                        'label' => 'آدرس',
                        'v_model' => 'address',
                        'value' => $organ->company->address,
                        'type' => 'textarea',
                    ],
                    [
                        'label' => 'درباره',
                        'v_model' => 'about',
                        'value' => $organ->about,
                        'type' => 'textarea',
                    ],
                    [
                        'label' => 'رابط سازمان',
                        'v_model' => 'agent_name',
                        'value' => $organ->agent->full_name,
                        'type' => 'text',
                        'width' => '6',
                        'disabled' => true,
                    ],
                    [
                        'label' => 'موبایل رابط',
                        'v_model' => 'agent_mobile',
                        'value' => $organ->agent->mobile,
                        'type' => 'text',
                        'width' => '6',
                        'disabled' => true,
                    ],
                ],
            ],
        ];

        return Useful::ok($res);
    }

    //ADMIN
    public function delete(Request $request)
    {
        if (Organ::find($request->id)->delete()) return Useful::ok();
        return Useful::nok(['dbo', 'خطا در اتصال به دیتابیس']);
    }

    //ADMIN
    public function change_status(Request $request)
    {
        $id = $request->id;
        $status = $request->status;

        switch ($status) {
            case 'active':
                Organ::find($id)->update(['status' => 'active']);
                $organ = Organ::find($id);
                if (empty($organ->start_at)) {
                    $organ->update(['start_at' => Carbon::now()]);
                    Sms::send(
                        Organ::mobile($organ->id),
                        "رابط محترم " . ($organ->fame ?? $organ->name) . "، سازمان شما در قسطا با کد سازمانی " . $organ->code . " فعال شد"
                    );
                }
                break;
            case 'inactive':
                Organ::find($id)->update(['status' => 'inactive']);
                break;
            case 'pending':
                Organ::find($id)->update(['status' => 'pending']);
                break;
            case 'uploading':
                Organ::find($id)->update(['status' => 'uploading']);
                break;
            default:
                break;
        }

        return Useful::ok();
    }

    //ADMIN
    protected function create_organ(Request $request)
    {
        $user_id = Auth::user()->id;
        if (Organ::where('user_id', $user_id)->exists())
            return Useful::ok(null, [
                'custom_message' => [
                    'msg_title' => 'خطا',
                    'msg' => '',
                    'msg_id' => 0,
                    'type' => 'danger',
                    'style' => 'modal',
                ]
            ]);

        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'employees' => ['required', 'numeric'],
            'age' => ['required', 'numeric'],
            'phone' => ['required', 'max:11'],
            'state' => ['required', 'string', 'max:30'],
            'city' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:200'],
            'agent_position' => ['required', 'string', 'max:10']
        ]);

        $company = Company::create([
            'name' => $request->name,
            'fame' => $request->fame,
            'state' => Address::state_code($request->state),
            'city' => $request->city,
            'address' => $request->address,
            'postal_code' => $request->postal_code,
            'phone' => $request->phone,
        ]);

        $organ = Organ::create([
            'user_id' => $user_id,
            'company_id' => $company->id,
            'name' => $request->name,
            'fame' => $request->fame,
            'employees' => $request->employees,
            'age' => $request->age,
            'website' => $request->website,
            'email' => $request->email,
            'phone' => $request->phone,
            'phone_direct' => $request->phone_direct,
            'about' => $request->about,
            'agent_position' => $request->position,
            'status' => 'uploading',
        ]);

        if ($organ) {
            $organ->update(['code' => (mt_rand(10, 99) . ($organ->id + 59) . mt_rand(0, 99))]);
            Sms::send(
                config('globals.cbdo.mobile'),
                "سازمان جدید با نام " . Organ::title($organ->id) . " ثبت نام کرد"
            );
            return Useful::ok();
        }
        return Useful::nok(['dbo', 'متأسفانه مشکلی در ارتباط با سرور رخ داده است.']);
    }

    public static function review_docs($organ)
    {
        $organ_id = $organ->id;
        $user_id = $organ->user_id;
        $docs = [];
        $n = 0;
        $point = 0;
        $point_accepted = 0;
        foreach (Doc::$registration['organ'][$organ->level] as $type) {
            $docTypes = Doc::$types;
            if ($docTypes[$type]['scope'] == 'user') {
                $doc = Doc::where('user_id', $user_id)->where('type', $type)->orderBy('id', 'desc')->first();
                $docs[$n]['url'] = '/api/upload/' . $type;
                $docs[$n]['concatUrl'] = '/concat/' . $type;
            } else {
                $doc = Doc::where('organ_id', $organ_id)->where('type', $type)->orderBy('id', 'desc')->first();
                $docs[$n]['url'] = '/api/organ/upload/' . $type;
                $docs[$n]['concatUrl'] = '/organ/concat/' . $type;
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
        $required_point = count(Doc::$registration['organ'][$organ->level]);

        $new_status = $organ->status;
        if ($organ->status == 'docs_uploaded') {
            if ($point < $required_point) {
                $new_status = 'uploading';
                $organ->update(['status' => $new_status, 'docs_uploaded_at' => null]);
            }
            if ($point_accepted == $required_point) {
                $new_status = 'final';
                $organ->update(['status' => $new_status, 'docs_accepted_at' => Carbon::now()]);
            }
        } else if ($organ->status == 'uploading') {
            if ($point_accepted == $required_point) {
                $new_status = 'final';
                $organ->update(['status' => $new_status, 'docs_accepted_at' => Carbon::now()]);
            } else if ($point == $required_point) {
                $new_status = 'docs_uploaded';
                $organ->update(['status' => $new_status, 'docs_uploaded_at' => Carbon::now()]);
            }
        }
        $docs[0]['new_status'] = $new_status;
        return $docs;
    }

    public function info(Request $request)
    {
        $user_id = Auth::user()->id;
        $organ = Organ::where('user_id', $user_id)->with(['company'])->first();
        if (in_array($organ->status, ['uploading', 'docs_uploaded'])) {
            $organ->docs = self::review_docs($organ);
            $organ->status = $organ->docs[0]['new_status'];
        } else $organ->docs = null;
        if ($organ->status == 'final') {
            return Useful::ok([
                'status' => 'final',
                'mode' => 'show_message',
                'message' => '
              تمامی مدارک شما توسط کارشناسان قسطا بررسی و تأیید شده است و در حال بررسی نهایی هستیم. 
              پس از تأیید نهایی، سازمان شما فعال خواهدشد و نتیجه از طریق پیامک به اطلاعتان خواهدرسید.'
            ]);
        }
        $organ->status_farsi = Organ::$status[$organ->status];
        $organ->credit = self::summary($organ->id);
        $organ->agent = User::info($organ->user_id);
        return Useful::ok($organ);
    }

    public function docs(Request $request)
    {
        $id = Auth::user()->id;
        $organ = Organ::where('user_id', $id)->first();
        if (in_array($organ->status, ['uploading', 'docs_uploaded'])) $docs = self::review_docs($organ);
        else $docs = null;
        return Useful::ok(['cta' => null, 'docs' => $docs]);
    }

    public function last_pending_orders(Request $request)
    {
        $user = Auth::user();
        $organ_id = Organ::select('id')->where('user_id', $user->id)->first()->id;
        $orders = Order::pendedByOrgan($organ_id)->orderBy('docs_uploaded_at', 'asc')->offset(0)->limit(10)->get();
        $new = [];
        $n = 0;
        foreach ($orders as $order) {
            $user = User::info($order->user_id);
            $new[$n]['id'] = $order->id;
            $col['title'] = [
                'title' => 'کاربر',
                'value' => $user->full_name,
                'addition' => $user->mobile,
            ];
            $col['order'] = [
                'title' => 'سفارش',
                'value' => $order->oid,
                'css_classes' => 'ltr nova-font'
            ];
            $col['amount'] = [
                'title' => 'مبلغ',
                'value' => number_format($order->amount),
                'addition' => $order->months . ' ماهه، ' . $order->cheques . ' قسط',
                'tooltip' => 'پیش پرداخت: ' . number_format($order->prepayment) . ' تومان'
            ];
            $col['credit'] = [
                'title' => 'اعتبار مورد نیاز',
                'value' => $order->ghest * $order->cheques,
                'type' => 'toman'
            ];
            $col['action'] = [
                'type' => 'button',
                'buttons' => [
                    [
                        'value' => 'تأیید',
                        'icon' => 'check',
                        'btn_color' => 'success',
                        'type' => 'confirm',
                        'method' => 'post',
                        'endpoint' => '/organ/orders/' . $order->oid . '/accept',
                    ],
                    [
                        'value' => 'رد',
                        'icon' => 'times',
                        'css_classes' => 'text-danger',
                        'type' => 'modal',
                        'modal_name' => 'rejectModal',
                        'method' => 'post',
                        'endpoint' => '/organ/orders/' . $order->oid . '/reject',
                    ],
                ],
            ];
            $new[$n]['td'] = $col;
            $col = null;
            ++$n;
        }
        return Useful::ok($new);
    }

    public static function summary($organ_id = null)
    {
        if (is_null($organ_id)) $organ = Organ::select('id', 'credit', 'negative_percent')->where('user_id', Auth::user()->id)->first();
        else $organ = Organ::select('id', 'credit', 'negative_percent')->where('id', $organ_id)->first();
        $ghests_locked = Ghest::organ($organ->id)->notPassed()->sum('amount');
        $ghests_credit_extra = $organ->credit * ($organ->negative_percent / 100);
        $ghests_remained = $organ->credit - $ghests_locked;
        $ghests_remained_extra = $ghests_remained + $ghests_credit_extra;
        $ghests_expired_amount = Ghest::organ($organ->id)->expired()->sum('amount');
        return (object)[
            'total' => $organ->credit,
            'total_extra' => $organ->credit + $ghests_credit_extra,
            'locked' => (int)$ghests_locked,
            'available' => $ghests_remained,
            'available_extra' => $ghests_remained_extra,
            'expired' => $ghests_expired_amount,
        ];
    }

    public function check_code(Request $request, $code = null)
    {
        is_null($code) ? $organ_code = $request->code : $organ_code = $code;
        if (Organ::where(['code' => $organ_code, 'status' => 'active'])->exists()) return Useful::ok();
        else {
            return Useful::nok([
                'name' => 'organ_not_found',
                'message' => 'کد سازمانی معتبر نیست'
            ]);
        }
    }
}

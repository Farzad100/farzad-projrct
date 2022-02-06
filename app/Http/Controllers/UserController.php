<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Edate;
use App\Models\Useful;
use App\Models\Address;
use App\Models\Doc;
use App\Models\Ghest;
use App\Models\ICBS;
use App\Models\Order;
use App\Models\Organ;
use App\Models\Shop;
use App\Models\Sms;
use App\Models\Usermeta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class UserController extends Controller
{
  public function login(Request $request)
  {

    $request->validate([
      'username' => ['required', 'string', 'min:10', 'max:15'],
    ]);
    $username = Useful::enum($request->username, 'username');

    //Temp Login Failed
    /* if (User::where('username', $username)->first()->is_admin < 11) {
      return Useful::ok(null, [
        'custom_message' => [
          'msg_title' => '',
          'msg' => '<p>' . 'کاربر گرامی، ضمن عرض پوزش بعلت بروز مشکلات فنی، ورود به سایت تا چندساعت آینده مقدور نمی باشد. لطفاً چندساعت دیگر مجددا تلاش کنید.' . '</p>',
          'msg_id' => 0,
          'type' => 'warning',
          'icon' => 'exclamation-triangle',
          'style' => 'modal',
          'buttons' => [
            [
              'text' => 'بستن',
              'type' => 'light',
              'mode' => 'close',
            ],
          ]
        ]
      ]);
    } */
    
    if (Auth::attempt(['username' => $username, 'password' => $request->password])) {
      $user = Auth::user();
      $agent_string = User::agent()->overview;
      $token = $user->createToken($agent_string);
      $a = $token->accessToken;
      return Useful::ok([
        'roles' => User::roles($user),
        'token' => $a
      ]);
    }
    return Useful::nok();
  }

  public function logout()
  {
    Auth::user()->token()->revoke();
    return Useful::ok();
  }

  public function my_info()
  {
    $user = Auth::user();

    return Useful::ok([
      'username' => $user->username,
      'mobile' => '0' . $user->username,
      'email' => $user->email,
      'fname' => $user->fname,
      'lname' => $user->lname,
      'nid' => $user->nid,
      'is_verified' => $user->verified_at ? true : false,
      'roles' => User::roles($user),
      'birth' => $user->birth ? $user->birth : null,
      'docs' => self::review_docs(),
      'editable' => $user->verified_at ? false : true,
      'is_filled' => $user->nid && $user->birth
    ]);
  }

  public function edit_info(Request $request)
  {
    $user = Auth::user();
    $arr = ['email' => $request->email];
    $user->update($arr);
    return Useful::ok();
  }

  public function addresses(Request $request)
  {
    $user_id = Auth::user()->id;

    $addresses = Address::index($user_id);

    return Useful::ok([
      'addresses' => $addresses,
      'is_filled' => Address::where('user_id', $user_id)->where('type', 'home')->exists() ? true : false
    ]);
  }

  public function edit_address(Request $request)
  {
    $user = Auth::user();
    $type = $request->type;
    $state = Address::state_code($request->state);
    $city = Useful::std($request->city);
    $address = Useful::std($request->address);
    $postal_code = Useful::enum($request->postal_code);
    $phone = Useful::enum($request->phone, 'phone');

    if (!in_array($type, ['home', 'work'])) return Useful::nok(['type_invalid', 'نوع آدرس نامعتبر است']);

    if (mb_strlen($address, 'UTF-8') < 10) return Useful::nok(['address_invalid', 'آدرس نامعتبر است']);
    // $adr = Address::where(['user_id' => $user->id, 'type' => $type])->whereNotNull('address_verified_at')->orderBy('id', 'desc')->first();
    Address::create([
      'user_id' => $user->id,
      'type' => $type,
      'state' => $state,
      'city' => $city,
      'address' => $address,
      'postal_code' => $postal_code,
      'phone' => $phone,
    ]);

    return Useful::ok();
  }

  //ADMIN
  public function index(Request $request)
  {
    $method = ICBS::$method_MobileAndNationalCodeMatching;
    $uri = explode('/', Route::current()->uri);
    $role = $uri[1];
    $export = end($uri) == 'export' ? 1 : null;

    $users = User::search($request)->with([
      'addresses' => function ($q) {
        $q->select('id', 'user_id', 'state', 'city')->where('type', 'home')->orderBy('id', 'desc')->limit(1);
      },
      'orders' => function ($q) {
        $q->select('id', 'user_id', 'status');
      },
    ]);

    if ($export) return $this->export($users->get());

    $users = $users->paginate(20);

    $new = [];
    $n = 0;
    foreach ($users as $user) {
      $orders_all = count($user->orders);
      $orders_completed = $user->orders->where('status', 'completed')->count();
      $orders_cycle = $user->orders->where('status', 'cycle')->count();

      $new[$n]['id'] = $user->id;
      $col['name'] = [
        'title' => 'نام کاربر',
        'value' => $user->full_name,
        'addition' => '0' . $user->username,
        'badge' => $user->badge,
        'is_verified' => $user->verified_at ? true : false,
      ];
      $col['nid'] = [
        'title' => 'کد ملی',
        'value' => $user->nid,
        'addition' => $user->birth
      ];
      $col['utm'] = [
        'title' => 'UTM',
        'value' => $user->utm_source,
        'addition' => $user->utm_medium ? 'med: ' . $user->utm_medium : '',
      ];
      $col['orders'] = [
        'nosort' => true,
        'title' => 'سفارشها',
        'value' => $orders_all > 0 ? 'موفق: ' . ($orders_completed + $orders_cycle) : '',
        'addition' => $orders_all > 0 ? 'از مجموع ' . $orders_all . ' سفارش' : '',
      ];
      $col['date'] = [
        'title' => 'ثبت نام',
        'value' => Edate::edateFromCarbon('j F Y', $user->created_at),
        'addition' => 'ساعت ' . Edate::edateFromCarbon('H', $user->created_at)
      ];
      $col['action'] = [
        'type' => 'button',
        'buttons' => [
          [
            'value' => 'ویرایش',
            'icon' => 'edit',
            'btn_color' => 'primary',
            'type' => 'modal',
            'modal_name' => 'editModal',
            'method' => 'post',
            'endpoint' => '/admin/users/' . $user->id . '/edit',
          ],
          [
            'value' => 'یادداشت ها',
            'icon' => 'sticky-note',
            'btn_color' => 'primary',
            'type' => 'modal',
            'modal_name' => 'noteModal',
            'method' => 'get',
            'endpoint' => '/admin/users/' . $user->id . '/notes',
            'note_id' => $user->id
          ],
          [
            'value' => 'استعلام',
            'icon' => 'fingerprint',
            'type' => 'modal',
            'modal_name' => 'inquiryModal',
            'method' => 'post',
            'endpoint' => '/admin/users/' . $user->id . '/inquiry',
          ],
          [
            'value' => 'حذف کاربر',
            'icon' => 'trash',
            'css_classes' => 'text-danger',
            'type' => 'confirm',
            'method' => 'post',
            'endpoint' => '/admin/users/' . $user->id . '/delete',
          ],
        ],
      ];
      $new[$n]['td'] = $col;
      $col = null;
      ++$n;
    }
    return Useful::recollect($users, $new);
  }

  public function export($users)
  {
    $data[0] = [
      'ردیف',
      'موبایل',
      'نام',
      'نام خانوادگی',
      'کد ملی',
      'تولد',
      'استان',
      'شهر',
      'سفارشها',
      'س.پایان یافته',
      'س.دوره',
      'س.فرآیند',
      'س.بی ارزش',
      'تاریخ ثبت نام',
      'ساعت ثبت نام',
      'تلفن ثابت',
      'ایمیل',
      'source',
      'medium',
      'campaign',
      'content',
    ];

    $n = 1;
    foreach ($users as $user) {
      $orders_all = $user->orders->count();
      $orders_worthless = $user->orders->whereIn('status', ['cancelled', 'rejected'])->count();
      $orders_completed = $user->orders->where('status', 'completed')->count();
      $orders_cycle = $user->orders->where('status', 'cycle')->count();
      $orders_inprogress = $orders_all - $orders_completed - $orders_cycle - $orders_worthless;

      $data[$n] = [
        $n,
        $user->username,
        $user->fname,
        $user->lname,
        $user->nid,
        $user->birth,
        count($user->addresses) > 0 ? Address::state_name($user->addresses[0]->state) : '',
        count($user->addresses) > 0 ? $user->addresses[0]->city : '',
        $orders_all,
        $orders_completed,
        $orders_cycle,
        $orders_inprogress,
        $orders_worthless,
        Edate::edateFromCarbon('Y-m-d', $user->created_at),
        Edate::edateFromCarbon('H:i', $user->created_at),
        count($user->addresses) > 0 ? $user->addresses[0]->phone : '',
        $user->email,
        $user->utm_source,
        $user->utm_medium,
        $user->utm_campaign,
        $user->utm_content,
      ];
      ++$n;
    }

    $url = ExportController::excel($data, 'users');
    return Useful::ok(['url' => $url], ['action_type' => 'open_url']);
  }

  //ADMIN
  public function single(Request $request)
  {
    $id = $request->id;
    $user = User::info($id, 'all');
    return Useful::ok($user);
  }

  //ADMIN
  public function edit(Request $request)
  {
    $id = $request->id;
    foreach (['fname', 'lname', 'nid', 'birth', 'badge', 'email'] as $item) {
      $u[$item] = $request->$item;
    }
    User::find($id)->update($u);
    $home_address = $request->home_address;
    $work_address = $request->work_address;
    if (!empty($home_address)) {
      if (Address::where(['user_id' => $id, 'type' => 'home'])->doesntExist()) {
        Address::create(['user_id' => $id, 'type' => 'home']);
      }
      Address::where(['user_id' => $id, 'type' => 'home'])->orderBy('id', 'desc')->limit(1)->update([
        'state' => $request->home_state,
        'city' => $request->home_city,
        'address' => $request->home_address,
        'postal_code' => $request->home_postal_code,
        'phone' => $request->home_phone,
      ]);
    }
    if (!empty($work_address)) {
      if (Address::where(['user_id' => $id, 'type' => 'work'])->doesntExist()) {
        Address::create(['user_id' => $id, 'type' => 'work']);
      }
      Address::where(['user_id' => $id, 'type' => 'work'])->orderBy('id', 'desc')->limit(1)->update([
        'state' => $request->work_state,
        'city' => $request->work_city,
        'address' => $request->work_address,
        'postal_code' => $request->work_postal_code,
        'phone' => $request->work_phone,
      ]);
    }
    return Useful::ok();
  }

  //ADMIN
  public function edit_fields(Request $request)
  {
    $user = User::where('id', $request->id)->first();

    foreach (['home', 'work'] as $type) {
      $addresses = Address::where(['user_id' => $user->id, 'type' => $type])->orderBy('id', 'desc')->first();
      if ($addresses) {
        $state[$type] = $addresses->state;
        $city[$type] = $addresses->city;
        $address[$type] = $addresses->address;
        $postal_code[$type] = $addresses->postal_code;
        $phone[$type] = $addresses->phone;
      } else {
        $state[$type] = null;
        $city[$type] = null;
        $address[$type] = null;
        $postal_code[$type] = null;
        $phone[$type] = null;
      }
    }
    $res = [
      [
        'section' => 'مشخصات فردی',
        'size' => 'lg',
        'fields' => [
          [
            'label' => 'نام',
            'v_model' => 'fname',
            'value' => $user->fname,
            'type' => 'text',
            'width' => '6',
          ],
          [
            'label' => 'نام خانوادگی',
            'v_model' => 'lname',
            'value' => $user->lname,
            'type' => 'text',
            'width' => '6',
          ],
          [
            'label' => 'تاریخ تولد',
            'v_model' => 'birth',
            'value' => $user->birth,
            'type' => 'date',
            'values' => [
              'y' => $user->birth ? explode('-', $user->birth)[0] : null,
              'm' => $user->birth ? explode('-', $user->birth)[1] : null,
              'd' => $user->birth ? explode('-', $user->birth)[2] : null,
            ],
          ],
          [
            'label' => 'کدملی',
            'v_model' => 'nid',
            'value' => $user->nid,
            'type' => 'text',
            'width' => '6',
          ],
          [
            'label' => 'موبایل',
            'v_model' => 'mobile',
            'value' => $user->username,
            'type' => 'text',
            'width' => '6',
            'disabled' => true,
          ],
          [
            'label' => 'ایمیل',
            'v_model' => 'email',
            'value' => $user->email,
            'type' => 'text',
            'width' => '6',
          ],
          [
            'label' => 'برچسب',
            'v_model' => 'badge',
            'value' => $user->badge,
            'type' => 'select',
            'options' => [
              [
                'label' => 'سفید',
                'value' => '',
              ],
              [
                'label' => 'زرد',
                'value' => 'yellow',
              ],
              [
                'label' => 'قرمز',
                'value' => 'red',
              ],
              [
                'label' => 'خاکستری',
                'value' => 'gray',
              ],
            ],
            'width' => '6',
          ],
        ],
      ],
      [
        'section' => 'آدرس محل سکونت',
        'size' => 'lg',
        'fields' => [
          [
            'label' => 'استان',
            'v_model' => 'home_state',
            'value' => $state['home'],
            'type' => 'select',
            'options' => Address::list_states(),
            'width' => '6',
          ],
          [
            'label' => 'شهر',
            'v_model' => 'home_city',
            'value' => $city['home'],
            'type' => 'text',
            'width' => '6',
          ],
          [
            'label' => 'کدپستی',
            'v_model' => 'home_postal_code',
            'value' => $postal_code['home'],
            'type' => 'text',
            'width' => '6',
          ],
          [
            'label' => 'تلفن ثابت',
            'v_model' => 'home_phone',
            'value' => $phone['home'],
            'type' => 'text',
            'width' => '6',
          ],
          [
            'label' => 'آدرس',
            'v_model' => 'home_address',
            'value' => $address['home'],
            'type' => 'textarea',
          ],
        ],
      ],
      [
        'section' => 'آدرس محل کار',
        'size' => 'lg',
        'fields' => [
          [
            'label' => 'استان',
            'v_model' => 'work_state',
            'value' => $state['work'],
            'type' => 'select',
            'options' => Address::list_states(),
            'width' => '6',
          ],
          [
            'label' => 'شهر',
            'v_model' => 'work_city',
            'value' => $city['work'],
            'type' => 'text',
            'width' => '6',
          ],
          [
            'label' => 'کدپستی',
            'v_model' => 'work_postal_code',
            'value' => $postal_code['work'],
            'type' => 'text',
            'width' => '6',
          ],
          [
            'label' => 'تلفن ثابت',
            'v_model' => 'work_phone',
            'value' => $phone['work'],
            'type' => 'text',
            'width' => '6',
          ],
          [
            'label' => 'آدرس',
            'v_model' => 'work_address',
            'value' => $address['work'],
            'type' => 'textarea',
          ],
        ],
      ],
    ];

    return Useful::ok($res);
  }

  //ADMIN
  public function meta(Request $request)
  {
    $inputs = [];

    $sections = $this->meta_fields($request)['result'];

    foreach ($sections as $section) {
      $fields = $section['fields'];
      foreach ($fields as $field) {
        $key = $field['v_model'];
        $inputs[$key] = $request->$key;
      }
    }

    $user_id = Auth::user()->id;
    if (Usermeta::where('user_id', $user_id)->update($inputs))
      return Useful::ok();
    return Useful::nok(['dbsh-um', 'خطا در اتصال به دیتابیس']);
  }

  //ADMIN
  public function meta_fields()
  {
    $user_id = Auth::user()->id;
    // $user_id = $request->user_id ? $request->user_id : Auth::user()->id;

    $usermeta = Usermeta::where('user_id', $user_id)->first();
    if (!$usermeta)
      $usermeta = Usermeta::create([
        'user_id' => $user_id
      ]);

    $section_personal = [
      'section' => 'مشخصات فردی',
      'size' => 'lg',
      'fields' => [
        [
          'label' => 'جنسیت',
          'v_model' => 'gender',
          'value' => $usermeta->gender,
          'type' => 'radio',
          'options' => [
            ['label' => 'خانم', 'value' => 'F'],
            ['label' => 'آقا', 'value' => 'M']
          ],
          'validation_rules' => 'required',
          'required' => true
        ],
        [
          'label' => 'وضعیت تأهل',
          'v_model' => 'marital',
          'value' => $usermeta->marital,
          'type' => 'radio',
          'options' => Useful::list_maker(Usermeta::$martial_status),
          'validation_rules' => 'required',
          'required' => true
        ],
        [
          'label' => 'تعداد فرزندان',
          'v_model' => 'children',
          'value' => $usermeta->children,
          'type' => 'radio',
          'options' => [
            [
              'label' => '0',
              'value' => '0',
            ],
            [
              'label' => '1',
              'value' => '1',
            ],
            [
              'label' => '2',
              'value' => '2',
            ],
            [
              'label' => '3',
              'value' => '3',
            ],
            [
              'label' => '4',
              'value' => '4',
            ],
            [
              'label' => '5',
              'value' => '5',
            ],
            [
              'label' => '6',
              'value' => '6',
            ],
            [
              'label' => '7',
              'value' => '7',
            ],
            [
              'label' => '8',
              'value' => '8',
            ],
            [
              'label' => '9',
              'value' => '9',
            ],
            [
              'label' => '10',
              'value' => '10',
            ],
          ],
          'validation_rules' => 'required',
          'required' => true
        ],
        [
          'label' => 'وضعیت تملک محل سکونت',
          'v_model' => 'home_ownership',
          'value' => $usermeta->home_ownership,
          'type' => 'radio',
          'options' => Useful::list_maker(Usermeta::$home_ownership),
          'validation_rules' => 'required',
          'required' => true
        ],
        [
          'label' => 'تعداد افراد تحت تکفل',
          'v_model' => 'dependants',
          'value' => $usermeta->dependants,
          'type' => 'radio',
          'options' => [
            [
              'label' => '0',
              'value' => '0',
            ],
            [
              'label' => '1',
              'value' => '1',
            ],
            [
              'label' => '2',
              'value' => '2',
            ],
            [
              'label' => '3',
              'value' => '3',
            ],
            [
              'label' => '4',
              'value' => '4',
            ],
            [
              'label' => '5',
              'value' => '5',
            ],
            [
              'label' => '6',
              'value' => '6',
            ],
            [
              'label' => '7',
              'value' => '7',
            ],
            [
              'label' => '8',
              'value' => '8',
            ],
            [
              'label' => '9',
              'value' => '9',
            ],
            [
              'label' => '10',
              'value' => '10',
            ],
          ],
          'validation_rules' => 'required',
          'required' => true
        ],
        [
          'label' => 'از آخرین جابه جایی محل سکونتتان حدوداً چه مدت می گذرد؟',
          'v_model' => 'residence_years',
          'value' => $usermeta->residence_years,
          'type' => 'range',
          'value_name' => 'سال',
          'min' => '0',
          'max' => '30',
          'step' => '1',
          'validation_rules' => 'required',
          'required' => true
        ],
      ]
    ];

    $section_professional = [
      'section' => 'مشخصات شغلی',
      'size' => 'lg',
      'fields' => [
        [
          'label' => 'چندسال تجربه کاری دارید؟',
          'v_model' => 'job_exprience',
          'value' => $usermeta->job_exprience,
          'type' => 'range',
          'value_name' => 'سال',
          'min' => '0',
          'max' => '50',
          'step' => '1',
          'validation_rules' => 'required',
          'required' => true
        ],
        [
          'label' => 'نوع شغل اصلی:',
          'v_model' => 'job_section',
          'value' => $usermeta->job_section,
          'type' => 'radio',
          'options' => Useful::list_maker(Usermeta::$job_section),
          'validation_rules' => 'required',
          'required' => true
        ],
        [
          'label' => 'نوع قرارداد شما در شغل فعلیتان چیست؟',
          'v_model' => 'job_contract_type',
          'value' => $usermeta->job_contract_type,
          'type' => 'radio',
          'options' => Useful::list_maker(Usermeta::$job_contract_type),
          'validation_rules' => 'required',
          'required' => true
        ],
        [
          'label' => 'کدام یک از حوزه های زیر به شغل شما نزدیک تر ست؟',
          'v_model' => 'job_category',
          'value' => $usermeta->job_category,
          'type' => 'radio',
          'options' => Useful::list_maker(Usermeta::$job_category),
          'validation_rules' => 'required',
          'required' => true
        ],
      ]
    ];

    $section_finance = [
      'section' => 'وضعیت اعتباری-بانکی',
      'size' => 'lg',
      'fields' => [
        [
          'label' => 'چندسال است که دسته چک دارید؟',
          'v_model' => 'fin_cheque_years',
          'value' => $usermeta->fin_cheque_years,
          'type' => 'range',
          'value_name' => 'سال',
          'min' => '0',
          'max' => '50',
          'step' => '1',
          'validation_rules' => 'required',
          'required' => true
        ],
        [
          'label' => 'درآمد یا حقوق ماهانه حرفه اصلی شما در سال جاری حدوداً چقدر است؟',
          'v_model' => 'fin_salary',
          'value' => $usermeta->fin_salary,
          'type' => 'range',
          'value_name' => 'تومان',
          'min' => '0',
          'max' => '30000000',
          'step' => '500000',
          'validation_rules' => 'required',
          'required' => true
        ],
        [
          'label' => 'درآمد ماهانه همسر شما حدوداً چقدر است؟',
          'v_model' => 'fin_salary_spouse',
          'value' => $usermeta->fin_salary_spouse,
          'type' => 'range',
          'value_name' => 'تومان',
          'min' => '0',
          'max' => '30000000',
          'step' => '500000',
          'validation_rules' => 'required',
          'required' => true
        ],
        [
          'label' => 'سایر درآمدهای شما حدوداً چقدر است؟',
          'v_model' => 'fin_salary_etc',
          'value' => $usermeta->fin_salary_etc,
          'type' => 'range',
          'value_name' => 'تومان',
          'min' => '0',
          'max' => '30000000',
          'step' => '500000',
          'validation_rules' => 'required',
          'required' => true
        ],
        [
          'label' => 'مخارج ضروری زندگیتان در ماه حدوداً چقدر است؟',
          'v_model' => 'fin_costs',
          'value' => $usermeta->fin_costs,
          'description' => 'منظور از مخارج ضروری، چیزهایی مثل غذا، پوشاک، مسکن و... است.',
          'type' => 'range',
          'value_name' => 'تومان',
          'min' => '0',
          'max' => '30000000',
          'step' => '500000',
          'validation_rules' => 'required',
          'required' => true
        ],
        [
          'label' => 'مخارج غیرضروری زندگیتان در ماه حدوداً چقدر است؟',
          'v_model' => 'fin_costs_etc',
          'value' => $usermeta->fin_costs_etc,
          'type' => 'range',
          'value_name' => 'تومان',
          'min' => '0',
          'max' => '30000000',
          'step' => '500000',
          'validation_rules' => 'required',
          'required' => true
        ],
      ]
    ];

    $section_assets = [
      'section' => 'دارایی ها',
      'size' => 'lg',
      'fields' => [
        [
          'label' => 'وضعیت مالکیت خودرو',
          'v_model' => 'assets_car',
          'value' => $usermeta->assets_car,
          'type' => 'radio',
          'options' => Useful::list_maker(Usermeta::$assets_car),
          'validation_rules' => 'required',
          'required' => true
        ],
        [
          'label' => 'در صورتی که خودرو دارید، برند آن را بنویسید:',
          'v_model' => 'assets_car_brand',
          'value' => $usermeta->assets_car_brand,
          'type' => 'text',
          'maxlength' => '30',
        ],
        [
          'label' => 'کدام یک از موارد زیر را دارید؟',
          'v_model' => 'assets_haves',
          'value' => $usermeta->assets_haves ? $usermeta->assets_haves : [],
          'type' => 'checkbox',
          'options' => Useful::list_maker(Usermeta::$assets_haves),
          'validation_rules' => 'required',
          'required' => true
        ]
      ]
    ];

    $res = [
      $section_personal,
      $section_professional,
      $section_finance,
      $section_assets,
    ];

    return Useful::ok($res);
  }

  //ADMIN
  public function forms_fill_status(Request $request)
  {
    $inputs = [];

    $sections = $this->meta_fields()['result'];

    foreach ($sections as $section) {
      $fields = $section['fields'];
      foreach ($fields as $field) {
        $key = $field['v_model'];
        if ($key == 'assets_car_brand') continue;
        $value = $field['value'];
        $inputs[$key] = $value;
      }
    }

    $null_count = 0;
    foreach ($inputs as $key => $value) {
      if (is_null($value)) ++$null_count;
    }

    $input_count = count($inputs) - 1;

    $form_extra = round((1 - ($null_count / $input_count)) * 100);
    if ($form_extra < 50) $form_extra = 0;

    $user = User::select('nid', 'birth', 'verified_at', 'email')->whereId(Auth::user()->id)->first();

    if ($user->verified_at) $form_personal = 100;
    else {
      $null_count = 0;
      if (is_null($user->nid)) ++$null_count;
      if (is_null($user->birth)) ++$null_count;
      if (is_null($user->email)) ++$null_count;
      $form_personal = round((1 - ($null_count / 6)) * 100);
    }

    $address = Address::select('address')->where('type', 'home')->where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();
    ($address && $address->address) ? $form_address_home = 100 : $form_address_home = 0;

    $address = Address::select('address')->where('type', 'work')->where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();
    ($address && $address->address) ? $form_address_work = 100 : $form_address_work = 0;

    return Useful::ok([
      'overall' => round(((4 * $form_personal) + (2 * $form_address_home) + (1 * $form_address_work) + (3 * $form_extra)) / 10),
      'form_personal' => $form_personal,
      'form_address_home' => $form_address_home,
      'form_address_work' => $form_address_work,
      'form_extra' => $form_extra,
    ]);
  }

  //ADMIN
  public function delete(Request $request)
  {
    $id = $request->id;
    if (User::find($id)->delete()) {
      Ghest::user($id)->delete();
      Order::where('user_id', $id)->delete();
      Shop::where('user_id', $id)->delete();
      Organ::where('user_id', $id)->delete();
      return Useful::ok();
    }
    return Useful::nok(['dbu']);
  }

  public static function review_docs($user = null)
  {
    $user = Auth::user();
    $user_id = $user->id;
    $docs = [];
    $n = 0;
    foreach (['ncf'] as $type) {
      $docTypes = Doc::$types;
      $doc = Doc::where('user_id', $user_id)->where('type', $type)->first();
      $docs[$n]['url'] = '/api/upload/' . $type;
      $docs[$n]['concatUrl'] = '/concat/' . $type;
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
            $docs[$n]['message'] = 'این مدرک به دلیل ' . $doc->reason . ' تأیید نشده.';
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
      $docs[$n]['required'] = true;
      $docs[$n]['name'] = $type;
      $docs[$n]['title'] = $docTypes[$type]['title'];
      $docs[$n]['description'] = $docTypes[$type]['description'];
      if (isset($docTypes[$type]['acceptedFiles'])) $docs[$n]['acceptedFiles'] = $docTypes[$type]['acceptedFiles'];
      $docs[$n]['maxFiles'] = $docTypes[$type]['maxFiles'];
      $docs[$n]['title'] = $docTypes[$type]['title'];

      if (User::is_admin()) {
        $docs[$n]['url'] .= '/' . $user_id;
        $docs[$n]['concatUrl'] .= '/' . $user_id;
      }

      ++$n;
    }
    return $docs;
  }

  //ADMIN
  public function send_message(Request $request)
  {
    $id = $request->id;
    $text = $request->message;
    $request->validate(['message' => ['required', 'string']]);
    $uri = Route::current()->uri;
    $type = substr(explode('/', $uri)[2], 0, -1);
    switch ($type) {
      case 'shop':
        $shop = Shop::select('id', 'user_id')->whereId($id)->with('user', function ($q) {
          $q->select('id', 'username');
        })->first();
        $mobile = $shop->user->username;
        break;
      case 'organ':
        $organ = Organ::select('id', 'user_id')->whereId($id)->with('user', function ($q) {
          $q->select('id', 'username');
        })->first();
        $mobile = $organ->user->username;
        break;
      case 'order':
        $order = Order::select('id', 'user_id')->where('oid', $request->oid)->with('user', function ($q) {
          $q->select('id', 'username');
        })->first();
        $mobile = $order->user->username;
        $id = $order->id;
        break;
      case 'user':
        $mobile = User::select('username')->whereId($id)->first()->username;
        break;
    }

    if (Sms::send($mobile, $text)) NoteController::make($type, $id, $text, 'یک پیامک به 0' . $mobile . ' ارسال شد', 'sms');

    return Useful::ok();
  }

  //ADMIN
  public function chart_overview(Request $request)
  {
    $states = [
      [
        'name' => 'today',
        'title' => 'امروز',
        'from_date' => Edate::edate('Y-m-d'),
        'to_date' => Edate::edate('Y-m-d'),
      ],
      [
        'name' => 'yesterday',
        'title' => 'دیروز',
        'from_date' => Edate::edate('Y-m-d', time() - 82000),
        'to_date' => Edate::edate('Y-m-d', time() - 82000),
      ],
      [
        'name' => 'this_month',
        'title' => 'ماه جاری' . ' (' . Edate::edate('F') . ')',
        'from_date' => Edate::this_month_span()[0],
        'to_date' => Edate::this_month_span()[1],
      ],
      [
        'name' => 'last_month',
        'title' => 'ماه گذشته' . ' (' . Edate::last_month_span()[2] . ')',
        'from_date' => Edate::last_month_span()[0],
        'to_date' => Edate::last_month_span()[1],
      ],
      [
        'name' => 'this_year',
        'title' => 'سال جاری',
        'from_date' => Edate::edate('Y-') . '01-01',
        'to_date' => Edate::edate('Y-m-d'),
      ],
      [
        'name' => 'last_year',
        'title' => 'سال گذشته',
        'from_date' => Edate::last_year_span()[0],
        'to_date' => Edate::last_year_span()[1],
      ],
    ];

    $new = [];
    $n = 0;
    foreach ($states as $state) {
      $organic = User::select('id')->whereNull('utm_source');
      $ads = User::select('id')->whereNotNull('utm_source');
      $users_count_organic = $organic->searchDate('created_at', $state['from_date'], $state['to_date'])->count();
      $users_count_ads = $ads->searchDate('created_at', $state['from_date'], $state['to_date'])->count();

      $col['date'] = [
        'title' => '',
        'value' => $state['title'],
      ];
      $col['organic'] = [
        'title' => 'ارگانیک',
        'value' => (int) $users_count_organic,
        'type' => 'countable'
      ];
      $col['ads'] = [
        'title' => 'تبلیغات',
        'value' => (int) $users_count_ads,
        'type' => 'countable'
      ];
      $col['total'] = [
        'title' => 'مجموع',
        'value' => (int) ($users_count_organic + $users_count_ads),
        'type' => 'countable'
      ];
      $new[$n]['td'] = $col;
      $col = null;
      ++$n;
    }

    return Useful::ok($new);
  }

  //ADMIN
  public function chart_pie(Request $request)
  {
    $months = $request->months;
    if (!$months) $months = 1;
    $months = 'all';
    $organic = User::select('id')->whereNull('utm_source');
    $ads = User::select('id')->whereNotNull('utm_source');
    if ($months == 'all') {
      $users_count_organic = $organic->count();
      $users_count_ads = $ads->count();
    } else {
      $users_count_organic = $organic->whereDate('created_at', '>=', Carbon::now()->subMonths($months))->count();
      $users_count_ads = $ads->whereDate('created_at', '>=', Carbon::now()->subMonths($months))->count();
    }

    $data = [
      [
        'name' => 'ارگانیک',
        'data' => (int) $users_count_organic,
      ],
      [
        'name' => 'تبلیغات',
        'data' => (int) $users_count_ads,
      ],
    ];

    $colors = ['#03A9F4', '#00E676'];

    return Useful::ok(['series' => $data, 'colors' => $colors]);
  }

  //ADMIN
  public function chart_line(Request $request)
  {
    $months = $request->months;
    if (!$months) $months = 1;
    $months = explode('?', $months)[0];
    $from_date = $request->from_date;
    $to_date = $request->to_date;
    if ($from_date && $to_date) {
      $users = User::select('id', 'created_at', 'utm_source')->searchDate('created_at', $from_date, $to_date)->orderBy('id', 'asc')->get();
    } else {
      $users = User::select('id', 'created_at', 'utm_source')->whereDate('created_at', '>=', Carbon::now()->subMonths($months))->orderBy('id', 'asc')->get();
    }

    $filtered = $users->groupBy(function ($item, $key) {
      return Carbon::parse($item->created_at)->format('Y-m-d');
    });
    $datetimes = $filtered->keys()->toArray();

    $organic_counts = [];
    $ads_counts = [];

    $n = 0;
    foreach ($filtered as $group) {
      $ads_counts[$n] = [
        $datetimes[$n] . 'T06:00:00.000Z', (int) count($group->whereNotNull('utm_source'))
      ];
      $organic_counts[$n] = [
        $datetimes[$n] . 'T06:00:00.000Z', (int) count($group->whereNull('utm_source'))
      ];
      ++$n;
    }

    $data = [
      [
        'name' => 'ارگانیک',
        'data' => $organic_counts,
      ],
      [
        'name' => 'تبلیغات',
        'data' => $ads_counts,
      ],
    ];

    $colors = ['#03A9F4', '#00E676', '#0D47A1'];

    return Useful::ok(['series' => $data, 'colors' => $colors]);
  }
}

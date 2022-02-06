<?php

namespace App\Http\Controllers;

use App\Models\Useful;
use App\Models\Edate;
use App\Models\Address;
use App\Models\Backcheque;
use App\Models\Bontx;
use App\Models\Card;
use App\Models\Charge;
use App\Models\Chunk;
use App\Models\Client;
use App\Models\Comment;
use App\Models\Company;
use App\Models\Doc;
use App\Models\Facility;
use App\Models\Ghest;
use App\Models\Discount;
use App\Models\Order;
use App\Models\Organ;
use App\Models\Payment;
use App\Models\Report;
use App\Models\Setting;
use App\Models\Shop;
use App\Models\Link;
use App\Models\Pattern;
use App\Models\Sms;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class RevController extends Controller
{
    public function __construct()
    {
        $this->start_ts = time();
    }

    public function cache(Request $request)
    { 
        if (config('app.env') == 'production') { 
            Artisan::call('optimize');
            Artisan::call('view:cache');
        } else {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            Artisan::call('route:clear');
            Artisan::call('config:cache');
        }

        abort(403, 'Cached Successfully');
    }

    public function test()
    {
    }

    public function sync_settings()
    {
        DB::table('settings')->truncate();
        $this->sql_importer('../app/Imports/settings.sql');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('patterns')->truncate();
        $this->sql_importer('../app/Imports/patterns.sql');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return 1;
    }

    public function delete_old_temps()
    {
        $path = public_path('t3mpDox/');
        if ($handle = opendir($path)) {
            return readdir($handle);
            while (false !== ($file = readdir($handle))) {
                $filelastmodified = filemtime($path . $file);
                //24 hours in a day * 3600 seconds per hour
                if ((time() - $filelastmodified) > 24 * 3600) {
                    unlink($path . $file);
                }
            }

            closedir($handle);
        }
        return 1;
    }

    public function new_client()
    {
        Client::create([
            'name' => 'payping',
            'is_active' => 1,
            'title' => 'پی پینگ',
            'scopes' => [1, 2, 3, 4, 5, 6],
            'token' => 'BsmALahR' . Useful::randomStringX(100) . md5(Useful::randomStringX(20)) . Useful::randomStringX(260),
            'refresh_token' => Useful::randomStringX(200),
            'token_expired_at' => Carbon::now()->addDays(15),
            'refresh_token_expired_at' => Carbon::now()->addDays(45),
        ]);
        return 1;
    }

    public function sync_cards()
    {
        $cards = Card::whereHas('charges')->whereNull('delivered_at')->with(['charges' => function ($q) {
            $q->select('id', 'card_id', 'created_at')->orderBy('id', 'asc');
        }])->get();
        foreach ($cards as $card) {
            $card->update(['sent_at' => $card->created_at, 'delivered_at' => $card->charges[0]->created_at]);
        }
        return 1;
    }

    public function recheck_payments_orders()
    {
        $payments = Payment::select('id', 'order_id', 'paid_at', 'status')->prepayment()->successful()->with(['order' => function ($q) {
            $q->select('id', 'prepaid_at');
        }])->get();

        foreach ($payments as $payment) {
            Order::find($payment->order_id)->update(['prepaid_at' => $payment->paid_at]);
        }

        return 1;
    }

    public function recheck_user_verified()
    {
        $users = User::whereNull('verified_at')->whereHas('docs', function ($q) {
            $q->where(['type' => 'ncf', 'is_verified' => 1]);
        })->with('docs', function ($q) {
            $q->select('id', 'user_id', 'decided_at')->where(['type' => 'ncf', 'is_verified' => 1]);
        })->get();

        foreach ($users as $user) {
            $user->update(['verified_at' => $user->docs[0]->decided_at]);
        }

        return 1;
    }

    public function sync_shop_docs()
    {
        $shops = Shop::where('is_active', 1)->whereDoesntHave('docs', function ($q) {
            $q->where('type', 'sd');
        })->get();
        return $shops;
        foreach ($shops as $shop) {
            $docs = DB::connection('old')->table('docs')->where('shop_id', $shop->id)->get();
            foreach ($docs as $doc) {
                if (in_array($doc->type, ['ncf', 'ncb'])) {
                    if (Doc::where('user_id', $shop->user_id)->where('type', $doc->type)->exists()) continue;
                    else {
                        Doc::create([
                            'user_id' => $shop->user_id,
                            'type' => $doc->type,
                            'uploaded_by' => $shop->user_id,
                            'address' => $doc->address,
                            'uploaded_at' => $doc->uploaded_at,
                            'decided_at' => $doc->is_verified == 1 ? $doc->verified_at : null,
                            'is_verified' => $doc->is_verified,
                            'created_at' => $doc->created_at,
                            'updated_at' => $doc->updated_at,
                        ]);
                    }
                } else {
                    if (Doc::where('shop_id', $shop->id)->where('type', $doc->type)->exists()) continue;
                    Doc::create([
                        'shop_id' => $shop->id,
                        'type' => $doc->type,
                        'uploaded_by' => $shop->user_id,
                        'address' => $doc->address,
                        'uploaded_at' => $doc->uploaded_at,
                        'decided_at' => $doc->is_verified == 1 ? $doc->verified_at : null,
                        'is_verified' => $doc->is_verified,
                        'created_at' => $doc->created_at,
                        'updated_at' => $doc->updated_at,
                    ]);
                }
            }
        }
        return $shops;
    }

    public function sync_order_docs()
    {
        $orders = Order::select('id', 'oid', 'user_id')->whereIn('status', ['completed', 'cycle'])->where('payback_type', 'cheque')->whereDoesntHave('docs', function ($q) {
            $q->where('type', 'cheques');
        })->with('user', function ($q) {
            $q->select('id', 'fname', 'lname', 'username');
        })->orderBy('id', 'desc')->get();
        return $orders;
    }

    public function revx()
    {
        $start = time();
        $this->sql_importer('../app/Imports/patterns.sql');
        $this->sql_importer('../app/Imports/settings.sql');
        $this->users();
        $this->shops();
        $this->organs();
        $this->cards();
        $this->bontxes();
        $this->docs();
        $this->comments();
        $this->shorts();
        $this->reports();
        $this->discounts();
        $this->backcheques();
        $this->facilities();
        $this->orders();
        $this->payments();
        $this->ghests_regenerate();
        $this->ghests_adjust_passed();
        $this->ghests_recheck_passed();
        $this->charges();
        // $this->faker();
        return (time() - $start) / 60;
    }

    public function rev(Request $request)
    {
        switch ($request->step) {
            case 'imports':
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                DB::table('patterns')->truncate();
                $this->sql_importer('../app/Imports/patterns.sql');
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                Setting::truncate();
                $this->sql_importer('../app/Imports/settings.sql');
                break;
            case 1:
                $this->users();
                break;
            case 2:
                $this->sql_importer('../app/Imports/patterns.sql');
                $this->sql_importer('../app/Imports/settings.sql');
                $this->shops();
                $this->organs();
                $this->cards();
                $this->bontxes();
                $this->docs();
                $this->comments();
                $this->settings();
                $this->shorts();
                $this->reports();
                $this->discounts();
                $this->backcheques();
                $this->facilities();
                break;
            case 3:
                $this->orders();
                $this->payments();
                break;
            case 4:
                Ghest::query()->truncate();
                $this->ghests_regenerate();
                $this->ghests_adjust_passed();
                $this->ghests_recheck_passed();
                $this->charges();
                break;
            case 5:
                break;
        }
        return 'done!';
    }

    public function ghests_regenerate()
    {
        $orders = Order::successful()->get();
        foreach ($orders as $order) {
            $chs = Order::cheques_info($order)['cheques'];
            $arr = [];
            foreach ($chs as $ch) {
                $arr[] = [
                    'order_id' => $order->id,
                    'amount' => $ch['amount'],
                    'ghest_date' => Edate::j2carbon($ch['date']),
                    'shamsi' => $ch['date'],
                    'type' => $order->payback_type,
                ];
            }
            if (Ghest::insert($arr)) continue;
            else abort(403, 'order_id ' . $order->id);
        }
        return 1;
    }

    public function ghests_adjust_passed()
    {
        Ghest::where('type', 'cheque')->expired(7)->update(['passed_at' => DB::raw("`ghest_date`")]);
        $payments = Payment::ghest()->successful()->orderBy('id', 'asc')->get();
        foreach ($payments as $payment) {
            Ghest::where('order_id', $payment->order_id)->notPassed()->orderBy('ghest_date', 'asc')->limit(1)->update(['passed_at' => $payment->paid_at, 'payment_id' => $payment->id]);
        }
        return 1;
    }

    public function ghests_recheck_passed()
    {
        $ghests = Ghest::where('type', 'epay')->passed()->count();
        $payments = Payment::ghest()->successful()->count();
        if ($ghests != $payments) abort(403, 'ghests:' . $ghests . '  |  payments:' . $payments);

        $ghests = Ghest::where('type', 'epay')->passed()->sum('amount');
        $payments = Payment::ghest()->successful()->sum('amount');
        if ($ghests != $payments) abort(403, 'ghests:' . $ghests . '  |  payments:' . $payments);

        return 1;
    }

    public function users()
    {
        /* $ldate = User::select('updated_at')->orderBy('updated_at', 'desc')->first();
        $ldate ? $fdate = $ldate->updated_at : $fdate = '2010-01-01 10:00:00';
        $users = DB::connection('old')->table('users')->where('is_user', 1)->where('updated_at', '>', $fdate)->orderBy('updated_at', 'asc')->get(); */
        $offset = User::count();
        $users = DB::connection('old')->table('users')->where('is_user', 1)->offset($offset)->limit(20000)->orderBy('id', 'asc')->get();
        // $users =  DB::connection('old')->table('users')->where('is_user', 1)->where('id', '>', 17811)->get();
        foreach ($users as $user) {
            $this->checkpoint();
            $u = null;
            $username = substr($user->username, -10);
            if (User::where('username', $username)->exists()) {
                continue;
            } else {
                $u = User::updateOrCreate(
                    [
                        'id' => $user->id
                    ],
                    [
                        'username' => $username,
                        'fname' => $user->fname,
                        'lname' => $user->lname,
                        'nid' => User::check_nid($user->nid) ? User::check_nid($user->nid) : null,
                        'birth' => $user->birth ? str_replace('/', '-', Edate::tr_num($user->birth)) : null,
                        'email' => $user->email,
                        'utm_source' => $user->utm_source,
                        'utm_medium' => $user->utm_medium,
                        'utm_campaign' => $user->utm_campaign,
                        'utm_content' => $user->utm_content,
                        'click_id' => $user->click_id,
                        'invite_code' => $user->invite_code,
                        'badge' => $user->badge,
                        'password' => $user->password,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                    ]
                );
                $user_id = $u->id;
                if ($user->state) {
                    $user->phone = Edate::tr_num('0' . substr($user->phone, -10));
                    $state_code = Address::state_code($user->state);
                    Address::updateOrCreate(
                        [
                            'user_id' => $user_id,
                            'type' => 'home'
                        ],
                        [
                            'state' => Address::state_code($user->state),
                            'city' => $user->city,
                            'address' => $user->address,
                            'postal_code' => Edate::tr_num($user->postal),
                            'phone' => (is_numeric($user->phone) && mb_strlen($user->phone) >= 10) ? $user->phone : null,
                            'created_at' => $user->created_at,
                            'updated_at' => $user->created_at,
                        ]
                    );
                }
                if ($user->nc && $u) {
                    Doc::updateOrCreate(
                        [
                            'user_id' => $user_id,
                            'type' => 'ncf'
                        ],
                        [
                            'address' => $user->nc,
                            'uploaded_at' => $user->created_at,
                            'created_at' => $user->created_at,
                            'updated_at' => $user->created_at,
                            'decided_at' => $user->nc_verified == 0 ? null : $user->created_at,
                            'is_verified' => $user->nc_verified,
                        ]
                    );
                }
            }

            if (!$u) abort(403, 'user ' . $user->id);
        }
        return "yes";
    }

    public function organs()
    {
        /* $ldate = Organ::select('updated_at')->orderBy('updated_at', 'desc')->first();
        $ldate ? $fdate = $ldate->updated_at : $fdate = '2010-01-01 10:00:00';
        $organs = DB::connection('old')->table('organs')->where('updated_at', '>', $fdate)->orderBy('updated_at', 'asc')->get(); */
        $offset = Organ::count();
        $organs = DB::connection('old')->table('organs')->where('is_active', '>', '-1')->offset($offset)->limit(20000)->orderBy('id', 'asc')->get();
        foreach ($organs as $organ) {
            $this->checkpoint();
            $u = null;
            $username = substr($organ->mobile, -10);
            $user = DB::connection('old')->table('users')->where('id', $organ->user_id)->first();
            if ($user) $password = $user->password;
            else $password = 'first';

            if (User::where('username', $username)->exists()) {
                $u = User::where('username', $username)->first();
                $user_id = $u->id;
            } else {
                $agent = explode(' ', $organ->agent);
                $fname = $agent[0];
                if (count($agent) > 1) $lname = $agent[1];
                else $lname = '';
                $u = User::create([
                    'nid' => User::check_nid($organ->nid) ? User::check_nid($organ->nid) : null,
                    'username' => $username,
                    'fname' => $fname,
                    'lname' => $lname,
                    'password' => $password,
                    'created_at' => $organ->created_at,
                    'updated_at' => $organ->created_at,
                ]);
                if (!$u) abort(403, 'userorgan ' . $organ->id);
                User::generateUserInviteCode($u->id);
                $user_id = $u->id;
            }
            $state_code = Address::state_code($organ->state);
            $c = Company::create([
                'created_at' => $organ->created_at,
                'name' => $organ->name,
                'fame' => $organ->fame,
                'state' => Address::state_code($organ->state),
                'city' => $organ->city,
                'address' => $organ->address,
                'phone' => Edate::tr_num($organ->phone),
            ]);
            if (!$c) abort(403, 'organcompany ' . $organ->id);
            $company_id = $c->id;

            $o = Organ::updateOrCreate(['id' => $organ->id], [
                'user_id' => $user_id,
                'company_id' => $company_id,
                'created_at' => $organ->created_at,
                'updated_at' => $organ->updated_at,
                'name' => $organ->name,
                'fame' => $organ->fame,
                'type' => $organ->type ? $organ->type : 'i',
                'code' => $organ->code ? $organ->code : mt_rand(10, 99) . ($organ->id + 59) . mt_rand(0, 9),
                'credit' => $organ->credit ? $organ->credit : 0,
                'negative_percent' => $organ->negative_percent ? $organ->negative_percent : 0,
                'employees' => $organ->employees,
                'age' => $organ->age,
                'phone' => Edate::tr_num($organ->phone),
                'phone_direct' => $organ->phone_direct,
                'website' => $organ->website,
                'email' => $organ->email,
                'about' => $organ->about,
                'is_active' => $organ->is_active,
                'status' => $organ->is_active ? 'active' : 'uploading',
                'start_at' => $organ->start_at,
                'agent_position' => $organ->position,
            ]);
            if (!($o && $u)) abort(403, 'organ ' . $organ->id);
            if ($organ->comment) NoteController::make('organ', $organ->id, $organ->comment);
            unset($u);
            unset($o);
        }
        return "yes";
    }

    public function shops()
    {
        /* $ldate = Shop::select('updated_at')->orderBy('updated_at', 'desc')->first();
        $ldate ? $fdate = $ldate->updated_at : $fdate = '2010-01-01 10:00:00';
        $shops = DB::connection('old')->table('shops')->where('updated_at', '>', $fdate)->orderBy('updated_at', 'asc')->get(); */
        $offset = Shop::count();
        $shops = DB::connection('old')->table('shops')->where('is_active', '>', '-1')->offset($offset)->limit(20000)->orderBy('id', 'asc')->get();
        $x = [];
        foreach ($shops as $shop) {
            $this->checkpoint();
            $u = null;
            $username = substr($shop->mobile, -10);
            $user = DB::connection('old')->table('users')->where('id', $shop->user_id)->first();
            if ($user) $password = $user->password;
            else $password = 'first';
            if (User::where('username', $username)->exists()) {
                $u = User::where('username', $username)->first();
                $user_id = $u->id;
            } else if (User::where('nid', User::check_nid($shop->nid))->exists()) {
                $u = User::where('nid', User::check_nid($shop->nid))->first();
                if (in_array($shop->id, [64, 95, 107, 127, 188, 276, 312, 331, 342, 346, 354, 356, 431, 562, 563])) {
                    if ($shop->birth)
                        User::where('nid', User::check_nid($shop->nid))->update(['birth' => $shop->birth]);
                } else {
                    $password = DB::connection('old')->table('users')->where('username', 's' . $shop->mobile)->first()->password;
                    User::where('nid', $shop->nid)->update([
                        'username' => $username,
                        'password' => $password,
                    ]);
                    if ($shop->birth)
                        User::where('nid', User::check_nid($shop->nid))->update(['birth' => $shop->birth]);
                }
                $user_id = $u->id;
            } else {
                $u = User::create([
                    'fname' => $shop->fname,
                    'lname' => $shop->lname,
                    'nid' => User::check_nid($shop->nid) ? User::check_nid($shop->nid) : null,
                    'username' => $username,
                    'password' => $password,
                    'created_at' => $shop->created_at,
                    'updated_at' => $shop->created_at,
                ]);
                if (!$u) abort(403, 'shopuser ' . $shop->id);
                User::generateUserInviteCode($u->id);
                $user_id = $u->id;
            }
            if ($shop->is_company == 1) {
                $ceoname = explode(' ', $shop->ceo_name);
                $fname = $ceoname[0];
                if (count($ceoname) > 1) $lname = $ceoname[1];
                else $lname = null;
                $state_code = Address::state_code($shop->registration_state);
                $c = Company::updateOrCreate(['nic' => $shop->nic], [
                    'created_at' => $shop->created_at,
                    'name' => $shop->company_name,
                    'ec' => $shop->ec,
                    'rn' => $shop->rn,
                    'state' => Address::state_code($shop->registration_state),
                    'address' => $shop->company_address,
                    'postal_code' => $shop->company_postal,
                    'ceo_fname' => $fname,
                    'ceo_lname' => $lname,
                    'ceo_nid' => User::check_nid($shop->ceo_nid) ? $shop->ceo_nid : null,
                ]);
                if (!$c) abort(403, 'shopcompany ' . $shop->id);
                $company_id = $c->id;
            } else {
                $company_id = null;
            }

            switch ($shop->status) {
                case 'docsUploaded':
                    $status = 'docs_uploaded';
                    break;
                default:
                    $status = $shop->status;
                    break;
            }
            $state_code = Address::state_code($shop->state);
            $s = Shop::updateOrCreate(['id' => $shop->id], [
                'user_id' => $user_id,
                'name' => $shop->name,
                'category' => $shop->category,
                'company_id' => $company_id,
                'type' => $shop->type == 'agent' ? 'offline' : $shop->type,
                'website' => $shop->website,
                'email' => $shop->email,
                'state' => Address::state_code($shop->state),
                'city' => $shop->city,
                'address' => $shop->address,
                'postal_code' => $shop->postal,
                'phone' =>  Edate::tr_num($shop->phone),
                'about' => $shop->about,
                'status' => $status,
                'is_active' => $shop->is_active,
                'start_at' => $shop->start_at,
                'agreed_at' => $shop->docs_uploaded_at,
                'docs_uploaded_at' => $shop->docs_uploaded_at,
                'docs_accepted_at' => $shop->docs_accepted_at,
                'commission_default' => 1,
                'commission_percent' => $shop->commission_percent,
                'commission_amount' => $shop->commission_amount,
                'created_at' => $shop->created_at,
                'updated_at' => $shop->updated_at,
            ]);
            if (!($s && $u)) abort(403, 'shop ' . $shop->id);
            if ($shop->comment_admin) NoteController::make('shop', $shop->id, $shop->comment_admin);
        }
        /* $x['count'] = count($x['data']);
        return $x; */
        return "yes";
    }

    public function docs()
    {
        /* $ldate = Doc::select('updated_at')->orderBy('updated_at', 'desc')->first();
        $ldate ? $fdate = $ldate->updated_at : $fdate = '2010-01-01 10:00:00';
        $docs = DB::connection('old')->table('docs')->where('updated_at', '>', $fdate)->orderBy('updated_at', 'asc')->get(); */
        $docs = DB::connection('old')->table('docs')->orderBy('id', 'asc')->get();
        foreach ($docs as $doc) {
            $this->checkpoint();
            if ($doc->user_id) {
                $username = DB::connection('old')->table('users')->select('username')->where('id', $doc->user_id)->first()->username;
                $user = User::select('id')->where('username', substr($username, -10))->first();
                if (!$user) continue;
                $user_id = $user->id;
            } else {
                $user_id = null;
            }
            if ($doc->shop_id) {
                if (!Shop::find($doc->shop_id)) continue;
            }
            $d = Doc::updateOrCreate(
                [
                    'user_id' => $user_id,
                    'shop_id' => $doc->shop_id,
                    'type' => $doc->type,
                ],
                [
                    'uploaded_by' => $user_id,
                    'address' => $doc->address,
                    'uploaded_at' => $doc->uploaded_at,
                    'decided_at' => $doc->is_verified == 1 ? $doc->verified_at : null,
                    'is_verified' => $doc->is_verified,
                    'created_at' => $doc->created_at,
                    'updated_at' => $doc->updated_at,
                ]
            );
            if (!$d) abort(403, 'docs ' . $doc->id);
        }
        return "yes";
    }

    public function cards()
    {
        $ldate = Card::select('updated_at')->orderBy('updated_at', 'desc')->first();
        $ldate ? $fdate = $ldate->updated_at : $fdate = '2010-01-01 10:00:00';
        $records = DB::connection('old')->table('cards')->where('updated_at', '>', $fdate)->orderBy('updated_at', 'asc')->get();
        foreach ($records as $record) {
            $this->checkpoint();
            $res = Card::updateOrCreate(['id' => $record->id], [
                'user_id' => $record->user_id,
                'card_number' => $record->card_number,
                'status' => $record->status,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
            ]);
            if (!$res) abort(403, 'card ' . $record->id);
        }
        return 'yes';
    }

    public function bontxes()
    {
        $ldate = Bontx::select('updated_at')->orderBy('updated_at', 'desc')->first();
        $ldate ? $fdate = $ldate->updated_at : $fdate = '2010-01-01 10:00:00';
        $records = DB::connection('old')->table('bontxes')->where('updated_at', '>', $fdate)->orderBy('updated_at', 'asc')->get();
        foreach ($records as $record) {
            $this->checkpoint();
            $res = Bontx::updateOrCreate(['id' => $record->id], [
                'card_id' => $record->card_id,
                'trace' => $record->trace,
                'amount' => $record->amount,
                'available' => $record->available,
                'done_at' => $record->done_at,
                'tx_date' => $record->tx_date,
                'refnum' => $record->refnum,
                'prcode' => $record->prcode,
                'acc_termid' => $record->acc_termid,
                'error' => $record->error,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
            ]);
            if (!$res) abort(403, 'bontx ' . $record->id);
        }
        return 'yes';
    }

    public function charges()
    {
        $ldate = Charge::select('updated_at')->orderBy('updated_at', 'desc')->first();
        $ldate ? $fdate = $ldate->updated_at : $fdate = '2010-01-01 10:00:00';
        $charges = DB::connection('old')->table('charges')->where('updated_at', '>', $fdate)->orderBy('updated_at', 'asc')->get();
        foreach ($charges as $charge) {
            $this->checkpoint();
            $res = Charge::updateOrCreate(['id' => $charge->id], [
                'user_id' => $charge->user_id,
                'card_id' => $charge->card_id,
                'amount' => $charge->amount,
                'status' => $charge->status,
                'charged_at' => $charge->charged_at,
                'tracker' => $charge->tracker,
                'track_id' => $charge->track_id,
                'deposit' => $charge->deposit,
                'created_at' => $charge->created_at,
                'updated_at' => $charge->updated_at,
            ]);
            if (!$res) abort(403, 'charge ' . $charge->id);
        }
        return 'yes';
    }

    public function comments()
    {
        $records = DB::connection('old')->table('comments')->get();
        foreach ($records as $record) {
            $this->checkpoint();
            $res = Comment::updateOrCreate(['id' => $record->id], [
                'user_id' => $record->user_id,
                'comment_id' => $record->comment_id,
                'route' => $record->route,
                'page' => $record->page,
                'name' => $record->name,
                'mobile' => $record->mobile,
                'comment' => $record->comment,
                'meta' => $record->meta,
                'is_verified' => $record->is_verified,
                'created_at' => $record->created_at
            ]);
            if (!$res) abort(403, 'charge ' . $record->id);
        }
        return 'yes';
    }

    public function settings()
    {
        $settings = DB::connection('old')->table('settings')->get();
        foreach ($settings as $setting) {
            $this->checkpoint();
            $res = Setting::updateOrCreate(['id' => $setting->id], [
                'group' => $setting->group,
                'type' => $setting->type,
                'name' => $setting->name,
                'is_public' => $setting->public,
                'prop' => $setting->prop,
                'val' => $setting->val,
                'comment' => $setting->comment,
                'display' => $setting->display,
                'created_at' => $setting->created_at
            ]);
            if (!$res) abort(403, 'setting ' . $setting->id);
        }
        return 'yes';
    }

    public function shorts()
    {
        $records = DB::connection('old')->table('shorts')->get();
        foreach ($records as $record) {
            $this->checkpoint();
            $res = Link::updateOrCreate(['id' => $record->id], [
                'short' => $record->short,
                'dest' => $record->dest,
                'click' => $record->click,
                'expired_at' => $record->expired_at,
                'created_by' => $record->creator_id,
                'created_at' => $record->created_at,
            ]);
            if (!$res) abort(403, 'short ' . $record->id);
        }
        return 'yes';
    }

    public function reports()
    {
        $records = DB::connection('old')->table('reports')->get();
        foreach ($records as $record) {
            $this->checkpoint();
            $res = Report::updateOrCreate(['id' => $record->id], [
                'week_id' => $record->week_id,
                'sale_week' => $record->sale_week,
                'sale_total' => $record->sale_total,
                'start' => $record->start,
                'end' => $record->end,
                'startj' => $record->startj,
                'endj' => $record->endj,
                'year' => $record->year,
                'passed' => $record->passed,
                'comment' => $record->comment,
                'created_at' => $record->created_at,
            ]);
            if (!$res) abort(403, 'report ' . $record->id);
        }
        return 'yes';
    }

    public function discounts()
    {
        $ldate = Discount::select('updated_at')->orderBy('updated_at', 'desc')->first();
        $ldate ? $fdate = $ldate->updated_at : $fdate = '2010-01-01 10:00:00';
        $records = DB::connection('old')->table('offers')->where('updated_at', '>', $fdate)->orderBy('updated_at', 'asc')->get();
        foreach ($records as $record) {
            $this->checkpoint();
            $res = Discount::updateOrCreate(['id' => $record->id], [
                'code' => $record->code,
                'amount' => $record->amount,
                'percent' => $record->percent,
                'title' => $record->title,
                'tags' => $record->tags ? str_replace(['first', '-'], ['', ''], $record->tags) : null,
                'just_first' => 1,
                'mobile' => $record->mobile,
                'expired_at' => $record->expired_at,
                'limit' => $record->limit,
                'limit_per_user' => $record->limit_per_user,
                'is_active' => $record->active,
                'comment' => $record->comment,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
            ]);
            if (!$res) abort(403, 'discount ' . $record->id);
        }
        return 'yes';
    }

    public function backcheques()
    {
        $ldate = Backcheque::select('updated_at')->orderBy('updated_at', 'desc')->first();
        $ldate ? $fdate = $ldate->updated_at : $fdate = '2010-01-01 10:00:00';
        $records = DB::connection('old')->table('inquiries')->where('type', 'back_cheques')->where('updated_at', '>', $fdate)->orderBy('updated_at', 'asc')->get();
        foreach ($records as $record) {
            $this->checkpoint();
            $result = json_decode($record->result, true);
            $res = Backcheque::updateOrCreate(['id' => $record->id], [
                'nid' => $record->nid,
                'result' => $result,
                'comment' => $record->comment,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
            ]);
            if (!$res) abort(403, 'backcheque ' . $record->id);
        }
        return 'yes';
    }

    public function facilities()
    {
        $ldate = Facility::select('updated_at')->orderBy('updated_at', 'desc')->first();
        $ldate ? $fdate = $ldate->updated_at : $fdate = '2010-01-01 10:00:00';
        $records = DB::connection('old')->table('inquiries')->where('type', 'facilities')->where('updated_at', '>', $fdate)->orderBy('updated_at', 'asc')->get();
        foreach ($records as $record) {
            $this->checkpoint();
            $result = json_decode($record->result, true);
            $res = Facility::updateOrCreate(['id' => $record->id], [
                'nid' => $record->nid,
                'result' => $result,
                'comment' => $record->comment,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
            ]);
            if (!$res) abort(403, 'facility ' . $record->id);
        }
        return 'yes';
    }

    public function orders()
    {
        /* $ldate = Order::select('updated_at')->orderBy('updated_at', 'desc')->first();
        $ldate ? $fdate = $ldate->updated_at : $fdate = '2010-01-01 10:00:00';
        $records = DB::connection('old')->table('orders')->where('updated_at', '>', $fdate)->orderBy('updated_at', 'asc')->get(); */
        $offset = Order::count();
        $records = DB::connection('old')->table('orders')->offset($offset)->limit(20000)->orderBy('id', 'asc')->get();
        foreach ($records as $record) {
            if (empty($record->amount)) continue;
            $this->checkpoint();
            switch ($record->status) {
                case 'docsUploaded':
                    $status = 'docs_uploaded';
                    break;
                case 'waitForCheques':
                    $status = 'wait_for_cheques';
                    break;
                case 'cycle_cheque':
                case 'cycle_epay':
                    $status = 'cycle';
                    break;
                case 'extraCharge':
                    $status = 'extra_charge';
                    break;
                default:
                    $status = $record->status;
                    break;
            }

            $reason = null;
            if ($record->comments_user) {
                $x = json_decode($record->comments_user, true)['cancel'];
                if (!empty($x) && mb_strlen($x) > 6) $reason = $x;
            }

            if ($record->comments_admin) {
                $x = json_decode($record->comments_admin, true);
                if (isset($x['reject'])) $reason = $x['reject'];
                foreach ($x as $key => $value) {
                    if (mb_strlen($value) < 7) continue;
                    switch ($key) {
                        case 'userv':
                            $title = 'در مورد هویت';
                            break;
                        case 'order':
                            $title = 'یادداشت';
                            break;
                        case 'report':
                            $title = 'در مورد پرینت حساب';
                            break;
                        case 'backup':
                            $title = 'در مورد پرینت حساب پشتیبان';
                            break;
                        case 'cheque':
                            $title = 'در مورد تصویر چک';
                            break;
                        case 'extra':
                            $title = 'در مورد مدرک اضافی';
                            break;
                        case 'wage':
                            $title = 'در مورد گواهی سازمانی';
                            break;
                        case 'nc':
                            $title = 'در مورد کارت ملی';
                            break;
                        case 'reject':
                        default:
                            $title = null;
                            break;
                    }
                    if ($title) NoteController::make('order', $record->id, $value, $title);
                }
            }

            if (in_array($status, ['submitted', 'docs_uploaded', 'wait_for_cheques'])) $record->gain = '0.045';
            $ghestify = Order::ghestify($record, 1, 1);
            $res = Order::updateOrCreate(['id' => $record->id], [
                'user_id' => $record->user_id,
                'oid' => $record->oid,
                'status' => $status,
                'amount' => $record->amount,
                'prepayment' => $record->prepayment,
                'months' => $record->months,
                'cheques' => $record->cheques,
                'passed' => $record->passed,
                'ghest' => $ghestify->ghest,
                'total' => $ghestify->total,
                'gain' => $record->gain,
                'product' => mb_substr($record->product, 0, 100, 'UTF-8'),
                'organ_id' => $record->organ_id,
                'organ_accepted_at' => $record->organ_accepted_at,
                'shop_id' => $record->shop_id,
                'address' => $record->address,
                'payback_type' => $record->payment_type,
                'series' => $record->series,
                'first_ghest_at' => $record->first_ghest_at,
                'prepaid_at' => $record->prepaid_at,
                'reason' => $reason,
                'charged_at' => $record->finished_at,
                'charge_type' => $record->finish_type,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
            ]);
            if (!$res) abort(403, 'order ' . $record->id);
            if ($record->bank_id) {
                $doc = DB::connection('old')->table('banks')->where('id', $record->bank_id)->first();
                if ($doc->report) {
                    $d = Doc::updateOrCreate(
                        [
                            'order_id' => $record->id,
                            'type' => 'report',
                        ],
                        [
                            'address' => $doc->report,
                            'uploaded_at' => $doc->report_uploaded_at,
                            'decided_at' => $doc->report_verified == 1 ? Carbon::parse($doc->report_uploaded_at)->addDay(1) : null,
                            'is_verified' => $doc->report_verified,
                            'created_at' => $doc->created_at
                        ]
                    );
                    if (!$d) return 'orderDoc ' . $doc->id;
                }
                if ($doc->cheque) {
                    $d = Doc::updateOrCreate(
                        [
                            'order_id' => $record->id,
                            'type' => 'cheque',
                        ],
                        [
                            'address' => $doc->cheque,
                            'uploaded_at' => $doc->cheque_uploaded_at,
                            'decided_at' => $doc->cheque_verified == 1 ? Carbon::parse($doc->cheque_uploaded_at)->addDay(1) : null,
                            'is_verified' => $doc->cheque_verified,
                            'created_at' => $doc->created_at
                        ]
                    );
                    if (!$d) return 'orderDoc ' . $doc->id;
                }
                if ($doc->backup) {
                    $d = Doc::updateOrCreate(
                        [
                            'order_id' => $record->id,
                            'type' => 'backup',
                        ],
                        [
                            'address' => $doc->backup,
                            'uploaded_at' => $doc->backup_uploaded_at,
                            'decided_at' => $doc->backup_verified == 1 ? Carbon::parse($doc->backup_uploaded_at)->addDay(1) : null,
                            'is_verified' => $doc->backup_verified,
                            'created_at' => $doc->created_at
                        ]
                    );
                    if (!$d) return 'orderDoc ' . $doc->id;
                }
                if ($doc->extra) {
                    $d = Doc::updateOrCreate(
                        [
                            'order_id' => $record->id,
                            'type' => 'extra',
                        ],
                        [
                            'address' => $doc->extra,
                            'uploaded_at' => $doc->extra_uploaded_at,
                            'decided_at' => $doc->extra_verified == 1 ? Carbon::parse($doc->extra_uploaded_at)->addDay(1) : null,
                            'is_verified' => $doc->extra_verified,
                            'created_at' => $doc->created_at
                        ]
                    );
                    if (!$d) return 'orderDoc ' . $doc->id;
                }
                if ($doc->wage) {
                    $d = Doc::updateOrCreate(
                        [
                            'order_id' => $record->id,
                            'type' => 'wage',
                        ],
                        [
                            'address' => $doc->wage,
                            'uploaded_at' => $doc->wage_uploaded_at,
                            'decided_at' => $doc->wage_verified == 1 ? Carbon::parse($doc->wage_uploaded_at)->addDay(1) : null,
                            'is_verified' => $doc->wage_verified,
                            'created_at' => $doc->created_at
                        ]
                    );
                    if (!$d) return 'orderDoc ' . $doc->id;
                }
                if ($doc->contract) {
                    $d = Doc::updateOrCreate(
                        [
                            'order_id' => $record->id,
                            'type' => 'contract',
                        ],
                        [
                            'address' => $doc->contract,
                            'uploaded_at' => $doc->contract_uploaded_at,
                            'decided_at' => $doc->contract_verified == 1 ? Carbon::parse($doc->contract_uploaded_at)->addDay(1) : null,
                            'is_verified' => $doc->contract_verified,
                            'created_at' => $doc->created_at
                        ]
                    );
                    if (!$d) return 'orderDoc ' . $doc->id;
                }
                if ($doc->cheques) {
                    $d = Doc::updateOrCreate(
                        [
                            'order_id' => $record->id,
                            'type' => 'cheques',
                        ],
                        [
                            'address' => $doc->cheques,
                            'uploaded_at' => $doc->cheques_uploaded_at,
                            'decided_at' => $doc->cheques_verified == 1 ? Carbon::parse($doc->cheques_uploaded_at)->addDay(1) : null,
                            'is_verified' => $doc->cheques_verified,
                            'created_at' => $doc->created_at
                        ]
                    );
                    if (!$d) return 'orderDoc ' . $doc->id;
                }
                if ($doc->factor) {
                    $d = Doc::updateOrCreate(
                        [
                            'order_id' => $record->id,
                            'type' => 'factor',
                        ],
                        [
                            'address' => $doc->factor,
                            'uploaded_at' => $doc->factor_uploaded_at,
                            'decided_at' => $doc->factor_verified == 1 ? Carbon::parse($doc->factor_uploaded_at)->addDay(1) : null,
                            'is_verified' => $doc->factor_verified,
                            'created_at' => $doc->created_at
                        ]
                    );
                    if (!$d) return 'orderDoc ' . $doc->id;
                }
                if ($doc->upload_at) {
                    $res->update([
                        'docs_uploaded_at' => $doc->upload_at,
                        'docs_warning_at' => $doc->warning_at,
                        'docs_accepted_at' => $record->status == 'docs_uploaded' ? null : $doc->upload_at,
                    ]);
                }
            }
            if ($record->series_card > 0 && Card::where('user_id', $record->user_id)->exists()) {
                $c = Card::updateOrCreate(['user_id' => $record->user_id], ['series' => $record->series_card]);
                if (!$c) abort(403, 'series_card ' . $record->id);
                if ($record->finished_at) {
                    Card::where('user_id', $record->user_id)->whereNull('delivered_at')->update([
                        'sent_at' => Carbon::parse($record->finished_at)->subDays(3),
                        'delivered_at' => Carbon::parse($record->finished_at)->subDay()
                    ]);
                }
            }
        }
        return 'yes';
    }

    public function payments()
    {
        // $ldate = Payment::select('updated_at')->orderBy('updated_at', 'desc')->first();
        // $ldate ? $fdate = $ldate->updated_at : $fdate = '2010-01-01 10:00:00';
        $records = DB::connection('old')->table('payments')->orderBy('id', 'asc')->get();
        foreach ($records as $record) {
            $this->checkpoint();
            if (Order::where('id', $record->order_id)->doesntExist()) continue;
            $res = Payment::updateOrCreate(['id' => $record->id], [
                'bill_id' => $record->bill_id,
                'user_id' => $record->user_id,
                'order_id' => $record->order_id,
                'type' => $record->type,
                'amount' => $record->amount,
                'paid_at' => $record->paid_at,
                'status' => $record->status,
                'ref_id' => $record->ref_id,
                'discount_id' => $record->offer_id,
                'authority' => $record->authority,
                'card_mask' => $record->card_mask,
                'card_hash' => $record->card_hash,
                'token' => $record->token,
                'ip' => $record->pay_ip,
                'charged_at' => $record->charged_at,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
            ]);
            if (!$res) abort(403, 'payment ' . $record->id);
            if ($record->type == 'prepayment' && $record->status == 100) {
                DB::table('orders')->where('id', $record->id)->update([
                    'docs_received_at' => Carbon::parse($record->paid_at)->subDay(),
                    'prepaid_at' => $record->paid_at,
                ]);
            }
        }
        return 'yes';
    }

    public function ghests()
    {
        /* $ldate = Ghest::select('updated_at')->orderBy('updated_at', 'desc')->first();
        $ldate ? $fdate = $ldate->updated_at : $fdate = '2010-01-01 10:00:00'; */
        $ghests = DB::connection('old')->table('ghests')->orderBy('id', 'asc')->get();
        foreach ($ghests as $ghest) {
            $this->checkpoint();
            $res = Ghest::updateOrCreate(['id' => $ghest->id], [
                'order_id' => $ghest->order_id,
                'amount' => $ghest->amount,
                'ghest_date' => $ghest->ghest_date,
                'shamsi' => substr($ghest->shamsi, 0, 4) . '-' . substr($ghest->shamsi, 4, 2) . '-' . substr($ghest->shamsi, -2),
                'first_alarm_at' => $ghest->first_alarm_at,
                'last_alarm_at' => $ghest->last_alarm_at,
                'passed_at' => $ghest->passed ? $ghest->ghest_date : null,
                'created_at' => $ghest->created_at,
                'updated_at' => $ghest->updated_at,
            ]);
            if (!$res) abort(403, 'ghest ' . $ghest->id);
        }
        return 'yes';
    }

    public function checkpoint()
    {
        if (time() - $this->start_ts > 1700) abort(403, 'run again');
        return true;
    }

    public function artisan(Request $request)
    {
        $command = str_replace('.', ':', $request->command);
        $options = $request->options;
        $options ? $options = explode('.', $options) : $options = null;
        if (is_null($options)) {
            switch ($command) {
                case 'cache':
                    Artisan::call('optimize:clear');
                    Artisan::call('view:clear');
                    Artisan::call('cache:clear');
                    Artisan::call('config:cache');
                    Artisan::call('route:cache');
                    Artisan::call('view:cache');
                    break;
                case 'import':
                    break;
                default:
                    Artisan::call($command);
                    break;
            }
        } else Artisan::call($command, $options);
        abort(403, ':|');
    }

    public static function array_search_keyword($keyword, $array, $offset = 0)
    {
        if ($offset > 0) $array = array_slice($array, $offset);
        foreach ($array as $index => $line) {
            if (Useful::str_has($line, $keyword)) return $index + $offset;
        }
        return false;
    }

    public function sql_importer($file)
    {
        $lines = file($file);
        $start = self::array_search_keyword('INSERT INTO', $lines);
        $end = self::array_search_keyword('Indexes for dumped tables', $lines, $start) - 3;
        $query = mb_substr($lines[$start], 0, -1, 'UTF-8') . ' ';
        $lines = array_slice($lines, $start + 1, $end - $start);
        foreach ($lines as $value) $query .= $value;
        unset($lines);
        return DB::insert($query);
    }
}

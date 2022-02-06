<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\ICBS;
use App\Models\Useful;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public static function captchaRefresh()
    {
        return ['captcha' => captcha_src('flat')];
    }

    public function testz(Request $request)
    {
        // $icbs = ICBS::BankCs('4900382647');
        return view('pages.test', compact('icbs'));
    }

    public function ok()
    {
        return Useful::ok();
    }


    public function index()
    {
        return view('home');
    }

    public function index_org()
    {
        $tag_alt = true;
        return view('home', compact('tag_alt'));
    }

    public function indexH(Request $request)
    {
        $inviter = $request->code;
        if (!empty($inviter) && substr($inviter, 0, 1) == 'u') Session::put('utm_source', $inviter);
        return view('home');
    }

    public function faq()
    {
        $Faq = json_decode(file_get_contents(public_path() . '/faq.json'), true);
        return view('pages.faq', compact('Faq'));
    }

    public function estefta()
    {
        return view('pages.estefta');
    }

    public function landing(Request $request)
    {
        $thing = $request->thing;
        if (!$thing) $thing = $uri = Route::current()->uri;
        if ($thing === 'خرید_اقساطی_گوشی') $thing = 'mobile';
        $data['img'] = $thing;
        $types = [
            'mobile' => [
                'title'=> 'گوشی موبایل',
                'description' => ''
            ],
            'laptop' => [
                'title'=> 'لپ تاپ',
                'description'=> ''
            ],
            'appliance' => [
                'title'=> 'لوازم خانگی',
                'description'=> ''
            ],
            'game-console' => [
                'title'=> 'کنسول بازی',
                'description'=> ''
            ],
            'camera' => [
                'title'=> 'دوربین',
                'description'=> ''
            ],
            'gold' => [
                'title'=> 'طلا',
                'description'=> ''
            ],
            'music' => [
                'title'=> 'سازهای موسیقی',
                'description'=> ''
            ],
            'smartwatch' => [
                'title'=> 'اسمارت واچ',
                'description'=> ''
            ],
        ];
        if (!isset($types[$thing])) abort(404);
        $data['type'] = $types[$thing];
        return view('landings.' . $thing, compact('data'));
    }

    public function landingPhone(Request $request)
    {
        if ($request->page == 'calc') {
            $brand = $request->brand;
            $model = $request->model;
            $mobile = $request->mobile;
            /*if (strlen($mobile) > 9 && strlen($mobile) < 14) Session::put('order.mobile', subst($mobile, -11));*/
            if ($model == 'سایر مدل ها') $model = 'سایر';
            if ($brand == 'other') {
                Session::put('order.product', 'گوشی موبایل');
            } else if ($brand) Session::put('order.product', 'گوشی ' . $brand . " مدل " . $model);
        }
        $comments = Comment::where('page', 'phone')->where('is_verified', 1)->whereNull('comment_id')->orderBy('id', 'desc')->with(['reply' => function ($q) {
            $q->select('id', 'created_at', 'comment_id', 'comment');
        }])->paginate(40);
        return view('landings.phone', compact('comments'));
    }

    public function redirectOrg(Request $request)
    {
        return Redirect::to(config('app.url') . '/' . $request->any);
    }
}

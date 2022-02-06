<?php

namespace App\Http\Controllers;

use App\Models\Useful;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class LinkController extends Controller
{
    //ADMIN
    public function index(Request $request)
    {
        $links = Link::orderBy('created_at', 'desc')->paginate(20);
        $new = [];
        $n = 0;
        foreach ($links as $link) {
            $new[$n]['id'] = $link->id;

            $col['name'] = [
                'title' => 'کوتاه شده',
                'value' => 'https://ghsta.ir/' . $link->short,
                'link' => 'https://ghsta.ir/' . $link->short,
                'target' => '_blank',
            ];

            $col['click'] = [
                'title' => 'کلیک',
                'value' => number_format($link->click)
            ];

            $col['path'] = [
                'title' => 'مسیر',
                'value' =>  '',
                'css_classes'=> 'ltr text-right',
                'addition' =>  mb_substr($link->dest, 0, 50, 'UTF-8'),
                // 'addition' => $link->title ? $link->title : '',
            ];
            if(strlen($link->dest) > 50) $col['path']['addition'].= '...';

            $col['action'] = [
                'type' => 'button',
                'buttons' => [
                    [
                        'value' => '',
                        'icon' => 'trash',
                        'css_classes' => 'text-danger',
                        'type' => 'confirm',
                        'method' => 'post',
                        'endpoint' => '/admin/links/' . $link->id . '/delete',
                    ],
                ],
            ];

            $new[$n]['td'] = $col;
            $col = null;
            ++$n;
        }
        return Useful::recollect($links, $new);
    }

    public function map(Request $request)
    {
        $url = "https://goo.gl/maps/R4qSS1BgHyUTKk8T6";
        return Redirect::away($url);
    }

    public function get(Request $request)
    {
        $dest = Link::select('dest')->where('short', $request->short)->first();
        if ($dest) {
            Link::query()->where('short', $request->short)->increment('click');
            return Redirect::away($dest->dest);
        }
        return Redirect::to(config('app.url'));
    }

    public function create(Request $request)
    {
        $user_id = Auth::user()->id;
        $dest = $request->url;
        $short = Useful::randomString(5);
        if ($res = Link::create(['created_by' => $user_id, 'short' => $short, 'dest' => $dest])) return Useful::ok();
        return Useful::nok();
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        if (Link::find($id)->delete()) return Useful::ok();
        return Useful::nok();
    }
}

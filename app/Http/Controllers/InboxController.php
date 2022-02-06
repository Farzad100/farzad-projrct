<?php

namespace App\Http\Controllers;

use App\Models\Useful;
use App\Models\Edate;
use App\Models\Inbox;
use App\Models\Seen;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class InboxController extends Controller
{
    public function index(Request $request)
    {
        $user_id = Auth::user()->id;
        $uri = Route::current()->uri;
        $role = explode('/', $uri)[1];
        switch ($role) {
            case 'shop':
                $q = Inbox::where('related_to', 'shops');
                break;
            case 'organ':
                $q = Inbox::where('related_to', 'organs');
                break;
            default:
                $q = Inbox::where('related_to', 'users');
                break;
        }
        $inboxes = $q->where(function ($query) use ($user_id) {
            $query->whereNull('user_id')->orWhere('user_id', $user_id);
        })->orWhere('related_to', 'all')->withCount('seen')->orderBy('id', 'desc')->paginate(20);

        $new = [];
        $n = 0;
        foreach ($inboxes as $inbox) {
            $new[$n]['id'] = $inbox->id;
            $new[$n]['route'] = 'dash-inbox-single';
            $new[$n]['seen'] =  $inbox->seen_count > 0 ? true : false;
            $new[$n]['td']['title'] = [
                'title' => 'پیام',
                'value' => $inbox->title,
                'addition' => mb_substr($inbox->caption, 0, 50, 'UTF-8') . '...',
            ];
            $new[$n]['td']['date'] = [
                'title' => 'تاریخ',
                'value' => Carbon::parse($inbox->created_at)->format('Y-m-d H:m:s'),
                'type' => 'date'
            ];
            $new[$n]['td']['action'] = [
                'type' => 'button',
                'buttons' => [
                    [
                        'value' => 'مشاهده پیام',
                        'icon' => 'envelope-open-text',
                        'css_classes' => 'text-primary border-primary',
                        'type' => 'route',
                        'route' => 'dash-inbox-single',
                        'params' => ['id' => $inbox->id],
                    ],
                ],
            ];
            ++$n;
        }
        return Useful::recollect($inboxes, $new);
    }

    //ADMIN
    public function index_admin(Request $request)
    {
        $inboxes = Inbox::withCount('seen')->orderBy('id', 'desc')->paginate(20);
        $new = [];
        $n = 0;
        foreach ($inboxes as $inbox) {
            $new[$n]['id'] = $inbox->id;
            $new[$n]['route'] = 'dash-inbox-single';
            $new[$n]['seen'] =  $inbox->seen_count > 0 ? true : false;

            $col['title'] = [
                'title' => 'پیام',
                'value' => $inbox->title,
                'addition' => mb_substr($inbox->caption, 0, 50, 'UTF-8') . '...',
            ];
            $col['related_to'] = [
                'title' => 'مخاطب',
                'value' => Inbox::$types[$inbox->related_to],
                'addition' => $inbox->user_id ? 'خصوصی: 0' . User::select('username')->whereId($inbox->user_id)->first()->username : ''
            ];
            $col['view_count'] = [
                'title' => 'ویو',
                'value' => number_format($inbox->seen_count),
            ];
            $col['date'] = [
                'title' => 'تاریخ',
                'value' => Carbon::parse($inbox->created_at)->format('Y-m-d H:m:s'),
                'type' => 'date'
            ];
            $col['action'] = [
                'type' => 'button',
                'buttons' => [
                    [
                        'value' => 'مشاهده پیام',
                        'icon' => 'envelope-open-text',
                        'btn_color' => 'info',
                        'type' => 'route',
                        'route' => 'dash-inbox-single',
                        'params' => ['id' => $inbox->id],
                    ],
                    [
                        'value' => '',
                        'icon' => 'trash',
                        'css_classes' => 'text-danger',
                        'type' => 'confirm',
                        'method' => 'post',
                        'endpoint' => '/admin/inbox/' . $inbox->id . '/delete',
                    ],
                ],
            ];

            $new[$n]['td'] = $col;
            $col = null;
            ++$n;
        }
        return Useful::recollect($inboxes, $new);
    }

    public function single(Request $request)
    {
        $id = $request->id;
        $user_id = Auth::user()->id;
        $inbox = Inbox::whereId($request->id)->withCount('seen')->first();
        if (!empty($inbox->user_id)) {
            if ($user_id == $inbox->user_id) $error = 0;
            else $error = 1;
        } else {
            $uri = Route::current()->uri;
            $role = explode('/', $uri)[1] . 's';
            if ($role == $inbox->related_to || in_array($inbox->related_to, ['all', 'users'])) $error = 0;
            else $error = 1;
        }
        if ($error == 1) return Useful::nok(['forbidden', 'شما اجازه دسترسی به این پیام را ندارید']);
        else {
            $res = [
                'id' => $inbox->id,
                'created_at' => Edate::edateFromCarbon('fluent', $inbox->created_at),
                'title' => $inbox->title,
                'caption' => $inbox->caption,
                'seen' => $inbox->seen_count > 0 ? true : false,
            ];
            if (Seen::where(['user_id' => $user_id, 'inbox_id' => $id])->doesntExist()) {
                Seen::create(['user_id' => $user_id, 'inbox_id' => $id]);
            }
            return Useful::ok($res);
        }
        return Useful::nok(['forbidden', 'شما اجازه دسترسی به این پیام را ندارید']);
    }

    public function single_admin(Request $request)
    {
        $inbox = Inbox::whereId($request->id)->with('seens')->first();
        if ($inbox) {
            if ($inbox->user_id) {
                $user = User::info($inbox->user_id);
                $user_info = ['name' => $user->full_name, 'username' => $user->username];
            }
            $res = [
                'id' => $inbox->id,
                'created_at' => Edate::edateFromCarbon('fluent', $inbox->created_at),
                'title' => $inbox->title,
                'type' => $inbox->related_to,
                'type_farsi' => Inbox::$types[$inbox->related_to],
                'is_private' => $inbox->user_id ? true : false,
                'user' => $inbox->user_id ? $user_info : null,
                'caption' => $inbox->caption,
                'seen' => count($inbox->seens) > 0 ? true : false
            ];
            return Useful::ok($res);
        }
        return Useful::nok();
    }

    public function unread_count(Request $request)
    {
        $user_id = Auth::user()->id;
        $uri = Route::current()->uri;
        $role = explode('/', $uri)[1];
        switch ($role) {
            case 'shop':
                $rel = 'shops';
                break;
            case 'organ':
                $rel = 'organs';
                break;
            default:
                $rel = 'users';
                break;
        }

        return Inbox::where('related_to', $rel)->where(function ($query) use ($user_id) {
            $query->whereNull('user_id')->orWhere('user_id', $user_id);
        })->whereDoesntHave('seen')->orWhere('related_to', 'all')->whereDoesntHave('seen')->count();
    }

    public function create(Request $request)
    {
        $type = $request->type;
        $title = $request->title;
        $caption = $request->caption;
        $username = $request->mobile;
        $arr = ['created_by' => Auth::user()->id, 'caption' => $caption, 'title' => $title, 'related_to' => $type];
        if ($username) {
            $arr['user_id'] = User::select('id')->where('username', substr($username, -10))->first()->id;
        }
        if (Inbox::create($arr)) return Useful::ok();
        return Useful::nok();
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $title = $request->title;
        $caption = $request->caption;
        if (Inbox::whereId($id)->update(['title' => $title, 'caption' => $caption, 'edited_by' => Auth::user()->id])) return Useful::ok();
        return Useful::nok();
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $inbox = Inbox::find($id);
        $inbox->update(['edited_by' => Auth::user()->id]);
        if ($inbox->delete()) return Useful::ok();
        return Useful::nok();
    }
}

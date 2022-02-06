<?php

namespace App\Http\Controllers;

use App\Models\Useful;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    //ADMIN
    public function index(Request $request)
    {
        $comments = Comment::whereNull('comment_id')->orderBy('id', 'desc')->with(['reply', 'user' => function ($q) {
            $q->select('fname', 'lname');
        }])->paginate(20);

        $new = [];
        $n = 0;
        foreach ($comments as $comment) {
            $new[$n]['id'] = $comment->id;

            $col['path'] = [
                'title' => 'مسیر',
                'value' => $comment->route,
                'link' => env('APP_URL') . $comment->route,
            ];

            $col['status'] = [
                'title' => 'وضعیت',
                'value' => $comment->reply ? 'پاسخ داده شد' : 'بی پاسخ',
                'type' => 'status',
                'css_classes' => $comment->reply ? 'badge-success' : 'badge-warning'
            ];

            $col['comment'] = [
                'title' => 'متن سوال',
                'value' => mb_substr($comment->comment, 0, 70),
                'css_classes' => 'text-small'
            ];

            $col['user'] = [
                'title' => 'توسط',
                'value' =>  $comment->name,
                'addition' =>  $comment->mobile,
            ];

            $col['action'] = [
                'type' => 'button',
                'buttons' => [
                    [
                        'value' => 'پاسخ',
                        'icon' => 'reply',
                        'btn_color' => 'info',
                        'type' => 'modal',
                        'modal_name' => 'replyModal',
                        'method' => 'post',
                        'endpoint' => '/admin/comments/' . $comment->id . '/reply',
                    ],
                    [
                        'value' => '',
                        'icon' => 'trash',
                        'css_classes' => 'text-danger',
                        'type' => 'confirm',
                        'method' => 'post',
                        'endpoint' => '/admin/comments/' . $comment->id . '/delete',
                    ],
                ],
            ];

            $new[$n]['td'] = $col;
            $col = null;
            ++$n;
        }
        return Useful::recollect($comments, $new);
    }

    public function create(Request $request)
    {
        $request->validate([
            'captcha' => ['required', 'captcha'],
            'name' => ['max:30'],
            'mobile' => ['max:11'],
        ]);
        $name = $request->name;
        $mobile = $request->mobile;
        if (strlen($mobile) == 10 || strlen($mobile) == 11) $mobile = '0' . substr($mobile, -10);
        $comment = $request->comment;
        $page = $request->page;
        if (Comment::create([
            'route' => $request->route,
            'name' => $name,
            'mobile' => $mobile,
            'comment' => $comment,
            'page' => $page
        ])) return Useful::ok();
        return Useful::nok();
    }

    //ADMIN
    public function delete(Request $request)
    {
        $comment = Comment::find($request->id);
        $comment->reply->delete();
        if ($comment->delete()) {
            return Useful::ok();
        }
        return Useful::nok();
    }

    //ADMIN
    public function reply(Request $request)
    {
        if (Comment::updateOrCreate(
            [
                'comment_id' => $request->id
            ],
            [
                'user_id' => Auth::user()->id,
                'comment' => $request->comment,
                'is_verified' => 1,
            ]
        )) {
            Comment::find($request->id)->update(['is_verified' => 1]);
            return Useful::ok('');
        }
        return Useful::nok('');
    }

    //ADMIN
    public function reply_fields(Request $request)
    {
        $comment = Comment::where('id', $request->id)->with('reply')->first();
        $comment->reply ? $reply =  $comment->reply->comment : $reply = null;
        $res = [
            [
                'section' => 'پاسخ به نظر کاربر',
                'size' => 'lg',
                'fields' => [
                    [
                        'label' => 'نظر کاربر',
                        'v_model' => 'user_comment',
                        'value' => $comment->comment,
                        'type' => 'textarea',
                        'disabled' => true,
                    ],
                    [
                        'label' => 'پاسخ',
                        'v_model' => 'comment',
                        'value' => $reply,
                        'type' => 'textarea',
                    ],
                ],
            ]
        ];

        return Useful::ok($res);
    }

}

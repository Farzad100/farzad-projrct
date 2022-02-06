<?php

namespace App\Http\Controllers;

use App\Models\Useful;
use App\Models\Doc;
use App\Models\Note;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class NoteController extends Controller
{
    //ADMIN
    public function index(Request $request)
    {
        $id = $request->id;
        $oid = $request->oid;
        if ($oid) $id = Order::select('id')->where('oid', $oid)->first()->id;
        $uri = Route::current()->uri;
        $type = substr(explode('/', $uri)[2], 0, -1);
        $notes = Note::where('type', $type)->where('type_id', $id)->with([
            'creator' => function ($q) {
                $q->select('id', 'fname', 'lname', 'username');
            },
            'editor' => function ($q) {
                $q->select('id', 'fname', 'lname', 'username');
            }
        ])->orderBy('updated_at', 'desc')->get();
        foreach ($notes as $note) {
            if ($note->template == 'reject_doc') {
                $note->title = Doc::$types[$note->title]['title'] . ' رد شد';
                $note->caption = 'علت: ' . $note->caption;
            } else if ($note->template == 'reject_ch') {
                $note->title = Doc::$types[$note->title]['title'] . ' ' . Useful::oum($note->meta['badge'] + 1) . ' رد شد';
                $note->caption = '<ul>' . $note->caption . '</ul>';
            }
            $note->editable = $note->template ? false : true;
        }
        return Useful::ok($notes);
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $title = $request->title;
        $caption = $request->caption;
        if (Note::whereId($id)->update(['title' => $title, 'caption' => $caption, 'edited_by' => Auth::user()->id])) return Useful::ok();
        return Useful::nok();
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        if (Note::whereId($id)->delete()) return Useful::ok();
        return Useful::nok();
    }

    //ADMIN
    public function create(Request $request)
    {
        $id = $request->id;
        $type = $request->type;
        $title = $request->title;
        $caption = $request->caption;

        if ($type == 'order') {
            $oid = $request->oid;
            if ($oid) $id = $oid;
            $id = Order::select('id')->where('oid', $id)->first()->id;
        }

        return self::make($type, $id, $caption, $title);
    }

    public static function make($type, $id, $caption, $title = null, $template = null, $meta = null)
    {
        if (Note::create([
            'type' => $type,
            'type_id' => $id,
            'template' => $template,
            'title' => $title,
            'caption' => $caption,
            'created_by' => Auth::check() ? Auth::user()->id : null,
            'meta' => $meta
        ])) return Useful::ok();
        return Useful::nok();
    }
}

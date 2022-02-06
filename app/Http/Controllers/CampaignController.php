<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Useful;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CampaignController extends Controller
{
    public function create(Request $request)
    {
        $title = $request->title;
        $description = $request->description;
        $receps = $request->receps;

        $camp = Campaign::cre
        if ($receps == 'custom') {
            $file = $request->file('file');
            $ext = $file->extension();
            if (!in_array($ext, ['xls', 'xlsx'])) return Useful::nok(['ext', 'فرمت فایل باید اکسل باشد']);
            $uploaded = Storage::put('camps/' . $file);
            $filename = explode('/', $uploaded);
            if ($uploaded && $chunk->update(['name' => end($filename)])) {
            }
        }
    }
}

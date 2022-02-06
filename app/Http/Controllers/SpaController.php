<?php

namespace App\Http\Controllers;

use App\Models\Setting;

class SpaController extends Controller
{

    public function index()
    {
        return view('spa', ['version' => Setting::version()->all]);
    }
}

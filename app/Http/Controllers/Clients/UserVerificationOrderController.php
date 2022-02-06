<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Useful;
use App\Models\User;
use Illuminate\Http\Request;

class UserVerificationOrderController extends Controller
{
    public function index(Request $request)
    {
        $username = Useful::enum($request->mobile, 'username');
        if (strlen($username) != 10)
            return Useful::no(['code' => 'mobile_invalid', 'message' => 'موبایل نامعتبر است'], 400);
        $nid = User::check_nid($request->nid);
        if (!$nid)
            return Useful::no(['code' => 'nid_invalid', 'message' => 'کدملی نامعتبر است'], 400);
    }
}

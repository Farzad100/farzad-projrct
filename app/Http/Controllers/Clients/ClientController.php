<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Credit;
use App\Models\Payment;
use App\Models\Useful;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{

    public function token_recovery(Request $request)
    {
        $client = $request->client;
        $mode = $request->mode;

        $cli = Client::where('name', $client)->first();

        if (!$cli)
            return Useful::no(['code' => 'DontDont!', 'message' => "Don't Don't! O_o"], 400, $request->log_id);

        if (Hash::check($request->username . ':' . $request->password, $cli->passkey)) {
            if ($mode == 'renew') {
                $res = $this->token_refresh($request);
                if($res) $cli = Client::where('name', $client)->first();
            }

            return Useful::yes([
                'token' => $cli->token,
                'refresh_token' => $cli->refresh_token,
                'token_expired_at' => Carbon::parse($cli->token_expired_at)->timestamp,
                'refresh_token_expired_at' => Carbon::parse($cli->refresh_token_expired_at)->timestamp,
            ]);
        }
        return Useful::no(['code' => 'DontDont!', 'message' => "Don't Don't! O_o"], 400, $request->log_id);
    }

    public function token_refresh(Request $request)
    {
        $client = $request->client;
        $token = 'BsmALahR' . Useful::randomStringX(100) . md5(Useful::randomStringX(20)) . Useful::randomStringX(260);
        $rtoken = Useful::randomStringX(200);
        $tx = Carbon::now()->addDays(30);
        $rtx = Carbon::now()->addDays(60);

        if (Client::where('name', $client)->update([
            'token' => $token,
            'refresh_token' => $rtoken,
            'token_expired_at' => $tx,
            'refresh_token_expired_at' => $rtx,
        ])) {
            if ($request->mode && $request->mode == 'renew') return true;
            return Useful::yes([
                'token' => $token,
                'refresh_token' => $rtoken,
                'token_expired_at' => $tx->timestamp,
                'refresh_token_expired_at' => $rtx->timestamp,
            ]);
        }

        if ($request->mode && $request->mode == 'renew') return false;
        return Useful::no();
    }

    public function logs(Request $request)
    {
        return Useful::no(['code' => 'under_construction', 'message' => '']);
    }

    public function reset(Request $request)
    {
        $credits = Credit::where('user_id', 21961)->get();
        foreach ($credits as $credit) {
            Payment::whereId($credit->payment_id)->delete();
            $credit->delete();
        }
        return Useful::yes(null, 200, $request->log_id);
    }
}

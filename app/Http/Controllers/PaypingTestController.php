<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaypingTestController extends Controller
{
    public function create(Request $request)
    {
        $response = Http::withToken(config('globals.payment.payping_token'))
            ->withHeaders([
                "Accept: application/json",
                "Content-Type: application/json",
            ])
            ->post('https://api.payping.ir/v2/pay', [
                'amount' => $invoice->payable,
                'payerIdentity' => '0' . $user->username,
                'returnUrl' => "https://ghesta.ir/payments/result/ghesta_payping",
                'clientRefId' => $track_id,
            ]);
    }
}

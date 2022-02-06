<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EshopVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    } 

    public function build()
    {
        return $this->subject('کد تایید فروشگاه')->view('emails.eshop_vfy')->with([
            'otp' => $this->otp
        ]);
    }
}

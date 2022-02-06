<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $guarded = ['id']; 

    protected $casts = ['meta' => 'array', 'scopes' => 'array'];

    public const BASE_URL = 'https://api.ghesta.ir';

    public static $scopes = [ 
        100 => 'token-recovery',
        101 => 'reset',
        102 => 'logs',
        103 => 'token-refresh',
        201 => 'shop-inquiry',
        202 => 'user-inquiry',
        203 => 'user-verification-and-invoice',
        204 => 'credit-recheck',
        901 => 'cs',
    ];
}

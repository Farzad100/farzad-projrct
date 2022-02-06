<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = ['numbers' => 'array'];
}

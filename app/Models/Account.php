<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use SoftDeletes; 
    
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ghests()
    {
        return $this->hasMany(Ghest::class, 'account_id');
    }

    public function docs()
    {
        return $this->hasMany(Doc::class, 'account_id');
    }

    public static function bank_name($bank_id){
        return config('banks')["$bank_id"];
    }
}

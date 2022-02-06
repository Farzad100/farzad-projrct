<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    
  
  protected $guarded = ['id'];

    protected $casts = ['docs' => 'array'];

    public function orders()
    {
        return $this->hasMany(Order::class, 'provider_id');
    }
}

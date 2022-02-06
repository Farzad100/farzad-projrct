<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    
  
  protected $guarded = ['id'];

    public function organ()
    {
        return $this->hasOne(Organ::class, 'company_id');
    }

    public function shop()
    {
        return $this->hasOne(Shop::class, 'company_id');
    } 
}

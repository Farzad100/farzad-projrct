<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{ 
  protected $guarded = ['id']; 

  protected $casts = ['meta'=>'array']; 
}

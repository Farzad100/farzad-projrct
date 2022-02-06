<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{ 
    
  
  protected $guarded = ['id'];

  protected $casts = ['result' => 'array'];

  public function scopeNid($query, $nid)
  {
    return $query->where('nid', $nid);
  }
}

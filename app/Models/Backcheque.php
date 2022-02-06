<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Backcheque extends Model
{
  

  protected $guarded = ['id'];

  protected $casts = ['result' => 'array'];

  public function scopeNid($query, $nid)
  {
    return $query->where('nid', $nid);
  }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{ 
    use SoftDeletes; 
  
    protected $guarded = ['id'];

    protected $casts = ['meta' => 'array']; 

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    } 

    public function mention()
    {
        return $this->belongsTo(User::class, 'mention_id');
    } 
}

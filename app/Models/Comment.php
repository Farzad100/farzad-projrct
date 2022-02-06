<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    
  
  protected $guarded = ['id'];

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }

    public function reply()
    {
        return $this->hasOne(Comment::class, 'comment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

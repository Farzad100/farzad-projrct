<?php

namespace App\Models; 

use Illuminate\Database\Eloquent\Model;


class Seen extends Model
{
    protected $guarded = ['id']; 

    const UPDATED_AT = null;

    public function inbox()
    {
        return $this->belongsTo(Inbox::class, 'inbox_id');
    } 
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Linkview extends Model
{
    public function link()
    {
        return $this->belongsTo(Link::class, 'link_id');
    }
}

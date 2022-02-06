<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 

class Link extends Model
{


    protected $guarded = ['id'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function views()
    {
        return $this->hasMany(Linkview::class, 'link_id');
    }

    public function scopeWhereCase($query, $col, $value)
    {
        return $query->whereRaw("BINARY `$col` like ?", $value);
    }
}

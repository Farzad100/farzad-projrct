<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Inbox extends Model
{
    use SoftDeletes;

    

    protected $guarded = ['id'];

    protected $casts = ['meta' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function seens()
    {
        return $this->hasMany(Seen::class, 'inbox_id');
    }

    public function seen()
    {
        $user_id = Auth::user()->id;
        return $this->hasMany(Seen::class, 'inbox_id')->where('user_id', $user_id);
    }

    public function scopeThisUser($query)
    {
        return $query->where('user_id', Auth::user()->id);
    }

    public static $types = [
        'all' => 'همگانی',
        'users' => 'کاربران',
        'organs' => 'سازمان ها',
        'shops' => 'فروشگاه ها',
        'private' => 'خصوصی',
    ];
}

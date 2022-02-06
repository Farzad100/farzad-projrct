<?php

namespace App\Http\Middleware;

use App\Models\Useful;
use Closure;
use Illuminate\Http\Request;

class CdnsMiddleware
{ 
    public function handle(Request $request, Closure $next)
    {
        $ips = ['5.9.31.18'];
        if (!in_array(Useful::ip(), $ips))
            return Useful::no(['code' => 'auth_failed', 'message' => ''], 401);

        $token = $request->header('Authorization');
        if ($token != "Bearer " . config('globals.cdn.cdn1.token'))
            return Useful::no(['code' => 'auth_failed', 'message' => ''], 401);

        return $next($request);
    }
}

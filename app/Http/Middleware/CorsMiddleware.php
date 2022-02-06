<?php

namespace App\Http\Middleware;

use App\Models\Useful;
use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $headers = [
            'Access-Control-Allow-Origin' => ' *',
            'Access-Control-Allow-Methods' => ' POST, GET, OPTIONS',
            'Access-Control-Allow-Headers' => ' Content-Type, Accept, Authorization, X-Requested-With, Cache-Control'
        ];
        if ($request->getMethod() == "OPTIONS")
            return Useful::yes($headers);
        
        $response = $next($request);
        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }
        
        return $response;
    }
}

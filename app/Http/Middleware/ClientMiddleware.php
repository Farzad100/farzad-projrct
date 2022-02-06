<?php

namespace App\Http\Middleware;

use App\Models\Client;
use App\Models\ClientLog;
use App\Models\Useful;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class ClientMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $client = strtolower($request->client);
        $token = $request->header('Authorization');
        if (Useful::str_has($token, 'Bearer')) $auth_mode = 'bearer';
        else if (Useful::str_has($token, 'Basic')) $auth_mode = 'basic';
        else return Useful::no(['code' => 'auth_failed', 'message' => ''], 401);

        if (!in_array($auth_mode, ['basic', 'bearer']))
            return Useful::no(['code' => 'auth_failed', 'message' => ''], 401);

        $method = explode('/', Route::current()->uri);
        $method = end($method);

        if (strlen($client) < 3)
            return Useful::no(['code' => 'auth_failed', 'message' => ''], 401);

        if ($method == 'token-refresh') {
            $client = DB::table('clients')->select('id', 'refresh_token', 'refresh_token_expired_at', 'scopes')->where('name', $client)->first();
            if ($client) {
                $token_expiration_date = $client->refresh_token_expired_at;
                $the_token = $client->refresh_token;
            }
        } else {
            $client = DB::table('clients')->select('id', 'name', 'token', 'token_expired_at', 'scopes', 'meta', 'apikey')->where('name', $client)->first();
            if ($client) {
                $token_expiration_date = $client->token_expired_at;
                $the_token = $client->token;
            }
        }
        $client_scopes = json_decode($client->scopes, true);

        $scope_id = array_search($method, Client::$scopes);

        if ($scope_id === false || !in_array($scope_id, $client_scopes))
            return Useful::no(['code' => 'forbidden', 'message' => ''], 403);

        if (!$client)
            return Useful::no(['code' => 'auth_failed', 'message' => ''], 401);

        if ($auth_mode == 'bearer' && $scope_id != 100) {
            if (Carbon::parse($token_expiration_date)->timestamp < time())
                return Useful::no(['code' => 'token_expired', 'message' => ''], 401);

            if ($token != "Bearer " . $the_token)
                return Useful::no(['code' => 'auth_failed', 'message' => ''], 401);
        }

        if ($auth_mode == 'basic') {
            $apikey = str_replace($client->name . ':', '', base64_decode(str_replace('Basic ', '', $token)));
            if ($apikey != $client->apikey || strlen($apikey) < 10)
                return Useful::no(['code' => 'auth_failed', 'message' => ''], 401);
        }

        $req = $request->all();
        if (isset($request->password)) $req['password'] = 'UNREADABLE';

        $client_log = ClientLog::create([
            'client_id' => $client->id,
            'scope' => $scope_id,
            'request' => $req,
            'ip' => Useful::ip()
        ]);

        $request->log_id = $client_log->id ?? null;

        return $next($request);
    }
}

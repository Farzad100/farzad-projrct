<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientLog extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'ok' => 'boolean',
        'request' => 'array',
        'response' => 'array',
        'meta' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public static function submit($client_id, $scope, $ok, $request, $response, $track_id = null, $meta = null)
    {
        if ($log = ClientLog::create([
            'client_id' => $client_id,
            'scope' => $scope,
            'ok' => $ok,
            'request' => $request,
            'response' => $response,
            'track_id' => $track_id,
            'meta' => $meta,
        ]))
            return $track_id;
        return false;
    }
}

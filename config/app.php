<?php

return [
  'name' => env('APP_NAME', 'قسطا'),

  'env' => env('APP_ENV', 'production'),

  'debug' => (bool)env('APP_DEBUGx', true),

  'debug_blacklist' => [
    '_COOKIE' => array_diff(
      array_keys($_COOKIE),
      explode(",", env('DEBUG_COOKIE_WHITELIST', ""))
    ),
    '_SERVER' => array_diff(
      array_keys($_SERVER),
      explode(",", env('DEBUG_SERVER_WHITELIST', ""))
    ),
    '_ENV' => array_diff(
      array_keys($_ENV),
      explode(",", env('DEBUG_ENV_WHITELIST', ""))
    ),
  ],

  'debug_hide' => [
    '_ENV' => [
      'APP_KEY',
      'DB_DATABASE',
      'DB_USERNAME',
      'DB_PASSWORD',
      'PAY_ZARINPAL_TOKEN',
      'PAY_PAYPING_TOKEN',
      'SMS_FARAZ_APIKEY',
      'SMS_FARAZ_USERNAME',
      'SMS_FARAZ_PASSWORD',
      'SMS_KAVENEGAR_APIKEY',
      'FT_USERNAME',
      'FT_PASSWORD',
      'FT_PROXY_TOKEN',
      'ICBS_USERNAME',
      'ICBS_PASSWORD',
      'CDN_CDN1_TOKEN'
    ],

    '_SERVER' => [
      'APP_KEY',
      'DB_DATABASE',
      'DB_USERNAME',
      'DB_PASSWORD',
      'PAY_ZARINPAL_TOKEN',
      'PAY_PAYPING_TOKEN',
      'SMS_FARAZ_APIKEY',
      'SMS_FARAZ_USERNAME',
      'SMS_FARAZ_PASSWORD',
      'SMS_KAVENEGAR_APIKEY',
      'FT_USERNAME',
      'FT_PASSWORD',
      'FT_PROXY_TOKEN',
      'ICBS_USERNAME',
      'ICBS_PASSWORD',
      'CDN_CDN1_TOKEN'
    ],
  ],

  'url' => env('APP_URL', 'http://localhost:8000'),

  'domain' => env('APP_DOMAIN', 'ghesta.ir'),

  'asset_url' => env('ASSET_URL', null),

  'timezone' => 'Asia/Tehran',

  'locale' => 'en',

  'fallback_locale' => 'en',

  'faker_locale' => 'en_US',

  'key' => env('APP_KEY'),

  'cipher' => 'AES-256-CBC',

  'providers' => [

    /*
     * Laravel Framework Service Providers...
     */
    Illuminate\Auth\AuthServiceProvider::class,
    Illuminate\Broadcasting\BroadcastServiceProvider::class,
    Illuminate\Bus\BusServiceProvider::class,
    Illuminate\Cache\CacheServiceProvider::class,
    Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
    Illuminate\Cookie\CookieServiceProvider::class,
    Illuminate\Database\DatabaseServiceProvider::class,
    Illuminate\Encryption\EncryptionServiceProvider::class,
    Illuminate\Filesystem\FilesystemServiceProvider::class,
    Illuminate\Foundation\Providers\FoundationServiceProvider::class,
    Illuminate\Hashing\HashServiceProvider::class,
    Illuminate\Mail\MailServiceProvider::class,
    Illuminate\Notifications\NotificationServiceProvider::class,
    Illuminate\Pagination\PaginationServiceProvider::class,
    Illuminate\Pipeline\PipelineServiceProvider::class,
    Illuminate\Queue\QueueServiceProvider::class,
    Illuminate\Redis\RedisServiceProvider::class,
    Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
    Illuminate\Session\SessionServiceProvider::class,
    Illuminate\Translation\TranslationServiceProvider::class,
    Illuminate\Validation\ValidationServiceProvider::class,
    Illuminate\View\ViewServiceProvider::class,

    /*
     * Package Service Providers...
     */
    Stevebauman\Location\LocationServiceProvider::class,
    Madnest\Madzipper\MadzipperServiceProvider::class,
    Maatwebsite\Excel\ExcelServiceProvider::class,

    /*
     * Application Service Providers...
     */
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    // App\Providers\BroadcastServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
  ],

  'aliases' => [

    'App' => Illuminate\Support\Facades\App::class,
    'Arr' => Illuminate\Support\Arr::class,
    'Artisan' => Illuminate\Support\Facades\Artisan::class,
    'Auth' => Illuminate\Support\Facades\Auth::class,
    'Blade' => Illuminate\Support\Facades\Blade::class,
    'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
    'Bus' => Illuminate\Support\Facades\Bus::class,
    'Cache' => Illuminate\Support\Facades\Cache::class,
    'Config' => Illuminate\Support\Facades\Config::class,
    'Cookie' => Illuminate\Support\Facades\Cookie::class,
    'Crypt' => Illuminate\Support\Facades\Crypt::class,
    'DB' => Illuminate\Support\Facades\DB::class,
    'Eloquent' => Illuminate\Database\Eloquent\Model::class,
    'Event' => Illuminate\Support\Facades\Event::class,
    'File' => Illuminate\Support\Facades\File::class,
    'Gate' => Illuminate\Support\Facades\Gate::class,
    'Hash' => Illuminate\Support\Facades\Hash::class,
    'Http' => Illuminate\Support\Facades\Http::class,
    'Lang' => Illuminate\Support\Facades\Lang::class,
    'Log' => Illuminate\Support\Facades\Log::class,
    'Mail' => Illuminate\Support\Facades\Mail::class,
    'Notification' => Illuminate\Support\Facades\Notification::class,
    'Password' => Illuminate\Support\Facades\Password::class,
    'Queue' => Illuminate\Support\Facades\Queue::class,
    'Redirect' => Illuminate\Support\Facades\Redirect::class,
    'Redis' => Illuminate\Support\Facades\Redis::class,
    'Request' => Illuminate\Support\Facades\Request::class,
    'Response' => Illuminate\Support\Facades\Response::class,
    'Route' => Illuminate\Support\Facades\Route::class,
    'Schema' => Illuminate\Support\Facades\Schema::class,
    'Session' => Illuminate\Support\Facades\Session::class,
    'Storage' => Illuminate\Support\Facades\Storage::class,
    'Str' => Illuminate\Support\Str::class,
    'URL' => Illuminate\Support\Facades\URL::class,
    'Validator' => Illuminate\Support\Facades\Validator::class,
    'View' => Illuminate\Support\Facades\View::class,
    'XLS' => App\Helpers\XLSMaker::class,
    'Excel' => Maatwebsite\Excel\Facades\Excel::class,
    'Madzipper' => Madnest\Madzipper\Madzipper::class,
    'Agent' => Jenssegers\Agent\Facades\Agent::class,
  ],

];

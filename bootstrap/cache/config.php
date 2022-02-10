<?php return array (
  'app' => 
  array (
    'name' => 'Laravel',
    'env' => 'local',
    'debug' => true,
    'debug_hide' => 
    array (
      '_ENV' => 
      array (
        0 => 'APP_KEY',
        1 => 'DB_DATABASE',
        2 => 'DB_USERNAME',
        3 => 'DB_PASSWORD',
        4 => 'PAY_ZARINPAL_TOKEN',
        5 => 'PAY_PAYPING_TOKEN',
        6 => 'SMS_FARAZ_APIKEY',
        7 => 'SMS_FARAZ_USERNAME',
        8 => 'SMS_FARAZ_PASSWORD',
        9 => 'SMS_KAVENEGAR_APIKEY',
        10 => 'FT_USERNAME',
        11 => 'FT_PASSWORD',
        12 => 'ICBS_USERNAME',
        13 => 'ICBS_PASSWORD',
      ),
      '_SERVER' => 
      array (
        0 => 'APP_KEY',
        1 => 'DB_DATABASE',
        2 => 'DB_USERNAME',
        3 => 'DB_PASSWORD',
        4 => 'PAY_ZARINPAL_TOKEN',
        5 => 'PAY_PAYPING_TOKEN',
        6 => 'SMS_FARAZ_APIKEY',
        7 => 'SMS_FARAZ_USERNAME',
        8 => 'SMS_FARAZ_PASSWORD',
        9 => 'SMS_KAVENEGAR_APIKEY',
        10 => 'FT_USERNAME',
        11 => 'FT_PASSWORD',
        12 => 'ICBS_USERNAME',
        13 => 'ICBS_PASSWORD',
      ),
    ),
    'url' => 'http://localhost',
    'domain' => 'ghesta.ir',
    'asset_url' => NULL,
    'timezone' => 'Asia/Tehran',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'key' => 'base64:alqxb0DU/SsB3KOi4a3d/UKT/YZ0SxnGruvRGqJXGRg=',
    'cipher' => 'AES-256-CBC',
    'providers' => 
    array (
      0 => 'Illuminate\\Auth\\AuthServiceProvider',
      1 => 'Illuminate\\Broadcasting\\BroadcastServiceProvider',
      2 => 'Illuminate\\Bus\\BusServiceProvider',
      3 => 'Illuminate\\Cache\\CacheServiceProvider',
      4 => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
      5 => 'Illuminate\\Cookie\\CookieServiceProvider',
      6 => 'Illuminate\\Database\\DatabaseServiceProvider',
      7 => 'Illuminate\\Encryption\\EncryptionServiceProvider',
      8 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
      9 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
      10 => 'Illuminate\\Hashing\\HashServiceProvider',
      11 => 'Illuminate\\Mail\\MailServiceProvider',
      12 => 'Illuminate\\Notifications\\NotificationServiceProvider',
      13 => 'Illuminate\\Pagination\\PaginationServiceProvider',
      14 => 'Illuminate\\Pipeline\\PipelineServiceProvider',
      15 => 'Illuminate\\Queue\\QueueServiceProvider',
      16 => 'Illuminate\\Redis\\RedisServiceProvider',
      17 => 'Illuminate\\Auth\\Passwords\\PasswordResetServiceProvider',
      18 => 'Illuminate\\Session\\SessionServiceProvider',
      19 => 'Illuminate\\Translation\\TranslationServiceProvider',
      20 => 'Illuminate\\Validation\\ValidationServiceProvider',
      21 => 'Illuminate\\View\\ViewServiceProvider',
      22 => 'Stevebauman\\Location\\LocationServiceProvider',
      23 => 'Madnest\\Madzipper\\MadzipperServiceProvider',
      24 => 'Maatwebsite\\Excel\\ExcelServiceProvider',
      25 => 'App\\Providers\\AppServiceProvider',
      26 => 'App\\Providers\\AuthServiceProvider',
      27 => 'App\\Providers\\EventServiceProvider',
      28 => 'App\\Providers\\RouteServiceProvider',
    ),
    'aliases' => 
    array (
      'App' => 'Illuminate\\Support\\Facades\\App',
      'Arr' => 'Illuminate\\Support\\Arr',
      'Artisan' => 'Illuminate\\Support\\Facades\\Artisan',
      'Auth' => 'Illuminate\\Support\\Facades\\Auth',
      'Blade' => 'Illuminate\\Support\\Facades\\Blade',
      'Broadcast' => 'Illuminate\\Support\\Facades\\Broadcast',
      'Bus' => 'Illuminate\\Support\\Facades\\Bus',
      'Cache' => 'Illuminate\\Support\\Facades\\Cache',
      'Config' => 'Illuminate\\Support\\Facades\\Config',
      'Cookie' => 'Illuminate\\Support\\Facades\\Cookie',
      'Crypt' => 'Illuminate\\Support\\Facades\\Crypt',
      'DB' => 'Illuminate\\Support\\Facades\\DB',
      'Eloquent' => 'Illuminate\\Database\\Eloquent\\Model',
      'Event' => 'Illuminate\\Support\\Facades\\Event',
      'File' => 'Illuminate\\Support\\Facades\\File',
      'Gate' => 'Illuminate\\Support\\Facades\\Gate',
      'Hash' => 'Illuminate\\Support\\Facades\\Hash',
      'Http' => 'Illuminate\\Support\\Facades\\Http',
      'Lang' => 'Illuminate\\Support\\Facades\\Lang',
      'Log' => 'Illuminate\\Support\\Facades\\Log',
      'Mail' => 'Illuminate\\Support\\Facades\\Mail',
      'Notification' => 'Illuminate\\Support\\Facades\\Notification',
      'Password' => 'Illuminate\\Support\\Facades\\Password',
      'Queue' => 'Illuminate\\Support\\Facades\\Queue',
      'Redirect' => 'Illuminate\\Support\\Facades\\Redirect',
      'Redis' => 'Illuminate\\Support\\Facades\\Redis',
      'Request' => 'Illuminate\\Support\\Facades\\Request',
      'Response' => 'Illuminate\\Support\\Facades\\Response',
      'Route' => 'Illuminate\\Support\\Facades\\Route',
      'Schema' => 'Illuminate\\Support\\Facades\\Schema',
      'Session' => 'Illuminate\\Support\\Facades\\Session',
      'Storage' => 'Illuminate\\Support\\Facades\\Storage',
      'Str' => 'Illuminate\\Support\\Str',
      'URL' => 'Illuminate\\Support\\Facades\\URL',
      'Validator' => 'Illuminate\\Support\\Facades\\Validator',
      'View' => 'Illuminate\\Support\\Facades\\View',
      'XLS' => 'App\\Helpers\\XLSMaker',
      'Excel' => 'Maatwebsite\\Excel\\Facades\\Excel',
      'Madzipper' => 'Madnest\\Madzipper\\Madzipper',
      'Agent' => 'Jenssegers\\Agent\\Facades\\Agent',
    ),
  ),
  'auth' => 
  array (
    'defaults' => 
    array (
      'guard' => 'web',
      'passwords' => 'users',
    ),
    'guards' => 
    array (
      'web' => 
      array (
        'driver' => 'session',
        'provider' => 'users',
      ),
      'api' => 
      array (
        'driver' => 'passport',
        'provider' => 'users',
      ),
    ),
    'providers' => 
    array (
      'users' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Models\\User',
      ),
    ),
    'passwords' => 
    array (
      'users' => 
      array (
        'provider' => 'users',
        'table' => 'password_resets',
        'expire' => 60,
        'throttle' => 60,
      ),
    ),
    'password_timeout' => 10800,
  ),
  'banks' => 
  array (
    '011' => 
    array (
      'id' => '011',
      'name' => 'Sanat_Madan',
      'fa' => 'بانک صنعت و معدن',
    ),
    '012' => 
    array (
      'id' => '012',
      'name' => 'Mellat',
      'fa' => 'بانک ملت',
    ),
    '013' => 
    array (
      'id' => '013',
      'name' => 'Refah',
      'fa' => 'بانک رفاه',
    ),
    '014' => 
    array (
      'id' => '014',
      'name' => 'Maskan',
      'fa' => 'بانک مسکن',
    ),
    '015' => 
    array (
      'id' => '015',
      'name' => 'Sepah',
      'fa' => 'بانک سپه',
    ),
    '016' => 
    array (
      'id' => '016',
      'name' => 'Keshavarzi',
      'fa' => 'بانک کشاورزی',
    ),
    '017' => 
    array (
      'id' => '017',
      'name' => 'Melli',
      'fa' => 'بانک ملی ایران',
    ),
    '018' => 
    array (
      'id' => '018',
      'name' => 'Tejarat',
      'fa' => 'بانک تجارت',
    ),
    '019' => 
    array (
      'id' => '019',
      'name' => 'Saderat',
      'fa' => 'بانک صادرات ایران',
    ),
    '020' => 
    array (
      'id' => '020',
      'name' => 'Tosee_Saderat',
      'fa' => 'بانک توسعه صادرات',
    ),
    '021' => 
    array (
      'id' => '021',
      'name' => 'Postbank',
      'fa' => 'پست بانک ایران',
    ),
    '022' => 
    array (
      'id' => '022',
      'name' => 'Tosee_Taavon',
      'fa' => 'بانک توسعه تعاون',
    ),
    '051' => 
    array (
      'id' => '051',
      'name' => 'Tosee',
      'fa' => 'موسسه اعتباری توسعه',
    ),
    '052' => 
    array (
      'id' => '052',
      'name' => 'Ghavamin',
      'fa' => 'بانک قوامین',
    ),
    '053' => 
    array (
      'id' => '053',
      'name' => 'Karafarin',
      'fa' => 'بانک کارآفرین',
    ),
    '054' => 
    array (
      'id' => '054',
      'name' => 'Parsian',
      'fa' => 'بانک پارسیان',
    ),
    '055' => 
    array (
      'id' => '055',
      'name' => 'Eghtesad_Novin',
      'fa' => 'بانک اقتصاد نوین',
    ),
    '056' => 
    array (
      'id' => '056',
      'name' => 'Saman',
      'fa' => 'بانک سامان',
    ),
    '057' => 
    array (
      'id' => '057',
      'name' => 'Pasargad',
      'fa' => 'بانک پاسارگاد',
    ),
    '058' => 
    array (
      'id' => '058',
      'name' => 'Sarmayeh',
      'fa' => 'بانک سرمایه',
    ),
    '059' => 
    array (
      'id' => '059',
      'name' => 'Sina',
      'fa' => 'بانک سینا',
    ),
    '060' => 
    array (
      'id' => '060',
      'name' => 'Mehr_Iran',
      'fa' => 'بانک مهر ايران',
    ),
    '061' => 
    array (
      'id' => '061',
      'name' => 'Shahr',
      'fa' => 'بانک شهر',
    ),
    '062' => 
    array (
      'id' => '062',
      'name' => 'Ayandeh',
      'fa' => 'بانک آینده',
    ),
    '063' => 
    array (
      'id' => '063',
      'name' => 'Ansar',
      'fa' => 'بانک انصار',
    ),
    '064' => 
    array (
      'id' => '064',
      'name' => 'Gardeshgari',
      'fa' => 'بانک گردشگری',
    ),
    '065' => 
    array (
      'id' => '065',
      'name' => 'Hekmat',
      'fa' => 'بانک حكمت ايرانيان',
    ),
    '066' => 
    array (
      'id' => '066',
      'name' => 'Dey',
      'fa' => 'بانک دی',
    ),
    '069' => 
    array (
      'id' => '069',
      'name' => 'Iran_Zamin',
      'fa' => 'بانک ايران زمين',
    ),
    '070' => 
    array (
      'id' => '070',
      'name' => 'Resalat',
      'fa' => 'بانک رسالت',
    ),
    '073' => 
    array (
      'id' => '073',
      'name' => 'Kosar',
      'fa' => 'موسسه اعتباری کوثر',
    ),
    '075' => 
    array (
      'id' => '075',
      'name' => 'Melall',
      'fa' => 'موسسه اعتباری ملل',
    ),
    '078' => 
    array (
      'id' => '078',
      'name' => 'Khavar_Mianeh',
      'fa' => 'بانک خاورمیانه',
    ),
    '079' => 
    array (
      'id' => '079',
      'name' => 'Mehr_Eghtesad',
      'fa' => 'بانک مهر اقتصاد',
    ),
    '095' => 
    array (
      'id' => '095',
      'name' => 'Iran_Venezuela',
      'fa' => 'بانک ايران و ونزوئلا',
    ),
  ),
  'broadcasting' => 
  array (
    'default' => 'log',
    'connections' => 
    array (
      'pusher' => 
      array (
        'driver' => 'pusher',
        'key' => '',
        'secret' => '',
        'app_id' => '',
        'options' => 
        array (
          'cluster' => 'mt1',
          'useTLS' => true,
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
      ),
      'log' => 
      array (
        'driver' => 'log',
      ),
      'null' => 
      array (
        'driver' => 'null',
      ),
    ),
  ),
  'cache' => 
  array (
    'default' => 'file',
    'stores' => 
    array (
      'apc' => 
      array (
        'driver' => 'apc',
      ),
      'array' => 
      array (
        'driver' => 'array',
        'serialize' => false,
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'cache',
        'connection' => NULL,
      ),
      'file' => 
      array (
        'driver' => 'file',
        'path' => 'G:\\ghesta\\ghesta-git\\q3til2\\storage\\framework/cache/data',
      ),
      'memcached' => 
      array (
        'driver' => 'memcached',
        'persistent_id' => NULL,
        'sasl' => 
        array (
          0 => NULL,
          1 => NULL,
        ),
        'options' => 
        array (
        ),
        'servers' => 
        array (
          0 => 
          array (
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 100,
          ),
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'cache',
      ),
      'dynamodb' => 
      array (
        'driver' => 'dynamodb',
        'key' => '',
        'secret' => '',
        'region' => 'us-east-1',
        'table' => 'cache',
        'endpoint' => NULL,
      ),
    ),
    'prefix' => 'laravel_cache',
  ),
  'captcha' => 
  array (
    'characters' => 
    array (
      0 => '2',
      1 => '3',
      2 => '4',
      3 => '6',
      4 => '7',
      5 => '8',
      6 => '9',
      7 => 'a',
      8 => 'b',
      9 => 'c',
      10 => 'd',
      11 => 'e',
      12 => 'f',
      13 => 'g',
      14 => 'h',
      15 => 'k',
      16 => 'm',
      17 => 'n',
      18 => 'p',
      19 => 'q',
      20 => 'r',
      21 => 't',
      22 => 'u',
      23 => 'x',
      24 => 'y',
      25 => 'z',
      26 => 'A',
      27 => 'B',
      28 => 'C',
      29 => 'D',
      30 => 'E',
      31 => 'F',
      32 => 'G',
      33 => 'H',
      34 => 'K',
      35 => 'M',
      36 => 'N',
      37 => 'P',
      38 => 'Q',
      39 => 'R',
      40 => 'T',
      41 => 'U',
      42 => 'W',
      43 => 'X',
      44 => 'Y',
      45 => 'Z',
    ),
    'default' => 
    array (
      'length' => 9,
      'width' => 120,
      'height' => 36,
      'quality' => 30,
      'math' => false,
    ),
    'math' => 
    array (
      'length' => 9,
      'width' => 120,
      'height' => 36,
      'quality' => 90,
      'math' => true,
    ),
    'flat' => 
    array (
      'length' => 4,
      'width' => 100,
      'height' => 40,
      'quality' => 10,
      'lines' => 4,
      'bgImage' => false,
      'bgColor' => '#ecf2f4',
      'fontColors' => 
      array (
        0 => '#2c3e50',
        1 => '#c0392b',
        2 => '#16a085',
        3 => '#c0392b',
        4 => '#8e44ad',
        5 => '#303f9f',
        6 => '#f57c00',
        7 => '#795548',
      ),
      'contrast' => -5,
    ),
    'mini' => 
    array (
      'length' => 3,
      'width' => 60,
      'height' => 32,
    ),
    'inverse' => 
    array (
      'length' => 5,
      'width' => 120,
      'height' => 36,
      'quality' => 50,
      'sensitive' => true,
      'angle' => 12,
      'sharpen' => 10,
      'blur' => 2,
      'invert' => true,
      'contrast' => -5,
    ),
  ),
  'cors' => 
  array (
    'paths' => 
    array (
      0 => 'api/*',
    ),
    'allowed_methods' => 
    array (
      0 => '*',
    ),
    'allowed_origins' => 
    array (
      0 => '*',
    ),
    'allowed_origins_patterns' => 
    array (
    ),
    'allowed_headers' => 
    array (
      0 => '*',
    ),
    'exposed_headers' => 
    array (
    ),
    'max_age' => 0,
    'supports_credentials' => false,
  ),
  'database' => 
  array (
    'default' => 'mysql',
    'connections' => 
    array (
      'sqlite' => 
      array (
        'driver' => 'sqlite',
        'url' => NULL,
        'database' => 'ghesta',
        'prefix' => '',
        'foreign_key_constraints' => true,
      ),
      'mysql' => 
      array (
        'driver' => 'mysql',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'ghesta',
        'username' => 'root',
        'password' => '',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => NULL,
        'options' => 
        array (
        ),
      ),
      'old' => 
      array (
        'driver' => 'mysql',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'forge',
        'username' => 'root',
        'password' => '',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => NULL,
        'options' => 
        array (
        ),
      ),
      'hastinja' => 
      array (
        'driver' => 'mysql',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'forge',
        'username' => 'root',
        'password' => '',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => NULL,
        'options' => 
        array (
        ),
      ),
      'pgsql' => 
      array (
        'driver' => 'pgsql',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'ghesta',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'schema' => 'public',
        'sslmode' => 'prefer',
      ),
      'sqlsrv' => 
      array (
        'driver' => 'sqlsrv',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'ghesta',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
      ),
    ),
    'migrations' => 'migrations',
    'redis' => 
    array (
      'client' => 'phpredis',
      'options' => 
      array (
        'cluster' => 'redis',
        'prefix' => 'laravel_database_',
      ),
      'default' => 
      array (
        'url' => NULL,
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => '6379',
        'database' => '0',
      ),
      'cache' => 
      array (
        'url' => NULL,
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => '6379',
        'database' => '1',
      ),
    ),
  ),
  'dompdf' => 
  array (
    'show_warnings' => false,
    'orientation' => 'portrait',
    'defines' => 
    array (
      'font_dir' => 'G:\\ghesta\\ghesta-git\\q3til2\\storage\\fonts/',
      'font_cache' => 'G:\\ghesta\\ghesta-git\\q3til2\\storage\\fonts/',
      'temp_dir' => 'C:\\Users\\Farzad\\AppData\\Local\\Temp',
      'chroot' => 'G:\\ghesta\\ghesta-git\\q3til2',
      'enable_font_subsetting' => false,
      'pdf_backend' => 'CPDF',
      'default_media_type' => 'screen',
      'default_paper_size' => 'a4',
      'default_font' => 'helvetica',
      'dpi' => 96,
      'enable_php' => false,
      'enable_javascript' => true,
      'enable_remote' => false,
      'font_height_ratio' => 1.1,
      'enable_html5_parser' => true,
    ),
  ),
  'excel' => 
  array (
    'exports' => 
    array (
      'chunk_size' => 1000,
      'pre_calculate_formulas' => false,
      'strict_null_comparison' => false,
      'csv' => 
      array (
        'delimiter' => ',',
        'enclosure' => '"',
        'line_ending' => '
',
        'use_bom' => false,
        'include_separator_line' => false,
        'excel_compatibility' => false,
      ),
      'properties' => 
      array (
        'creator' => '',
        'lastModifiedBy' => '',
        'title' => '',
        'description' => '',
        'subject' => '',
        'keywords' => '',
        'category' => '',
        'manager' => '',
        'company' => '',
      ),
    ),
    'imports' => 
    array (
      'read_only' => true,
      'ignore_empty' => false,
      'heading_row' => 
      array (
        'formatter' => 'slug',
      ),
      'csv' => 
      array (
        'delimiter' => ',',
        'enclosure' => '"',
        'escape_character' => '\\',
        'contiguous' => false,
        'input_encoding' => 'UTF-8',
      ),
      'properties' => 
      array (
        'creator' => '',
        'lastModifiedBy' => '',
        'title' => '',
        'description' => '',
        'subject' => '',
        'keywords' => '',
        'category' => '',
        'manager' => '',
        'company' => '',
      ),
    ),
    'extension_detector' => 
    array (
      'xlsx' => 'Xlsx',
      'xlsm' => 'Xlsx',
      'xltx' => 'Xlsx',
      'xltm' => 'Xlsx',
      'xls' => 'Xls',
      'xlt' => 'Xls',
      'ods' => 'Ods',
      'ots' => 'Ods',
      'slk' => 'Slk',
      'xml' => 'Xml',
      'gnumeric' => 'Gnumeric',
      'htm' => 'Html',
      'html' => 'Html',
      'csv' => 'Csv',
      'tsv' => 'Csv',
      'pdf' => 'Dompdf',
    ),
    'value_binder' => 
    array (
      'default' => 'Maatwebsite\\Excel\\DefaultValueBinder',
    ),
    'cache' => 
    array (
      'driver' => 'memory',
      'batch' => 
      array (
        'memory_limit' => 60000,
      ),
      'illuminate' => 
      array (
        'store' => NULL,
      ),
    ),
    'transactions' => 
    array (
      'handler' => 'db',
    ),
    'temporary_files' => 
    array (
      'local_path' => 'G:\\ghesta\\ghesta-git\\q3til2\\storage\\framework/laravel-excel',
      'remote_disk' => NULL,
      'remote_prefix' => NULL,
      'force_resync_remote' => NULL,
    ),
  ),
  'filesystems' => 
  array (
    'default' => 'local',
    'cloud' => 's3',
    'disks' => 
    array (
      'local' => 
      array (
        'driver' => 'local',
        'root' => 'G:\\ghesta\\ghesta-git\\q3til2\\storage\\app',
      ),
      'public' => 
      array (
        'driver' => 'local',
        'root' => 'G:\\ghesta\\ghesta-git\\q3til2\\storage\\app/public',
        'url' => 'http://localhost/storage',
        'visibility' => 'public',
      ),
      's3' => 
      array (
        'driver' => 's3',
        'key' => '',
        'secret' => '',
        'region' => 'us-east-1',
        'bucket' => '',
        'url' => NULL,
        'endpoint' => NULL,
      ),
    ),
    'links' => 
    array (
      'G:\\ghesta\\ghesta-git\\q3til2\\public\\storage' => 'G:\\ghesta\\ghesta-git\\q3til2\\storage\\app/public',
    ),
  ),
  'globals' => 
  array (
    'ceo' => 
    array (
      'name' => 'محمدرضا آشتیانی',
      'mobile' => '09109270876',
    ),
    'cfo' => 
    array (
      'name' => 'محمدرضا آشتیانی',
      'mobile' => '09109270876',
    ),
    'cso' => 
    array (
      'name' => 'علیرضا اختری',
      'mobile' => '09109270876',
    ),
    'cbdo' => 
    array (
      'name' => 'امیر مهرابی',
      'mobile' => '09109270876',
    ),
    'cmo' => 
    array (
      'name' => 'مهدی حاجی',
      'mobile' => '09109270876',
    ),
    'cto' => 
    array (
      'name' => 'احسان برهان',
      'mobile' => '09109270876',
    ),
    'support' => 
    array (
      'name' => 'انیس برهان',
      'mobile' => '09109270876',
      'mobile_alt' => '09383506289',
    ),
    'address' => 'تهران، خیابان آزادی، بعد از دانشگاه شریف، خیابان صادقی، پلاک 33، واحد 6',
    'postal_code' => '1458883542',
    'phone' => '02191070092',
    'phone_alt' => '02166013373',
    'cheque_to' => 'شرکت پیشگامان اعتبارآفرین شریف',
    'cheque_to_nic' => '14009118150',
    'sms' => 
    array (
      'faraz_apikey' => 'q7e-dJUqbJU6sQ3HlJLYGNUjFUUIi-c--iBOT_aZCXM=',
      'faraz_username' => '',
      'faraz_password' => '',
      'faraz_number' => '1000888188',
      'faraz_number_alt' => '5000125475',
      'kavenegar_apikey' => '',
      'kavenegar_number' => '',
      'kavenegar_number_alt' => '',
    ),
    'order' => 
    array (
      'guarantee_ratio' => 1.3,
    ),
    'payment' => 
    array (
      'zarinpal_token' => '',
      'payping_token' => '',
      'payping_token_botika' => '',
    ),
    'cdn' => 
    array (
      'cdn1' => 
      array (
        'token' => '',
        'host' => 'qestaa.ir',
      ),
    ),
    'finnotech' => 
    array (
      'username' => '',
      'password' => '',
    ),
    'icbs' => 
    array (
      'username' => '',
      'password' => '',
    ),
  ),
  'hashing' => 
  array (
    'driver' => 'bcrypt',
    'bcrypt' => 
    array (
      'rounds' => 10,
    ),
    'argon' => 
    array (
      'memory' => 1024,
      'threads' => 2,
      'time' => 2,
    ),
  ),
  'image' => 
  array (
    'driver' => 'gd',
  ),
  'kavenegar' => 
  array (
    'apikey' => '736475456353483531786C33324135736373783255444171476F555A62486C365054736242492B724C74773D',
  ),
  'logging' => 
  array (
    'default' => 'stack',
    'channels' => 
    array (
      'stack' => 
      array (
        'driver' => 'stack',
        'channels' => 
        array (
          0 => 'single',
        ),
        'ignore_exceptions' => false,
      ),
      'single' => 
      array (
        'driver' => 'single',
        'path' => 'G:\\ghesta\\ghesta-git\\q3til2\\storage\\logs/laravel.log',
        'level' => 'debug',
      ),
      'daily' => 
      array (
        'driver' => 'daily',
        'path' => 'G:\\ghesta\\ghesta-git\\q3til2\\storage\\logs/laravel.log',
        'level' => 'debug',
        'days' => 14,
      ),
      'slack' => 
      array (
        'driver' => 'slack',
        'url' => NULL,
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
        'level' => 'critical',
      ),
      'papertrail' => 
      array (
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => 'Monolog\\Handler\\SyslogUdpHandler',
        'handler_with' => 
        array (
          'host' => NULL,
          'port' => NULL,
        ),
      ),
      'stderr' => 
      array (
        'driver' => 'monolog',
        'handler' => 'Monolog\\Handler\\StreamHandler',
        'formatter' => NULL,
        'with' => 
        array (
          'stream' => 'php://stderr',
        ),
      ),
      'syslog' => 
      array (
        'driver' => 'syslog',
        'level' => 'debug',
      ),
      'errorlog' => 
      array (
        'driver' => 'errorlog',
        'level' => 'debug',
      ),
      'null' => 
      array (
        'driver' => 'monolog',
        'handler' => 'Monolog\\Handler\\NullHandler',
      ),
      'emergency' => 
      array (
        'path' => 'G:\\ghesta\\ghesta-git\\q3til2\\storage\\logs/laravel.log',
      ),
    ),
  ),
  'mail' => 
  array (
    'default' => 'smtp',
    'mailers' => 
    array (
      'smtp' => 
      array (
        'transport' => 'smtp',
        'host' => 'smtp.mailtrap.io',
        'port' => '2525',
        'encryption' => NULL,
        'username' => NULL,
        'password' => NULL,
        'timeout' => NULL,
      ),
      'ses' => 
      array (
        'transport' => 'ses',
      ),
      'mailgun' => 
      array (
        'transport' => 'mailgun',
      ),
      'postmark' => 
      array (
        'transport' => 'postmark',
      ),
      'sendmail' => 
      array (
        'transport' => 'sendmail',
        'path' => '/usr/sbin/sendmail -bs',
      ),
      'log' => 
      array (
        'transport' => 'log',
        'channel' => NULL,
      ),
      'array' => 
      array (
        'transport' => 'array',
      ),
    ),
    'from' => 
    array (
      'address' => NULL,
      'name' => 'Laravel',
    ),
    'markdown' => 
    array (
      'theme' => 'default',
      'paths' => 
      array (
        0 => 'G:\\ghesta\\ghesta-git\\q3til2\\resources\\views/vendor/mail',
      ),
    ),
  ),
  'pdf' => 
  array (
    'mode' => 'utf-8',
    'format' => 'A4',
    'author' => 'Ghesta',
    'subject' => '',
    'keywords' => '',
    'creator' => 'Ghesta',
    'display_mode' => 'fullpage',
    'tempDir' => 'G:\\ghesta\\ghesta-git\\q3til2\\../temp/',
  ),
  'queue' => 
  array (
    'default' => 'sync',
    'connections' => 
    array (
      'sync' => 
      array (
        'driver' => 'sync',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
      ),
      'beanstalkd' => 
      array (
        'driver' => 'beanstalkd',
        'host' => 'localhost',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => 0,
      ),
      'sqs' => 
      array (
        'driver' => 'sqs',
        'key' => '',
        'secret' => '',
        'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
        'queue' => 'your-queue-name',
        'suffix' => NULL,
        'region' => 'us-east-1',
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => NULL,
      ),
    ),
    'failed' => 
    array (
      'driver' => 'database',
      'database' => 'mysql',
      'table' => 'failed_jobs',
    ),
  ),
  'services' => 
  array (
    'mailgun' => 
    array (
      'domain' => NULL,
      'secret' => NULL,
      'endpoint' => 'api.mailgun.net',
    ),
    'postmark' => 
    array (
      'token' => NULL,
    ),
    'ses' => 
    array (
      'key' => '',
      'secret' => '',
      'region' => 'us-east-1',
    ),
  ),
  'session' => 
  array (
    'driver' => 'file',
    'lifetime' => '1200000',
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => 'G:\\ghesta\\ghesta-git\\q3til2\\storage\\framework/sessions',
    'connection' => NULL,
    'table' => 'sessions',
    'store' => NULL,
    'lottery' => 
    array (
      0 => 2,
      1 => 100,
    ),
    'cookie' => 'laravel_session',
    'path' => '/',
    'domain' => NULL,
    'secure' => NULL,
    'http_only' => true,
    'same_site' => 'lax',
  ),
  'view' => 
  array (
    'paths' => 
    array (
      0 => 'G:\\ghesta\\ghesta-git\\q3til2\\resources\\views',
    ),
    'compiled' => 'G:\\ghesta\\ghesta-git\\q3til2\\storage\\framework\\views',
  ),
  'debugbar' => 
  array (
    'enabled' => NULL,
    'except' => 
    array (
      0 => 'telescope*',
      1 => 'horizon*',
    ),
    'storage' => 
    array (
      'enabled' => true,
      'driver' => 'file',
      'path' => 'G:\\ghesta\\ghesta-git\\q3til2\\storage\\debugbar',
      'connection' => NULL,
      'provider' => '',
      'hostname' => '127.0.0.1',
      'port' => 2304,
    ),
    'include_vendors' => true,
    'capture_ajax' => true,
    'add_ajax_timing' => false,
    'error_handler' => false,
    'clockwork' => false,
    'collectors' => 
    array (
      'phpinfo' => true,
      'messages' => true,
      'time' => true,
      'memory' => true,
      'exceptions' => true,
      'log' => true,
      'db' => true,
      'views' => true,
      'route' => true,
      'auth' => false,
      'gate' => true,
      'session' => true,
      'symfony_request' => true,
      'mail' => true,
      'laravel' => false,
      'events' => false,
      'default_request' => false,
      'logs' => false,
      'files' => false,
      'config' => false,
      'cache' => false,
      'models' => true,
      'livewire' => true,
    ),
    'options' => 
    array (
      'auth' => 
      array (
        'show_name' => true,
      ),
      'db' => 
      array (
        'with_params' => true,
        'backtrace' => true,
        'backtrace_exclude_paths' => 
        array (
        ),
        'timeline' => false,
        'explain' => 
        array (
          'enabled' => false,
          'types' => 
          array (
            0 => 'SELECT',
          ),
        ),
        'hints' => false,
        'show_copy' => false,
      ),
      'mail' => 
      array (
        'full_log' => false,
      ),
      'views' => 
      array (
        'data' => false,
      ),
      'route' => 
      array (
        'label' => true,
      ),
      'logs' => 
      array (
        'file' => NULL,
      ),
      'cache' => 
      array (
        'values' => true,
      ),
    ),
    'inject' => true,
    'route_prefix' => '_debugbar',
    'route_domain' => NULL,
    'theme' => 'auto',
  ),
  'flare' => 
  array (
    'key' => NULL,
    'reporting' => 
    array (
      'anonymize_ips' => true,
      'collect_git_information' => false,
      'report_queries' => true,
      'maximum_number_of_collected_queries' => 200,
      'report_query_bindings' => true,
      'report_view_data' => true,
      'grouping_type' => NULL,
      'report_logs' => true,
      'maximum_number_of_collected_logs' => 200,
      'censor_request_body_fields' => 
      array (
        0 => 'password',
      ),
    ),
    'send_logs_as_events' => true,
    'censor_request_body_fields' => 
    array (
      0 => 'password',
    ),
  ),
  'ignition' => 
  array (
    'editor' => 'phpstorm',
    'theme' => 'light',
    'enable_share_button' => true,
    'register_commands' => false,
    'ignored_solution_providers' => 
    array (
      0 => 'Facade\\Ignition\\SolutionProviders\\MissingPackageSolutionProvider',
    ),
    'enable_runnable_solutions' => NULL,
    'remote_sites_path' => '',
    'local_sites_path' => '',
    'housekeeping_endpoint_prefix' => '_ignition',
  ),
  'passport' => 
  array (
    'private_key' => NULL,
    'public_key' => NULL,
    'client_uuids' => false,
    'personal_access_client' => 
    array (
      'id' => NULL,
      'secret' => NULL,
    ),
    'storage' => 
    array (
      'database' => 
      array (
        'connection' => 'mysql',
      ),
    ),
  ),
  'trustedproxy' => 
  array (
    'proxies' => NULL,
    'headers' => 94,
  ),
  'location' => 
  array (
    'driver' => 'Stevebauman\\Location\\Drivers\\IpApi',
    'fallbacks' => 
    array (
      0 => 'Stevebauman\\Location\\Drivers\\IpInfo',
      1 => 'Stevebauman\\Location\\Drivers\\GeoPlugin',
      2 => 'Stevebauman\\Location\\Drivers\\MaxMind',
    ),
    'position' => 'Stevebauman\\Location\\Position',
    'maxmind' => 
    array (
      'web' => 
      array (
        'enabled' => false,
        'user_id' => '',
        'license_key' => '',
        'options' => 
        array (
          'host' => 'geoip.maxmind.com',
        ),
      ),
      'local' => 
      array (
        'path' => 'G:\\ghesta\\ghesta-git\\q3til2\\database\\maxmind/GeoLite2-City.mmdb',
      ),
    ),
    'ip_api' => 
    array (
      'token' => NULL,
    ),
    'ipinfo' => 
    array (
      'token' => NULL,
    ),
    'testing' => 
    array (
      'enabled' => true,
      'ip' => '66.102.0.0',
    ),
  ),
  'tinker' => 
  array (
    'commands' => 
    array (
    ),
    'alias' => 
    array (
    ),
    'dont_alias' => 
    array (
      0 => 'App\\Nova',
    ),
  ),
);

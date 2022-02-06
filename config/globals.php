+<?php

  return [
    'ceo' => [
      'name' => 'محمدرضا آشتیانی',
      'mobile' => env('CONTACTS_CEO', '09109270876'),
    ],
    'cfo' => [
      'name' => 'محمدرضا آشتیانی',
      'mobile' => env('CONTACTS_CFO', '09109270876'),
    ],
    'cso' => [
      'name' => 'علیرضا اختری',
      'mobile' => env('CONTACTS_CSO', '09109270876'),
    ],
    'cbdo' => [
      'name' => 'امیر مهرابی',
      'mobile' => env('CONTACTS_CBDO', '09109270876'),
    ],
    'cmo' => [
      'name' => 'مهدی حاجی',
      'mobile' => env('CONTACTS_CMO', '09109270876'),
    ],
    'cto' => [
      'name' => 'احسان برهان',
      'mobile' => '09109270876'
    ],
    'support' => [
      'name' => 'انیس برهان',
      'mobile' =>  env('CONTACTS_SUPPORT', '09109270876'),
      'mobile_alt' => '09383506289'
    ],
    'crm2' => [
      'name' => 'فاطمه عزتی',
      'mobile' =>  env('CONTACTS_CRM2', '09928725685'),
      'mobile_alt' => '09383506289'
    ],

    'address' => 'تهران، میدان آزادی، اتوبان لشکری، جنب متروی بیمه، کارخانه نوآوری آزادی، شرکت قسطا',
    'postal_code' => '1391955412',
    'phone' => '02191070092',
    'phone_alt' => '02166013373',
    'cheque_to' => 'شرکت پیشگامان اعتبارآفرین شریف',
    'cheque_to_nic' => '14009118150',

    'sms' => [
      'faraz_apikey' => env('SMS_FARAZ_APIKEY', 'q7e-dJUqbJU6sQ3HlJLYGNUjFUUIi-c--iBOT_aZCXM='),
      'faraz_username' => env('SMS_FARAZ_USERNAME', ''),
      'faraz_password' => env('SMS_FARAZ_PASSWORD', ''),
      'faraz_number' => '1000888188',
      'faraz_number_alt' => '5000125475',
      'kavenegar_apikey' => env('SMS_KAVENEGAR_APIKEY', ''),
      'kavenegar_number' => '',
      'kavenegar_number_alt' => '',
    ],

    'order' => [
      'guarantee_ratio' => 1.3,
    ],

    'payment' => [
      'zarinpal_token' => env('PAY_ZARINPAL_TOKEN', ''),
      'payping_token' => env('PAY_PAYPING_TOKEN', ''),
      'payping_token_botika' => env('PAY_PAYPING_TOKEN_BOTIKA', ''),
    ],

    'cdn' => [
      'cdn1' => [
        'token' => env('CDN_CDN1_TOKEN', ''),
        'host' => 'qestaa.ir'
      ]
    ],

    'finnotech' => [
      'username' => env('FT_USERNAME', ''),
      'password' => env('FT_PASSWORD', ''),
      'proxy_token' => env('FT_PROXY_TOKEN', ''),
      'proxy_url' => env('FT_PROXY_URL', ''),
    ],

    'icbs' => [
      'username' => env('ICBS_USERNAME', ''),
      'password' => env('ICBS_PASSWORD', ''),
    ],

    'venesh' => [
      'username' => env('VENESH_USERNAME', ''),
      'password' => env('VENESH_PASSWORD', ''),
      'secret' => env('VENESH_SECRET', ''),
    ],

    'telegram' => [
      'bot_token' => env('TELEGRAM_REP_TOKEN', ''),
      'report_channel_id' => env('TELEGRAM_REP_ID', '')
    ]
  ];

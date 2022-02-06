<?php

use Illuminate\Support\Facades\Route;

// Route::get('/testx', 'AdminController@test');

//AUTH
Route::post('/ok', 'HomeController@ok')->middleware('throttle:30,1');
Route::post('/otp', 'VerificationController@generate_otp')->middleware('throttle:15,1'); //username
Route::post('/otpv', 'VerificationController@validate_otp')->middleware('throttle:15,1'); //username, track_id, otp
Route::post('/register', 'Auth\RegisterController@create'); //username, password, token, utm, fname, lname
Route::post('/password-recovery', 'Auth\RegisterController@password_recovery'); //username, password, token
Route::post('/login', 'UserController@login'); //username, password 
Route::post('/payments/result/{gateway}', 'PaymentController@result');
Route::get('/payments/result/{authority}', 'PaymentController@result');
Route::get('/globals', 'SettingController@globals');
Route::get('/testx/{slug?}', 'TestController@get');
Route::post('/testx', 'TestController@post');

//ORDER
Route::group(['middleware' => ['auth:api', 'user'], 'prefix' => '/orders/'], function () {
  Route::post('{oid}/upload-cheque/{type}/{badge}', 'DocController@upload_cheque'); //file
  Route::post('{oid}/upload/{type}/{extra?}', 'DocController@upload'); //file
  Route::post('{oid}/concat/{type}/{extra?}', 'DocController@concat'); //file
  Route::post('{oid}/skip/{type}/{extra?}', 'DocController@concat'); //file
  Route::post('{oid}/user-info/essentials', 'OrderController@essentials'); //file
  Route::post('check-organ/{code}', 'OrganController@check_code'); //code
  Route::get('limits', 'OrderController@limits');
  Route::post('create', 'OrderController@create'); //amount, months, cheques, organ_code
  Route::get('', 'OrderController@my_orders');
  Route::get('{oid}', 'OrderController@single');
  Route::post('{oid}/cancel', 'OrderController@cancel');
  Route::get('{oid}/docs', 'OrderController@docs');
  Route::get('{oid}/cheques-preview', 'OrderController@cheques_preview');
  Route::get('{oid}/contract', 'OrderController@contract');
  Route::get('{oid}/card-stick/{address_type?}', 'OrderController@card_stick');
  Route::get('{oid}/ghests', 'GhestController@single');
  Route::post('{oid}/check-please', 'OrderController@check_please');
  Route::post('{oid}/payment/check-coupon/{type?}', 'PaymentController@check_coupon');
  Route::post('{oid}/payment/inquiry', 'PaymentController@inquiry');
  Route::post('{oid}/payment/prepayment', 'PaymentController@prepayment');
  Route::post('{oid}/payment/ghest', 'PaymentController@ghest');
  Route::post('{oid}/payment/extra', 'PaymentController@extra');
  Route::get('{oid}/notes', 'NoteController@index');
  Route::get('{oid}/accounts', 'OrderController@accounts');
  Route::post('{oid}/account', 'OrderController@define_account');
  Route::post('{oid}/cheques/{item?}', 'OrderController@cheques');
  Route::post('{oid}/cheque/account', 'OrderController@cheque_account');
});

//ESHOP
Route::group(['middleware' => ['auth:api', 'shop'], 'prefix' => '/eshop/'], function () {
  Route::get('info/{just?}', 'ShopController@info');
  Route::get('docs', 'ShopController@docs');
  Route::post('upload/{type}/{extra?}', 'DocController@upload');
  Route::post('concat/{type}/{extra?}', 'DocController@concat');
  Route::post('agree', 'ShopController@agree');

  Route::group(['prefix' => 'credits/'], function () {
    Route::get('', 'CreditController@index');
    Route::get('{id}', 'CreditController@single');
  }); 

  Route::get('inbox', 'InboxController@index');
  Route::get('inbox/unread-count', 'InboxController@unread_count')->name('nou-1');
  Route::get('inbox/{id}', 'InboxController@single');

  Route::get('commissions', 'OrderChargeController@index');

  Route::post('verify-domain', 'ShopController@verify_domain');
  Route::post('verify-email', 'ShopController@verify_email');
  Route::post('send-email-again', 'ShopController@email_again');
});

//SHOP
Route::group(['middleware' => ['auth:api', 'shop'], 'prefix' => '/shop/'], function () {
  Route::get('info/{just?}', 'ShopController@info');
  Route::get('docs', 'ShopController@docs');
  Route::post('upload/{type}/{extra?}', 'DocController@upload');
  Route::post('concat/{type}/{extra?}', 'DocController@concat');
  Route::post('agree', 'ShopController@agree');

  Route::group(['prefix' => 'orders/'], function () {
    Route::get('', 'OrderController@index');
    Route::post('create', 'OrderController@create'); //amount, months, cheques, organ_code,username,tracker
    Route::get('limits/{username}', 'OrderController@limits');
    Route::get('{oid}', 'OrderController@single');
    Route::post('{oid}/edit-user', 'OrderController@edit_user_by_shop'); //nid, birth
    Route::post('{oid}/edit-address', 'OrderController@edit_address_by_shop');
    Route::get('{oid}/docs', 'OrderController@docs');
    Route::post('{oid}/upload/{type}/{extra?}', 'DocController@upload'); //file
    Route::post('{oid}/concat/{type}/{extra?}', 'DocController@concat'); //file
    Route::post('{oid}/change/cancelled', 'OrderController@cancel');
    Route::post('{oid}/change/rejected', 'OrderController@cancel');
  });

  Route::get('ghests', 'GhestController@index');
  Route::get('ghests/counts', 'GhestController@status_counts');
  Route::get('inbox', 'InboxController@index');
  Route::get('inbox/unread-count', 'InboxController@unread_count')->name('nou-1');
  Route::get('inbox/{id}', 'InboxController@single');

  Route::get('commissions', 'OrderChargeController@index');
});

//ORGAN
Route::group(['middleware' => ['auth:api', 'organ'], 'prefix' => '/organ/'], function () {
  Route::get('info', 'OrganController@info');
  Route::get('docs', 'OrganController@docs');
  Route::post('upload/{type}/{extra?}', 'DocController@upload');
  Route::post('concat/{type}/{extra?}', 'DocController@concat');
  Route::get('orders', 'OrderController@index');
  Route::get('orders/pending', 'OrganController@last_pending_orders');
  Route::get('orders/{oid}', 'OrderController@single');
  Route::get('orders/limits/{code}', 'OrderController@limits')->withoutMiddleware('organ');
  Route::post('orders/{oid}/accept', 'OrderController@accept_order');
  Route::post('orders/{oid}/reject', 'OrderController@reject_order');
  Route::get('ghests', 'GhestController@index');
  Route::get('inbox', 'InboxController@index');
  Route::get('inbox/unread-count', 'InboxController@unread_count')->name('nou-2');
  Route::get('inbox/{id}', 'InboxController@single');
});

//ADMIN
Route::group(['middleware' => ['auth:api', 'admin'], 'prefix' => '/admin/'], function () {
  Route::group(['prefix' => 'users/'], function () {
    Route::get('', 'UserController@index');
    Route::get('export', 'UserController@index');
    Route::get('{id}', 'UserController@single');
    Route::get('{id}/edit', 'UserController@edit_fields');
    Route::get('{id}/notes', 'NoteController@index');
    Route::post('{id}/edit', 'UserController@edit');
    Route::post('{id}/delete', 'UserController@delete');
    Route::post('{id}/send-message', 'UserController@send_message');
    Route::get('{id}/inquiry/{type}/{force?}', 'FinnotechController@inquiry');
  });

  Route::group(['prefix' => 'shops/'], function () {
    Route::get('{id}/notes', 'NoteController@index');
    Route::get('', 'ShopController@index');
    Route::get('export', 'ShopController@index');
    Route::get('{id}', 'ShopController@single');
    Route::get('{id}/edit', 'ShopController@edit_fields');
    Route::post('{id}/edit', 'ShopController@edit');
    Route::post('{id}/delete', 'ShopController@delete');
    Route::post('{id}/change/{status}', 'ShopController@change_status');
    Route::post('{id}/send-message', 'UserController@send_message');
  });

  Route::group(['prefix' => 'organs/'], function () {
    Route::get('{id}/notes', 'NoteController@index');
    Route::get('', 'OrganController@index');
    Route::get('export', 'OrganController@index');
    Route::get('{id}/edit', 'OrganController@edit_fields');
    Route::post('{id}/edit', 'OrganController@edit');
    Route::post('{id}/delete', 'OrganController@delete');
    Route::post('{id}/change/{status}', 'OrganController@change_status');
    Route::post('create', 'OrganController@create');
    Route::post('{id}/send-message', 'UserController@send_message');
    Route::get('{id}', 'OrganController@single');
  });

  Route::group(['prefix' => 'discounts/'], function () {
    Route::get('', 'DiscountController@index');
    Route::post('{id}/edit', 'DiscountController@edit');
    Route::get('{id}/edit', 'DiscountController@edit_fields');
    Route::post('{id}/delete', 'DiscountController@delete');
    Route::post('create', 'DiscountController@create');
  });

  Route::group(['prefix' => 'links/'], function () {
    Route::get('', 'LinkController@index');
    Route::post('{id}/delete', 'LinkController@delete');
    Route::post('create', 'LinkController@create');
  });

  Route::group(['prefix' => 'orders/'], function () {
    Route::get('', 'OrderController@index');
    Route::get('export', 'OrderController@index');
    Route::get('counts', 'OrderController@counts');
    Route::get('{oid}', 'OrderController@single');
    Route::get('{oid}/docs', 'OrderController@docs');
    Route::post('{oid}/edit', 'OrderController@edit');
    Route::post('{oid}/change/{status}/{mode?}', 'OrderController@change_status');
    Route::get('{oid}/notes', 'NoteController@index');
    Route::post('{oid}/send-message', 'UserController@send_message');
    Route::post('{oid}/payment/prepayment', 'PaymentController@prepayment_offline');
    Route::post('{oid}/payment/ghest', 'PaymentController@ghest_offline');
    Route::post('{oid}/payment/extra', 'PaymentController@extra_offline');
    Route::get('{oid}/payment/check/{id}', 'PaymentController@check_again');
    Route::post('{oid}/charge', 'OrderController@charge');
    Route::post('{oid}/upload/{type}/{extra?}', 'DocController@upload'); //file
    Route::post('{oid}/concat/{type}/{extra?}', 'DocController@concat'); //file
    Route::get('{oid}/cheques-download', 'OrderController@cheques_download');
  });

  Route::group(['prefix' => 'cards/'], function () {
    Route::get('', 'CardController@index');
    Route::get('export', 'CardController@index');
    Route::post('export-cards-to-print', 'CardController@cards_to_print_export');
    Route::post('import-cards-to-submit', 'CardController@cards_to_submit_import');
    Route::get('{id}/edit', 'CardController@edit_fields');
    Route::post('{id}/edit', 'CardController@edit');
    Route::post('{id}/delete', 'CardController@delete');
    Route::get('{id}/charge-history', 'CardController@charge_history');
    Route::get('{id}/statement', 'CardController@statement');
    Route::post('{id}/charge', 'CardController@extra_charge'); //amount, card_number
    Route::post('create', 'CardController@create'); //card_number, mobile, series
    Route::post('{id}/delivered', 'CardController@delivered');
    Route::post('{id}/sent', 'CardController@sent');
  });

  Route::group(['prefix' => 'comments/'], function () {
    Route::get('', 'CommentController@index');
    Route::post('{id}/delete', 'CommentController@delete');
    Route::post('{id}/reply', 'CommentController@reply'); //comment

    Route::get('{id}/reply', 'CommentController@reply_fields');
  });

  Route::group(['prefix' => 'docs/'], function () {
    Route::post('{id}/accept/{scope?}', 'DocController@accept');
    Route::post('{id}/reject/{scope?}', 'DocController@reject');
    Route::post('{id}/readability/{is_readable}', 'DocController@is_readable');
    Route::get('{id}/{scope?}', 'DocController@single');
  });

  Route::group(['prefix' => 'cheques/'], function () {
    Route::get('{id}/download', 'DocController@download_it');
    Route::post('{id}/accept/{case}', 'DocController@accept_cheque');
    Route::post('{id}/reject/{case?}', 'DocController@reject_cheque');
  });

  Route::group(['prefix' => 'notes/'], function () {
    Route::get('', 'NoteController@index');
    Route::post('{id}/edit', 'NoteController@edit');
    Route::post('{id}/delete', 'NoteController@delete');
    Route::post('create', 'NoteController@create'); //id, type, title, caption
  });

  Route::group(['prefix' => 'inbox/'], function () {
    Route::get('', 'InboxController@index_admin');
    Route::get('{id}', 'InboxController@single_admin');
    Route::post('{id}/delete', 'InboxController@delete');
    Route::post('create', 'InboxController@create');
  });

  Route::group(['prefix' => 'ghests/'], function () {
    Route::get('', 'GhestController@index');
    Route::get('export', 'GhestController@index');
    Route::post('{id}/change/{action}', 'GhestController@change_status');
  });

  Route::group(['prefix' => 'settings/'], function () {
    Route::get('', 'SettingController@index');
    Route::post('', 'SettingController@edit');
  });

  Route::group(['prefix' => 'accounting/'], function () {
    Route::get('users', 'AccountingController@users');
    Route::get('cheques', 'AccountingController@cheques');
    Route::get('prepayments', 'AccountingController@prepayments');
    Route::get('charges', 'AccountingController@charges');
  });

  Route::group(['prefix' => 'charts/'], function () {
    Route::get('users/overview', 'UserController@chart_overview');
    Route::get('users/pie/{months?}', 'UserController@chart_pie');
    Route::get('users/line/{months?}', 'UserController@chart_line');

    Route::get('orders/overview', 'OrderController@chart_overview');
    Route::get('orders/pie/{months?}', 'OrderController@chart_pie');
    Route::get('orders/line/{months?}', 'OrderController@chart_line');

    Route::get('sales/overview', 'OrderController@chart_overview_sales');
    Route::get('sales/pie/{months?}', 'OrderController@chart_pie_sales');
    Route::get('sales/line/{months?}', 'OrderController@chart_line_sales');
  });

  Route::get('commissions', 'OrderChargeController@index');

  Route::get('exports/{filename}', 'ExportController@download');
});

//SALAR
Route::group(['middleware' => ['auth:api', 'salar'], 'prefix' => '/salar/'], function () {
  Route::get('cards/{id}', 'CardController@single');
  Route::post('cards/{id}/charge', 'CardController@extra_charge');
  Route::get('permissions', 'PermissionController@index');
  Route::get('permissions/create', 'PermissionController@create_fields');
  Route::post('permissions/create', 'PermissionController@create');
  Route::post('permissions/{id}/delete', 'PermissionController@delete');
});

Route::group(['middleware' => 'auth:api'], function () {
  Route::post('/otpx', 'VerificationController@generate_otp');
  Route::post('/shop/register', 'ShopController@create_shop');
  Route::post('/organ/register', 'OrganController@create_organ');
  Route::get('/profile/info', 'UserController@my_info');
  // Route::post('/profile/info/essentials', 'UserController@essentials');
  Route::post('/profile/info/edit', 'UserController@edit_info');
  Route::get('/profile/meta/edit', 'UserController@meta_fields');
  Route::post('/profile/meta/edit', 'UserController@meta');
  Route::get('/profile/forms-status', 'UserController@forms_fill_status');
  Route::get('/profile/addresses', 'UserController@addresses');
  Route::post('/profile/addresses/edit', 'UserController@edit_address');
  Route::post('/logout', 'UserController@logout');
  Route::post('/cards/card-received', 'CardController@card_received');
  Route::post('/upload/{type}/{extra?}', 'DocController@upload');
  Route::post('/concat/{type}/{extra?}', 'DocController@concat');
  Route::get('/inbox', 'InboxController@index');
  Route::get('/inbox/unread-count', 'InboxController@unread_count')->name('nou-3');
  Route::get('/inbox/{id}', 'InboxController@single');
});

<?php

use Illuminate\Support\Facades\Route; 

Route::group(['middleware' => ['client'], 'prefix' => '/{client}/'], function () { 
  Route::post('token-recovery', 'ClientController@token_recovery');
  Route::get('token-refresh', 'ClientController@token_refresh');
  Route::get('logs', 'ClientController@logs');

  //200
  Route::get('shop-inquiry', 'InquiryController@shop');
  Route::get('user-inquiry', 'InquiryController@user'); 
  Route::post('user-verification-and-invoice', 'InquiryController@otp_validation');
  Route::post('credit-recheck', 'InquiryController@credit_recheck');
  Route::get('reset', 'ClientController@reset');

  //900
  Route::get('cs', 'MalexController@cs');
});
<?php 

use Illuminate\Support\Facades\Route;

// Route::view('/testyyy', 'layouts.cardstick');

Route::post('/payments/result/{gateway}', 'PaymentController@result');

//TEMP 
Route::get('/rev/cache', 'RevController@cache');

Route::group(['domain' => 'ghsta.ir'], function () {
    Route::redirect('/', 'https://ghesta.ir', 301);
    Route::get('/{short}', 'LinkController@get');
});

Route::group(['domain' => 'ghesta.net'], function () {
    Route::get('/{any}', 'HomeController@redirectOrg');
});

Route::group(['domain' => 'ghesta.org'], function () {
    Route::get('/', 'HomeController@index_org');
    Route::get('/landing/{thing?}', 'HomeController@landing');
    Route::get('/l/{thing?}', 'HomeController@landing');
});

Route::get('/l/{thing?}', 'HomeController@landing');

Route::get('/خرید_اقساطی_گوشی', 'HomeController@landing');

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/xx', function () { 
    return 1;
    // return Finnotech::facilities('0016974654','manual-ehsan-fac-1');
});

Route::get('/xxx', function () {
});

//GENERAL
Route::get('/dox/{id}', 'DocController@download')->middleware('throttle:60,1');

//PAGES 
Route::get('/u/{code?}', 'HomeController@indexH');
Route::get('/faq', 'HomeController@faq');
Route::view('/shop', 'pages.shop');
Route::view('/eshop', 'pages.eshop');
Route::view('/developers', 'pages.developers');
Route::view('/organ', 'pages.organ');
Route::view('/help', 'pages.help');
Route::view('/estefta', 'pages.estefta');
Route::view('/payresult', 'pages.payresult');
Route::get('/testz', 'HomeController@testz');

Route::view('/from', 'landings.came-from-shops');
Route::view('/eshop', 'pages.eshop');
Route::view('/developers', 'developers.home');

//EMAILS
Route::view('/mail', 'emails.default');
Route::view('/mailx', 'emails.eshop_vfy');

//SHORT LINKS
Route::get('/map', 'LinkController@map');
Route::get('/o/{code}', 'HomeController@redirectOrder');
Route::redirect('/lgn', '/login', 301);
Route::redirect('/lgns', '/login', 301);
Route::redirect('/lgno', '/login', 301);
Route::redirect('/rgs15', '/?utm_source=registered&utm_medium=sms&utm_campaign=acquisition', 301);
Route::redirect('/ord20', '/?utm_source=ordered&utm_medium=sms&utm_campaign=acquisition', 301); 

Route::redirect('/dashboard', '/dashboard/user', 301);

// Vue Router
Route::get('{vue_capture?}', 'SpaController@index')->where('vue_capture', '[\/\w\.-]*');

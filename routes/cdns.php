<?php

use Illuminate\Support\Facades\Route; 

Route::post('checksum/{id}', 'CdnsController@checksum');
Route::post('remove/{id}', 'CdnsController@remove');
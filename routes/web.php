<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'    => 'api/' . config('email-record.route_prefix'),
    'namespace' => '\Larangular\EmailRecord\Http\Controllers',
], static function () {
    Route::resource('requests', 'EmailRequests\Gateway');
    Route::get('types/{id?}', 'EmailTypesBuilder\EmailTypes@types');
});

Route::group([
    'prefix'    => config('email-record.route_prefix'),
    'namespace' => '\Larangular\EmailRecord\Http\Controllers',
], static function () {
    Route::get('preview/{id}/{emailType?}', 'Emails\EmailPreview@preview');
});

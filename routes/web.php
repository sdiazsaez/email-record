<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'    => 'api/' . config('email-record.route_prefix'),
    'namespace' => '\Larangular\EmailRecord\Http\Controllers',
], static function () {
    Route::resource('requests', 'EmailRequests\Gateway');
    Route::get('test', 'Emails\SendEmailController@test');
    Route::get('types', 'EmailTypesBuilder\EmailTypes@types');
});

Route::group([
    'prefix'    => config('email-record.route_prefix'),
    'namespace' => '\Larangular\EmailRecord\Http\Controllers',
], static function () {

    Route::get('preview/{id}/{emailType?}', 'Emails\SendEmailController@preview');

    Route::get('preview/{idCotizacion}/{type}', 'Gateway@preview');

    //it gets the type from database
    Route::get('view/{id}', 'Gateway@view');


    Route::resource('scheduled-emails', 'ScheduledEmails\Gateway');
    Route::resource('email-requests', 'EmailRequests\Gateway');

    Route::get('emails', 'Emails\Email@make');
    Route::post('emails', 'Emails\Gateway@make');
});

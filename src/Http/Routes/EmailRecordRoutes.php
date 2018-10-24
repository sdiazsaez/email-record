<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 10/21/18
 * Time: 19:26
 */

Route::group([
                 'prefix'    => 'api/' . config('email-record.route_prefix'),
                 'namespace' => '\Larangular\EmailRecord\Http\Controllers',
             ], function () {
    Route::resource('requests', 'EmailRequests\Gateway');
    Route::get('test', 'Emails\SendEmailController@test');
});
Route::group([
                 'prefix'    => config('email-record.route_prefix'),
                 'namespace' => '\Larangular\EmailRecord\Http\Controllers',
             ], function () {

    Route::get('preview/{id}/{emailType?}', 'Emails\SendEmailController@preview');

    Route::get('preview/{idCotizacion}/{type}', 'Gateway@preview');

    //it gets the type from database
    Route::get('view/{id}', 'Gateway@view');


    Route::resource('scheduled-emails', 'ScheduledEmails\Gateway');
    Route::resource('email-requests', 'EmailRequests\Gateway');

    Route::get('emails', 'Emails\Email@make');
    Route::post('emails', 'Emails\Gateway@make');
});

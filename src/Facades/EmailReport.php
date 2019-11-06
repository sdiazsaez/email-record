<?php

namespace Larangular\EmailRecord\Facades;

use Illuminate\Support\Facades\Facade;

class EmailReport extends Facade {
    protected static function getFacadeAccessor() {
        return \Larangular\EmailRecord\Http\Controllers\Emails\EmailReport::class;
    }
}

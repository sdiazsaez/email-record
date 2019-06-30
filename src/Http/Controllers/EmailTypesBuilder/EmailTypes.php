<?php

namespace Larangular\EmailRecord\Http\Controllers\EmailTypesBuilder;

use Larangular\RoutingController\MakeResponse;

class EmailTypes {

    use MakeResponse;

    public function types() {
        return $this->makeResponse(config('email-record.email_types'));
    }

}

<?php

namespace Larangular\EmailRecord\EmailTypesBuilder;

use Larangular\RoutingController\MakeResponse;

class EmailTypes {

    use MakeResponse;

    public function types() {
        return $this->makeResponse(config('email_types'));
    }

}

<?php

namespace Larangular\EmailRecord\Http\Controllers\EmailTypesBuilder;

use Larangular\EmailRecord\Http\Controllers\Emails\RecordableEmailLoader;
use Larangular\RoutingController\MakeResponse;

class EmailTypes {

    use MakeResponse, RecordableEmailLoader;

    public function types(int $id = null) {
        return $this->makeResponse($this->getTypes($id));
    }

}

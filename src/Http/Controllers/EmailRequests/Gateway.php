<?php

namespace Larangular\EmailRecord\Http\Controllers\EmailRequests;

use Larangular\EmailRecord\Models\EmailRequest;
use Larangular\RoutingController\{Controller,
    Contracts\IGatewayModel};
use Illuminate\Http\Request;
use Larangular\Support\Instance;

class Gateway extends Controller implements IGatewayModel {

    public function model() {
        return EmailRequest::class;
    }

    public function save($data) {
        $request = parent::save($data);
        return $request;
    }

}

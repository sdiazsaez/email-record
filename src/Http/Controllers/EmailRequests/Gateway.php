<?php

namespace Larangular\EmailRecord\Http\Controllers\EmailRequests;

use Larangular\EmailRecord\Models\EmailRequest;
use Larangular\RoutingController\{Contracts\IGatewayModel, Controller};

class Gateway extends Controller implements IGatewayModel {

    public function model() {
        return EmailRequest::class;
    }

}

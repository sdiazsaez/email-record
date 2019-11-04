<?php

namespace Larangular\EmailRecord\Http\Controllers\EmailRequests;

use Larangular\EmailRecord\Http\Controllers\Emails\RecordableEmailLoader;
use Larangular\EmailRecord\Models\EmailRequest;
use Larangular\RoutingController\{Contracts\IGatewayModel, Controller};

class Gateway extends Controller implements IGatewayModel {

    use RecordableEmailLoader;

    public function model() {
        return EmailRequest::class;
    }

    public function save($data) {
        if (!array_key_exists('to', $data)) {
            $this->requestDefaultValues();
        }

        return parent::save($data);
    }

    private function requestDefaultValues(&$data) {
        $mailable = $this->getRecodableEmailWithRequest($data);
        $mailable->build();
        $data['to'] = $mailable->to;
        $data['from'] = $mailable->from;
        $data['bcc'] = $mailable->bcc;

        return $data;
    }


}

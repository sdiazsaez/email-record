<?php

namespace Larangular\EmailRecord\Http\Controllers;

use Larangular\EmailRecord\Models\SentEmail;
use Larangular\RoutingController\{Controller,
    Contracts\IGatewayModel};
use Illuminate\Http\Request;

class Gateway extends Controller implements IGatewayModel {

    public function model() {
        return SentEmail::class;
    }

    public function preview(Request $request, $id) {
        $email = SentEmail::withTrashed()
                          ->find($id);
        $template = $this->templatePath($email->email_request->type);
        if ($template !== false) {
            return view($template, $email);
        }
    }

    public function templatePath($emailType) {
        $path = false;
        switch ($emailType) {
            case Types::pendientePago:
                $path = 'emails.pendiente-pago.index';
                break;
            case Types::pendienteSeleccionOferta:
                $path = 'emails.cotizacion-productos.index';
                break;
        }

        return $path;
    }
}


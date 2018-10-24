<?php

namespace App\Http\Controllers\Emails;

use App\Http\Controllers\EmailRequests\{EmailRequest, Types};
use App\Http\Controllers\MsdAssets\Request;
use App\Http\Controllers\ScheduledEmails\SentEmail;
use App\Mail\{
    Common,
    PendienteInspeccion,
    PendientePago,
    PendienteFactura,
    ContratoGeneral,
    PostVenta,
    CotizacionProductos,
    PendienteRenovacion
};

class Email {

    use Request;

    public function deliver(){
        $scheduledEmails = SentEmail::all();

        foreach ($scheduledEmails as $item){
            $mailable = false;
            switch($item->email_request->type){
                case Types::pendienteInspeccion:
                    $mailable = new PendienteInspeccion($item);
                break;
                case Types::pendientePago:
                    $mailable = new PendientePago($item);
                break;
                case Types::pendienteFactura:
                    $mailable = new PendienteFactura($item);
                break;
                case Types::contratoGeneral:
                    $mailable = new ContratoGeneral($item);
                break;
                case Types::postVenta:
                    $mailable = new PostVenta($item);
                break;
                case Types::pendienteSeleccionOferta:
                    $mailable = new CotizacionProductos($item);
                break;
                case Types::pendienteRenovacion:
                    $mailable = new PendienteRenovacion($item);
                break;
                case Types::suraInspeccion:
                    $mailable = new PendienteInspeccion($item);
                break;
            }

            if($mailable !== false) {
                $mailable->bcc('contacto@misegurodirecto.cl');
                \Mail::send($mailable);
                if(count(\Mail::failures()) <= 0){
                    $item->delete();
                }else{
                    mail('sdiaz.sz@gmail.com', 'error en envio de correos', 'base.misegurodirecto.cl '. $item->id);

                    $item->delete();
                }
            }
        }
    }

    public function make($idCotizacion, $attributes = []){
        $cotizacion = $this->getCotizacion($idCotizacion);
        if($cotizacion){
            $mailContent = $this->getMailContent($cotizacion, $attributes['type']);

            if($mailContent){
                $mailContent['from'] = @$attributes['from'];
                $mailContent['type'] = @$attributes['type'];
                if(array_key_exists('message', $attributes)) {
                    $mailContent['content']['message'] = $attributes['message'];
                }

                $scheduledEmail = new SentEmail($mailContent);
                $scheduledEmail->save();

                $emailRequest = new EmailRequest([
                    'cotizacion_id' => $idCotizacion,
                    'scheduled_email_id' => $scheduledEmail->id,
                    'type' => @$attributes['type']
                ]);
                $emailRequest->save();
                return $emailRequest;
            }
        }

    }

    public function view($id){
        $emailRequest = EmailRequest::find($id);
        dd($emailRequest);
        $template = $this->templatePath($emailRequest->type);
        if($template !== false){
            return view($template, $emailRequest->scheduled_email);
        }

        return 'La cotización no cumple los requisitos para el tipo de correo';
    }

    public function preview($idCotizacion, $type){
        $cotizacion = $this->getCotizacion($idCotizacion);
        if($cotizacion){
            $mailContent = $this->getMailContent($cotizacion, $type);
            $template = $this->templatePath($type);
            if($mailContent && $template !== false){
                $scheduledEmail = new SentEmail($mailContent);
                return view($template, $scheduledEmail);
            }
        }

        return 'La cotización no cumple los requisitos para el tipo de correo';
    }

    private function templatePath($emailType){
        $path = false;
        switch($emailType){
            case Types::pendienteInspeccion:
                $path = 'pendiente-inspeccion';
                break;
            case Types::pendientePago:
                $path = 'pendiente-pago';
                break;
            case Types::pendienteFactura:
                $path = 'pendiente-factura';
                break;
            case Types::contratoGeneral:
                $path = 'contrato-general';
                break;
            case Types::postVenta:
                $path = 'post-venta';
                break;
            case Types::pendienteSeleccionOferta:
                $path = 'cotizacion-productos';
                break;
            case Types::pendienteRenovacion:
                $path = 'pendiente-renovacion';
                break;
            case Types::suraInspeccion:
                $path = 'sura-inspeccion';
            break;
        }

        return ($path !== false)?'emails.'.$path.'.index':false;
    }

    private function getCotizacion($id){
        return array_first($this->find('Cotizacion', [$id]));
    }

    private function getMailContent($data, $type){
        $response = false;
        switch ($type) {
            case Types::pendienteInspeccion:
                $response = $this->pendienteInspeccion($data);
            break;
            case Types::pendientePago:
                $response = $this->pendientePago($data);
            break;
            case Types::pendienteFactura:
                $response = $this->pendienteFactura($data);
                break;
            case Types::contratoGeneral:
                $response = $this->contratoGeneral($data);
                break;
            case Types::postVenta:
                $response = $this->postVenta($data);
            break;
            case Types::pendienteSeleccionOferta:
                $response = $this->cotizacionProducto($data);
                break;
            case Types::pendienteRenovacion:
                $response = $this->pendienteRenovacion($data);
                break;
            case Types::suraInspeccion:
                $response = $this->suraInspeccion($data);
            break;
        }

        return $response;
    }



    private function pendienteInspeccion($cotizacion){
        $data = [
            'to' => $cotizacion->cotizante->Correo,
            'content' => [
                'name' => $cotizacion->cotizante->Nombre
            ]
        ];

        return $data;
    }

    private function pendientePago($cotizacion){
        if($cotizacion->idSeleccion <= 0) return false;

        $propuesta = $cotizacion->seleccion->propuesta;
        $coberturas = array_column($propuesta->coberturas, 'nombre');
        $tarificacion = $propuesta->tarificacion->total->tarifaFinal;

        $assets = [];
        foreach($cotizacion->bienes as $item){

            $assets[] = [
                'marca' => $item->Detalle->marca,
                'modelo' => $item->Detalle->modelo,
                'ano' => $item->Detalle->ano,
                'patente' => $this->patente(@$item->Detalle->patente, @$item->Detalle->estado)
            ];
        }

        $data = [
            'to' => $cotizacion->cotizante->Correo,
            'content' => [
                'name' => $cotizacion->cotizante->Nombre,
                'assets' => $assets,
                'product' => [
                    'coverages' => $coberturas,
                    'price' => $tarificacion->mensual,
                    'uf' => $tarificacion->primaAnualUFBruta,
                    'buy_url' => $this->createUrlContinue($cotizacion)
                ]
            ]
        ];

        return $data;
    }

    private function pendienteFactura($cotizacion){
        //TODO falta implementacion
        return false;
        $data = [
            'to' => $cotizacion->cotizante->Correo,
            'content' => [
                'name' => $cotizacion->cotizante->Nombre
            ]
        ];

        return $data;
    }

    private function contratoGeneral($cotizacion){
        if($cotizacion->idSeleccion <= 0) return false;

        $propuesta = $cotizacion->seleccion->propuesta;
        $coberturas = array_column($propuesta->coberturas, 'nombre');
        $tarificacion = $propuesta->tarificacion->total->tarifaFinal;

        $assets = [];
        foreach($cotizacion->bienes as $item){
            $assets[] = [
                'marca' => $item->Detalle->marca,
                'modelo' => $item->Detalle->modelo,
                'ano' => $item->Detalle->ano,
                'patente' => $this->patente(@$item->Detalle->patente, @$item->Detalle->estado)
            ];
        }

        $data = [
            'to' => $cotizacion->cotizante->Correo,
            'content' => [
                'name' => $cotizacion->cotizante->Nombre,
                'assets' => $assets,
                'login_url' => $this->createUrlLogin($cotizacion),
                'pol_url' => $this->createUrlPoliza($cotizacion),
                'product' => [
                    'coverages' => $coberturas,
                    'price' => $tarificacion->mensual,
                    'uf' => $tarificacion->primaAnualUFBruta
                ]
            ]
        ];

        return $data;
    }

    private function postVenta($cotizacion){
        $data = [
            'to' => $cotizacion->cotizante->Correo,
            'content' => [
                'name' => $cotizacion->cotizante->Nombre,
                'login_url' => $this->createUrlLogin($cotizacion),
            ]
        ];

        return $data;
    }

    private function cotizacionProducto($cotizacion){
        $assets = [];
        foreach($cotizacion->bienes as $item){
            $assets[] = $item->Detalle->marca.' '.$item->Detalle->modelo.' '.$item->Detalle->ano;
        }

        $products = [];
        foreach($cotizacion->propuestas as $propuesta){
            $tarificacion = $propuesta->tarificacion->total->tarifaFinal;
            $products[] = [
                'coverages' => array_column($propuesta->coberturas, 'nombre'),
                'price' => $tarificacion->mensual,
                'uf' => $tarificacion->primaAnualUFBruta,
                'buy_url' => $this->createUrlContinue($cotizacion)
            ];
        }

        return [
            'to' => $cotizacion->cotizante->Correo,
            'content' => [
                'name' => $cotizacion->cotizante->Nombre,
                'assets' => $assets,
                'products' => $products
            ]
        ];

    }

    private function pendienteRenovacion($cotizacion){
        //TODO unccomment
        //if($cotizacion->cotizacion_compra_id <= 0) return false;

        $propuesta = $cotizacion->seleccion->propuesta;
        $coberturas = array_column($propuesta->coberturas, 'nombre');
        $tarificacion = $propuesta->tarificacion->total->tarifaFinal;

        $assets = [];
        foreach($cotizacion->bienes as $item){
            $assets[] = [
                'marca' => $item->Detalle->marca,
                'modelo' => $item->Detalle->modelo,
                'ano' => $item->Detalle->ano,
                'patente' => $item->Detalle->patente
            ];
        }

        $data = [
            'to' => $cotizacion->cotizante->Correo,
            'content' => [
                'name' => $cotizacion->cotizante->Nombre,
                'expires' => @$cotizacion->cotizacion_compra->termino_vigencia,
                'assets' => $assets,
                'product' => [
                    'coverages' => $coberturas,
                    'price' => $tarificacion->mensual,
                    'uf' => $tarificacion->primaAnualUFBruta,
                    'buy_url' => $this->createUrlLogin($cotizacion)
                ]
            ]
        ];

        return $data;
    }


    private function suraInspeccion($cotizacion){
        if($cotizacion->idSeleccion <= 0) return false;

        $propuesta = $cotizacion->seleccion->propuesta;
        $coberturas = array_column($propuesta->coberturas, 'nombre');
        $tarificacion = $propuesta->tarificacion->total->tarifaFinal;

        $assets = [];
        foreach($cotizacion->bienes as $item){

            $assets[] = [
                'marca' => $item->Detalle->marca,
                'modelo' => $item->Detalle->modelo,
                'ano' => $item->Detalle->ano,
                'patente' => $this->patente(@$item->Detalle->patente, @$item->Detalle->estado)
            ];
        }

        $data = [
            'to' => $cotizacion->cotizante->Correo,
            'content' => [
                'name' => $cotizacion->cotizante->Nombre,
                'assets' => $assets,
                'product' => [
                    'coverages' => $coberturas,
                    'price' => $tarificacion->mensual,
                    'uf' => $tarificacion->primaAnualUFBruta,
                    //'buy_url' => $this->createUrlContinue($cotizacion)
                ]
            ]
        ];

        return $data;
    }


    //TODO move to other place
    private function createUrlLogin($cotizacion){

        return 'https://www.misegurodirecto.cl/ingresar/'.$cotizacion->cotizante->Rut.'/'.$cotizacion->cotizante->Correo;
    }

    private function createUrlPoliza($cotizacion){
        return 'https://www.misegurodirecto.cl/penta_pol/?sid='.$cotizacion->id;
    }

    private function createUrlContinue($cotizacion){
        $data = [
            'id' => $cotizacion->id,
            'correo' => $cotizacion->cotizante->Correo,
            'rut' => $cotizacion->cotizante->Rut
        ];

        if($cotizacion->estado_cotizacion->status == 1 ||
            $cotizacion->estado_cotizacion->status == 3) {
            $code = base64_encode(\GuzzleHttp\json_encode($data));
            return 'https://www.misegurodirecto.cl/continua-cotizacion/'.$code;
        }

        return false;
    }

    private function patente($value, $estado){
        $patente = (isset($value) && !empty($value))? $value : '';
        if(empty($patente) && $estado == '1'){
            $patente = 'Vehículo nuevo';
        }

        return $patente;
    }
}

<?php

namespace App\Http\Controllers\Emails;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Gateway extends Controller {

    private $emailInstance;
    public function __construct() {
        //parent::__construct();
        $this->emailInstance = new Email();
    }

    public function make(Request $request){
        return $this->emailInstance->make($request->input('idCotizacion'), $request->input('attributes'));
    }

    public function preview(Request $request, $idCotizacion, $type){
        return $this->emailInstance->preview($idCotizacion, $type);
    }

    public function view(Request $request, $id){
        return $this->emailInstance->view($id);
    }

}

<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 10/4/17
 * Time: 18:07
 */

namespace App\Http\Controllers\EmailRequests;

abstract class Types{
    const pendienteInspeccion = "0";
    const pendientePago = "1";
    const pendienteFactura = "2";
    const contratoGeneral = "3";
    const contratoDpp = "4";
    const postVenta = "5";
    const pendienteSeleccionOferta = "6";
    const pendienteRenovacion = "7";
    const suraInspeccion = "8";
}

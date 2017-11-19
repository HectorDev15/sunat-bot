<?php
/**
 * Created by PhpStorm.
 * User: Giansalex
 * Date: 19/11/2017
 * Time: 18:05
 */

namespace Sunat\Bot\Model;

class RrhhResult
{
    /**
     * @var string
     */
    public $fecEmision;
    /**
     * @var string
     */
    public $tipoDoc;
    /**
     * @var string
     */
    public $serieNroDoc;
    /**
     * @var string
     */
    public $estado;
    /**
     * @var string
     */
    public $clientTipoDoc;
    /**
     * @var string
     */
    public $clientNroDoc;
    /**
     * @var string
     */
    public $clientRzSocial;
    /**
     * @var string
     */
    public $tipoRenta;
    /**
     * @var bool
     */
    public $isGratuito;
    /**
     * @var string
     */
    public $descripcion;
    /**
     * @var string
     */
    public $observacion;
    /**
     * @var string
     */
    public $moneda;
    /**
     * @var float
     */
    public $rentaBruta;
    /**
     * @var float
     */
    public $impuestoRenta;
    /**
     * @var float
     */
    public $rentaNeta;
    /**
     * @var float
     */
    public $montoNetoPago;

}
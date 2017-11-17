<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 17/11/2017
 * Time: 05:57 PM
 */

namespace Sunat\Bot\Model;

/**
 * Class SaleResult
 * @package Sunat\Bot\Model
 */
class SaleResult
{
    /**
     * @var string
     */
    public $codFactura;
    /**
     * @var string
     */
    public $codigoMoneda;
    /**
     * @var string
     */
    public $codigoMonedaDesc;
    /**
     * @var string
     */
    public $comprobantePorElQueSeEmite;
    /**
     * @var string
     */
    public $estadoDesc;
    /**
     * @var string
     */
    public $fechaEmisionDesc;
    /**
     * @var string
     */
    public $fechaEstado;
    /**
     * @var string
     */
    public $fechaRechazoDesc;
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $idPDF;
    /**
     * @var string
     */
    public $importeTotalDesc;
    /**
     * @var string
     */
    public $indEstado;
    /**
     * @var string
     */
    public $ind_anulado;
    /**
     * @var string
     */
    public $ind_emisionNC21;
    /**
     * @var string
     */
    public $ind_puede_descargar;
    /**
     * @var string
     */
    public $nombrePDF;
    /**
     * @var string
     */
    public $nroFactura;
    /**
     * @var string
     */
    public $nroFacturaDesc;
    /**
     * @var string
     */
    public $nroRucEmisor;
    /**
     * @var string
     */
    public $nroRucEmisorDesc;
    /**
     * @var string
     */
    public $nroRucReceptor;
    /**
     * @var string
     */
    public $nroRucReceptorDesc;
    /**
     * @var string
     */
    public $nroSerie;
    /**
     * @var string
     */
    public $numeroIdXml;
    /**
     * @var string
     */
    public $tipoCPE;

    /**
     * @param array $data
     * @return SaleResult
     */
    public static function createFromArray($data)
    {
        $obj = new SaleResult();
        foreach($data as $key => $val) {
            if(property_exists(__CLASS__, $key)) {
                $obj->$key = $val;
            }
        }

        return $obj;
    }
}
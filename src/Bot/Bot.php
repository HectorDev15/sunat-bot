<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 17/11/2017
 * Time: 05:12 PM
 */

namespace Sunat\Bot;

use Sunat\Bot\Helper\ZipReader;
use Sunat\Bot\Model\ClaveSol;
use Sunat\Bot\Model\RrhhResult;
use Sunat\Bot\Model\SaleResult;
use Sunat\Bot\Request\CookieRequest;

/**
 * Class Bot
 * @package Sunat\Bot
 */
class Bot
{
    const URL_AUTH = 'https://e-menu.sunat.gob.pe/cl-ti-itmenu/AutenticaMenuInternet.htm';
    const URL_FORMAT_VENTAS = 'https://ww1.sunat.gob.pe/ol-ti-itconscpemype/consultar.do?action=realizarConsulta&buscarPor=porPer&estado=0&fec_desde=%s&fec_hasta=%s&tipoConsulta=10';
    const URL_DOWNLOAD_XML = 'https://ww1.sunat.gob.pe/ol-ti-itconscpemype/consultar.do';

    /**
     * @var ClaveSol
     */
    private $user;
    /**
     * @var CookieRequest
     */
    private $req;

    /**
     * Bot constructor.
     * @param ClaveSol $user
     */
    public function __construct(ClaveSol $user)
    {
        $this->user = $user;
        $this->req = new CookieRequest();
    }

    /**
     * @return bool
     */
    public function login()
    {
        $curl = $this->req->getCurl();

        $curl->post(self::URL_AUTH, [
            'tipo' => '2',
            'dni' => '',
            'username' => $this->user->ruc . $this->user->user,
            'password' => $this->user->password,
            'captcha'  =>  '',
            'params' => '*&*&/cl-ti-itmenu/MenuInternet.htm&b64d26a8b5af091923b23b6407a1c1db41e733a6',
            'exe' => ''
        ]);
        /**@var $headers \Curl\CaseInsensitiveArray*/
        $headers = $curl->responseHeaders;
        if (!isset($headers['Location'])) {
            return false;
        }

        $this->navigate([$headers['Location']]);

        return true;
    }

    /**
     * @param string $start
     * @param string $end
     * @return SaleResult[]
     */
    public function getVentas($start, $end)
    {
        $url = sprintf(self::URL_FORMAT_VENTAS, urlencode($start), urlencode($end));
        $curl = $this->req->getCurl();
        $html = $curl->get($url);

        return $this->getList($html);
    }

    /**
     * @param $start
     * @param $end
     * @return RrhhResult[]
     */
    public function getRrhh($start, $end)
    {
        $curl = $this->req->getCurl();
        /*$html = $curl->post('https://ww1.sunat.gob.pe/ol-ti-itreciboelectronico/cpelec001Alias', [
            'accion' => 'CapturaCriterioBusqueda1',
            'proceso' => '31196ALTA',
            'indicadoralta' => 'PNAT',
            'tipocomprobante' => '01;',
            'cod_docide' => '-',
            'num_docide' => '',
            'num_serie' => '',
            'num_comprob' => '',
            'fec_desde' => '01/08/2017',
            'fec_hasta' => '24/08/2017',
            'tipoestado' => '00',
            'tipocomprobante1' => '01',
        ]);*/

        $start = urlencode($start);
        $end = urlencode($end);

        $data = "accion=CapturaCriterioBusqueda1&proceso=31196ALTA&indicadoralta=PNAT&tipocomprobante=01%3B02%3B03%3B&cod_docide=-&num_docide=&num_serie=&num_comprob=&fec_desde=$start&fec_hasta=$end&tipoestado=00&tipocomprobante1=01&tipocomprobante1=02&tipocomprobante1=03";
//
        $curl->setUrl('https://ww1.sunat.gob.pe/ol-ti-itreciboelectronico/cpelec001Alias');
        $curl->setOpt(CURLOPT_POST, true);
        $curl->setOpt(CURLOPT_POSTFIELDS, $data);
        $curl->exec();

        $curl->setOpt(CURLOPT_ENCODING , 'utf-8');
        $html = $curl->post('https://ww1.sunat.gob.pe/ol-ti-itreciboelectronico/cpelec001Alias', [
            'accion' => 'descargaConsultaEmisor'
        ]);

        return iterator_to_array($this->parseTxt($html));
    }

    private function getRrhhXml($pos)
    {
        $curl = $this->req->getCurl();
        $xml = $curl->post('https://ww1.sunat.gob.pe/ol-ti-itreciboelectronico/cpelec001Alias', [
            'posirecibo' => $pos,
            'accion' => 'CapturaCriterioBusqueda2',
        ]);

        $curl->setOpt(CURLOPT_ENCODING, '');
        $xml = $curl->post('https://ww1.sunat.gob.pe/ol-ti-itreciboelectronico/cpelec001Alias', [
            'accion' => 'descargarreciboxml',
        ]);

    }

    public function navigate(array $urls)
    {
        $curl = $this->req->getCurl();
        foreach ($urls as $url) {
            $curl->get($url);
            if($curl->error) {
                return false;
            }
            $headers = $curl->responseHeaders;
            if (isset($headers['Location'])) {
                $curl->get($headers['Location']);
            }
        }

        return true;
    }

    private function parseTxt($txt)
    {
        $separator = "\r\n";
        strtok($txt, $separator);
        $line = strtok($separator);
        while ($line !== false) {
            $items = explode('|', $line);
            $rrhh = new RrhhResult();
            $rrhh->fecEmision = $items[0];
            $rrhh->tipoDoc = $items[1];
            $rrhh->serieNroDoc = $items[2];
            $rrhh->estado = $items[3];
            $rrhh->clientTipoDoc = $items[4];
            $rrhh->clientNroDoc = rtrim($items[5]);
            $rrhh->clientRzSocial = $items[6];
            $rrhh->tipoRenta = $items[7];
            $rrhh->isGratuito = $items[8] == 'SI';
            $rrhh->descripcion = $items[9];
            $rrhh->observacion = $items[10];
            $rrhh->moneda = $items[11];
            $rrhh->rentaBruta = floatval($items[12]);
            $rrhh->impuestoRenta = floatval($items[13]);
            $rrhh->rentaNeta = floatval($items[14]);
            $rrhh->montoNetoPago = floatval($items[15]);

            yield $rrhh;
            $line = strtok($separator);
        }
    }

    private function getList($html)
    {
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);
        $nodes = $doc->getElementsByTagName('textarea');
        $text = $nodes->item(0)->textContent;

        $root = json_decode($text);
        if ($root->codeError != 0) {
            throw new \Exception("Error obteniendo ventas json");
        }

        $all = [];
        $objs = json_decode($root->data);

        foreach ($objs as $item) {
            $all[] = SaleResult::createFromArray($item);
        }

        return $all;
    }

    /**
     * @param string $serie
     * @param string $correlativo
     * @return string El contenido del xml del comprabante electrónico.
     */
    public function getXml($serie, $correlativo)
    {
        $curl = $this->req->getCurl();
        $fileZip = $curl->post(self::URL_DOWNLOAD_XML, [
            'action' => 'descargarFactura',
            'ruc' => $this->user->ruc,
            'tipo' => '10',
            'serie' => $serie,
            'numero' => $correlativo,
        ]);

        $reader = new ZipReader();
        $xml = $reader->decompressXmlFile($fileZip);

        return $xml;
    }
}
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
use Sunat\Bot\Model\SaleResult;
use Sunat\Bot\Request\CookieRequest;

/**
 * Class Bot
 * @package Sunat\Bot
 */
class Bot
{
    const URL_AUTH = 'https://e-menu.sunat.gob.pe/cl-ti-itmenu/AutenticaMenuInternet.htm';
    const URL_MENU = 'https://e-menu.sunat.gob.pe/cl-ti-itmenu/MenuInternet.htm?action=execute&code=11.5.3.1.2&s=ww1';
    const URL_FORMAT_VENTAS = 'https://ww1.sunat.gob.pe/ol-ti-itconscpemype/consultar.do?action=realizarConsulta&buscarPor=porPer&estado=0&fec_desde=%s&fec_hasta=%s&tipoConsulta=10';
    const URL_DOWNLOAD_XML = 'https://ww1.sunat.gob.pe/ol-ti-itconscpemype/consultar.do';

    /**
     * @var \Curl\Curl
     */
    private $curl;
    /**
     * @var ClaveSol
     */
    private $user;

    /**
     * Bot constructor.
     * @param ClaveSol $user
     */
    public function __construct(ClaveSol $user)
    {
        $this->user = $user;
        $this->curl = (new CookieRequest())->getCurl();
        $this->curl->setUserAgent('');
    }

    /**
     * @return bool
     */
    public function login()
    {
        $curl = $this->curl;

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

        $this->navigateUrls([
            $headers['Location'],
            self::URL_MENU,
        ]);

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
        $curl = $this->curl;
        $html = $curl->get($url);

        return $this->getList($html);
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
        $curl = $this->curl;
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

    private function navigateUrls(array $urls)
    {
        $curl = $this->curl;
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
}
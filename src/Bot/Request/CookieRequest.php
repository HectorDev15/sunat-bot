<?php
/**
 * Created by PhpStorm.
 * User: Giansalex
 * Date: 16/11/2017
 * Time: 20:04
 */

namespace Sunat\Bot\Request;

use Curl\Curl;

/**
 * Class CookieRequest
 * @package Sunat\Bot\Request
 */
class CookieRequest
{
    /**
     * @var array
     */
    public $cookies;

    /**
     * CookieRequest constructor.
     */
    public function __construct()
    {
        $this->clearCookies();
    }

    /**
     * @return Curl
     */
    public function getCurl()
    {
        $curl = new Curl();
//        $curl->setOpt(CURLOPT_PROXY, '127.0.0.1:8888');
//        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setUserAgent('');
        if (!empty($this->cookies)) {
            $curl->setCookies($this->cookies);
        }
        $curl->completeCallback = function (Curl $instance) {
            $this->cookies = array_merge($this->cookies, $instance->responseCookies);
            $instance->setCookies($this->cookies);
        };

        return $curl;
    }

    public function clearCookies()
    {
        $this->cookies = [];
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 17/11/2017
 * Time: 05:36 PM
 */

namespace Tests\Sunat\Bot;

use Sunat\Bot\Bot;
use Sunat\Bot\Menu;
use Sunat\Bot\Model\ClaveSol;

class BotTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Bot
     */
    private $bot;

    public function setUp()
    {
        $user = new ClaveSol();
        $user->ruc = getenv('COMPANY_RUC');
        $user->user = getenv('COMPANY_USER');
        $user->password = getenv('COMPANY_PASS');

        $this->bot = new Bot($user);
    }

    public function testLogin()
    {
        $this->assertTrue($this->bot->login());
    }

    public function testGetList()
    {
        $this->bot->login();
        $this->bot->navigate([Menu::CONSULTA_SOL_FACTURA]);
        $start = '01/08/2017';
        $end = '24/08/2017';
        $sales = $this->bot->getVentas($start, $end);

        $this->assertGreaterThanOrEqual(1, count($sales));
    }

    public function testGetSee()
    {
        $this->bot->login();
        $this->bot->navigate([Menu::CONSULTA_SEE_FE]);
        $doc = $this->bot->getVentaSee('F001', '184');

        $this->assertNotNull($doc);
    }

    public function testGetXmlSee()
    {
        $this->bot->login();
        $this->bot->navigate([Menu::CONSULTA_SEE_FE]);
        $xml = $this->bot->getSeeXml('F001', '184');

        $this->assertNotEmpty($xml);
//        file_put_contents('data.xml', $xml);
    }

    public function testGetXml()
    {
        $this->bot->login();
        $this->bot->navigate([Menu::CONSULTA_SOL_FACTURA]);
        $xml = $this->bot->getXml('E001', '180');

        $this->assertNotEmpty($xml);
//        file_put_contents('xml.xml', $xml);
    }
}
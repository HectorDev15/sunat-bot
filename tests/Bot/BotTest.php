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

        $this->assertGreaterThanOrEqual(0, count($sales));
    }

    public function testGetListBol()
    {
        $this->bot->login();
        $this->bot->navigate([Menu::CONSULTA_SOL_BOLETA]);
        $start = '01/08/2017';
        $end = '24/08/2017';
        $sales = $this->bot->getVentasBol($start, $end);

        $this->assertEquals(0, count($sales));
    }

    public function testGetSee()
    {
        $this->bot->login();
        $this->bot->navigate([Menu::CONSULTA_SEE_FE]);
        $doc = $this->bot->getVentaSee('F001', '1');

        $this->assertNotNull($doc);
    }

    public function testGetXmlSee()
    {
        $ruc = getenv('COMPANY_RUC_EMISOR');
        $this->bot->login();
        $this->bot->navigate([Menu::CONSULTA_SEE_FE]);
        $xml = $this->bot->getSeeXml($ruc,'F001', '184');

        $this->assertNotEmpty($xml);
    }

    public function testGetXmlFac()
    {
        $rucEmisor = getenv('COMPANY_RUC_EMISOR');
        $this->bot->login();
        $this->bot->navigate([Menu::CONSULTA_SOL_FACTURA]);
        $xml = $this->bot->getXmlFac($rucEmisor, 'E001', '180');

        $this->assertNotEmpty($xml);
    }

    public function testNotFoundXml()
    {
        $rucEmisor = getenv('COMPANY_RUC_EMISOR');
        $this->bot->login();
        $this->bot->navigate([Menu::CONSULTA_SOL_FACTURA]);
        $xml = $this->bot->getXmlFac($rucEmisor, 'E003', '180');

        $this->assertFalse($xml);
    }
}
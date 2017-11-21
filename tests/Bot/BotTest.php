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
        $this->bot->navigate([Menu::CONSULTA_FACTURA_NOTA]);
        $start = '01/08/2017';
        $end = '24/08/2017';
        $sales = $this->bot->getVentas($start, $end);

        $this->assertGreaterThanOrEqual(1, count($sales));
    }

    public function testRrhh()
    {
        $this->bot->login();
        $this->bot->navigate([Menu::CONSULTA_RRHH]);
        $elements = $this->bot->getRrhh('01/08/2017', '24/08/2017');

        $this->assertTrue(count($elements) > 0);

        $this->bot->getRrhhXml(0);
    }

    public function testGetXml()
    {
        $this->bot->login();
        $this->bot->navigate([Menu::CONSULTA_FACTURA_NOTA]);
        $xml = $this->bot->getXml('E001', '180');

        file_put_contents('xml.xml', $xml);
    }
}
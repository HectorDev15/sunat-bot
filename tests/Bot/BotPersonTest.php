<?php
/**
 * Created by PhpStorm.
 * User: Giansalex
 * Date: 29/11/2017
 * Time: 00:07
 */

namespace Tests\Sunat\Bot;

use Sunat\Bot\Bot;
use Sunat\Bot\Menu;
use Sunat\Bot\Model\ClaveSol;

class BotPersonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Bot
     */
    private $bot;

    public function setUp()
    {
        $user = new ClaveSol();
        $user->ruc = getenv('PERSON_RUC');
        $user->user = getenv('PERSON_USER');
        $user->password = getenv('PERSON_PASS');

        $this->bot = new Bot($user);
    }

    public function testLogin()
    {
        $this->assertTrue($this->bot->login());
    }

    public function testRrhh()
    {
        $this->bot->login();
        $this->bot->navigate([Menu::CONSULTA_RRHH]);
        $elements = $this->bot->getRrhh('01/08/2017', '24/08/2017');

        $this->assertTrue(count($elements) > 0);

        $xml = $this->bot->getRrhhXml(0);
        $this->assertNotEmpty($xml);
//        file_put_contents('file.xml', $xml);
    }
}
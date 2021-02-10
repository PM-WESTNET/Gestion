<?php

use app\modules\westnet\models\Node;
use app\tests\fixtures\ServerFixture;
use app\tests\fixtures\ZoneFixture;
use app\tests\fixtures\NodeFixture;
use app\modules\westnet\models\IpRange;
use app\tests\fixtures\IpRangeFixture;
use Codeception\Util\Debug;

class NodeTest extends \Codeception\Test\Unit
{
    protected function _before()
    {

    }

    protected function _after()
    {

    }

    public function _fixtures()
    {
        return [
            'zone' => [
                'class' => ZoneFixture::class,
            ],
            'server' => [
                'class' => ServerFixture::class
            ],
            'node' => [
                'class' => NodeFixture::class,
            ],
            'ip_range' => [
                'class' => IpRangeFixture::class
            ]
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new Node();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new Node([
            'zone_id' => 1,
            'name' => 'Nodo',
            'status' => 'enabled',
            'subnet' => 3,
            'server_id' => 1
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new Node();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new Node([
            'zone_id' => 1,
            'name' => 'Nodo',
            'status' => 'enabled',
            'subnet' => 3,
            'server_id' => 1
        ]);

        expect('Saved when full and new', $model->save())->true();
    }

    /*
        Prueba asignacion de ip desde el nodo, con el metodo de toda la vida
    */
    public function testUsableIpWithLegacyStrategy()
    {
        $node = Node::findOne(1);

        $ip = $node->getUsableIp();

        Debug::debug(long2ip($ip));

        $result = false;

        $pos = strpos(long2ip($ip), '10.'. $node->subnet);

        if ($pos !== false) {
            $result = true;
        } 

        expect('Fail asigment IP Legacy', $result)->true();
    }

    //TODO resto de la clase
}
<?php

use app\modules\westnet\models\Node;
use app\tests\fixtures\ServerFixture;
use app\tests\fixtures\ZoneFixture;

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
            'subnet' => 1,
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
            'subnet' => 1,
            'server_id' => 1
        ]);

        expect('Saved when full and new', $model->save())->true();
    }

    //TODO resto de la clase
}
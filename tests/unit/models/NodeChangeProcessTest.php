<?php

use app\modules\westnet\models\NodeChangeProcess;
use app\tests\fixtures\NodeFixture;
use app\tests\fixtures\UserFixture;
use Codeception\Test\Unit;

class NodeChangeProcessTest extends Unit
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
            'node' => [
                'class' => NodeFixture::class,
            ],
            'user' => [
                'class' => UserFixture::class
            ]
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new NodeChangeProcess();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new NodeChangeProcess([
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            'status' => NodeChangeProcess::STATUS_CREATED,
            'node_id' => 1,
            'creator_user_id' => 1
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new NodeChangeProcess();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new NodeChangeProcess([
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            'status' => NodeChangeProcess::STATUS_CREATED,
            'node_id' => 1,
            'creator_user_id' => 1
        ]);

        expect('Saved when full and new', $model->save())->true();
    }
}
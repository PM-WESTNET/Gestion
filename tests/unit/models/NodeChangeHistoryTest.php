<?php

use app\modules\westnet\models\Connection;
use app\modules\westnet\models\NodeChangeHistory;
use app\modules\westnet\models\NodeChangeProcess;
use app\tests\fixtures\ConnectionFixture;
use app\tests\fixtures\NodeChangeProcessFixture;
use app\tests\fixtures\NodeFixture;
use app\tests\fixtures\UserFixture;
use Codeception\Test\Unit;

class NodeChangeHistoryTest extends Unit
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
            'connection' => [
                'class' => ConnectionFixture::class
            ],
            'node_change_process' => [
                'class' => NodeChangeProcessFixture::class
            ]
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new NodeChangeHistory();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $connection = Connection::findOne(1);
        $model = new NodeChangeHistory([
            'new_node_id' => 1,
            'connection_id' => $connection->connection_id,
            'old_ip' => $connection->ip4_1,
            'new_ip' => 1270000001,
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            'node_change_process_id' => 1
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new NodeChangeHistory();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $connection = Connection::findOne(1);
        $model = new NodeChangeHistory([
            'new_node_id' => 1,
            'connection_id' => $connection->connection_id,
            'old_ip' => $connection->ip4_1,
            'new_ip' => 1270000001,
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            'node_change_process_id' => 1
        ]);

        expect('Saved when full and new', $model->save())->true();
    }
}
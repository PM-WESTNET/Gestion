<?php

use app\modules\westnet\models\Connection;
use app\modules\westnet\models\NodeChangeHistory;
use app\modules\westnet\models\NodeChangeProcess;
use app\tests\fixtures\ConnectionFixture;
use app\tests\fixtures\NodeChangeProcessFixture;
use app\tests\fixtures\NodeChangeHistoryFixture;
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
            ],
            'node_change_history' => [
                'class' => NodeChangeHistoryFixture::class
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
            //'new_node_id' => 1,
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
            //'new_node_id' => 1,
            'connection_id' => $connection->connection_id,
            'old_ip' => $connection->ip4_1,
            'new_ip' => 1270000001,
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            'node_change_process_id' => 1
        ]);

        expect('Saved when full and new', $model->save())->true();
    }

    public function testRollbackSuccess() {
        $history = NodeChangeHistory::findOne(1);
        $result = false;
        if ($history) {
            $oldServer = $history->connection->old_server_id;
            $history->rollback();

            $result = $history->status === 'reverted' && $history->connection->ip4_1 === $history->old_ip 
            && $history->connection->node_id === $history->old_node_id && $history->connection->server_id === $oldServer;
        }

        expect('Fail rollback', $result)->true();
    }

    public function testRollbackFailOnChangeIsReverted() {
        $history = NodeChangeHistory::findOne(2);
        $r = false;
        if ($history) {
            $oldServer = $history->connection->old_server_id;
            $result = $history->rollback();

            $r = $result['status'] === 'success' ? true : false;
        }

        expect('Fail rollback', $r)->false();
    }

    public function testRollbackFailOnChangeIsError() {
        $history = NodeChangeHistory::findOne(3);
        $r = false;
        if ($history) {
            $oldServer = $history->connection->old_server_id;
            $result = $history->rollback();

            $r = $result['status'] === 'success' ? true : false;
        }

        expect('Fail rollback', $r)->false();
    }
}
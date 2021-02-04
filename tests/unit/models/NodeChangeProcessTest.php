<?php

use app\modules\westnet\models\Connection;
use app\modules\westnet\models\NodeChangeHistory;
use app\modules\westnet\models\NodeChangeProcess;
use app\tests\fixtures\ConnectionFixture;
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
            ],
            'connection' => [
                'class' => ConnectionFixture::class
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

    public function testChangeStatus()
    {
        $model = new NodeChangeProcess([
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            'status' => NodeChangeProcess::STATUS_CREATED,
            'node_id' => 1,
            'creator_user_id' => 1
        ]);
        $model->save();

        expect('Change status from created to pending', $model->changeStatus(NodeChangeProcess::STATUS_PENDING))->true();
        $model->updateAttributes(['status' => NodeChangeProcess::STATUS_CREATED]);
        expect('Change status from created to finished', $model->changeStatus(NodeChangeProcess::STATUS_FINISHED))->true();
        $model->updateAttributes(['status' => NodeChangeProcess::STATUS_PENDING]);

        expect('Change status from pending to created', $model->changeStatus(NodeChangeProcess::STATUS_CREATED))->true();
        $model->updateAttributes(['status' => NodeChangeProcess::STATUS_PENDING]);
        expect('Change status from pending to finished', $model->changeStatus(NodeChangeProcess::STATUS_FINISHED))->true();
        $model->updateAttributes(['status' => NodeChangeProcess::STATUS_FINISHED]);

        expect('Change status from finished to created', $model->changeStatus(NodeChangeProcess::STATUS_CREATED))->false();
        $model->updateAttributes(['status' => NodeChangeProcess::STATUS_FINISHED]);
        expect('Change status from finished to pending', $model->changeStatus(NodeChangeProcess::STATUS_PENDING))->false();


    }

    public function testGetDeletable()
    {
        $model = new NodeChangeProcess([
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            'status' => NodeChangeProcess::STATUS_CREATED,
            'node_id' => 1,
            'creator_user_id' => 1,
            'status' => NodeChangeProcess::STATUS_CREATED
        ]);
        $model->save();

        expect('getDeletable returns true on status created', $model->getDeletable())->true();

        $model->updateAttributes(['status' => NodeChangeProcess::STATUS_PENDING]);

        expect('getDeletable returns false on status pending', $model->getDeletable())->false();

        $model->updateAttributes(['status' => NodeChangeProcess::STATUS_FINISHED]);

        expect('getDeletable returns false on status finished', $model->getDeletable())->false();
    }

    public function testCanBeProcessed()
    {
        $model = new NodeChangeProcess([
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            'status' => NodeChangeProcess::STATUS_CREATED,
            'node_id' => 1,
            'creator_user_id' => 1,
            'status' => NodeChangeProcess::STATUS_CREATED
        ]);
        $model->save();

        expect('canBeProcessed returns true on status created', $model->canBeProcessed())->true();

        $model->updateAttributes(['status' => NodeChangeProcess::STATUS_PENDING]);

        expect('canBeProcessed returns false on status pending', $model->canBeProcessed())->false();

        $model->updateAttributes(['status' => NodeChangeProcess::STATUS_FINISHED]);

        expect('canBeProcessed returns false on status finished', $model->canBeProcessed())->false();
    }

    public function testGetStatuses()
    {
        $statuses = NodeChangeProcess::getStatuses();

        expect('getStatuses returns an array', is_array($statuses))->true();

        expect('pending exists on array', array_key_exists(NodeChangeProcess::STATUS_PENDING, $statuses))->true();
        expect('created exists on array', array_key_exists(NodeChangeProcess::STATUS_CREATED, $statuses))->true();
        expect('finished exists on array', array_key_exists(NodeChangeProcess::STATUS_FINISHED, $statuses))->true();
    }

    public function testChangeNode()
    {
        $model = new NodeChangeProcess([
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            'status' => NodeChangeProcess::STATUS_CREATED,
            'node_id' => 1,
            'creator_user_id' => 1,
            'status' => NodeChangeProcess::STATUS_CREATED
        ]);
        $model->save();

        $result = $model->changeNode(Connection::findOne(1), 1);
        expect('Returns true when the destination node its the same', $result['status'])->true();

        $history_count = count(NodeChangeHistory::find()->all());
        $result = $model->changeNode(Connection::findOne(1), 2);
        expect('Returns true', $result['status'])->true();
        expect('History created', count(NodeChangeHistory::find()->all()))->equals($history_count + 1);
    }
}
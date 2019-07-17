<?php

use app\modules\ticket\models\TicketManagement;
use app\tests\fixtures\TicketFixture;
use app\tests\fixtures\UserFixture;

class TicketManagementTest extends \Codeception\Test\Unit
{
    public function _fixtures(){
        return [
            'ticket' => [
                'class' => TicketFixture::class
            ],
            'user' => [
                'class' => UserFixture::class
            ]
        ];
    }

    public function testInvalidWhenEmptyAndNew()
    {
        $model = new TicketManagement();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new TicketManagement([
            'ticket_id' => 1,
            'user_id' => 1,
        ]);

        $model->validate();

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new TicketManagement();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new TicketManagement([
            'ticket_id' => 1,
            'user_id' => 1,
        ]);

        expect('Valid when full and new', $model->save())->true();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 21/03/19
 * Time: 15:26
 */

use app\modules\ticket\models\Ticket;
use app\tests\fixtures\TicketStatusFixture;
use app\tests\fixtures\CustomerFixture;
use app\tests\fixtures\TicketCategoryFixture;
use app\tests\fixtures\UserFixture;
use app\modules\ticket\models\Assignation;

class TicketTest extends \Codeception\Test\Unit
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
            'status' => [
                'class' => TicketStatusFixture::class
            ],
            'customer' => [
                'class' => CustomerFixture::class
            ],
            'category' => [
                'class' => TicketCategoryFixture::class
            ],
            'user' => [
                'class' => UserFixture::class
            ]
        ];
    }


    public function testInvalidWhenEmptyAndNew()
    {
        $model = new Ticket();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new Ticket([
            'status_id' => 1,
            'customer_id' => 45900,
            'title' => 'Ticket1',
            'content' => 'Content ticket1',
            'category_id' => 1,
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new Ticket();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new Ticket([
            'status_id' => 1,
            'customer_id' => 45900,
            'title' => 'Ticket1',
            'content' => 'Content ticket1',
            'category_id' => 1,
        ]);

        expect('Valid when full and new', $model->save())->true();
    }

    public function testAssignTicketToUser()
    {
        $model = new Ticket([
            'status_id' => 1,
            'customer_id' => 45900,
            'title' => 'Ticket1',
            'content' => 'Content ticket1',
            'category_id' => 1,
        ]);
        $model->save();

        Ticket::assignTicketToUser($model->ticket_id, 1);
        $assignation_exists = Assignation::find()->where(['ticket_id' => $model->ticket_id, 'user_id' => 1])->exists();

        expect('Assignation exists', $assignation_exists)->true();
    }

    public function testDeleteAssignedUser()
    {
        $model = new Ticket([
            'status_id' => 1,
            'customer_id' => 45900,
            'title' => 'Ticket1',
            'content' => 'Content ticket1',
            'category_id' => 1,
        ]);
        $model->save();

        Ticket::assignTicketToUser($model->ticket_id, 1);
        $assignation_exists = Assignation::find()->where(['ticket_id' => $model->ticket_id, 'user_id' => 1])->exists();

        expect('Assignation exists', $assignation_exists)->true();

        $model->deleteAssignedUser(1);
        $assignation_exists = Assignation::find()->where(['ticket_id' => $model->ticket_id, 'user_id' => 1])->exists();

        expect('Assignation doesnt exists', $assignation_exists)->false();
    }

//    TODO resto funciones anteriores de la clase
}
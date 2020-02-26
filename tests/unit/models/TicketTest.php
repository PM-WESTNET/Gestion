<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 21/03/19
 * Time: 15:26
 */

use app\modules\ticket\models\Observation;
use app\modules\ticket\models\Ticket;
use app\tests\fixtures\TicketStatusFixture;
use app\tests\fixtures\CustomerFixture;
use app\tests\fixtures\TicketCategoryFixture;
use app\tests\fixtures\UserFixture;
use app\modules\ticket\models\Assignation;
use app\modules\config\models\Config;

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

    public function testDeleteAllAsignations()
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

        Ticket::assignTicketToUser($model->ticket_id, 2);
        $assignation_exists = Assignation::find()->where(['ticket_id' => $model->ticket_id, 'user_id' => 2])->exists();

        expect('Assignation exists', $assignation_exists)->true();

        Ticket::deleteAllAssignations($model->ticket_id, [2]);

        $assignation_exists = Assignation::find()->where(['ticket_id' => $model->ticket_id, 'user_id' => 1])->exists();
        expect('Assignation not exists after deletion', $assignation_exists)->false();

        $assignation_exists = Assignation::find()->where(['ticket_id' => $model->ticket_id, 'user_id' => 2])->exists();
        expect('Assignation exists after deletion', $assignation_exists)->true();

    }

    public function testCanAddTicketManagement()
    {
        $model = new Ticket([
            'status_id' => 1,
            'customer_id' => 45900,
            'title' => 'Ticket1',
            'content' => 'Content ticket1',
            'category_id' => 1,
        ]);
        $model->save();

        expect('Cant add ticket management cause doesnt have any observation', $model->canAddTicketManagement())->false();

        $observation = new Observation([
            'title' => 'Título de la observación',
            'description' => 'Descripción de la observación',
            'user_id' => 1,
            'ticket_id' => $model->ticket_id
        ]);
        $observation->save();

        expect('Can add ticket management cause ticket have one observation', $model->canAddTicketManagement())->true();
    }

    public function testAddTicketManagement()
    {
        $model = new Ticket([
            'status_id' => 1,
            'customer_id' => 45900,
            'title' => 'Ticket1',
            'content' => 'Content ticket1',
            'category_id' => 1,
        ]);
        $model->save();

        expect('Cant add ticket management cause ticket doesnt have observations', $model->addTicketManagement(1))->false();

        $observation = new Observation([
            'title' => 'Título de la observación',
            'description' => 'Descripción de la observación',
            'user_id' => 1,
            'ticket_id' => $model->ticket_id
        ]);
        $observation->save();

        expect('Can add ticket management cause ticket have one observation', $model->addTicketManagement(1))->true();
        expect('Ticket has one observation', count($model->observations))->equals(1);
    }

    public function testGetTicketManagementQuantity()
    {
        $model = new Ticket([
            'status_id' => 1,
            'customer_id' => 45900,
            'title' => 'Ticket1',
            'content' => 'Content ticket1',
            'category_id' => 1,
        ]);
        $model->save();

        expect('Ticket management total is 0', $model->getTicketManagementQuantity())->equals(0);

        $observation = new Observation([
            'title' => 'Título de la observación',
            'description' => 'Descripción de la observación',
            'user_id' => 1,
            'ticket_id' => $model->ticket_id
        ]);
        $observation->save();
        $model->addTicketManagement(1);

        expect('Ticket management total is 1', $model->getTicketManagementQuantity())->equals(1);
    }

    public function testCreateGestionADSTicket()
    {
        $ticket_category_gestion_ads = Config::getValue('ticket_category_gestion_ads');
        $inital_ticket_qty = count(Ticket::find()->where(['category_id' => $ticket_category_gestion_ads])->all());

        expect('Ticket created', Ticket::createGestionADSTicket(45900))->true();
        expect('Ticket qty increased', count(Ticket::find()->where(['category_id' => $ticket_category_gestion_ads])->all()))->equals($inital_ticket_qty + 1);


    }
//    TODO resto funciones anteriores de la clase
}
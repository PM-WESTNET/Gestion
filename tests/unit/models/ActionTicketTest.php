<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 27/03/19
 * Time: 10:24
 */

use app\modules\ticket\components\actions\ActionTicket;
use app\tests\fixtures\TicketFixture;
use app\modules\ticket\models\Ticket;
use app\tests\fixtures\StatusHasActionFixture;

class ActionTicketTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _fixtures(){
        return [
            'ticket' => [
                'class' => TicketFixture::class
            ],
            'status_has_action' => [
                'class' => StatusHasActionFixture::class
            ]
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new ActionTicket();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new ActionTicket([
            'name' => 'Action 1'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new ActionTicket();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new ActionTicket([
            'name' => 'Action 1',
        ]);

        expect('Valid when full and new', $model->save())->true();
    }

    public function testGenerate()
    {
        $ticket = Ticket::findOne(1);
        $ticket->updateAttributes(['status_id' => 43]);

        expect('No tickets before', count(Ticket::find()->all()))->equals(1);

        ActionTicket::generate($ticket);

        expect('One ticket after', count(Ticket::find()->all()))->equals(2);
    }
}
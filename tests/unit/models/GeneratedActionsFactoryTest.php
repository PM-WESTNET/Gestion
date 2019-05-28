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
use app\modules\agenda\models\Task;
use app\modules\ticket\components\factories\GeneratedActionsFactory;

class GeneratedActionsFactoryTest extends \Codeception\Test\Unit
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
    public function testGenerate()
    {
        $ticket = Ticket::findOne(1);

        expect('No task before before', count(Task::find()->all()))->equals(0);

        GeneratedActionsFactory::generate($ticket);

        expect('One task after', count(Task::find()->all()))->equals(1);

        $ticket->updateAttributes(['status_id' => 43]);

        expect('No ticket before before', count(Ticket::find()->all()))->equals(1);

        GeneratedActionsFactory::generate($ticket);

        expect('One ticket after', count(Ticket::find()->all()))->equals(2);
    }
}
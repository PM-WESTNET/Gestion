<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 27/03/19
 * Time: 10:24
 */

use app\modules\ticket\components\actions\ActionEvent;
use app\tests\fixtures\TicketFixture;
use app\modules\ticket\models\Ticket;
use app\tests\fixtures\StatusHasActionFixture;
use app\modules\agenda\models\Task;

class ActionEventTest extends \Codeception\Test\Unit
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
        $model = new ActionEvent();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new ActionEvent([
            'name' => 'Action 1'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new ActionEvent();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new ActionEvent([
            'name' => 'Action 1',
        ]);

        expect('Valid when full and new', $model->save())->true();
    }

    public function testGenerate()
    {
        $ticket = Ticket::findOne(1);

        expect('No task before', count(Task::find()->all()))->equals(0);

        ActionEvent::generate($ticket);

        expect('One task after', count(Task::find()->all()))->equals(1);
    }
}
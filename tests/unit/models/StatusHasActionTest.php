<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 27/03/19
 * Time: 10:15
 */

use app\modules\ticket\models\StatusHasAction;
use app\tests\fixtures\TicketStatusFixture;
use app\modules\ticket\models\Action;

class StatusHasActionTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _fixtures(){
        return [
            'status' => [
                'class' => TicketStatusFixture::class
            ],
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new StatusHasAction();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new StatusHasAction([
            'status_id' => 1,
        ]);
        $model->validate();

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new StatusHasAction();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new StatusHasAction([
            'status_id' => 1,
        ]);
        expect('Valid when full and new', $model->save())->true();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 20/03/19
 * Time: 11:00
 */

use app\modules\ticket\models\Action;

class ActionTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new Action();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new Action([
            'name' => 'Action 1'
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new Action();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new Action([
           'name' => 'Action 1',
        ]);

        expect('Valid when full and new', $model->save())->true();
    }

    public function testGetForSelect()
    {
        $model = new Action([
            'name' => 'Action 1',
        ]);
        $model->save();

        $model2 = new Action([
            'name' => 'Action 2',
        ]);
        $model2->save();

        $select = Action::getForSelect();
        expect('Get for select is not empty', $select)->notEmpty();
        expect('Get for select has key action 1', array_key_exists($model->action_id, $select))->true();
        expect('Get for select has key action 2', array_key_exists($model2->action_id, $select))->true();
        expect('Get for select has action 1', $select[$model->action_id])->equals('Action 1');
        expect('Get for select has action 2', $select[$model2->action_id])->equals('Action 2');
    }

    public function testGetTypeForSelect()
    {
        $select = Action::getTypeForSelect();

        expect('Select is not empty', $select)->notEmpty();
        expect('Select has type event', array_key_exists(Action::TYPE_EVENT, $select))->true();
        expect('Select has type ticket', array_key_exists(Action::TYPE_TICKET, $select))->true();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 01/03/19
 * Time: 16:17
 */

use app\modules\accounting\models\AccountMovementItem;
use app\tests\fixtures\AccountFixture;
use app\tests\fixtures\AccountMovementFixture;

class AccountMovementItemTest extends \Codeception\Test\Unit
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
            'account_movement' => [
                'class' => AccountMovementFixture::class,
            ],
            'account' => [
                'class' => AccountFixture::class,
            ],
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new AccountMovementItem();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new AccountMovementItem([
            'account_movement_id' => 1,
            'account_id' => 1
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new AccountMovementItem();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new AccountMovementItem([
            'account_movement_id' => 1,
            'account_id' => 1
        ]);

        expect('Saved when full and new', $model->save())->true();
    }

    public function testGetWorkflowStates()
    {
        $model = new AccountMovementItem([
            'account_movement_id' => 1,
            'account_id' => 1
        ]);
        $model->save();

        expect('Get workflow have draft status', array_key_exists(AccountMovementItem::STATE_DRAFT, $model->getWorkflowStates()));
        expect('Get workflow have draft status', array_key_exists(AccountMovementItem::STATE_CONCILED, $model->getWorkflowStates()));
    }

    public function testGetWorkFlowAttr()
    {
        $model = new AccountMovementItem([
            'account_movement_id' => 1,
            'account_id' => 1
        ]);
        $model->save();

        $attribute = $model->getWorkflowAttr();
        expect('Attribute exists', $model->hasAttribute($attribute))->true();
    }
}
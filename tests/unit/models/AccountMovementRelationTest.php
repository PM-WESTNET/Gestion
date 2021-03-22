<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 01/03/19
 * Time: 12:17
 */

use app\modules\accounting\models\AccountMovementRelation;
use app\tests\fixtures\AccountMovementFixture;
use app\tests\fixtures\PaymentFixture;
use app\modules\checkout\models\Payment;

class AccountMovementRelationTest extends \Codeception\Test\Unit
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
                'class' => AccountMovementFixture::class
            ],
            'payment' => [
                'class' => PaymentFixture::class
            ]
        ];
    }

    // tests
    public function testInvalidWhenEmptyAndNew()
    {
        $model = new AccountMovementRelation();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new AccountMovementRelation([
            'class' => Payment::class,
            'model_id' => 1,
            'account_movement_id' => 1,
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new AccountMovementRelation();
        expect('Not saved when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        $model = new AccountMovementRelation([
            'class' => Payment::class,
            'model_id' => 1,
            'account_movement_id' => 1,
        ]);

        expect('Saved when full and new', $model->save())->true();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 14/01/19
 * Time: 15:08
 */

use app\modules\westnet\ecopagos\models\BatchClosure;
use app\tests\fixtures\EcopagoFixture;
use app\tests\fixtures\CollectorFixture;
use app\modules\westnet\ecopagos\models\BatchClosureHasPayout;
use app\tests\fixtures\CustomerFixture;
use app\modules\sale\models\Customer;
use app\modules\westnet\ecopagos\models\Payout;
use Codeception\Stub;
use app\modules\westnet\ecopagos\models\Cashier;

class BatchClosureHasPayoutTest extends \Codeception\Test\Unit
{

    public function _fixtures()
    {
        return [
            'ecopago' => [
                'class' => EcopagoFixture::class
            ],
            'collector' => [
                'class' => CollectorFixture::class
            ],
            'customer' => [
                'class' => CustomerFixture::class
            ]
        ];
    }

    public function testInvalidWhenEmptyAndNew()
    {
        $model = new BatchClosureHasPayout();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        //Batch_clousure
        $batch_closure = new BatchClosure([
            'ecopago_id' => 1,
            'collector_id' => 1,
        ]);
        $batch_closure->save();

        //Payout
        $customer = Customer::findOne(45900);
        $cashier = Stub::make(Cashier::class, [
            'ecopago_id' => 1,      //Fixture
            'name' => 'Laura',
            'lastname' => 'Mazza',
            'number' => '1',
            'document_number' => '12456789',
            'document_type' => 'DNI',
            'username' => 'lmazza',
            'status' => Cashier::STATUS_ACTIVE,
            'username' => 'lmazza',
            'password' => '123',
            'password_repeat' => '123',
            'beforeSave' => function () { return true; }
        ]);
        $cashier->save();
        $payout = Stub::make(Payout::class, [
            'ecopago_id' => 1,      //Fixture
            'cashier_id' => $cashier->cashier_id,       //Fixture
            'amount' => '123',
            'customer_number' => $customer->code,      //Fixture
            'beforeSave' => function () { return true; },
            'afterSave' => function () { return true; }
        ]);
        $payout->save();

        $model = new BatchClosureHasPayout([
            'batch_closure_id' => $batch_closure->batch_closure_id,
            'payout_id' => $payout->payout_id,
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new BatchClosureHasPayout();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        //Batch_clousure
        $batch_closure = new BatchClosure([
            'ecopago_id' => 1,
            'collector_id' => 1,
        ]);
        $batch_closure->save();

        //Payout
        $customer = Customer::findOne(45900);
        $cashier = Stub::make(Cashier::class, [
            'ecopago_id' => 1,      //Fixture
            'name' => 'Laura',
            'lastname' => 'Mazza',
            'number' => '1',
            'document_number' => '12456789',
            'document_type' => 'DNI',
            'username' => 'lmazza',
            'status' => Cashier::STATUS_ACTIVE,
            'username' => 'lmazza',
            'password' => '123',
            'password_repeat' => '123',
            'beforeSave' => function () { return true; }
        ]);
        $cashier->save();
        $payout = Stub::make(Payout::class, [
            'ecopago_id' => 1,      //Fixture
            'cashier_id' => $cashier->cashier_id,       //Fixture
            'amount' => '123',
            'customer_number' => $customer->code,      //Fixture
            'beforeSave' => function () { return true; },
            'afterSave' => function () { return true; }
        ]);
        $payout->save();

        $model = new BatchClosureHasPayout([
            'batch_closure_id' => $batch_closure->batch_closure_id,
            'payout_id' => $payout->payout_id,
        ]);

        expect('Valid when full and new', $model->save())->true();
    }
}
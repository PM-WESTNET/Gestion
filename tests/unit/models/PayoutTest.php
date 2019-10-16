<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 14/01/19
 * Time: 15:30
 */

use app\modules\westnet\ecopagos\models\Payout;
use app\tests\fixtures\EcopagoFixture;
use app\tests\fixtures\CustomerFixture;
use app\modules\sale\models\Customer;
use app\tests\fixtures\CashierFixture;
use Codeception\Stub;
use app\modules\westnet\ecopagos\models\Cashier;
use app\tests\fixtures\UserFixture;

class PayoutTest extends \Codeception\Test\Unit
{

    public function _fixtures()
    {
        return [
            'ecopago' => [
                'class' => EcopagoFixture::class
            ],
            'customer' => [
                'class' => CustomerFixture::class
            ],
            'cashier' => [
                'class' => CashierFixture::class
            ],
            'user' => [
                'class' => UserFixture::class
            ]
        ];
    }

    public function testInvalidWhenEmptyAndNew()
    {
        $model = new Payout();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $customer = Customer::findOne(45900);

        $model = new Payout([
            'ecopago_id' => 1,      //Fixture
            'cashier_id' => 1,       //Fixture
            'amount' => '123',
            'customer_number' => $customer->code,      //Fixture
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new Payout();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
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
            'beforeSave' => function () { return true; },
            'user_id' => 1
        ]);
        $cashier->save();

        $model = Stub::make(Payout::class, [
            'ecopago_id' => 1,      //Fixture
            'cashier_id' => $cashier->cashier_id,       //Fixture
            'amount' => '123',
            'customer_number' => $customer->code,      //Fixture
            'beforeSave' => function () { return true; },
            'afterSave' => function () { return true; },
            'payment_id' => 1,
            'customer_id' => 45900,
            'date' => (new \DateTime('now'))->format('d-m-Y'),
            'time' => (new \DateTime('now'))->format('H:i:s'),
            'datetime' => (new \DateTime('now'))->getTimestamp(),
        ]);
        Stub::update($model, [
            'beforeSave' => function () use ($model) { $model->date = Yii::$app->formatter->asDate($model->date, 'yyyy-MM-dd'); return true; }
        ]);

        expect('Valid when full and new', $model->save())->true();
    }

    public function testFetchStatuses() {
        $statuses = Payout::staticFetchStatuses();
        $valid = false;
        $reversed = false;
        $closed = false;
        $closed_by_batch = false;

        foreach ($statuses as $key => $value){
            if ($key == Payout::STATUS_VALID) {
                $valid = true;
            }
            if ($key == Payout::STATUS_REVERSED) {
                $reversed = true;
            }
            if ($key == Payout::STATUS_CLOSED) {
                $closed = true;
            }
            if ($key == Payout::STATUS_CLOSED_BY_BATCH) {
                $closed_by_batch = true;
            }
        }

        expect('status valid' , $valid)->true();
        expect('status reversed' , $reversed)->true();
        expect('status closed' , $closed)->true();
        expect('status closed_by_batch' , $closed_by_batch)->true();
    }

    public function testIsClosed() {
        $customer = Customer::findOne(45900);       //Fixture

        $model = new Payout([
            'ecopago_id' => 1,      //Fixture
            'cashier_id' => 1,       //Fixture
            'amount' => '123',
            'customer_number' => $customer->code,      //Fixture
            'status' => Payout::STATUS_VALID
        ]);

        expect('Payout is not closed', $model->isClosed())->false();

        $model->status = Payout::STATUS_CLOSED;

        expect('Payout is closed', $model->isClosed())->true();
    }

    public function testIsValid() {
        $customer = Customer::findOne(45900);       //Fixture

        $model = new Payout([
            'ecopago_id' => 1,      //Fixture
            'cashier_id' => 1,       //Fixture
            'amount' => '123',
            'customer_number' => $customer->code,      //Fixture
            'status' => Payout::STATUS_VALID
        ]);

        expect('Payout is valid', $model->isValid())->true();

        $model->status = Payout::STATUS_CLOSED;

        expect('Payout is not valid', $model->isValid())->false();
    }

    public function testIsReversable() {

        $customer = Customer::findOne(45900);       //Fixture

        $model = new Payout([
            'ecopago_id' => 1,      //Fixture
            'cashier_id' => 1,       //Fixture
            'amount' => '123',
            'customer_number' => $customer->code,      //Fixture
            'status' => Payout::STATUS_VALID
        ]);

        expect('is reversable with valid', $model->isReversable())->true();
        $model->status = Payout::STATUS_CLOSED_BY_BATCH;
        expect('is reversable with closed by batch', $model->isReversable())->false();
        $model->status = Payout::STATUS_CLOSED;
        expect('is reversable with closed', $model->isReversable())->false();
        $model->status = Payout::STATUS_REVERSED;
        expect('is reversable with reversed', $model->isReversable())->false();
    }

    public function testIncrementNumberCopy() {
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
            'beforeSave' => function () { return true; },
            'user_id' => 1
        ]);
        $cashier->save();

        $model = Stub::make(Payout::class, [
            'ecopago_id' => 1,      //Fixture
            'cashier_id' => $cashier->cashier_id,       //Fixture
            'amount' => '123',
            'customer_number' => $customer->code,      //Fixture
            'copy_number' => 1,
            'beforeSave' => function () { return true; },
            'afterSave' => function () { return true; },
            'payment_id' => 1,
            'customer_id' => 45900,
            'date' => (new \DateTime('now'))->format('d-m-Y'),
            'time' => (new \DateTime('now'))->format('H:i:s'),
            'datetime' => (new \DateTime('now'))->getTimestamp(),
        ]);
        Stub::update($model, [
            'beforeSave' => function () use ($model) { $model->date = Yii::$app->formatter->asDate($model->date, 'yyyy-MM-dd'); return true; }
        ]);

        $model->save();
        $model->incrementNumberCopy();

        expect('Increment copy number', $model->copy_number)->equals(2);
    }

}
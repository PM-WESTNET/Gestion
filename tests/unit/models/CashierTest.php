<?php
/**
 * Created by PhpStorm.
 * User: Perez Janet
 * Date: 11/01/19
 * Time: 15:41
 */

use app\modules\westnet\ecopagos\models\Cashier;
use app\tests\fixtures\EcopagoFixture;
use Codeception\Stub;
use app\modules\westnet\ecopagos\models\DailyClosure;

class CashierTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function _fixtures()
    {
        return [
            'ecopago' => [
                'class' => EcopagoFixture::class
            ],
        ];
    }

    public function testInvalidWhenEmptyAndNew()
    {
        $model = new Cashier();
        expect('Invalid when empty and new', $model->validate())->false();
    }

    public function testValidWhenFullAndNew()
    {
        $model = new Cashier([
            'ecopago_id' => 1,      //Fixture
            'name' => 'Laura',
            'lastname' => 'Mazza',
            'number' => '1',
            'document_number' => '12456789',
            'document_type' => 'DNI',
            'username' => 'lmazza',
            'status' => Cashier::STATUS_ACTIVE,
        ]);

        expect('Valid when full and new', $model->validate())->true();
    }

    public function testNotSaveWhenEmptyAndNew()
    {
        $model = new Cashier();
        expect('Invalid when empty and new', $model->save())->false();
    }

    public function testSaveWhenFullAndNew()
    {
        //TODO ver alternativa, se sobreescribe el beforeSave por error al guardar normalmente  [user_id] => Por favor, ejecute las migraciones para el módulo de Ecopago (westnet/ecopagos/migrations)

        $model = Stub::make(Cashier::class, [
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

        $model->save();
        expect('Valid when full and new', $model->save())->true();
    }

    public function testIsActive() {

        $model = new Cashier([
            'ecopago_id' => 1,      //Fixture
            'name' => 'Laura',
            'lastname' => 'Mazza',
            'number' => '1',
            'document_number' => '12456789',
            'document_type' => 'DNI',
            'username' => 'lmazza',
            'status' => Cashier::STATUS_INACTIVE,
        ]);

        $model2 = new Cashier([
            'ecopago_id' => 1,      //Fixture
            'name' => 'Laura',
            'lastname' => 'Mazza',
            'number' => '1',
            'document_number' => '12456789',
            'document_type' => 'DNI',
            'username' => 'lmazza',
            'status' => Cashier::STATUS_ACTIVE,
        ]);

        expect('Is not active', $model->isActive())->false();
        expect('Is active', $model2->isActive())->true();
    }

    public function testCurrentDailyClosure(){
        $model = Stub::make(Cashier::class, [
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

        $model->save();

        expect('Theres no daily closure', $model->currentDailyClosure())->false();

        $daily_closure = new DailyClosure([
            'cashier_id' => $model->cashier_id,      //Fixture
            'ecopago_id' => 1,      //Fixture
        ]);
        $daily_closure->save();

        expect('Theres daily closure', $model->currentDailyClosure())->isInstanceOf(DailyClosure::class);
    }
}